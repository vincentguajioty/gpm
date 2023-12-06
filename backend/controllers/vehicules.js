const db = require('../db');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const logger = require('../winstonLogger');
const multer = require('multer');

//Véhicules - section générale
exports.getAllVehicules = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                v.*,
                ve.libelleVehiculesEtat,
                t.libelleType,
                p.identifiant,
                p.prenomPersonne,
                p.nomPersonne,
                e.libelleNotificationEnabled,
                e.notifiationEnabled,
                l.libelleLieu,
                COUNT(a.idAlerte) as nbAlertesEnCours
            FROM
                VEHICULES v
                LEFT OUTER JOIN VEHICULES_ETATS ve ON v.idVehiculesEtat = ve.idVehiculesEtat
                LEFT OUTER JOIN VEHICULES_TYPES t ON v.idVehiculesType = t.idVehiculesType
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON v.idResponsable = p.idPersonne
                LEFT OUTER JOIN NOTIFICATIONS_ENABLED e ON v.idNotificationEnabled = e.idNotificationEnabled
                LEFT OUTER JOIN LIEUX l ON v.idLieu = l.idLieu
                LEFT OUTER JOIN (SELECT * FROM VEHICULES_ALERTES WHERE dateResolutionAlerte IS NULL) a ON a.idVehicule = v.idVehicule
            GROUP BY
                v.idVehicule
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneVehicule = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                v.*,
                car.libelleCarburant,
                ve.libelleVehiculesEtat,
                t.libelleType,
                p.identifiant,
                p.prenomPersonne,
                p.nomPersonne,
                e.libelleNotificationEnabled,
                e.notifiationEnabled,
                l.libelleLieu,
                COUNT(a.idAlerte) as nbAlertesEnCours
            FROM
                VEHICULES v
                LEFT OUTER JOIN VEHICULES_ETATS ve ON v.idVehiculesEtat = ve.idVehiculesEtat
                LEFT OUTER JOIN VEHICULES_TYPES t ON v.idVehiculesType = t.idVehiculesType
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON v.idResponsable = p.idPersonne
                LEFT OUTER JOIN NOTIFICATIONS_ENABLED e ON v.idNotificationEnabled = e.idNotificationEnabled
                LEFT OUTER JOIN LIEUX l ON v.idLieu = l.idLieu
                LEFT OUTER JOIN CARBURANTS car ON v.idCarburant = car.idCarburant
                LEFT OUTER JOIN (SELECT * FROM VEHICULES_ALERTES WHERE dateResolutionAlerte IS NULL) a ON a.idVehicule = v.idVehicule
            WHERE
                v.idVehicule = :idVehicule
            GROUP BY
                v.idVehicule
        ;`,{
            idVehicule: req.body.idVehicule
        });

        for(const vehicule of results)
        {
            let maintenancesPonctuelles = await db.query(`
                SELECT
                    mnt.*,
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne,
                    t.libelleTypeMaintenance
                FROM
                    VEHICULES_MAINTENANCE mnt
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON mnt.idExecutant = p.idPersonne
                    LEFT OUTER JOIN VEHICULES_MAINTENANCE_TYPES t ON mnt.idTypeMaintenance = t.idTypeMaintenance
                WHERE
                    mnt.idVehicule = :idVehicule
                ORDER BY
                    mnt.dateMaintenance DESC
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let maintenancesRegulieres = await db.query(`
                SELECT
                    mnt.*,
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne
                FROM
                    VEHICULES_HEALTH mnt
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON mnt.idPersonne = p.idPersonne
                WHERE
                    idVehicule = :idVehicule
                ORDER BY
                    mnt.dateHealth DESC
            ;`,{
                idVehicule: vehicule.idVehicule
            });
            for(const health of maintenancesRegulieres)
            {
                let checks = await db.query(`
                    SELECT
                        c.*,
                        t.affichageSynthese,
                        t.libelleHealthType
                    FROM
                        VEHICULES_HEALTH_CHECKS c
                        LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON c.idHealthType = t.idHealthType
                    WHERE
                        idVehiculeHealth = :idVehiculeHealth
                ;`,{
                    idVehiculeHealth: health.idVehiculeHealth
                });
                health.checks = checks;

                let remainingChecks = await db.query(`
                    SELECT
                        t.idHealthType,
                        t.libelleHealthType
                    FROM
                        VEHICULES_HEALTH_TYPES t
                        LEFT OUTER JOIN (SELECT * FROM VEHICULES_HEALTH_CHECKS WHERE idVehiculeHealth = :idVehiculeHealth) hc ON t.idHealthType = hc.idHealthType
                    WHERE
                        hc.idVehiculeHealth IS NULL
                ;`,{
                    idVehiculeHealth: health.idVehiculeHealth
                });
                health.remainingChecks = remainingChecks;

            }

            let maintenancesRegulieresAlertes = await db.query(`
                SELECT
                    alertes.*,
                    t.affichageSynthese,
                    t.libelleHealthType
                FROM
                    VEHICULES_HEALTH_ALERTES alertes
                    LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON alertes.idHealthType = t.idHealthType
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });
            for(const alerte of maintenancesRegulieresAlertes)
            {
                let getLast = await db.query(`
                    SELECT
                        MAX(h.dateHealth) as derniereMaintenance,
                        DATE_ADD(MAX(h.dateHealth), INTERVAL :frequenceHealth DAY) as nextMaintenance
                    FROM
                        VEHICULES_HEALTH_CHECKS c
                        LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth
                    WHERE
                        h.idVehicule = :idVehicule
                        AND
                        idHealthType = :idHealthType
                ;`,{
                    idVehicule: vehicule.idVehicule,
                    idHealthType: alerte.idHealthType,
                    frequenceHealth: alerte.frequenceHealth,
                });
                alerte.derniereMaintenance = getLast[0].derniereMaintenance;
                alerte.nextMaintenance = getLast[0].nextMaintenance;

                let isInAlert = false;
                if(new Date(getLast[0].nextMaintenance) <= new Date())
                {
                    isInAlert = true;
                }

                alerte.isInAlert = isInAlert;
            }

            let desinfections = await db.query(`
                SELECT
                    d.*,
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne,
                    t.libelleVehiculesDesinfectionsType,
                    t.affichageSynthese
                FROM
                    VEHICULES_DESINFECTIONS d
                    LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON d.idVehiculesDesinfectionsType = t.idVehiculesDesinfectionsType
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON d.idExecutant = p.idPersonne
                WHERE
                    idVehicule = :idVehicule
                ORDER BY
                    d.dateDesinfection DESC
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let desinfectionsAlertes = await db.query(`
                SELECT
                    alertes.*,
                    t.affichageSynthese,
                    t.libelleVehiculesDesinfectionsType
                FROM
                    VEHICULES_DESINFECTIONS_ALERTES alertes
                    LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON alertes.idVehiculesDesinfectionsType = t.idVehiculesDesinfectionsType
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });
            for(const alerte of desinfectionsAlertes)
            {
                let getLast = await db.query(`
                    SELECT
                        MAX(dateDesinfection) as derniereDesinfection,
                        DATE_ADD(MAX(dateDesinfection), INTERVAL :frequenceDesinfection DAY) as nextDesinfection
                    FROM
                        VEHICULES_DESINFECTIONS
                    WHERE
                        idVehicule = :idVehicule
                        AND
                        idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
                ;`,{
                    idVehicule: vehicule.idVehicule,
                    idVehiculesDesinfectionsType: alerte.idVehiculesDesinfectionsType,
                    frequenceDesinfection: alerte.frequenceDesinfection,
                });
                alerte.derniereDesinfection = getLast[0].derniereDesinfection;
                alerte.nextDesinfection = getLast[0].nextDesinfection;

                let isInAlert = false;
                if(new Date(getLast[0].nextDesinfection) <= new Date())
                {
                    isInAlert = true;
                }

                alerte.isInAlert = isInAlert;
            }

            let alertesBenevoles = await db.query(`
                SELECT
                    a.*,
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne,
                    e.libelleVehiculesAlertesEtat,
                    e.couleurVehiuclesAlertesEtat
                FROM
                    VEHICULES_ALERTES a
                    LEFT OUTER JOIN VEHICULES_ALERTES_ETATS e ON a.idVehiculesAlertesEtat = e.idVehiculesAlertesEtat
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
                WHERE
                    idVehicule = :idVehicule
                ORDER BY
                    a.dateCreationAlerte DESC
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let relevesKM = await db.query(`
                SELECT
                    km.*,
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne
                FROM
                    VIEW_VEHICULES_KM km
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON km.idPersonne = p.idPersonne
                WHERE
                    idVehicule = :idVehicule
                ORDER BY
                    km.dateReleve DESC
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let lots = await db.query(`
                SELECT
                    idLot,
                    libelleLot
                FROM
                    LOTS_LOTS
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let documents = await db.query(`
                SELECT
                    *
                FROM
                    VIEW_DOCUMENTS_VEHICULES
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            vehicule.maintenancesPonctuelles = maintenancesPonctuelles;
            vehicule.maintenancesRegulieres = req.verifyJWTandProfile.vehiculeHealth_lecture ? maintenancesRegulieres : [];
            vehicule.maintenancesRegulieresAlertes = req.verifyJWTandProfile.vehiculeHealth_lecture ? maintenancesRegulieresAlertes : [];
            vehicule.desinfections = req.verifyJWTandProfile.desinfections_lecture ? desinfections : [];
            vehicule.desinfectionsAlertes = req.verifyJWTandProfile.desinfections_lecture ? desinfectionsAlertes : [];
            vehicule.alertesBenevoles = req.verifyJWTandProfile.alertesBenevolesVehicules_lecture ? alertesBenevoles : [];
            vehicule.relevesKM = relevesKM;
            vehicule.lots = lots;
            vehicule.documents = documents;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addVehicule = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VEHICULES
            SET
                libelleVehicule = :libelleVehicule,
                affichageSyntheseDesinfections = 1,
                affichageSyntheseHealth = 1
        `,{
            libelleVehicule: req.body.libelleVehicule || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idVehicule) as idVehicule FROM VEHICULES;'
        );

        await fonctionsMetiers.checkOneMaintenance(selectLast[0].idVehicule);
        await fonctionsMetiers.checkOneDesinfection(selectLast[0].idVehicule);

        res.status(201);
        res.json({idVehicule: selectLast[0].idVehicule});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateVehicule = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VEHICULES
            SET
                libelleVehicule = :libelleVehicule,
                immatriculation = :immatriculation,
                marqueModele = :marqueModele,
                idLieu = :idLieu,
                nbPlaces = :nbPlaces,
                dimensions = :dimensions,
                idVehiculesType = :idVehiculesType,
                idNotificationEnabled = :idNotificationEnabled,
                idResponsable = :idResponsable,
                dateAchat = :dateAchat,
                assuranceNumero = :assuranceNumero,
                pneusAVhivers = :pneusAVhivers,
                pneusARhivers = :pneusARhivers,
                climatisation = :climatisation,
                signaletiqueOrange = :signaletiqueOrange,
                signaletiqueBleue = :signaletiqueBleue,
                signaletique2tons = :signaletique2tons,
                signaletique3tons = :signaletique3tons,
                pmv = :pmv,
                fleche = :fleche,
                nbCones = :nbCones,
                poidsVehicule = :poidsVehicule,
                priseAlimentation220 = :priseAlimentation220,
                remarquesVehicule = :remarquesVehicule,
                idVehiculesEtat = :idVehiculesEtat,
                idCarburant = :idCarburant,
                affichageSyntheseDesinfections = :affichageSyntheseDesinfections,
                affichageSyntheseHealth = :affichageSyntheseHealth
            WHERE
                idVehicule = :idVehicule
        `,{
            libelleVehicule : req.body.libelleVehicule || null,
            immatriculation : req.body.immatriculation || null,
            marqueModele : req.body.marqueModele || null,
            idLieu : req.body.idLieu || null,
            nbPlaces : req.body.nbPlaces || null,
            dimensions : req.body.dimensions || null,
            idVehiculesType : req.body.idVehiculesType || null,
            idNotificationEnabled : req.body.idNotificationEnabled || null,
            idResponsable : req.body.idResponsable || null,
            dateAchat : req.body.dateAchat || null,
            assuranceNumero : req.body.assuranceNumero || null,
            pneusAVhivers : req.body.pneusAVhivers || null,
            pneusARhivers : req.body.pneusARhivers || null,
            climatisation : req.body.climatisation || null,
            signaletiqueOrange : req.body.signaletiqueOrange || null,
            signaletiqueBleue : req.body.signaletiqueBleue || null,
            signaletique2tons : req.body.signaletique2tons || null,
            signaletique3tons : req.body.signaletique3tons || null,
            pmv : req.body.pmv || null,
            fleche : req.body.fleche || null,
            nbCones : req.body.nbCones || null,
            poidsVehicule : req.body.poidsVehicule || null,
            priseAlimentation220 : req.body.priseAlimentation220 || null,
            remarquesVehicule : req.body.remarquesVehicule || null,
            idVehiculesEtat : req.body.idVehiculesEtat || null,
            idCarburant : req.body.idCarburant || null,
            affichageSyntheseDesinfections : req.body.affichageSyntheseDesinfections || null,
            affichageSyntheseHealth : req.body.affichageSyntheseHealth || null,
            idVehicule: req.body.idVehicule,
        });

        await fonctionsMetiers.checkOneMaintenance(req.body.idVehicule);
        await fonctionsMetiers.checkOneDesinfection(req.body.idVehicule);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteVehicule = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesDelete(req.verifyJWTandProfile.idPersonne , req.body.idVehicule);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Véhicules PJ
const multerConfigVehicules = multer.diskStorage({
    destination: (req, file, callback) => {
        callback(null, 'uploads/vehicules');
    },
    filename: (req, file, callback) => {
        const ext = file.mimetype.split('/')[1];
        callback(null, `vehicules-${Date.now()}.${ext}`);
    }
});

const uploadVehicules = multer({
    storage: multerConfigVehicules,
});

exports.uploadVehiculeAttachedMulter = uploadVehicules.single('file');

exports.uploadVehiculeAttached = async (req, res, next)=>{
    try {
        const newFileToDB = await db.query(
            `INSERT INTO
                DOCUMENTS_VEHICULES
            SET
                urlFichierDocVehicule = :filename,
                idVehicule            = :idVehicule
        `,{
            filename : req.file.filename,
            idVehicule : req.query.idVehicule,
        });

        const lastSelect = await db.query(`SELECT MAX(idDocVehicules) as idDocVehicules FROM DOCUMENTS_VEHICULES`);

        res.status(200);
        res.json({idDocVehicules: lastSelect[0].idDocVehicules})
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMetaDataVehicule = async (req, res, next)=>{
    try {
        const document = await db.query(
            `SELECT
                *
            FROM
                DOCUMENTS_VEHICULES
            WHERE
                idDocVehicules = :idDocVehicules
        `,{
            idDocVehicules : req.body.idDocVehicules,
        });

        const update = await db.query(
            `UPDATE
                DOCUMENTS_VEHICULES
            SET
                nomDocVehicule   = :nomDocVehicule,
                formatDocVehicule = :formatDocVehicule,
                dateDocVehicule   = :dateDocVehicule,
                idTypeDocument = :idTypeDocument
            WHERE
                idDocVehicules        = :idDocVehicules
        `,{
            nomDocVehicule    : req.body.nomDocVehicule || null,
            formatDocVehicule : document[0].urlFichierDocVehicule.split('.')[1],
            dateDocVehicule   : req.body.dateDocVehicule || new Date(),
            idDocVehicules     : req.body.idDocVehicules,
            idTypeDocument    : req.body.idTypeDocument || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.dropVehiculeDocument = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesDocDelete(req.verifyJWTandProfile.idPersonne , req.body.idDocVehicules);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Releves Kilométriques
exports.addReleveKM = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VEHICULES_RELEVES
            SET
                dateReleve = :dateReleve,
                releveKilometrique = :releveKilometrique,
                idPersonne = :idPersonne,
                idVehicule = :idVehicule
        `,{
            dateReleve: req.body.dateReleve || null,
            releveKilometrique: req.body.releveKilometrique || null,
            idPersonne: req.body.idPersonne || null,
            idVehicule: req.body.idVehicule || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateReleveKM = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VEHICULES_RELEVES
            SET
                dateReleve = :dateReleve,
                releveKilometrique = :releveKilometrique,
                idPersonne = :idPersonne
            WHERE
                idReleve = :idReleve
        `,{
            dateReleve: req.body.dateReleve || null,
            releveKilometrique: req.body.releveKilometrique || null,
            idPersonne: req.body.idPersonne || null,
            idReleve: req.body.idReleve || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteReleveKM = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesReleveDelete(req.verifyJWTandProfile.idPersonne , req.body.idReleve);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Maintenances ponctuelles
exports.addMaintenancePonctuelle = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VEHICULES_MAINTENANCE
            SET
                dateMaintenance = :dateMaintenance,
                idTypeMaintenance = :idTypeMaintenance,
                idExecutant = :idExecutant,
                detailsMaintenance = :detailsMaintenance,
                releveKilometrique = :releveKilometrique,
                idVehicule = :idVehicule
        `,{
            dateMaintenance: req.body.dateMaintenance || null,
            idTypeMaintenance: req.body.idTypeMaintenance || null,
            idExecutant: req.body.idExecutant || null,
            detailsMaintenance: req.body.detailsMaintenance || null,
            releveKilometrique: req.body.releveKilometrique || null,
            idVehicule: req.body.idVehicule || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMaintenancePonctuelle = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VEHICULES_MAINTENANCE
            SET
                dateMaintenance = :dateMaintenance,
                idTypeMaintenance = :idTypeMaintenance,
                idExecutant = :idExecutant,
                detailsMaintenance = :detailsMaintenance,
                releveKilometrique = :releveKilometrique
            WHERE
                idMaintenance = :idMaintenance
        `,{
            dateMaintenance: req.body.dateMaintenance || null,
            idTypeMaintenance: req.body.idTypeMaintenance || null,
            idExecutant: req.body.idExecutant || null,
            detailsMaintenance: req.body.detailsMaintenance || null,
            releveKilometrique: req.body.releveKilometrique || null,
            idMaintenance: req.body.idMaintenance || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteMaintenancePonctuelle = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesMaintenanceDelete(req.verifyJWTandProfile.idPersonne , req.body.idMaintenance);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Maintenances régulières
exports.addMaintenanceReguliere = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VEHICULES_HEALTH
            SET
                dateHealth = :dateHealth,
                idPersonne = :idPersonne,
                releveKilometrique = :releveKilometrique,
                idVehicule = :idVehicule
        `,{
            dateHealth: req.body.dateHealth || null,
            idPersonne: req.body.idPersonne || null,
            releveKilometrique: req.body.releveKilometrique || null,
            idVehicule: req.body.idVehicule || null,
        });

        const getLast = await db.query(`SELECT MAX(idVehiculeHealth) as idVehiculeHealth FROM VEHICULES_HEALTH`);

        for(const check of req.body.checks)
        {
            if(check.idHealthType && check.idHealthType > 0)
            {
                let insert = await db.query(`
                    INSERT INTO
                        VEHICULES_HEALTH_CHECKS
                    SET
                        idVehiculeHealth = :idVehiculeHealth,
                        idHealthType = :idHealthType,
                        remarquesCheck = :remarquesCheck
                ;`,{
                    idVehiculeHealth: getLast[0].idVehiculeHealth,
                    idHealthType: check.idHealthType || null,
                    remarquesCheck: check.remarquesCheck || null,
                });
            }
        }

        await fonctionsMetiers.checkOneMaintenance(req.body.idVehicule);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMaintenanceReguliere = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VEHICULES_HEALTH
            SET
                dateHealth = :dateHealth,
                idPersonne = :idPersonne,
                releveKilometrique = :releveKilometrique
            WHERE
                idVehiculeHealth = :idVehiculeHealth
        `,{
            dateHealth: req.body.dateHealth || null,
            idPersonne: req.body.idPersonne || null,
            releveKilometrique: req.body.releveKilometrique || null,
            idVehiculeHealth: req.body.idVehiculeHealth || null,
        });

        let clean = await db.query(`
            DELETE FROM
                VEHICULES_HEALTH_CHECKS
            WHERE
                idVehiculeHealth = :idVehiculeHealth
        ;`,{
            idVehiculeHealth: req.body.idVehiculeHealth,
        });

        for(const check of req.body.checks)
        {
            if(check.idHealthType && check.idHealthType > 0)
            {
                let insert = await db.query(`
                    INSERT INTO
                        VEHICULES_HEALTH_CHECKS
                    SET
                        idVehiculeHealth = :idVehiculeHealth,
                        idHealthType = :idHealthType,
                        remarquesCheck = :remarquesCheck
                ;`,{
                    idVehiculeHealth: req.body.idVehiculeHealth,
                    idHealthType: check.idHealthType || null,
                    remarquesCheck: check.remarquesCheck || null,
                });
            }
        }

        await fonctionsMetiers.checkOneMaintenance(req.body.idVehicule);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteMaintenanceReguliere = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesHealthDelete(req.verifyJWTandProfile.idPersonne , req.body.idVehiculeHealth);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMaintenanceReguliereAlertes = async (req, res)=>{
    try {
        let clean = await db.query(`
            DELETE FROM
                VEHICULES_HEALTH_ALERTES
            WHERE
                idVehicule = :idVehicule
        ;`,{
            idVehicule: req.body.idVehicule,
        });

        for(const alerte of req.body.typesMaintenance)
        {
            
            if(alerte.frequenceHealth && alerte.frequenceHealth > 0)
            {
                let insert = await db.query(`
                    INSERT INTO
                        VEHICULES_HEALTH_ALERTES
                    SET
                        idVehicule = :idVehicule,
                        frequenceHealth = :frequenceHealth,
                        idHealthType = :idHealthType
                ;`,{
                    idVehicule: req.body.idVehicule,
                    frequenceHealth: alerte.frequenceHealth || null,
                    idHealthType: alerte.value || null,
                });
            }
        }

        await fonctionsMetiers.checkOneMaintenance(req.body.idVehicule);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getMaintenancesRegulieresDashoard = async (req, res)=>{
    try {
        let vehicules = await db.query(`
            SELECT
                idVehicule,
                libelleVehicule
            FROM
                VEHICULES
            WHERE
                affichageSyntheseHealth = true
        ;`);

        let maintenances = await db.query(`
            SELECT
                *
            FROM
                VEHICULES_HEALTH_TYPES
            WHERE
                affichageSynthese = true
            ORDER BY
                libelleHealthType ASC
        ;`);

        for(const vehicule of vehicules)
        {
            vehicule.mntDashboard = [];
            for(const mnt of maintenances)
            {
                let alerte = await db.query(`
                    SELECT
                        *
                    FROM
                        VEHICULES_HEALTH_ALERTES
                    WHERE
                        idVehicule = :idVehicule
                        AND
                        idHealthType = :idHealthType
                ;`,{
                    idVehicule: vehicule.idVehicule,
                    idHealthType: mnt.idHealthType,
                });

                let getLast = await db.query(`
                    SELECT
                        MAX(dateHealth) as dateHealth,
                        DATE_ADD(MAX(dateHealth), INTERVAL :frequenceHealth DAY) as nextMaintenance
                    FROM
                        VEHICULES_HEALTH h
                        LEFT OUTER JOIN VEHICULES_HEALTH_CHECKS c ON h.idVehiculeHealth = c.idVehiculeHealth
                    WHERE
                        idVehicule = :idVehicule
                        AND
                        idHealthType = :idHealthType
                ;`,{
                    idVehicule: vehicule.idVehicule,
                    idHealthType: mnt.idHealthType,
                    frequenceHealth: alerte.length == 1 ? alerte[0].frequenceHealth : null,
                });

                vehicule.mntDashboard.push({
                    idHealthType: mnt.idHealthType,
                    dateHealth: getLast[0].dateHealth,
                    nextMaintenance: getLast[0].nextMaintenance,
                    alerte: alerte.length == 1 ? alerte[0] : null,
                })
            }

            //Ajout des alertes de maintenance
            let maintenancesRegulieresAlertes = await db.query(`
                SELECT
                    alertes.*,
                    t.affichageSynthese,
                    t.libelleHealthType
                FROM
                    VEHICULES_HEALTH_ALERTES alertes
                    LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON alertes.idHealthType = t.idHealthType
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });
            for(const alerte of maintenancesRegulieresAlertes)
            {
                let getLast = await db.query(`
                    SELECT
                        MAX(h.dateHealth) as derniereMaintenance,
                        DATE_ADD(MAX(h.dateHealth), INTERVAL :frequenceHealth DAY) as nextMaintenance
                    FROM
                        VEHICULES_HEALTH_CHECKS c
                        LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth
                    WHERE
                        h.idVehicule = :idVehicule
                        AND
                        idHealthType = :idHealthType
                ;`,{
                    idVehicule: vehicule.idVehicule,
                    idHealthType: alerte.idHealthType,
                    frequenceHealth: alerte.frequenceHealth,
                });
                alerte.derniereMaintenance = getLast[0].derniereMaintenance;
                alerte.nextMaintenance = getLast[0].nextMaintenance;

                let isInAlert = false;
                if(new Date(getLast[0].nextMaintenance) <= new Date())
                {
                    isInAlert = true;
                }

                alerte.isInAlert = isInAlert;
            }
            vehicule.maintenancesRegulieresAlertes = maintenancesRegulieresAlertes;
        }

        let lastThreeMonths = await db.query(`
            SELECT
                h.*,
                p.nomPersonne,
                p.prenomPersonne,
                v.libelleVehicule
            FROM
                VEHICULES_HEALTH h
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON h.idPersonne = p.idPersonne
                LEFT OUTER JOIN VEHICULES v ON h.idVehicule = v.idVehicule
            WHERE
                dateHealth >= DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY)
            ORDER BY
                dateHealth DESC
        ;`);
        for(const mnt of lastThreeMonths)
        {
            let details = await db.query(`
                SELECT
                    t.libelleHealthType
                FROM
                    VEHICULES_HEALTH_CHECKS c
                    LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON c.idHealthType = t.idHealthType
                WHERE
                    c.idVehiculeHealth = :idVehiculeHealth
            ;`,{
                idVehiculeHealth: mnt.idVehiculeHealth,
            });
            mnt.details = details;
        }

        res.send({vehicules: vehicules, maintenances: maintenances, lastThreeMonths: lastThreeMonths});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Désinfections
exports.addDesinfection = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VEHICULES_DESINFECTIONS
            SET
                dateDesinfection = :dateDesinfection,
                idExecutant = :idExecutant,
                remarquesDesinfection = :remarquesDesinfection,
                idVehicule = :idVehicule,
                idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
        `,{
            dateDesinfection: req.body.dateDesinfection || null,
            idExecutant: req.body.idExecutant || null,
            remarquesDesinfection: req.body.remarquesDesinfection || null,
            idVehicule: req.body.idVehicule || null,
            idVehiculesDesinfectionsType: req.body.idVehiculesDesinfectionsType || null,
        });

        await fonctionsMetiers.checkOneDesinfection(req.body.idVehicule);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateDesinfection = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VEHICULES_DESINFECTIONS
            SET
                dateDesinfection = :dateDesinfection,
                idExecutant = :idExecutant,
                remarquesDesinfection = :remarquesDesinfection,
                idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
            WHERE
                idVehiculesDesinfection = :idVehiculesDesinfection
        `,{
            dateDesinfection: req.body.dateDesinfection || null,
            idExecutant: req.body.idExecutant || null,
            remarquesDesinfection: req.body.remarquesDesinfection || null,
            idVehiculesDesinfectionsType: req.body.idVehiculesDesinfectionsType || null,
            idVehiculesDesinfection: req.body.idVehiculesDesinfection || null,
        });
        
        await fonctionsMetiers.checkOneDesinfection(req.body.idVehicule);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteDesinfection = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesDesinfectionsDelete(req.verifyJWTandProfile.idPersonne , req.body.idVehiculesDesinfection);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateDesinfectionAlertes = async (req, res)=>{
    try {
        let clean = await db.query(`
            DELETE FROM
                VEHICULES_DESINFECTIONS_ALERTES
            WHERE
                idVehicule = :idVehicule
        ;`,{
            idVehicule: req.body.idVehicule,
        });

        for(const alerte of req.body.typesDesinfection)
        {
            if(alerte.frequenceDesinfection && alerte.frequenceDesinfection > 0)
            {
                let insert = await db.query(`
                    INSERT INTO
                        VEHICULES_DESINFECTIONS_ALERTES
                    SET
                        idVehicule = :idVehicule,
                        frequenceDesinfection = :frequenceDesinfection,
                        idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
                ;`,{
                    idVehicule: req.body.idVehicule,
                    frequenceDesinfection: alerte.frequenceDesinfection || null,
                    idVehiculesDesinfectionsType: alerte.value || null,
                });
            }
        }

        await fonctionsMetiers.checkOneDesinfection(req.body.idVehicule);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getDesinfectionsDashoard = async (req, res)=>{
    try {
        let vehicules = await db.query(`
            SELECT
                idVehicule,
                libelleVehicule
            FROM
                VEHICULES
            WHERE
                affichageSyntheseDesinfections = true
        ;`);

        let desinfections = await db.query(`
            SELECT
                *
            FROM
                VEHICULES_DESINFECTIONS_TYPES
            WHERE
                affichageSynthese = true
            ORDER BY
                libelleVehiculesDesinfectionsType ASC
        ;`);

        for(const vehicule of vehicules)
        {
            vehicule.desinfDashboard = [];
            for(const desinfection of desinfections)
            {
                let alerte = await db.query(`
                    SELECT
                        *
                    FROM
                        VEHICULES_DESINFECTIONS_ALERTES
                    WHERE
                        idVehicule = :idVehicule
                        AND
                        idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
                ;`,{
                    idVehicule: vehicule.idVehicule,
                    idVehiculesDesinfectionsType: desinfection.idVehiculesDesinfectionsType,
                });

                let getLast = await db.query(`
                    SELECT
                        MAX(dateDesinfection) as dateDesinfection,
                        DATE_ADD(MAX(dateDesinfection), INTERVAL :frequenceDesinfection DAY) as nextDesinfection
                    FROM
                        VEHICULES_DESINFECTIONS
                    WHERE
                        idVehicule = :idVehicule
                        AND
                        idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
                ;`,{
                    idVehicule: vehicule.idVehicule,
                    idVehiculesDesinfectionsType: desinfection.idVehiculesDesinfectionsType,
                    frequenceDesinfection: alerte.length == 1 ? alerte[0].frequenceDesinfection : null,
                });

                vehicule.desinfDashboard.push({
                    idVehiculesDesinfectionsType: desinfection.idVehiculesDesinfectionsType,
                    dateDesinfection: getLast[0].dateDesinfection,
                    nextDesinfection: getLast[0].nextDesinfection,
                    alerte: alerte.length == 1 ? alerte[0] : null,
                })
            }
        }

        let lastThreeMonths = await db.query(`
            SELECT
                d.*,
                t.libelleVehiculesDesinfectionsType,
                p.nomPersonne,
                p.prenomPersonne,
                v.libelleVehicule
            FROM
                VEHICULES_DESINFECTIONS d
                LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON d.idVehiculesDesinfectionsType = t.idVehiculesDesinfectionsType
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON d.idExecutant = p.idPersonne
                LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule
            WHERE
                dateDesinfection >= DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY)
            ORDER BY
                dateDesinfection DESC
        ;`);

        res.send({vehicules: vehicules, desinfections: desinfections, lastThreeMonths: lastThreeMonths});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Alertes des bénévoles
exports.getVehiculesAlertes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                a.*,
                e.libelleVehiculesAlertesEtat,
                e.couleurVehiuclesAlertesEtat,
                v.libelleVehicule,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                VEHICULES_ALERTES a
                LEFT OUTER JOIN VEHICULES_ALERTES_ETATS e ON a.idVehiculesAlertesEtat = e.idVehiculesAlertesEtat
                LEFT OUTER JOIN VEHICULES v ON a.idVehicule = v.idVehicule
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
            ORDER BY
                a.dateCreationAlerte DESC
        ;`);

        if(req.body.idVehicule && req.body.idVehicule != null && req.body.idVehicule > 0)
        {
            results = results.filter(alerte => alerte.idVehicule == req.body.idVehicule)
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.autoAffect = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VEHICULES_ALERTES
            SET
                idTraitant = :idTraitant,
                idVehiculesAlertesEtat = 2,
                datePriseEnCompteAlerte = CURRENT_TIMESTAMP
            WHERE
                idAlerte = :idAlerte
        `,{
            idTraitant : req.verifyJWTandProfile.idPersonne,
            idAlerte: req.body.idAlerte,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.affectationTier = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VEHICULES_ALERTES
            SET
                idTraitant = :idTraitant,
                idVehiculesAlertesEtat = 2,
                datePriseEnCompteAlerte = CURRENT_TIMESTAMP
            WHERE
                idAlerte = :idAlerte
        `,{
            idTraitant : req.body.idTraitant,
            idAlerte: req.body.idAlerte,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.udpateStatut = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VEHICULES_ALERTES
            SET
                idVehiculesAlertesEtat = :idVehiculesAlertesEtat
            WHERE
                idAlerte = :idAlerte
        `,{
            idAlerte: req.body.idAlerte,
            idVehiculesAlertesEtat: req.body.idVehiculesAlertesEtat,
        });

        if(req.body.idVehiculesAlertesEtat == 4 || req.body.idVehiculesAlertesEtat == 5)
        {
            const result = await db.query(`
                UPDATE
                    VEHICULES_ALERTES
                SET
                    dateResolutionAlerte = CURRENT_TIMESTAMP
                WHERE
                    idAlerte = :idAlerte
            `,{
                idAlerte: req.body.idAlerte,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}