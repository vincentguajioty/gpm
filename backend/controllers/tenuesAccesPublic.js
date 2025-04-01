const db = require('../db');
const jwt = require('jsonwebtoken');
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
            const token = jwt.sign({externesMatch: externesMatch, mailExterne: req.body.mailDemandeur}, process.env.JWT_TENUESPUBLIC_TOKEN, {
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
                    *,
                    idTenue as value,
                    CONCAT_WS(' > ', libelleMateriel, taille) as label
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
            externe.tenuesWarning = tenues.filter(ten => ten.dateRetour && ten.dateRetour != null && new Date(ten.dateRetour) <= new Date());

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

exports.demandeRemplacement = async (req, res)=>{
    try {
        let verifTenueAppartenance = await db.query(`
            SELECT
                *
            FROM
                TENUES_AFFECTATION
            WHERE
                idTenue = :idTenue
        ;`,{
            idTenue: req.body.idTenue || null,
        });
        let externesToken = [];
        for(const externe of req.decryptPublicToken.externesMatch){externesToken.push(externe.idExterne)}

        if(externesToken.includes(verifTenueAppartenance[0].idExterne))
        {
            let updateDB = await db.query(`
                UPDATE
                    TENUES_AFFECTATION
                SET
                    demandeBenevoleRemplacement = true,
                    demandeBenevoleRemplacementMotif = :motif
                WHERE
                    idTenue = :idTenue
            ;`,{
                idTenue: req.body.idTenue || null,
                motif: req.body.motif || null,
            });

            if(req.decryptPublicToken.mailExterne && req.decryptPublicToken.mailExterne != null && req.decryptPublicToken.mailExterne != "")
            {
                await fonctionsMail.registerToMailQueue({
                    typeMail: 'confirmationDemandeRemplacementTenue',
                    idObject: req.body.idTenue,
                    otherMail: req.decryptPublicToken.mailExterne,
                });
            }
    
            const usersToNotify = await db.query(`
                SELECT
                    idPersonne
                FROM
                    VIEW_HABILITATIONS
                WHERE
                    notif_benevoles_tenues = true
                    AND
                    notifications = true
                    AND
                    mailPersonne IS NOT NULL
                    AND
                    mailPersonne <> ""
            `);
            for(const personne of usersToNotify)
            {
                await fonctionsMail.registerToMailQueue({
                    typeMail: 'benevoleDemandeRemplacementTenue',
                    idPersonne: personne.idPersonne,
                    idObject: req.body.idTenue,
                });
            }

            res.sendStatus(201);

        }else{
            res.sendStatus(400);
        }
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}