const db = require('../db');
const jwt = require('jsonwebtoken');
const jwtFunctions = require('../jwt');
const logger = require('../winstonLogger');
const fonctionsMail = require('../helpers/fonctionsMail');
const moment = require('moment');
const randomString = require("randomstring");

const jwtExpirySeconds = parseInt(process.env.JWT_TENUESPUBLIC_EXPIRATION);

exports.requestAuthentification = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_EXTERNE
            WHERE
                mailExterne = :mailExterne
                AND
                (
                    tokenValidityExterne IS NULL
                    OR
                    tokenValidityExterne < CURRENT_TIMESTAMP
                )
        ;`,{
            mailExterne: req.body.mailDemandeur || null,
        });
        
        if(results.length > 0)
        {
            let codeUsageUnique = randomString.generate({
                length: 9,
                charset: 'alphanumeric',
                capitalization: 'uppercase',
            });
            let validUntil = moment(new Date()).add(jwtExpirySeconds, 'seconds').format('YYYY-MM-DD HH:mm:ss');

            let saveCode = await db.query(`
                UPDATE
                    PERSONNE_EXTERNE
                SET
                    authExterne = :authExterne,
                    tokenValidityExterne = :tokenValidityExterne
                WHERE
                    mailExterne = :mailExterne
            ;`,{
                mailExterne: req.body.mailDemandeur || null,
                authExterne: codeUsageUnique,
                tokenValidityExterne: validUntil,
            });

            await fonctionsMail.registerToMailQueue({
                typeMail: 'authentTenuesPublic',
                otherMail: req.body.mailDemandeur,
                otherContent: codeUsageUnique,
            })
        }

        res.sendStatus(200);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.authenticateWithCode = async (req, res)=>{
    try {
        let externesMatch = await db.query(`
            SELECT DISTINCT
                idExterne
            FROM
                PERSONNE_EXTERNE
            WHERE
                mailExterne = :mailExterne
                AND
                authExterne = :authExterne
                AND
                tokenValidityExterne >= CURRENT_TIMESTAMP
        ;`,{
            mailExterne: req.body.mailDemandeur || null,
            authExterne: req.body.codeUnique || null,
        });

        if(externesMatch.length > 0)
        {
            const token = jwt.sign({externesMatch: externesMatch}, process.env.JWT_TENUESPUBLIC_TOKEN, {
                expiresIn: jwtExpirySeconds,
            });
            const tokenValidUntil = moment(new Date()).add(jwtExpirySeconds, 'seconds');

            let securiserCode = await db.query(`
                UPDATE
                    PERSONNE_EXTERNE
                SET
                    authExterne = null,
                    tokenValidityExterne = :tokenValidityExterne
                WHERE
                    mailExterne = :mailExterne
                    AND
                    authExterne = :authExterne
            ;`,{
                mailExterne: req.body.mailDemandeur || null,
                authExterne: req.body.codeUnique || null,
                tokenValidityExterne: moment(tokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
            });

            res.json({auth: true, token: token, tokenValidUntil: tokenValidUntil});
        }else{
            return res.json({auth: false});
        }

    } catch (error) {
        logger.error(error);
        res.status(500);
        return res.json({auth: false});
    }
}

exports.getTenuesDetailsPublic = async (req, res)=>{
    try {
        let externes = req.decryptPublicToken.externesMatch;

        for(const externe of externes)
        {
            let externeDetails = await db.query(`
                SELECT
                    *
                FROM
                    PERSONNE_EXTERNE
                WHERE
                    idExterne = :idExterne
            ;`,{
                idExterne: externe.idExterne,
            });
            externe.nomPrenomExterne = externeDetails[0].nomPrenomExterne;
            externe.mailExterne = externeDetails[0].mailExterne;


            let tenues = await db.query(`
                SELECT
                    *
                FROM
                    VIEW_TENUES_AFFECTATION
                WHERE
                    idExterne = :idExterne
                ORDER BY
                    libelleMateriel,
                    taille
            ;`,{
                idExterne: externe.idExterne,
            });
            externe.tenues = tenues;

            let cautions = await db.query(`
                SELECT
                    *
                FROM
                    VIEW_CAUTIONS
                WHERE
                    idExterne = :idExterne
            ;`,{
                idExterne: externe.idExterne,
            });
            externe.cautions = cautions;
        }

        res.send(externes);

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.seDeconnecterTenuesPublic = async (req, res)=>{
    try {
        let externes = req.decryptPublicToken.externesMatch;

        for(const externe of externes)
        {
            let supprimerContrainteTemps = await db.query(`
                UPDATE
                    PERSONNE_EXTERNE
                SET
                    authExterne = null,
                    tokenValidityExterne = null
                WHERE
                    idExterne = :idExterne
            ;`,{
                idExterne: externe.idExterne,
            });
        }

        res.sendStatus(200);

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}