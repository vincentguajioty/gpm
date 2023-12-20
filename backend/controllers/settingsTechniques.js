const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

exports.getConfigForAdmin = async (req, res) => {
    try {
        const general = await db.query(
            `SELECT
                appname,
                urlsite,
                mailserver,
                mailcopy,
                maintenance,
                resetPassword
            FROM
                CONFIG
        `);

        const cnil = await db.query(
            `SELECT
                cnilDisclaimer,
                mailcnil
            FROM
                CONFIG
        `);

        const aes = await db.query(
            `SELECT
                aesFournisseurTemoin
            FROM
                CONFIG
        `);

        const notifcmd = await db.query(
            `SELECT
                notifications_commandes_demandeur_validation,
                notifications_commandes_demandeur_validationOK,
                notifications_commandes_demandeur_validationNOK,
                notifications_commandes_demandeur_passee,
                notifications_commandes_demandeur_livraisonOK,
                notifications_commandes_demandeur_livraisonNOK,
                notifications_commandes_demandeur_savOK,
                notifications_commandes_demandeur_cloture,
                notifications_commandes_demandeur_abandon,
                notifications_commandes_valideur_validation,
                notifications_commandes_valideur_validationOK,
                notifications_commandes_valideur_validationNOK,
                notifications_commandes_valideur_passee,
                notifications_commandes_valideur_centreCout,
                notifications_commandes_valideur_livraisonOK,
                notifications_commandes_valideur_livraisonNOK,
                notifications_commandes_valideur_savOK,
                notifications_commandes_valideur_cloture,
                notifications_commandes_valideur_abandon,
                notifications_commandes_affectee_validation,
                notifications_commandes_affectee_validationOK,
                notifications_commandes_affectee_validationNOK,
                notifications_commandes_affectee_passee,
                notifications_commandes_affectee_livraisonOK,
                notifications_commandes_affectee_livraisonNOK,
                notifications_commandes_affectee_savOK,
                notifications_commandes_affectee_cloture,
                notifications_commandes_affectee_abandon,
                notifications_commandes_observateur_validation,
                notifications_commandes_observateur_validationOK,
                notifications_commandes_observateur_validationNOK,
                notifications_commandes_observateur_passee,
                notifications_commandes_observateur_livraisonOK,
                notifications_commandes_observateur_livraisonNOK,
                notifications_commandes_observateur_savOK,
                notifications_commandes_observateur_cloture,
                notifications_commandes_observateur_abandon
            FROM
                CONFIG
        `);

        const alertesbenevoles = await db.query(
            `SELECT
                alertes_benevoles_lots,
                alertes_benevoles_vehicules,
                consommation_benevoles,
                consommation_benevoles_auto
            FROM
                CONFIG
        `);


        res.send({
            general: general[0],
            cnil: cnil[0],
            aes: aes[0],
            notifcmd: notifcmd[0],
            alertesbenevoles: alertesbenevoles[0],
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.saveGlobalConfig = async (req, res) => {
    try {
        const general = await db.query(
            `UPDATE
                CONFIG
            SET
                appname = :appname,
                urlsite = :urlsite,
                mailserver = :mailserver,
                mailcopy = :mailcopy,
                maintenance = :maintenance,
                resetPassword = :resetPassword
        `,{
            appname : req.body.appname || null,
            urlsite : req.body.urlsite || null,
            mailserver : req.body.mailserver || null,
            mailcopy : req.body.mailcopy || 0,
            maintenance : req.body.maintenance || 0,
            resetPassword : req.body.resetPassword || 0,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.saveCnilConfig = async (req, res) => {
    try {
        const cnil = await db.query(
            `UPDATE
                CONFIG
            SET
                mailcnil = :mailcnil,
                cnilDisclaimer = :cnilDisclaimer
        `,{
            mailcnil : req.body.mailcnil || null,
            cnilDisclaimer : req.body.cnilDisclaimer || null,
        });

        const resetUsers = await db.query(
            `UPDATE
                PERSONNE_REFERENTE
            SET 
                disclaimerAccept = Null
        `);

        await fonctionsMetiers.deconnecterToutLeMonde();

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.saveAlertesConfig = async (req, res) => {
    try {
        const cnil = await db.query(
            `UPDATE
                CONFIG
            SET
                alertes_benevoles_lots = :alertes_benevoles_lots,
                alertes_benevoles_vehicules = :alertes_benevoles_vehicules,
                consommation_benevoles = :consommation_benevoles,
                consommation_benevoles_auto = :consommation_benevoles_auto
        `,{
            alertes_benevoles_lots : req.body.alertes_benevoles_lots || 0,
            alertes_benevoles_vehicules : req.body.alertes_benevoles_vehicules || 0,
            consommation_benevoles : req.body.consommation_benevoles || 0,
            consommation_benevoles_auto : req.body.consommation_benevoles_auto || 0,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.saveNotifsCommandesConfig = async (req, res) => {
    try {
        const cnil = await db.query(
            `UPDATE
                CONFIG
            SET
                notifications_commandes_demandeur_validation      = :notifications_commandes_demandeur_validation,
                notifications_commandes_demandeur_validationOK    = :notifications_commandes_demandeur_validationOK,
                notifications_commandes_demandeur_validationNOK   = :notifications_commandes_demandeur_validationNOK,
                notifications_commandes_demandeur_passee          = :notifications_commandes_demandeur_passee,
                notifications_commandes_demandeur_livraisonOK     = :notifications_commandes_demandeur_livraisonOK,
                notifications_commandes_demandeur_livraisonNOK    = :notifications_commandes_demandeur_livraisonNOK,
                notifications_commandes_demandeur_savOK           = :notifications_commandes_demandeur_savOK,
                notifications_commandes_demandeur_cloture         = :notifications_commandes_demandeur_cloture,
                notifications_commandes_demandeur_abandon         = :notifications_commandes_demandeur_abandon,
                notifications_commandes_valideur_validation       = :notifications_commandes_valideur_validation,
                notifications_commandes_valideur_validationOK     = :notifications_commandes_valideur_validationOK,
                notifications_commandes_valideur_validationNOK    = :notifications_commandes_valideur_validationNOK,
                notifications_commandes_valideur_passee           = :notifications_commandes_valideur_passee,
                notifications_commandes_valideur_centreCout       = :notifications_commandes_valideur_centreCout,
                notifications_commandes_valideur_livraisonOK      = :notifications_commandes_valideur_livraisonOK,
                notifications_commandes_valideur_livraisonNOK     = :notifications_commandes_valideur_livraisonNOK,
                notifications_commandes_valideur_savOK            = :notifications_commandes_valideur_savOK,
                notifications_commandes_valideur_cloture          = :notifications_commandes_valideur_cloture,
                notifications_commandes_valideur_abandon          = :notifications_commandes_valideur_abandon,
                notifications_commandes_affectee_validation       = :notifications_commandes_affectee_validation,
                notifications_commandes_affectee_validationOK     = :notifications_commandes_affectee_validationOK,
                notifications_commandes_affectee_validationNOK    = :notifications_commandes_affectee_validationNOK,
                notifications_commandes_affectee_passee           = :notifications_commandes_affectee_passee,
                notifications_commandes_affectee_livraisonOK      = :notifications_commandes_affectee_livraisonOK,
                notifications_commandes_affectee_livraisonNOK     = :notifications_commandes_affectee_livraisonNOK,
                notifications_commandes_affectee_savOK            = :notifications_commandes_affectee_savOK,
                notifications_commandes_affectee_cloture          = :notifications_commandes_affectee_cloture,
                notifications_commandes_affectee_abandon          = :notifications_commandes_affectee_abandon,
                notifications_commandes_observateur_validation    = :notifications_commandes_observateur_validation,
                notifications_commandes_observateur_validationOK  = :notifications_commandes_observateur_validationOK,
                notifications_commandes_observateur_validationNOK = :notifications_commandes_observateur_validationNOK,
                notifications_commandes_observateur_passee        = :notifications_commandes_observateur_passee,
                notifications_commandes_observateur_livraisonOK   = :notifications_commandes_observateur_livraisonOK,
                notifications_commandes_observateur_livraisonNOK  = :notifications_commandes_observateur_livraisonNOK,
                notifications_commandes_observateur_savOK         = :notifications_commandes_observateur_savOK,
                notifications_commandes_observateur_cloture       = :notifications_commandes_observateur_cloture,
                notifications_commandes_observateur_abandon       = :notifications_commandes_observateur_abandon
        `,{
            notifications_commandes_demandeur_validation     : req.body.notifications_commandes_demandeur_validation || 0,
            notifications_commandes_demandeur_validationOK   : req.body.notifications_commandes_demandeur_validationOK || 0,
            notifications_commandes_demandeur_validationNOK  : req.body.notifications_commandes_demandeur_validationNOK || 0,
            notifications_commandes_demandeur_passee         : req.body.notifications_commandes_demandeur_passee || 0,
            notifications_commandes_demandeur_livraisonOK    : req.body.notifications_commandes_demandeur_livraisonOK || 0,
            notifications_commandes_demandeur_livraisonNOK   : req.body.notifications_commandes_demandeur_livraisonNOK || 0,
            notifications_commandes_demandeur_savOK          : req.body.notifications_commandes_demandeur_savOK || 0,
            notifications_commandes_demandeur_cloture        : req.body.notifications_commandes_demandeur_cloture || 0,
            notifications_commandes_demandeur_abandon        : req.body.notifications_commandes_demandeur_abandon || 0,
            notifications_commandes_valideur_validation      : req.body.notifications_commandes_valideur_validation || 0,
            notifications_commandes_valideur_validationOK    : req.body.notifications_commandes_valideur_validationOK || 0,
            notifications_commandes_valideur_validationNOK   : req.body.notifications_commandes_valideur_validationNOK || 0,
            notifications_commandes_valideur_passee          : req.body.notifications_commandes_valideur_passee || 0,
            notifications_commandes_valideur_centreCout      : req.body.notifications_commandes_valideur_centreCout || 0,
            notifications_commandes_valideur_livraisonOK     : req.body.notifications_commandes_valideur_livraisonOK || 0,
            notifications_commandes_valideur_livraisonNOK    : req.body.notifications_commandes_valideur_livraisonNOK || 0,
            notifications_commandes_valideur_savOK           : req.body.notifications_commandes_valideur_savOK || 0,
            notifications_commandes_valideur_cloture         : req.body.notifications_commandes_valideur_cloture || 0,
            notifications_commandes_valideur_abandon         : req.body.notifications_commandes_valideur_abandon || 0,
            notifications_commandes_affectee_validation      : req.body.notifications_commandes_affectee_validation || 0,
            notifications_commandes_affectee_validationOK    : req.body.notifications_commandes_affectee_validationOK || 0,
            notifications_commandes_affectee_validationNOK   : req.body.notifications_commandes_affectee_validationNOK || 0,
            notifications_commandes_affectee_passee          : req.body.notifications_commandes_affectee_passee || 0,
            notifications_commandes_affectee_livraisonOK     : req.body.notifications_commandes_affectee_livraisonOK || 0,
            notifications_commandes_affectee_livraisonNOK    : req.body.notifications_commandes_affectee_livraisonNOK || 0,
            notifications_commandes_affectee_savOK           : req.body.notifications_commandes_affectee_savOK || 0,
            notifications_commandes_affectee_cloture         : req.body.notifications_commandes_affectee_cloture || 0,
            notifications_commandes_affectee_abandon         : req.body.notifications_commandes_affectee_abandon || 0,
            notifications_commandes_observateur_validation   : req.body.notifications_commandes_observateur_validation || 0,
            notifications_commandes_observateur_validationOK : req.body.notifications_commandes_observateur_validationOK || 0,
            notifications_commandes_observateur_validationNOK: req.body.notifications_commandes_observateur_validationNOK || 0,
            notifications_commandes_observateur_passee       : req.body.notifications_commandes_observateur_passee || 0,
            notifications_commandes_observateur_livraisonOK  : req.body.notifications_commandes_observateur_livraisonOK || 0,
            notifications_commandes_observateur_livraisonNOK : req.body.notifications_commandes_observateur_livraisonNOK || 0,
            notifications_commandes_observateur_savOK        : req.body.notifications_commandes_observateur_savOK || 0,
            notifications_commandes_observateur_cloture      : req.body.notifications_commandes_observateur_cloture || 0,
            notifications_commandes_observateur_abandon      : req.body.notifications_commandes_observateur_abandon || 0,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}