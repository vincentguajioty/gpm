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
                e.libelleEtat as notifications,
                l.libelleLieu,
                COUNT(a.idAlerte) as nbAlertesEnCours
            FROM
                VEHICULES v
                LEFT OUTER JOIN VEHICULES_ETATS ve ON v.idVehiculesEtat = ve.idVehiculesEtat
                LEFT OUTER JOIN VEHICULES_TYPES t ON v.idVehiculesType = t.idVehiculesType
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON v.idResponsable = p.idPersonne
                LEFT OUTER JOIN ETATS e ON v.idEtat = e.idEtat
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
                ve.libelleVehiculesEtat,
                t.libelleType,
                p.identifiant,
                p.prenomPersonne,
                p.nomPersonne,
                e.libelleEtat as notifications,
                l.libelleLieu,
                COUNT(a.idAlerte) as nbAlertesEnCours
            FROM
                VEHICULES v
                LEFT OUTER JOIN VEHICULES_ETATS ve ON v.idVehiculesEtat = ve.idVehiculesEtat
                LEFT OUTER JOIN VEHICULES_TYPES t ON v.idVehiculesType = t.idVehiculesType
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON v.idResponsable = p.idPersonne
                LEFT OUTER JOIN ETATS e ON v.idEtat = e.idEtat
                LEFT OUTER JOIN LIEUX l ON v.idLieu = l.idLieu
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

            vehicule.maintenancesPonctuelles = maintenancesPonctuelles;
            vehicule.maintenancesRegulieres = maintenancesRegulieres;
            vehicule.maintenancesRegulieresAlertes = maintenancesRegulieresAlertes;
            vehicule.desinfections = desinfections;
            vehicule.desinfectionsAlertes = desinfectionsAlertes;
            vehicule.alertesBenevoles = alertesBenevoles;
            vehicule.relevesKM = relevesKM;
            vehicule.lots = lots;
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

exports.deleteVehicule = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesDelete(req.verifyJWTandProfile.idPersonne , req.body.idVehicule);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}