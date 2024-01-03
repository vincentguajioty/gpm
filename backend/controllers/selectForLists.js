const db = require('../db');
const logger = require('../winstonLogger');

exports.getNotificationsConditions = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idCondition as value,
                libelleCondition as label
            FROM
                NOTIFICATIONS_CONDITIONS
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getPersonnes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idPersonne as value,
                identifiant as label
            FROM
                PERSONNE_REFERENTE
            ORDER BY
                identifiant
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getActivePersonnes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idPersonne as value,
                identifiant as label
            FROM
                VIEW_HABILITATIONS
            WHERE
                connexion_connexion = true
            ORDER BY
                identifiant
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getActivePersonnesForCmdAffectation = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idPersonne as value,
                identifiant as label
            FROM
                VIEW_HABILITATIONS
            WHERE
                connexion_connexion = true
                AND
                commande_etreEnCharge = true
            ORDER BY
                identifiant
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getNonAnonymesPersonnes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idPersonne as value,
                identifiant as label
            FROM
                PERSONNE_REFERENTE
            WHERE
                cnil_anonyme = false
            ORDER BY
                identifiant
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getPersonnesWithMail = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idPersonne as value,
                identifiant as label
            FROM
                PERSONNE_REFERENTE
            WHERE
                mailPersonne IS NOT NULL
                AND
                mailPersonne <> ""
            ORDER BY
                identifiant
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getProfils = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idProfil as value,
                libelleProfil as label
            FROM
                PROFILS
            ORDER BY
                libelleProfil
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getNotificationsEnabled = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idNotificationEnabled as value,
                libelleNotificationEnabled as label
            FROM
                NOTIFICATIONS_ENABLED
            ORDER BY
                libelleNotificationEnabled
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCategoriesMateriels = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idCategorie as value,
                libelleCategorie as label
            FROM
                MATERIEL_CATEGORIES
            ORDER BY
                libelleCategorie
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getLieux = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idLieu as value,
                libelleLieu as label
            FROM
                LIEUX
            ORDER BY
                libelleLieu
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTypesVehicules = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVehiculesType as value,
                libelleType as label
            FROM
                VEHICULES_TYPES
            ORDER BY
                libelleType
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTypesDesinfections = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVehiculesDesinfectionsType as value,
                libelleVehiculesDesinfectionsType as label
            FROM
                VEHICULES_DESINFECTIONS_TYPES
            ORDER BY
                libelleVehiculesDesinfectionsType
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTypesMaintenancesRegulieresVehicules = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idHealthType as value,
                libelleHealthType as label
            FROM
                VEHICULES_HEALTH_TYPES
            ORDER BY
                libelleHealthType
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTypesMaintenancesPonctuellesVehicules = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idTypeMaintenance as value,
                libelleTypeMaintenance as label
            FROM
                VEHICULES_MAINTENANCE_TYPES
            ORDER BY
                libelleTypeMaintenance
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCarburants = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idCarburant as value,
                libelleCarburant as label
            FROM
                CARBURANTS
            ORDER BY
                libelleCarburant
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getEtatsLots = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idLotsEtat as value,
                libelleLotsEtat as label
            FROM
                LOTS_ETATS
            ORDER BY
                libelleLotsEtat
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getEtatsMateriels = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idMaterielsEtat as value,
                libelleMaterielsEtat as label
            FROM
                MATERIEL_ETATS
            ORDER BY
                libelleMaterielsEtat
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getEtatsVehicules = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVehiculesEtat as value,
                libelleVehiculesEtat as label
            FROM
                VEHICULES_ETATS
            ORDER BY
                libelleVehiculesEtat
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTypesDocuments = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idTypeDocument as value,
                libelleTypeDocument as label
            FROM
                DOCUMENTS_TYPES
            ORDER BY
                libelleTypeDocument
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCatalogueMateriel = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idMaterielCatalogue as value,
                libelleMateriel as label
            FROM
                MATERIEL_CATALOGUE
            ORDER BY
                libelleMateriel
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCatalogueMaterielFull = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idMaterielCatalogue as value,
                libelleMateriel as label,
                sterilite,
                peremptionAnticipationOpe,
                peremptionAnticipationRes,
                idFournisseur
            FROM
                MATERIEL_CATALOGUE
            ORDER BY
                libelleMateriel
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getVHFTypesAccessoires = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVhfAccessoireType as value,
                libelleVhfAccessoireType as label
            FROM
                VHF_ACCESSOIRES_TYPES
            ORDER BY
                libelleVhfAccessoireType
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getEtatsVHF = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVhfEtat as value,
                libelleVhfEtat as label
            FROM
                VHF_ETATS
            ORDER BY
                libelleVhfEtat
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTechnologiesVHF = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVhfTechno as value,
                libelleTechno as label
            FROM
                VHF_TECHNOLOGIES
            ORDER BY
                libelleTechno
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getVHFTypesEquipements = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVhfType as value,
                libelleType as label
            FROM
                VHF_TYPES_EQUIPEMENTS
            ORDER BY
                libelleType
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getFournisseurs = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idFournisseur as value,
                nomFournisseur as label
            FROM
                FOURNISSEURS
            ORDER BY
                nomFournisseur
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getMessagesTypes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idMessageType as value,
                libelleMessageType as label
            FROM
                MESSAGES_TYPES
            ORDER BY
                libelleMessageType
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getPioritesForTDL = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idTDLpriorite as value,
                libellePriorite as label
            FROM
                TODOLIST_PRIORITES
            ORDER BY
                libellePriorite
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTenuesCatalogue = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idCatalogueTenue as value,
                CONCAT(COALESCE(libelleCatalogueTenue,'')," (Taille:",COALESCE(tailleCatalogueTenue,''),")") as label
            FROM
                TENUES_CATALOGUE
            ORDER BY
                libelleCatalogueTenue
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getVhfPlans = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVhfPlan as value,
                libellePlan as label
            FROM
                VHF_PLAN
            ORDER BY
                libellePlan
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getVhfFrequences = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVhfCanal as value,
                chName as label
            FROM
                VHF_CANAL
            ORDER BY
                chName
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getEmplacements = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idEmplacement as value,
                libelleEmplacement as label
            FROM
                MATERIEL_EMPLACEMENT
            ORDER BY
                libelleEmplacement
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getEmplacementsFull = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idEmplacement as value,
                CONCAT_WS(' > ',l.libelleLot,s.libelleSac,e.libelleEmplacement) as label,
                l.inventaireEnCours,
                l.idLot
            FROM
                MATERIEL_EMPLACEMENT e
                LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
            ORDER BY
                l.libelleLot,
                s.libelleSac,
                e.libelleEmplacement
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getConteneurs = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idConteneur as value,
                libelleConteneur as label
            FROM
                RESERVES_CONTENEUR
            ORDER BY
                libelleConteneur
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getLots = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idLot as value,
                libelleLot as label
            FROM
                LOTS_LOTS
            ORDER BY
                libelleLot
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getLotsFull = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idLot as value,
                libelleLot as label,
                inventaireEnCours
            FROM
                LOTS_LOTS
            ORDER BY
                libelleLot
            
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getSacs = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idSac as value,
                libelleSac as label
            FROM
                MATERIEL_SAC
            ORDER BY
                libelleSac
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getSacsFull = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idSac as value,
                CONCAT_WS(' > ',l.libelleLot,s.libelleSac) as label,
                l.inventaireEnCours
            FROM
                MATERIEL_SAC s
                LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
            ORDER BY
                l.libelleLot,
                s.libelleSac
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTypesLots = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idTypeLot as value,
                libelleTypeLot as label
            FROM
                LOTS_TYPES 
            ORDER BY
                libelleTypeLot
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getVehicules = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVehicule as value,
                libelleVehicule as label
            FROM
                VEHICULES 
            ORDER BY
                libelleVehicule
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCodesBarreCatalogue = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                cb.*,
                mc.libelleMateriel,
                idCode as value,
                codeBarre as label
            FROM
                CODES_BARRE cb
                LEFT OUTER JOIN MATERIEL_CATALOGUE mc ON cb.idMaterielCatalogue = mc.idMaterielCatalogue
            WHERE
                cb.idMaterielCatalogue IS NOT NULL
            ORDER BY
                codeBarre
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getEtatsCommandes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idEtat as value,
                CONCAT(idEtat, ' - ', libelleEtat) as label
            FROM
                COMMANDES_ETATS
            ORDER BY
                idEtat
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCentresCouts = async (req, res)=>{
    try {
        let expires = await db.query(`
            SELECT
                idCentreDeCout as value,
                libelleCentreDecout as label
            FROM
                CENTRE_COUTS
            WHERE
                dateFermeture IS NOT NULL
                AND
                dateFermeture < CURRENT_DATE
            ORDER BY
                libelleCentreDecout
        ;`);

        let futurs = await db.query(`
            SELECT
                idCentreDeCout as value,
                libelleCentreDecout as label
            FROM
                CENTRE_COUTS
            WHERE
                dateOuverture IS NOT NULL
                AND
                dateOuverture > CURRENT_DATE
            ORDER BY
                libelleCentreDecout
        ;`);

        let enCours = await db.query(`
            SELECT
                idCentreDeCout as value,
                libelleCentreDecout as label
            FROM
                CENTRE_COUTS
            WHERE
                (dateOuverture <= CURRENT_DATE
                AND
                dateFermeture >= CURRENT_DATE)
                OR
                (dateOuverture IS NULL
                AND
                dateFermeture >= CURRENT_DATE)
                OR
                (dateOuverture <= CURRENT_DATE
                AND
                dateFermeture IS NULL)
                OR
                (dateOuverture IS NULL
                AND
                dateFermeture IS NULL)
            ORDER BY
                libelleCentreDecout
        ;`);

        res.send([
            {
                label: "En cours",
                options: enCours,
            },
            {
                label: "Futurs",
                options: futurs,
            },
            {
                label: "Expirés",
                options: expires,
            },
        ]);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/* ------------- PUBLIC ------------- */

exports.getConsommationsEnCours = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION
            WHERE
                declarationEnCours = true
                OR
                reapproEnCours = true
            ORDER BY
                dateConsommation
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getPublicCatalogueMateriel = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT DISTINCT
                cat.idMaterielCatalogue as value,
                cat.libelleMateriel as label,
                COALESCE(cat.frequenceRapportConso, 0) AS frequenceRapportConso
            FROM
                (SELECT
                    c.*,
                    SUM(conso.quantiteConsommation) as frequenceRapportConso
                FROM
                    MATERIEL_CATALOGUE c
                    LEFT OUTER JOIN MATERIEL_ELEMENT elem ON c.idMaterielCatalogue = elem.idMaterielCatalogue
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT emp ON elem.idEmplacement = emp.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON emp.idSac = s.idSac
                    LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                    LEFT OUTER JOIN LOTS_CONSOMMATION_MATERIEL conso ON c.idMaterielCatalogue = conso.idMaterielCatalogue
                WHERE
                    (conso.idLot IS NULL OR conso.idLot = l.idLot)
                    AND
                    l.dispoBenevoles = true
                GROUP BY
                    c.idMaterielCatalogue
                ) cat
            ORDER BY
                cat.libelleMateriel
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getLotsPublics = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idLot as value,
                libelleLot as label
            FROM
                LOTS_LOTS
            WHERE
                dispoBenevoles = true
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getConteneursPublics = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idConteneur as value,
                libelleConteneur as label
            FROM
                RESERVES_CONTENEUR
            WHERE
                dispoBenevoles = true
            ORDER BY
                libelleConteneur
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getVehiculesPublics = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                idVehicule as value,
                libelleVehicule as label
            FROM
                VEHICULES
            WHERE
                dispoBenevoles = true
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
