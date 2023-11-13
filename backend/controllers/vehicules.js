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
            vehicule.maintenancesRegulieres = maintenancesRegulieres;
            vehicule.maintenancesRegulieresAlertes = maintenancesRegulieresAlertes;
            vehicule.desinfections = desinfections;
            vehicule.desinfectionsAlertes = desinfectionsAlertes;
            vehicule.alertesBenevoles = alertesBenevoles;
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