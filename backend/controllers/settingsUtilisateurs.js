const db = require('../db');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const dotenv = require('dotenv').config();
const authenticator = require('authenticator');
const logger = require('../winstonLogger');

exports.getOneUser = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                v.*,
                pr.notif_lots_manquants,
                pr.notif_lots_peremptions,
                pr.notif_lots_inventaires,
                pr.notif_lots_conformites,
                pr.notif_reserves_manquants,
                pr.notif_reserves_peremptions,
                pr.notif_reserves_inventaires,
                pr.notif_vehicules_desinfections,
                pr.notif_vehicules_health,
                pr.notif_tenues_stock,
                pr.notif_tenues_retours,
                pr.notif_benevoles_lots,
                pr.notif_benevoles_vehicules,
                pr.conf_indicateur1Accueil,
                pr.conf_indicateur2Accueil,
                pr.conf_indicateur3Accueil,
                pr.conf_indicateur4Accueil,
                pr.conf_indicateur5Accueil,
                pr.conf_indicateur6Accueil,
                pr.conf_indicateur9Accueil,
                pr.conf_indicateur10Accueil,
                pr.conf_indicateur11Accueil,
                pr.conf_indicateur12Accueil
            FROM
                VIEW_HABILITATIONS v
                LEFT OUTER JOIN PERSONNE_REFERENTE pr ON v.idPersonne = pr.idPersonne
            WHERE
                v.idPersonne = :idPersonne
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

exports.updateMonCompte = async (req, res, next)=>{
    try {
        const utilisateur = await db.query(`
            UPDATE
                PERSONNE_REFERENTE
            SET
                nomPersonne = :nomPersonne,
                prenomPersonne = :prenomPersonne,
                mailPersonne = :mailPersonne,
                telPersonne = :telPersonne,
                fonction = :fonction,
                notif_lots_manquants = :notif_lots_manquants,
                notif_lots_peremptions = :notif_lots_peremptions,
                notif_lots_inventaires = :notif_lots_inventaires,
                notif_lots_conformites = :notif_lots_conformites,
                notif_reserves_manquants = :notif_reserves_manquants,
                notif_reserves_peremptions = :notif_reserves_peremptions,
                notif_reserves_inventaires = :notif_reserves_inventaires,
                notif_vehicules_desinfections = :notif_vehicules_desinfections,
                notif_vehicules_health = :notif_vehicules_health,
                notif_tenues_stock = :notif_tenues_stock,
                notif_tenues_retours = :notif_tenues_retours,
                notif_benevoles_lots = :notif_benevoles_lots,
                notif_benevoles_vehicules = :notif_benevoles_vehicules
            WHERE
                idPersonne = :idPersonne
        ;`,{
            idPersonne: req.verifyJWTandProfile.idPersonne,
            nomPersonne: req.body.data.nomPersonne || null,
            prenomPersonne: req.body.data.prenomPersonne || null,
            mailPersonne: req.body.data.mailPersonne || null,
            telPersonne: req.body.data.telPersonne || null,
            fonction: req.body.data.fonction || null,
            notif_lots_manquants: req.body.data.notif_lots_manquants ? true : false,
            notif_lots_peremptions: req.body.data.notif_lots_peremptions ? true : false,
            notif_lots_inventaires: req.body.data.notif_lots_inventaires ? true : false,
            notif_lots_conformites: req.body.data.notif_lots_conformites ? true : false,
            notif_reserves_manquants: req.body.data.notif_reserves_manquants ? true : false,
            notif_reserves_peremptions: req.body.data.notif_reserves_peremptions ? true : false,
            notif_reserves_inventaires: req.body.data.notif_reserves_inventaires ? true : false,
            notif_vehicules_desinfections: req.body.data.notif_vehicules_desinfections ? true : false,
            notif_vehicules_health: req.body.data.notif_vehicules_health ? true : false,
            notif_tenues_stock: req.body.data.notif_tenues_stock ? true : false,
            notif_tenues_retours: req.body.data.notif_tenues_retours ? true : false,
            notif_benevoles_lots: req.body.data.notif_benevoles_lots ? true : false,
            notif_benevoles_vehicules: req.body.data.notif_benevoles_vehicules ? true : false,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}