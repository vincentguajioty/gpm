const db = require('../db');
const fonctionsMetiers = require('../fonctionsMetiers');
const dotenv = require('dotenv').config();
const brcypt = require('bcryptjs');
const authenticator = require('authenticator');
const logger = require('../winstonLogger');

exports.getOneUser = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                *
            FROM
                VIEW_HABILITATIONS
            WHERE
                idPersonne = :idPersonne
        `,
        {
            idPersonne : req.body.idPersonne
        });

        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getProfilsOneUser = async (req, res, next)=>{
    try {
        const result = await db.query(`
            SELECT
                t.idProfil as value,
                t.nomProfil as label
            FROM
                PROFILS_PERSONNES p
                LEFT OUTER JOIN PROFILS t ON p.idProfil = t.idProfil
            WHERE
                idPersonne = :idPersonne
            ORDER BY
                nomProfil ASC
        `,
        {
            idPersonne : req.body.idPersonne
        });
        res.send(result);
        
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getMfaUrl = async (req, res, next)=>{
    try {
        const utilisateur = await db.query(
            'SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;',
        {
            idPersonne : req.body.idPersonne,
        });

        if(utilisateur[0].mfaEnabled)
        {
            res.sendStatus(400);
        }
        else
        {
            const formattedKey = authenticator.generateKey();
            const saveKey = await db.query(
                'UPDATE PERSONNE_REFERENTE SET mfaSecret = :mfaSecret WHERE idPersonne = :idPersonne;',
            {
                mfaSecret : formattedKey,
                idPersonne : req.body.idPersonne,
            });

            const url = authenticator.generateTotpUri(formattedKey, utilisateur[0].identifiant, process.env.APP_NAME, 'SHA1', 6, 30);

            return res.json({url: url});
        }
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.enableMfa = async (req, res, next)=>{
    try {
        const utilisateur = await db.query(
            'SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;',
        {
            idPersonne : req.body.idPersonne,
        });

        const mfaCheck = authenticator.verifyToken(utilisateur[0].mfaSecret, req.body.confirmation);

        if(mfaCheck != null)
        {
            const update = await db.query(
                'UPDATE PERSONNE_REFERENTE SET mfaEnabled = true WHERE idPersonne = :idPersonne;',
            {
                idPersonne : req.body.idPersonne,
            });
            await fonctionsMetiers.deconnecterUtilisateur(req.body.idPersonne);
            res.sendStatus(201);
        }
        else
        {
            res.sendStatus(400);
        }
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.disableMfa = async (req, res, next)=>{
    try {
        const update = await db.query(
            'UPDATE PERSONNE_REFERENTE SET mfaEnabled = false, mfaSecret = null WHERE idPersonne = :idPersonne;',
        {
            idPersonne : req.body.idPersonne,
        });
        await fonctionsMetiers.deconnecterUtilisateur(req.body.idPersonne);
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
