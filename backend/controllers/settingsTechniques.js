const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

exports.getConfigForAdmin = async (req, res) => {
    try {
        let general = await db.query(
            `SELECT
                urlsite,
                mailserver,
                mailcopy,
                maintenance,
                resetPassword
            FROM
                CONFIG
        `);
        general[0].appname = process.env.APP_NAME

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

        const reglesCmd = await db.query(
            `SELECT
                c.*,
                e1.libelleEtat as etatInitial,
                e2.libelleEtat as etatFinal
            FROM
                COMMANDES_CONTRAINTES c
                LEFT OUTER JOIN COMMANDES_ETATS e1 ON c.idEtatInitial = e1.idEtat
                LEFT OUTER JOIN COMMANDES_ETATS e2 ON c.idEtatFinal = e2.idEtat
            ORDER BY
                c.idEtatInitial, c.idEtatFinal
        `);

        res.send({
            general: general[0],
            cnil: cnil[0],
            aes: aes[0],
            notifcmd: notifcmd[0],
            alertesbenevoles: alertesbenevoles[0],
            reglesCmd: reglesCmd,
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
                urlsite = :urlsite,
                mailserver = :mailserver,
                mailcopy = :mailcopy,
                maintenance = :maintenance,
                resetPassword = :resetPassword
        `,{
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

exports.getMailQueue = async (req, res) => {
    try {
        const queueMail = await db.query(`
            SELECT
                q.*,
                p.identifiant
            FROM
                MAIL_QUEUE q
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON q.idPersonne = p.idPersonne
            ORDER BY
                sendRequest DESC
            LIMIT 1000
        `);

        res.send(queueMail);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addOneCmdContrainte = async (req, res) => {
    try {
        const general = await db.query(`
            INSERT INTO
                COMMANDES_CONTRAINTES
            SET
                libelleContrainte = :libelleContrainte,
                idEtatInitial = :idEtatInitial,
                idEtatFinal = :idEtatFinal,
                fournisseurObligatoire = :fournisseurObligatoire,
                minDemandeurs = :minDemandeurs,
                minAffectees = :minAffectees,
                minObservateurs = :minObservateurs,
                minValideurs = :minValideurs,
                centreCoutsObligatoire = :centreCoutsObligatoire,
                lieuLivraisonObligatoire = :lieuLivraisonObligatoire,
                minQttMateriel = :minQttMateriel,
                minMontant = :minMontant,
                maxMontant = :maxMontant,
                remarquesGeneralesObligatoires = :remarquesGeneralesObligatoires,
                idTypeDocumentObligatoire = :idTypeDocumentObligatoire,
                nbTypeDocumentObligatoire = :nbTypeDocumentObligatoire,
                remarquesValidationObligatoire = :remarquesValidationObligatoire,
                referenceCommandeFournisseurObligatoire = :referenceCommandeFournisseurObligatoire,
                datePassageCommandeObligatoire = :datePassageCommandeObligatoire,
                datePrevueLivraisonObligatoire = :datePrevueLivraisonObligatoire,
                dateLivraisonEffectiveObligatoire = :dateLivraisonEffectiveObligatoire,
                remarquesLivraisonsObligatoire = :remarquesLivraisonsObligatoire,
                integrationStockObligatoire = :integrationStockObligatoire
        `,{
            libelleContrainte: req.body.libelleContrainte || null,
            idEtatInitial: req.body.idEtatInitial || null,
            idEtatFinal: req.body.idEtatFinal || null,
            fournisseurObligatoire: req.body.fournisseurObligatoire || null,
            minDemandeurs: req.body.minDemandeurs || null,
            minAffectees: req.body.minAffectees || null,
            minObservateurs: req.body.minObservateurs || null,
            minValideurs: req.body.minValideurs || null,
            centreCoutsObligatoire: req.body.centreCoutsObligatoire || null,
            lieuLivraisonObligatoire: req.body.lieuLivraisonObligatoire || null,
            minQttMateriel: req.body.minQttMateriel || null,
            minMontant: req.body.minMontant || null,
            maxMontant: req.body.maxMontant || null,
            remarquesGeneralesObligatoires: req.body.remarquesGeneralesObligatoires || null,
            idTypeDocumentObligatoire: req.body.idTypeDocumentObligatoire || null,
            nbTypeDocumentObligatoire: req.body.nbTypeDocumentObligatoire || null,
            remarquesValidationObligatoire: req.body.remarquesValidationObligatoire || null,
            referenceCommandeFournisseurObligatoire: req.body.referenceCommandeFournisseurObligatoire || null,
            datePassageCommandeObligatoire: req.body.datePassageCommandeObligatoire || null,
            datePrevueLivraisonObligatoire: req.body.datePrevueLivraisonObligatoire || null,
            dateLivraisonEffectiveObligatoire: req.body.dateLivraisonEffectiveObligatoire || null,
            remarquesLivraisonsObligatoire: req.body.remarquesLivraisonsObligatoire || null,
            integrationStockObligatoire: req.body.integrationStockObligatoire || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.dropOneCmdContrainte = async (req, res) => {
    try {
        const general = await db.query(`
            DELETE FROM
                COMMANDES_CONTRAINTES
            WHERE
                idContrainte = :idContrainte
        `,{
            idContrainte : req.body.idContrainte || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}