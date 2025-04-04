const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const excelJS = require("exceljs");

/*--- Catégories de matériels ---*/
exports.getCategoriesMateriels = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM MATERIEL_CATEGORIES ORDER BY libelleCategorie;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addCategoriesMateriels = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                MATERIEL_CATEGORIES
            SET
                libelleCategorie = :libelleCategorie
            ;`,
        {
            libelleCategorie : req.body.libelleCategorie || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateCategoriesMateriels = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                MATERIEL_CATEGORIES
            SET
                libelleCategorie = :libelleCategorie
            WHERE
                idCategorie = :idCategorie
            ;`,
        {
            libelleCategorie : req.body.libelleCategorie || null,
            idCategorie      : req.body.idCategorie || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteCategoriesMateriels = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.categoriesDelete(req.verifyJWTandProfile.idPersonne , req.body.idCategorie);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Lieux de stockage --- */
exports.getLieux = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM LIEUX ORDER BY libelleLieu;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addLieux = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                LIEUX
            SET
                libelleLieu   = :libelleLieu,
                adresseLieu   = :adresseLieu,
                detailsLieu   = :detailsLieu,
                accesReserve = :accesReserve
            ;`,
        {
            libelleLieu   : req.body.libelleLieu || null,
            adresseLieu   : req.body.adresseLieu || null,
            detailsLieu   : req.body.detailsLieu || null,
            accesReserve : req.body.accesReserve || false,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateLieux = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                LIEUX
            SET
                libelleLieu   = :libelleLieu,
                adresseLieu   = :adresseLieu,
                detailsLieu   = :detailsLieu,
                accesReserve  = :accesReserve
            WHERE
                idLieu = :idLieu
            ;`,
        {
            libelleLieu   : req.body.libelleLieu || null,
            adresseLieu   : req.body.adresseLieu || null,
            detailsLieu   : req.body.detailsLieu || null,
            accesReserve  : req.body.accesReserve || false,
            idLieu        : req.body.idLieu || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteLieux = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.lieuxDelete(req.verifyJWTandProfile.idPersonne , req.body.idLieu);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Types de vehicules --- */
exports.getTypesVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VEHICULES_TYPES ORDER BY libelleType;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addTypesVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VEHICULES_TYPES
            SET
                libelleType = :libelleType
            ;`,
        {
            libelleType : req.body.libelleType || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateTypesVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VEHICULES_TYPES
            SET
                libelleType = :libelleType
            WHERE
                idVehiculesType = :idVehiculesType
            ;`,
        {
            libelleType     : req.body.libelleType || null,
            idVehiculesType : req.body.idVehiculesType || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteTypesVehicules = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesTypesDelete(req.verifyJWTandProfile.idPersonne , req.body.idVehiculesType);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Désinfections véhicules --- */
exports.getTypesDesinfections = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VEHICULES_DESINFECTIONS_TYPES ORDER BY libelleVehiculesDesinfectionsType;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addTypesDesinfections = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VEHICULES_DESINFECTIONS_TYPES
            SET
                libelleVehiculesDesinfectionsType = :libelleVehiculesDesinfectionsType,
                affichageSynthese                 = :affichageSynthese
            ;`,
        {
            libelleVehiculesDesinfectionsType : req.body.libelleVehiculesDesinfectionsType || null,
            affichageSynthese                 : req.body.affichageSynthese || false,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateTypesDesinfections = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VEHICULES_DESINFECTIONS_TYPES
            SET
                libelleVehiculesDesinfectionsType = :libelleVehiculesDesinfectionsType,
                affichageSynthese                 = :affichageSynthese
            WHERE
                idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
            ;`,
        {
            libelleVehiculesDesinfectionsType : req.body.libelleVehiculesDesinfectionsType || null,
            affichageSynthese                 : req.body.affichageSynthese || false,
            idVehiculesDesinfectionsType      : req.body.idVehiculesDesinfectionsType || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteTypesDesinfections = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesTypesDesinfectionsDelete(req.verifyJWTandProfile.idPersonne , req.body.idVehiculesDesinfectionsType);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Maintenances régulières --- */
exports.getTypesMaintenancesRegulieresVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VEHICULES_HEALTH_TYPES ORDER BY libelleHealthType;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addTypesMaintenancesRegulieresVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VEHICULES_HEALTH_TYPES
            SET
                libelleHealthType = :libelleHealthType,
                affichageSynthese = :affichageSynthese
            ;`,
        {
            libelleHealthType : req.body.libelleHealthType || null,
            affichageSynthese : req.body.affichageSynthese || false,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateTypesMaintenancesRegulieresVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VEHICULES_HEALTH_TYPES
            SET
                libelleHealthType = :libelleHealthType,
                affichageSynthese = :affichageSynthese
            WHERE
                idHealthType = :idHealthType
            ;`,
        {
            libelleHealthType : req.body.libelleHealthType || null,
            affichageSynthese : req.body.affichageSynthese || false,
            idHealthType      : req.body.idHealthType || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteTypesMaintenancesRegulieresVehicules = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesTypesMaintenanceReguliereDelete(req.verifyJWTandProfile.idPersonne , req.body.idHealthType);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Maintenances ponctuelles --- */
exports.getTypesMaintenancesPonctuellesVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VEHICULES_MAINTENANCE_TYPES ORDER BY libelleTypeMaintenance;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addTypesMaintenancesPonctuellesVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VEHICULES_MAINTENANCE_TYPES
            SET
                libelleTypeMaintenance = :libelleTypeMaintenance
            ;`,
        {
            libelleTypeMaintenance : req.body.libelleTypeMaintenance || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateTypesMaintenancesPonctuellesVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VEHICULES_MAINTENANCE_TYPES
            SET
                libelleTypeMaintenance = :libelleTypeMaintenance
            WHERE
                idTypeMaintenance = :idTypeMaintenance
            ;`,
        {
            libelleTypeMaintenance : req.body.libelleTypeMaintenance || null,
            idTypeMaintenance      : req.body.idTypeMaintenance || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteTypesMaintenancesPonctuellesVehicules = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesTypesMaintenancesPonctuellesDelete(req.verifyJWTandProfile.idPersonne , req.body.idTypeMaintenance);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Carburants --- */
exports.getCarburants = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM CARBURANTS ORDER BY libelleCarburant;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addCarburants = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                CARBURANTS
            SET
                libelleCarburant = :libelleCarburant
            ;`,
        {
            libelleCarburant : req.body.libelleCarburant || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateCarburants = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                CARBURANTS
            SET
                libelleCarburant = :libelleCarburant
            WHERE
                idCarburant = :idCarburant
            ;`,
        {
            libelleCarburant : req.body.libelleCarburant || null,
            idCarburant      : req.body.idCarburant || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteCarburants = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesCarburantsDelete(req.verifyJWTandProfile.idPersonne , req.body.idCarburant);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Pneumatiques --- */
exports.getPneumatiques = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VEHICULES_PNEUMATIQUES ORDER BY libellePneumatique;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addPneumatiques = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VEHICULES_PNEUMATIQUES
            SET
                libellePneumatique = :libellePneumatique
            ;`,
        {
            libellePneumatique : req.body.libellePneumatique || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updatePneumatiques = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VEHICULES_PNEUMATIQUES
            SET
                libellePneumatique = :libellePneumatique
            WHERE
                idPneumatique = :idPneumatique
            ;`,
        {
            libellePneumatique : req.body.libellePneumatique || null,
            idPneumatique      : req.body.idPneumatique || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deletePneumatiques = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vehiculesPneumatiquesDelete(req.verifyJWTandProfile.idPersonne , req.body.idPneumatique);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Etats lots --- */
exports.getEtatsLots = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM LOTS_ETATS ORDER BY libelleLotsEtat;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addEtatsLots = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                LOTS_ETATS
            SET
                libelleLotsEtat = :libelleLotsEtat
            ;`,
        {
            libelleLotsEtat : req.body.libelleLotsEtat || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateEtatsLots = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                LOTS_ETATS
            SET
                libelleLotsEtat = :libelleLotsEtat
            WHERE
                idLotsEtat = :idLotsEtat
            ;`,
        {
            libelleLotsEtat : req.body.libelleLotsEtat || null,
            idLotsEtat      : req.body.idLotsEtat || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteEtatsLots = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.etatsLotsDelete(req.verifyJWTandProfile.idPersonne , req.body.idLotsEtat);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Etats matériels --- */
exports.getEtatsMateriels = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM MATERIEL_ETATS ORDER BY libelleMaterielsEtat;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addEtatsMateriels = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                MATERIEL_ETATS
            SET
                libelleMaterielsEtat = :libelleMaterielsEtat
            ;`,
        {
            libelleMaterielsEtat : req.body.libelleMaterielsEtat || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateEtatsMateriels = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                MATERIEL_ETATS
            SET
                libelleMaterielsEtat = :libelleMaterielsEtat
            WHERE
                idMaterielsEtat = :idMaterielsEtat
            ;`,
        {
            libelleMaterielsEtat : req.body.libelleMaterielsEtat || null,
            idMaterielsEtat      : req.body.idMaterielsEtat || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteEtatsMateriels = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.etatsMaterielsDelete(req.verifyJWTandProfile.idPersonne , req.body.idMaterielsEtat);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Etats des véhicules --- */
exports.getEtatsVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VEHICULES_ETATS ORDER BY libelleVehiculesEtat;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addEtatsVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VEHICULES_ETATS
            SET
                libelleVehiculesEtat = :libelleVehiculesEtat
            ;`,
        {
            libelleVehiculesEtat : req.body.libelleVehiculesEtat || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateEtatsVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VEHICULES_ETATS
            SET
                libelleVehiculesEtat = :libelleVehiculesEtat
            WHERE
                idVehiculesEtat = :idVehiculesEtat
            ;`,
        {
            libelleVehiculesEtat : req.body.libelleVehiculesEtat || null,
            idVehiculesEtat      : req.body.idVehiculesEtat || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteEtatsVehicules = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.etatsVehiculesDelete(req.verifyJWTandProfile.idPersonne , req.body.idVehiculesEtat);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Types de documents --- */
exports.getTypesDocuments = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM DOCUMENTS_TYPES ORDER BY libelleTypeDocument;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addTypesDocuments = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                DOCUMENTS_TYPES
            SET
                libelleTypeDocument = :libelleTypeDocument
            ;`,
        {
            libelleTypeDocument : req.body.libelleTypeDocument || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateTypesDocuments = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                DOCUMENTS_TYPES
            SET
                libelleTypeDocument = :libelleTypeDocument
            WHERE
                idTypeDocument = :idTypeDocument
            ;`,
        {
            libelleTypeDocument : req.body.libelleTypeDocument || null,
            idTypeDocument      : req.body.idTypeDocument || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteTypesDocuments = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.typeDocumentsDelete(req.verifyJWTandProfile.idPersonne , req.body.idTypeDocument);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Catalogue de matériel --- */
exports.getCatalogueMateriel = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT
                cat.*,
                mc.libelleCategorie,
                f.nomFournisseur,
                COUNT(cb.idCode) as nbCodesBarre
            FROM
                MATERIEL_CATALOGUE cat
                LEFT OUTER JOIN MATERIEL_CATEGORIES mc ON cat.idCategorie = mc.idCategorie
                LEFT OUTER JOIN FOURNISSEURS f ON cat.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN CODES_BARRE cb ON cat.idMaterielCatalogue = cb.idMaterielCatalogue
            GROUP BY
                cat.idMaterielCatalogue
            ORDER BY
                libelleMateriel
            ;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.getCatalogueEnrichi = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT
                cat.*,
                cat.idMaterielCatalogue as value,
                CONCAT_WS(' > ',cat.idMaterielCatalogue,cat.libelleMateriel,cat.taille) as label,
                mc.libelleCategorie,
                f.nomFournisseur
            FROM
                MATERIEL_CATALOGUE cat
                LEFT OUTER JOIN MATERIEL_CATEGORIES mc ON cat.idCategorie = mc.idCategorie
                LEFT OUTER JOIN FOURNISSEURS f ON cat.idFournisseur = f.idFournisseur
            GROUP BY
                cat.idMaterielCatalogue
            ORDER BY
                libelleMateriel
            ;`
        );
        for(const matos of results)
        {
            let comptage = await db.query(`
                SELECT
                    COUNT(idCode) as nb
                FROM
                    CODES_BARRE
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbCodesBarres = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idCommandeMateriel) as nb
                FROM
                    COMMANDES_MATERIEL
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbCommandes = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idInventaire) as nb
                FROM
                    INVENTAIRES_CONTENUS
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbInventairesLots = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idConsommationMateriel) as nb
                FROM
                    LOTS_CONSOMMATION_MATERIEL
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbConsommationLots = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idElement) as nb
                FROM
                    MATERIEL_ELEMENT
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbLotsOpe = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idTypeLot) as nb
                FROM
                    REFERENTIELS
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbReferentiels = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idReserveInventaire) as nb
                FROM
                    RESERVES_INVENTAIRES_CONTENUS
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbInventairesReserves = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idReserveElement) as nb
                FROM
                    RESERVES_MATERIEL
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbReserves = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idTenue) as nb
                FROM
                    TENUES_AFFECTATION
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbTenuesAffectees = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idCatalogueTenue) as nb
                FROM
                    TENUES_CATALOGUE
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbTenuesStock = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idVehiculesStock) as nb
                FROM
                    VEHICULES_STOCK
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbVehiculesStock = comptage[0].nb;

            comptage = await db.query(`
                SELECT
                    COUNT(idVhfStock) as nb
                FROM
                    VHF_STOCK
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
            ;`,
            {
                idMaterielCatalogue: matos.idMaterielCatalogue,
            });
            matos.nbVHFStock = comptage[0].nb;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.getCatalogueMaterielOpe = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT
                cat.*,
                mc.libelleCategorie,
                f.nomFournisseur,
                COUNT(cb.idCode) as nbCodesBarre
            FROM
                VIEW_MATERIEL_CATALOGUE_OPE cat
                LEFT OUTER JOIN MATERIEL_CATEGORIES mc ON cat.idCategorie = mc.idCategorie
                LEFT OUTER JOIN FOURNISSEURS f ON cat.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN CODES_BARRE cb ON cat.idMaterielCatalogue = cb.idMaterielCatalogue
            GROUP BY
                cat.idMaterielCatalogue
            ORDER BY
                libelleMateriel
            ;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.getCatalogueMaterielVehicules = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT
                cat.*,
                mc.libelleCategorie,
                f.nomFournisseur,
                COUNT(cb.idCode) as nbCodesBarre
            FROM
                VIEW_MATERIEL_CATALOGUE_VEHICULES cat
                LEFT OUTER JOIN MATERIEL_CATEGORIES mc ON cat.idCategorie = mc.idCategorie
                LEFT OUTER JOIN FOURNISSEURS f ON cat.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN CODES_BARRE cb ON cat.idMaterielCatalogue = cb.idMaterielCatalogue
            GROUP BY
                cat.idMaterielCatalogue
            ORDER BY
                libelleMateriel
            ;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.getCatalogueMaterielTenues = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT
                cat.*,
                mc.libelleCategorie,
                f.nomFournisseur,
                COUNT(cb.idCode) as nbCodesBarre
            FROM
                VIEW_MATERIEL_CATALOGUE_TENUES cat
                LEFT OUTER JOIN MATERIEL_CATEGORIES mc ON cat.idCategorie = mc.idCategorie
                LEFT OUTER JOIN FOURNISSEURS f ON cat.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN CODES_BARRE cb ON cat.idMaterielCatalogue = cb.idMaterielCatalogue
            GROUP BY
                cat.idMaterielCatalogue
            ORDER BY
                libelleMateriel
            ;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.getCatalogueMaterielVHF = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT
                cat.*,
                mc.libelleCategorie,
                f.nomFournisseur,
                COUNT(cb.idCode) as nbCodesBarre
            FROM
                VIEW_MATERIEL_CATALOGUE_VHF cat
                LEFT OUTER JOIN MATERIEL_CATEGORIES mc ON cat.idCategorie = mc.idCategorie
                LEFT OUTER JOIN FOURNISSEURS f ON cat.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN CODES_BARRE cb ON cat.idMaterielCatalogue = cb.idMaterielCatalogue
            GROUP BY
                cat.idMaterielCatalogue
            ORDER BY
                libelleMateriel
            ;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addCatalogueMateriel = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                MATERIEL_CATALOGUE
            SET
                libelleMateriel                = :libelleMateriel,
                idCategorie                    = :idCategorie,
                taille                         = :taille,
                sterilite                      = :sterilite,
                conditionnementMultiple        = :conditionnementMultiple,
                commentairesMateriel           = :commentairesMateriel,
                idFournisseur                  = :idFournisseur,
                peremptionAnticipationOpe      = :peremptionAnticipationOpe,
                peremptionAnticipationRes      = :peremptionAnticipationRes,
                peremptionAnticipationVehicule = :peremptionAnticipationVehicule,
                peremptionAnticipationTenues   = :peremptionAnticipationTenues,
                peremptionAnticipationVHF      = :peremptionAnticipationVHF,
                disponibleBenevolesConso       = :disponibleBenevolesConso,
                modules_ope                    = :modules_ope,
                modules_vehicules              = :modules_vehicules,
                modules_tenues                 = :modules_tenues,
                modules_vhf                    = :modules_vhf
            ;`,
        {
            libelleMateriel                : req.body.libelleMateriel || null,
            idCategorie                    : req.body.idCategorie || null,
            taille                         : req.body.taille || null,
            sterilite                      : req.body.sterilite || false,
            conditionnementMultiple        : req.body.conditionnementMultiple || null,
            commentairesMateriel           : req.body.commentairesMateriel || null,
            idFournisseur                  : req.body.idFournisseur || null,
            peremptionAnticipationOpe      : req.body.sterilite ? (req.body.peremptionAnticipationOpe > 0 ? req.body.peremptionAnticipationOpe : null) : null,
            peremptionAnticipationRes      : req.body.sterilite ? (req.body.peremptionAnticipationRes > 0 ? req.body.peremptionAnticipationRes : null) : null,
            peremptionAnticipationVehicule : req.body.sterilite ? (req.body.peremptionAnticipationVehicule > 0 ? req.body.peremptionAnticipationVehicule : null) : null,
            peremptionAnticipationTenues   : req.body.sterilite ? (req.body.peremptionAnticipationTenues > 0 ? req.body.peremptionAnticipationTenues : null) : null,
            peremptionAnticipationVHF      : req.body.sterilite ? (req.body.peremptionAnticipationVHF > 0 ? req.body.peremptionAnticipationVHF : null) : null,
            disponibleBenevolesConso       : req.body.disponibleBenevolesConso || false,
            modules_ope                    : req.body.modules_ope || false,
            modules_vehicules              : req.body.modules_vehicules || false,
            modules_tenues                 : req.body.modules_tenues || false,
            modules_vhf                    : req.body.modules_vhf || false,
        });

        await fonctionsMetiers.updatePeremptionsAnticipations();
        await fonctionsMetiers.checkAllConf();

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateCatalogueMateriel = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                MATERIEL_CATALOGUE
            SET
                libelleMateriel                = :libelleMateriel,
                idCategorie                    = :idCategorie,
                taille                         = :taille,
                sterilite                      = :sterilite,
                conditionnementMultiple        = :conditionnementMultiple,
                commentairesMateriel           = :commentairesMateriel,
                idFournisseur                  = :idFournisseur,
                peremptionAnticipationOpe      = :peremptionAnticipationOpe,
                peremptionAnticipationRes      = :peremptionAnticipationRes,
                peremptionAnticipationVehicule = :peremptionAnticipationVehicule,
                peremptionAnticipationTenues   = :peremptionAnticipationTenues,
                peremptionAnticipationVHF      = :peremptionAnticipationVHF,
                disponibleBenevolesConso       = :disponibleBenevolesConso
            WHERE
                idMaterielCatalogue = :idMaterielCatalogue
            ;`,
        {
            libelleMateriel                : req.body.libelleMateriel || null,
            idCategorie                    : req.body.idCategorie || null,
            taille                         : req.body.taille || null,
            sterilite                      : req.body.sterilite || false,
            conditionnementMultiple        : req.body.conditionnementMultiple || null,
            commentairesMateriel           : req.body.commentairesMateriel || null,
            idFournisseur                  : req.body.idFournisseur || null,
            peremptionAnticipationOpe      : req.body.sterilite ? (req.body.peremptionAnticipationOpe > 0 ? req.body.peremptionAnticipationOpe : null) : null,
            peremptionAnticipationRes      : req.body.sterilite ? (req.body.peremptionAnticipationRes > 0 ? req.body.peremptionAnticipationRes : null) : null,
            peremptionAnticipationVehicule : req.body.sterilite ? (req.body.peremptionAnticipationVehicule > 0 ? req.body.peremptionAnticipationVehicule : null) : null,
            peremptionAnticipationTenues   : req.body.sterilite ? (req.body.peremptionAnticipationTenues > 0 ? req.body.peremptionAnticipationTenues : null) : null,
            peremptionAnticipationVHF      : req.body.sterilite ? (req.body.peremptionAnticipationVHF > 0 ? req.body.peremptionAnticipationVHF : null) : null,
            idMaterielCatalogue            : req.body.idMaterielCatalogue || null,
            disponibleBenevolesConso       : req.body.disponibleBenevolesConso || false,
        });

        await fonctionsMetiers.updatePeremptionsAnticipations();
        await fonctionsMetiers.checkAllConf();
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.mergeCatalogueItems = async (req, res)=>{
    try {
        let update = await db.query(`
            UPDATE
                CODES_BARRE
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                COMMANDES_MATERIEL
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                INVENTAIRES_CONTENUS
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });
        await fonctionsMetiers.lissageTousInventairesLots();

        update = await db.query(`
            UPDATE
                LOTS_CONSOMMATION_MATERIEL
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                MATERIEL_ELEMENT
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                REFERENTIELS
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                RESERVES_INVENTAIRES_CONTENUS
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });
        await fonctionsMetiers.lissageTousInventairesReserves();

        update = await db.query(`
            UPDATE
                RESERVES_MATERIEL
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                TENUES_AFFECTATION
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                TENUES_CATALOGUE
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                VEHICULES_STOCK
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        update = await db.query(`
            UPDATE
                VHF_STOCK
            SET
                idMaterielCatalogue = :idMaterielCatalogueKEEP
            WHERE
                idMaterielCatalogue = :idMaterielCatalogueDELETE
        ;`,
        {
            idMaterielCatalogueKEEP: req.body.idMaterielCatalogueKEEP,
            idMaterielCatalogueDELETE: req.body.idMaterielCatalogueDELETE,
        });

        const deleteResult = await fonctionsDelete.catalogueDelete(req.verifyJWTandProfile.idPersonne , req.body.idMaterielCatalogueDELETE);

        await fonctionsMetiers.updatePeremptionsAnticipations();
        await fonctionsMetiers.checkAllConf();

        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
        
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteCatalogueMateriel = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.catalogueDelete(req.verifyJWTandProfile.idPersonne , req.body.idMaterielCatalogue);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.genererExportCatalogueMateriel = async (req, res)=>{
    try {
        const workbook = new excelJS.Workbook();

        //Catalogue
        const worksheetCatalogue = workbook.addWorksheet('Catalogue');
        const catalogue = await db.query(`
            SELECT
                catalogue.*,
                cat.libelleCategorie,
                four.nomFournisseur
            FROM
                MATERIEL_CATALOGUE catalogue
                LEFT OUTER JOIN MATERIEL_CATEGORIES cat ON catalogue.idCategorie = cat.idCategorie
                LEFT OUTER JOIN FOURNISSEURS four ON catalogue.idFournisseur = four.idFournisseur
            ORDER BY
                libelleMateriel ASC
        ;`);

        worksheetCatalogue.columns = [
            { header: "Référence interne",              key: "idMaterielCatalogue",            width: 10 },
            { header: "Libellé",                        key: "libelleMateriel",                width: 60 },
            { header: "Categorie",                      key: "idCategorie",                    width: 10 },
            { header: "Categorie Nom",                  key: "libelleCategorie",               width: 60 },
            { header: "Taille",                         key: "taille",                         width: 30 },
            { header: "Stérilité",                      key: "sterilite",                      width: 30 },
            { header: "Conditionnement multiple",       key: "conditionnementMultiple",        width: 30 },
            { header: "Commentaires",                   key: "commentairesMateriel",           width: 30 },
            { header: "Péremption Lots opérationnels",  key: "peremptionAnticipationOpe",      width: 30 },
            { header: "Péremption Réserves",            key: "peremptionAnticipationRes",      width: 30 },
            { header: "Péremption stock Véhicules",     key: "peremptionAnticipationVehicule", width: 30 },
            { header: "Péremption stock Tenues",        key: "peremptionAnticipationTenues",   width: 30 },
            { header: "Péremption stock Transmissions", key: "peremptionAnticipationVHF",      width: 30 },
            { header: "Disponibilité bénévoles",        key: "disponibleBenevolesConso",       width: 30 },
            { header: "Modules opérationnels",          key: "modules_ope",                    width: 30 },
            { header: "Module Véhicules",               key: "modules_vehicules",              width: 30 },
            { header: "Module TEnues",                  key: "modules_tenues",                 width: 30 },
            { header: "Module transmissions",           key: "modules_vhf",                    width: 30 },
            { header: "Fournisseur",                    key: "nomFournisseur",                 width: 30 },
        ];

        for(const cat of catalogue)
        {
            worksheetCatalogue.addRow(cat);
        }

        worksheetCatalogue.getRow(1).eachCell((cell) => {
            cell.font = { bold: true };
        });


        //Categories
        const worksheetCategories = workbook.addWorksheet('Catégories');
        const categories = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_CATEGORIES
            ORDER BY
                libelleCategorie ASC
        ;`);

        worksheetCategories.columns = [
            { header: "Référence interne",   key: "idCategorie",            width: 10 },
            { header: "Libellé",             key: "libelleCategorie",        width: 60 },
        ];

        for(const cat of categories)
        {
            worksheetCategories.addRow(cat);
        }

        worksheetCategories.getRow(1).eachCell((cell) => {
            cell.font = { bold: true };
        });

        



        let fileName = Date.now() + '-ExportCatalogue.xlsx';

        const saveFile = await workbook.xlsx.writeFile('temp/'+fileName);

        res.send({fileName: fileName});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Codes Barre --- */
exports.getCodesBarres = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT
                cb.*,
                mc.libelleMateriel
            FROM
                CODES_BARRE cb
                LEFT OUTER JOIN MATERIEL_CATALOGUE mc ON cb.idMaterielCatalogue = mc.idMaterielCatalogue
            ORDER BY
                mc.libelleMateriel
            ;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addCodeBarres = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                CODES_BARRE
            SET
                codeBarre = :codeBarre,
                internalReference = :internalReference,
                peremptionConsommable = :peremptionConsommable,
                commentairesCode = :commentairesCode,
                idMaterielCatalogue = :idMaterielCatalogue
            ;`,
        {
            codeBarre: req.body.codeBarre || null,
            internalReference: req.body.internalReference || 0,
            peremptionConsommable: req.body.peremptionConsommable || null,
            commentairesCode: req.body.commentairesCode || null,
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
        });

        if(req.body.internalReference == true)
        {
            let selectLast = await db.query(
                'SELECT MAX(idCode) as idCode FROM CODES_BARRE;'
            );

            let results = await db.query(
                `UPDATE
                    CODES_BARRE
                SET
                    codeBarre = CONCAT('GPM',idCode)
                WHERE
                    idCode = :idCode
                ;`,
            {
                idCode: selectLast[0].idCode,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateCodeBarres = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                CODES_BARRE
            SET
                codeBarre = :codeBarre,
                internalReference = :internalReference,
                peremptionConsommable = :peremptionConsommable,
                commentairesCode = :commentairesCode,
                idMaterielCatalogue = :idMaterielCatalogue
            WHERE
                idCode = :idCode
            ;`,
        {
            codeBarre: req.body.codeBarre || null,
            internalReference: req.body.internalReference || 0,
            peremptionConsommable: req.body.peremptionConsommable || null,
            commentairesCode: req.body.commentairesCode || null,
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idCode: req.body.idCode,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.codesBarreDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.codesBarreDelete(req.verifyJWTandProfile.idPersonne , req.body.idCode);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Types d`accessoires VHF --- */
exports.getVHFTypesAccessoires = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VHF_ACCESSOIRES_TYPES ORDER BY libelleVhfAccessoireType;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addVHFTypesAccessoires = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VHF_ACCESSOIRES_TYPES
            SET
                libelleVhfAccessoireType = :libelleVhfAccessoireType
            ;`,
        {
            libelleVhfAccessoireType : req.body.libelleVhfAccessoireType || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateVHFTypesAccessoires = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VHF_ACCESSOIRES_TYPES
            SET
                libelleVhfAccessoireType = :libelleVhfAccessoireType
            WHERE
                idVhfAccessoireType = :idVhfAccessoireType
            ;`,
        {
            libelleVhfAccessoireType : req.body.libelleVhfAccessoireType || null,
            idVhfAccessoireType      : req.body.idVhfAccessoireType || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteVHFTypesAccessoires = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfTypesAccessoiresDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfAccessoireType);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Etats VHF --- */
exports.getEtatsVHF = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VHF_ETATS ORDER BY libelleVhfEtat;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addEtatsVHF = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VHF_ETATS
            SET
                libelleVhfEtat = :libelleVhfEtat
            ;`,
        {
            libelleVhfEtat : req.body.libelleVhfEtat || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateEtatsVHF = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VHF_ETATS
            SET
                libelleVhfEtat = :libelleVhfEtat
            WHERE
                idVhfEtat = :idVhfEtat
            ;`,
        {
            libelleVhfEtat : req.body.libelleVhfEtat || null,
            idVhfEtat      : req.body.idVhfEtat || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteEtatsVHF = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfEtatsDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfEtat);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Technologies VHF --- */
exports.getTechnologiesVHF = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VHF_TECHNOLOGIES ORDER BY libelleTechno;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addTechnologiesVHF = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VHF_TECHNOLOGIES
            SET
                libelleTechno = :libelleTechno
            ;`,
        {
            libelleTechno : req.body.libelleTechno || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateTechnologiesVHF = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VHF_TECHNOLOGIES
            SET
                libelleTechno = :libelleTechno
            WHERE
                idVhfTechno = :idVhfTechno
            ;`,
        {
            libelleTechno    : req.body.libelleTechno || null,
            idVhfTechno      : req.body.idVhfTechno || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteTechnologiesVHF = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfTechnologiesDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfTechno);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

/*--- Types équipements VHF --- */
exports.getVHFTypesEquipements = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM VHF_TYPES_EQUIPEMENTS ORDER BY libelleType;`
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.addVHFTypesEquipements = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                VHF_TYPES_EQUIPEMENTS
            SET
                libelleType = :libelleType
            ;`,
        {
            libelleType : req.body.libelleType || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.updateVHFTypesEquipements = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                VHF_TYPES_EQUIPEMENTS
            SET
                libelleType = :libelleType
            WHERE
                idVhfType = :idVhfType
            ;`,
        {
            libelleType    : req.body.libelleType || null,
            idVhfType      : req.body.idVhfType || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
exports.deleteVHFTypesEquipements = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfTypesEquipementsDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfType);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}