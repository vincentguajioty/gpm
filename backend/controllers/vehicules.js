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
                    *
                FROM
                    VEHICULES_MAINTENANCE
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let maintenancesRegulieres = await db.query(`
                SELECT
                    *
                FROM
                    VEHICULES_HEALTH
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let desinfections = await db.query(`
                SELECT
                    *
                FROM
                    VEHICULES_DESINFECTIONS
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let alertesBenevoles = await db.query(`
                SELECT
                    *
                FROM
                    VEHICULES_ALERTES
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            let relevesKM = await db.query(`
                SELECT
                    *
                FROM
                    VIEW_VEHICULES_KM
                WHERE
                    idVehicule = :idVehicule
            ;`,{
                idVehicule: vehicule.idVehicule
            });

            vehicule.maintenancesPonctuelles = maintenancesPonctuelles;
            vehicule.maintenancesRegulieres = maintenancesRegulieres;
            vehicule.desinfections = desinfections;
            vehicule.alertesBenevoles = alertesBenevoles;
            vehicule.relevesKM = relevesKM;
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