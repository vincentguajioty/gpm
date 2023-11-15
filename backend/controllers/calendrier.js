const db = require('../db');
const logger = require('../winstonLogger');

exports.peremptionsLots = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                m.idElement,
                m.peremption,
                c.libelleMateriel,
                l.libelleLot,
                s.libelleSac,
                e.libelleEmplacement,
                l.idLot
            FROM
                MATERIEL_ELEMENT m
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON l.idNotificationEnabled = notif.idNotificationEnabled
            WHERE
                peremptionNotification IS NOT NULL
                AND
                notifiationEnabled = true
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.peremptionsReserves = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                m.idReserveElement,
                m.peremptionReserve,
                r.libelleMateriel,
                c.libelleConteneur,
                c.idConteneur
            FROM
                RESERVES_MATERIEL m
                LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur
                LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue
            WHERE
                peremptionReserve IS NOT NULL
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.inventairesPassesLots = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                i.idInventaire,
                i.dateInventaire,
                i.commentairesInventaire,
                l.libelleLot,
                p.prenomPersonne,
                p.nomPersonne
            FROM
                INVENTAIRES i
                LEFT OUTER JOIN LOTS_LOTS l ON i.idLot = l.idLot
                LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON l.idNotificationEnabled = notif.idNotificationEnabled
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne
            WHERE
                notifiationEnabled = true
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.inventairesPassesReserves = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                i.idReserveInventaire,
                i.dateInventaire,
                i.commentairesInventaire,
                c.libelleConteneur,
                p.prenomPersonne,
                p.nomPersonne
            FROM
                RESERVES_INVENTAIRES i
                LEFT OUTER JOIN RESERVES_CONTENEUR c ON i.idConteneur = c.idConteneur
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.inventairesFutursLots = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                l.idLot,
                l.libelleLot,
                DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) as nextInventaire
            FROM
                LOTS_LOTS l
                LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON l.idNotificationEnabled = notif.idNotificationEnabled
            WHERE
                notifiationEnabled = true
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.inventairesFutursReserves = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                idConteneur,
                libelleConteneur,
                DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) as nextInventaire
            FROM
                RESERVES_CONTENEUR
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.commandesLivraisons = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                c.idCommande,
                c.dateLivraisonPrevue,
                c.nomCommande,
                l.libelleLieu,
                f.nomFournisseur
            FROM
                COMMANDES c
                LEFT OUTER JOIN LIEUX l ON c.idLieuLivraison = l.idLieu
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
            WHERE
                idEtat = 4
                AND
                dateLivraisonPrevue IS NOT NULL
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.vehiculesMntPonctuelles = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                m.idMaintenance,
                m.dateMaintenance,
                m.detailsMaintenance,
                p.prenomPersonne,
                p.nomPersonne,
                v.libelleVehicule,
                v.idVehicule
            FROM
                VEHICULES_MAINTENANCE m
                LEFT OUTER JOIN VEHICULES v on m.idVehicule = v.idVehicule
                LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON v.idNotificationEnabled = notif.idNotificationEnabled
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idExecutant = p.idPersonne
            WHERE
                notifiationEnabled = true
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.vehiculesMntRegPassees = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                d.idVehiculeHealth,
                d.dateHealth,
                p.prenomPersonne,
                p.nomPersonne,
                v.libelleVehicule,
                v.idVehicule
            FROM
                VEHICULES_HEALTH d
                LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON d.idPersonne = p.idPersonne
            WHERE
                v.affichageSyntheseHealth = 1
        `);
        for(const mnt of result)
        {
            let details = await db.query(`
                SELECT
                    t.libelleHealthType
                FROM
                    VEHICULES_HEALTH_CHECKS c
                    LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON c.idHealthType = t.idHealthType
                WHERE
                    c.idVehiculeHealth = :idVehiculeHealth
            `,{
                idVehiculeHealth: mnt.idVehiculeHealth,
            });
            mnt.contenu = details;
        }
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.vehiculesMntRegFutures = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                a.idHealthAlerte,
                a.idVehicule,
                v.libelleVehicule,
                t.libelleHealthType,
                MAX(cal.dateHealth) as dateHealth,
                DATE_ADD(MAX(cal.dateHealth) , INTERVAL a.frequenceHealth DAY) as nextHealth
            FROM
                VEHICULES_HEALTH_ALERTES a
                LEFT OUTER JOIN VEHICULES v ON a.idVehicule=v.idVehicule
                LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON a.idHealthType = t.idHealthType
                LEFT OUTER JOIN (SELECT c.*, h.dateHealth, h.idVehicule FROM VEHICULES_HEALTH_CHECKS c LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth)cal ON a.idHealthType = cal.idHealthType AND cal.idVehicule = a.idVehicule
            GROUP BY
                a. idHealthAlerte
        `);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.vehiculesDesinfectionsPassees = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                d.idVehiculesDesinfection,
                d.dateDesinfection,
                t.libelleVehiculesDesinfectionsType,
                p.prenomPersonne,
                p.nomPersonne,
                v.libelleVehicule,
                v.idVehicule
            FROM
                VEHICULES_DESINFECTIONS d
                LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON d.idVehiculesDesinfectionsType = t.idVehiculesDesinfectionsType
                LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON d.idExecutant = p.idPersonne
        `);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.vehiculesDesinfectionsFutures = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                a.idDesinfectionsAlerte,
                a.idVehicule,
                vv.libelleVehicule,
                t.libelleVehiculesDesinfectionsType,
                MAX(v.dateDesinfection) as dateDesinfection,
                DATE_ADD(MAX(v.dateDesinfection), INTERVAL frequenceDesinfection DAY) as nextDesinfection
            FROM
                VEHICULES_DESINFECTIONS_ALERTES a
                LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON a.idVehiculesDesinfectionsType=t.idVehiculesDesinfectionsType
                LEFT OUTER JOIN VEHICULES_DESINFECTIONS v ON a.idVehiculesDesinfectionsType = v.idVehiculesDesinfectionsType AND a.idVehicule=v.idVehicule
                LEFT OUTER JOIN VEHICULES vv ON a.idVehicule = vv.idVehicule
            GROUP BY
                a.idDesinfectionsAlerte
        `);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.tenuesAffectations = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                ta.idTenue,
                ta.dateAffectation,
                tc.libelleCatalogueTenue,
                ta.personneNonGPM,
                p.prenomPersonne,
                p.nomPersonne
            FROM
                TENUES_AFFECTATION ta
                JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON ta.idPersonne = p.idPersonne
            WHERE
                dateAffectation IS NOT NULL
        `);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.tenuesRetours = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                ta.idTenue,
                ta.dateRetour,
                tc.libelleCatalogueTenue,
                ta.personneNonGPM,
                p.prenomPersonne,
                p.nomPersonne
            FROM
                TENUES_AFFECTATION ta
                JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON ta.idPersonne = p.idPersonne
            WHERE
                dateRetour IS NOT NULL
        ;`);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.cautionsEmissions = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                c.idCaution,
                c.dateEmissionCaution,
                c.detailsMoyenPaiement,
                c.personneNonGPM,
                p.prenomPersonne,
                p.nomPersonne
            FROM
                CAUTIONS c
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne
            WHERE
                dateEmissionCaution IS NOT NULL
        `);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.cautionsExpirations = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                c.idCaution,
                c.dateExpirationCaution,
                c.detailsMoyenPaiement,
                c.personneNonGPM,
                p.prenomPersonne,
                p.nomPersonne
            FROM
                CAUTIONS c
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne
            WHERE
                dateExpirationCaution IS NOT NULL
        `);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.toDoListOwn = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                t.idTache,
                t.titre,
                t.dateExecution,
                t.details,
                p.prenomPersonne,
                p.nomPersonne
            FROM
                TODOLIST_PERSONNES tp
                LEFT OUTER JOIN TODOLIST t ON tp.idTache=t.idTache
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idCreateur = p.idPersonne
            WHERE
                idExecutant = :idExecutant
                AND
                dateExecution IS NOT NULL
                AND
                dateCloture IS NULL
        ;`,{
            idExecutant: req.verifyJWTandProfile.idPersonne,
        });
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.toDoListAll = async (req, res) => {
    try {
        let result = await db.query(`
            SELECT
                t.idTache,
                t.titre,
                t.dateExecution,
                t.details,
                p.prenomPersonne,
                p.nomPersonne
            FROM
                TODOLIST t
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idCreateur = p.idPersonne
            WHERE
                dateExecution IS NOT NULL
                AND
                dateCloture IS NULL
        `);
        
        res.send(result);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}