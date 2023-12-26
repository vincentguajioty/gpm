const db = require('../db');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const authenticator = require('authenticator');
const logger = require('../winstonLogger');
const brcypt = require('bcryptjs');

exports.getAllUsers = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                pr.*,
                v.*
            FROM
                PERSONNE_REFERENTE pr
                LEFT OUTER JOIN VIEW_HABILITATIONS v ON v.idPersonne = pr.idPersonne
        `);
        for(const user of result)
        {
            let profils = await db.query(`
                SELECT
                    p.*,
                    p.idProfil as value,
                    p.libelleProfil as label
                FROM
                    PROFILS_PERSONNES pp    
                    LEFT OUTER JOIN PROFILS p ON pp.idProfil = p.idProfil
                WHERE
                    pp.idPersonne = :idPersonne
            `,{
                idPersonne: user.idPersonne
            });
            user.profils = profils;
        }
        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

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
                pr.notif_consommations_lots,
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

        for(const user of result)
        {
            let profils = await db.query(`
                SELECT
                    p.*,
                    p.idProfil as value,
                    p.libelleProfil as label
                FROM
                    PROFILS_PERSONNES pp    
                    LEFT OUTER JOIN PROFILS p ON pp.idProfil = p.idProfil
                WHERE
                    pp.idPersonne = :idPersonne
            `,{
                idPersonne: user.idPersonne
            });
            user.profils = profils;
        }

        for(const user of result)
        {
            let onGoingResetProcess = await db.query(`
                SELECT
                    *
                FROM
                    RESETPASSWORD
                WHERE
                    idPersonne = :idPersonne
                    AND
                    dateValidite >= CURRENT_TIMESTAMP
            `,{
                idPersonne: user.idPersonne
            });
            user.onGoingToken = onGoingResetProcess.length > 0 ? onGoingResetProcess[0].tokenReset : null;
        }

        for(const user of result)
        {
            let abonnementsNotificationsJournalieres = await db.query(`
                SELECT
                    abo.idCondition as value,
                    cond.libelleCondition as label
                FROM
                    NOTIFICATIONS_ABONNEMENTS abo
                    LEFT OUTER JOIN NOTIFICATIONS_CONDITIONS cond ON abo.idCondition = cond.idCondition
                WHERE
                    idPersonne = :idPersonne
            `,{
                idPersonne: user.idPersonne
            });
            user.abonnementsNotificationsJournalieres = abonnementsNotificationsJournalieres;
        }

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
                notif_benevoles_vehicules = :notif_benevoles_vehicules,
                notif_consommations_lots = :notif_consommations_lots,
                conf_indicateur1Accueil = :conf_indicateur1Accueil,
                conf_indicateur2Accueil = :conf_indicateur2Accueil,
                conf_indicateur3Accueil = :conf_indicateur3Accueil,
                conf_indicateur4Accueil = :conf_indicateur4Accueil,
                conf_indicateur5Accueil = :conf_indicateur5Accueil,
                conf_indicateur6Accueil = :conf_indicateur6Accueil,
                conf_indicateur9Accueil = :conf_indicateur9Accueil,
                conf_indicateur10Accueil = :conf_indicateur10Accueil,
                conf_indicateur11Accueil = :conf_indicateur11Accueil,
                conf_indicateur12Accueil = :conf_indicateur12Accueil
            WHERE
                idPersonne = :idPersonne
        ;`,{
            idPersonne: req.body.idPersonne,
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
            notif_consommations_lots: req.body.data.notif_consommations_lots ? true : false,
            conf_indicateur1Accueil: req.body.data.conf_indicateur1Accueil ? true : false,
            conf_indicateur2Accueil: req.body.data.conf_indicateur2Accueil ? true : false,
            conf_indicateur3Accueil: req.body.data.conf_indicateur3Accueil ? true : false,
            conf_indicateur4Accueil: req.body.data.conf_indicateur4Accueil ? true : false,
            conf_indicateur5Accueil: req.body.data.conf_indicateur5Accueil ? true : false,
            conf_indicateur6Accueil: req.body.data.conf_indicateur6Accueil ? true : false,
            conf_indicateur9Accueil: req.body.data.conf_indicateur9Accueil ? true : false,
            conf_indicateur10Accueil: req.body.data.conf_indicateur10Accueil ? true : false,
            conf_indicateur11Accueil: req.body.data.conf_indicateur11Accueil ? true : false,
            conf_indicateur12Accueil: req.body.data.conf_indicateur12Accueil ? true : false,
        });

        const cleanQuery = await db.query(`
            DELETE FROM
                NOTIFICATIONS_ABONNEMENTS
            WHERE
                idPersonne     = :idPersonne
        `,{
            idPersonne     : req.body.idPersonne || null,
        });

        for(const entry of req.body.data.abonnementsNotificationsJournalieres)
        {
            const insertQuery = await db.query(`
                INSERT INTO
                    NOTIFICATIONS_ABONNEMENTS
                SET
                    idPersonne     = :idPersonne,
                    idCondition = :idCondition
            `,{
                idPersonne     : req.body.idPersonne || null,
                idCondition : entry.value || null,
            });
        }

        await fonctionsMetiers.notificationsMAJpersonne();
        await fonctionsMetiers.majIndicateursPersonne(req.body.idPersonne, true);
        await fonctionsMetiers.majNotificationsPersonne(req.body.idPersonne, true);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addUser = async (req, res, next)=>{
    try {
        const utilisateur = await db.query(`
            INSERT INTO
                PERSONNE_REFERENTE
            SET
                identifiant                   = :identifiant,
                conf_indicateur1Accueil       = false,
                conf_indicateur2Accueil       = false,
                conf_indicateur3Accueil       = false,
                conf_indicateur4Accueil       = false,
                conf_indicateur5Accueil       = false,
                conf_indicateur6Accueil       = false,
                conf_indicateur9Accueil       = false,
                conf_indicateur10Accueil      = false,
                conf_indicateur11Accueil      = false,
                conf_indicateur12Accueil      = false,
                notif_lots_manquants          = false,
                notif_lots_peremptions        = false,
                notif_lots_inventaires        = false,
                notif_lots_conformites        = false,
                notif_reserves_manquants      = false,
                notif_reserves_peremptions    = false,
                notif_reserves_inventaires    = false,
                notif_vehicules_desinfections = false,
                notif_vehicules_health        = false,
                notif_tenues_stock            = false,
                notif_tenues_retours          = false,
                notif_benevoles_lots          = false,
                notif_benevoles_vehicules     = false,
                notif_consommations_lots      = false,
                notifications_abo_cejour      = false,
                cnil_anonyme                  = false,
                isActiveDirectory             = false,
                mfaEnabled                    = false
        ;`,{
            identifiant: req.body.identifiant || null,
        });

        let selectLast = await db.query(
            'SELECT MAX(idPersonne) as idPersonne FROM PERSONNE_REFERENTE;'
        );

        let newPasswordWithSalt = process.env.LOCAL_SELS_PRE + req.body.identifiant + process.env.LOCAL_SELS_POST;
        const saltRounds = parseInt(process.env.BCRYPT_SALTROUND);
        brcypt.hash(newPasswordWithSalt, saltRounds, async (err, hash) => {
            if(err){logger.error(err);}
    
            const update = await db.query(
                'UPDATE PERSONNE_REFERENTE SET motDePasse = :motDePasse WHERE idPersonne = :idPersonne;',
            {
                motDePasse    : hash,
                idPersonne : selectLast[0].idPersonne,
            });
        });

        res.status(201);
        res.json({idPersonne: selectLast[0].idPersonne});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.unlinkAD = async (req, res, next)=>{
    try {
        const update = await db.query(
            'UPDATE PERSONNE_REFERENTE SET isActiveDirectory = false WHERE idPersonne = :idPersonne;',
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

exports.linkAD = async (req, res, next)=>{
    try {
        const update = await db.query(
            'UPDATE PERSONNE_REFERENTE SET isActiveDirectory = true WHERE idPersonne = :idPersonne;',
        {
            idPersonne : req.body.idPersonne,
        });
        await fonctionsMetiers.majLdapOneUser(req.body.idPersonne);
        await fonctionsMetiers.deconnecterUtilisateur(req.body.idPersonne);
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateProfils = async (req, res, next)=>{
    try {
        let clean = await db.query(`
            DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne;
        `,
        {
            idPersonne : req.body.idPersonne,
        });
        for (const entry of req.body.profils) {
            const add = await db.query(`
                INSERT INTO PROFILS_PERSONNES SET idPersonne = :idPersonne, idProfil = :idProfil;
            `,
            {
                idPersonne : req.body.idPersonne,
                idProfil  : entry.value
            });
        }
        
        await fonctionsMetiers.majIndicateursPersonne(req.body.idPersonne, true);
        await fonctionsMetiers.majNotificationsPersonne(req.body.idPersonne, true);
        await fonctionsMetiers.majValideursPersonne(true);

        await fonctionsMetiers.deconnecterUtilisateur(req.body.idPersonne);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.anonymiserUser = async (req, res, next)=>{
    try {
        await fonctionsMetiers.cnilAnonyme(req.body.idPersonne);
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteUser = async (req, res, next)=>{
    try {
        const deleteResult = await fonctionsDelete.annuaireDelete(req.verifyJWTandProfile.idPersonne , req.body.idPersonne);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}