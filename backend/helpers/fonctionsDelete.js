const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsMetiers = require('./fonctionsMetiers');

const annuaireDelete = async (idLogger, idPersonne) => {
    try {
        logger.info("Suppression de l'utilisateur "+idPersonne, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let detacherAlertes = await db.query(`
            UPDATE VHF_ALERTES SET idTraitant = Null WHERE idTraitant = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });
        detacherAlertes = await db.query(`
            UPDATE VEHICULES_ALERTES SET idTraitant = Null WHERE idTraitant = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });
        detacherAlertes = await db.query(`
            UPDATE LOTS_ALERTES SET idTraitant = Null WHERE idTraitant = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let detacherLots = await db.query(`
            UPDATE LOTS_LOTS SET idPersonne = Null WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let anonymiserMessages = await db.query(`
            UPDATE MESSAGES SET idPersonne = Null WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let anonymiserInventaires = await db.query(`
            UPDATE INVENTAIRES SET idPersonne = Null WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let anonymiserInventairesReserves = await db.query(`
            UPDATE RESERVES_INVENTAIRES SET idPersonne = Null WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delierVHF = await db.query(`
            UPDATE VHF_EQUIPEMENTS SET idResponsable = Null WHERE idResponsable = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delierVehivules = await db.query(`
            UPDATE VEHICULES SET idResponsable = Null WHERE idResponsable = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delierMaintenances = await db.query(`
            UPDATE VEHICULES_MAINTENANCE SET idExecutant = Null WHERE idExecutant = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delierDesinfections = await db.query(`
            UPDATE VEHICULES_DESINFECTIONS SET idExecutant = Null WHERE idExecutant = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delierRelevesKM = await db.query(`
            UPDATE VEHICULES_RELEVES SET idPersonne = Null WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delierHealthCheck = await db.query(`
            UPDATE VEHICULES_HEALTH SET idPersonne = Null WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delierOperationsCentresCouts = await db.query(`
            UPDATE CENTRE_COUTS_OPERATIONS SET idPersonne = Null WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delierTDLcreateur = await db.query(`
            UPDATE CENTRE_COUTS_OPERATIONS SET idPersonne = Null WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let deleteCentreCOutsGerants = await db.query(`
            DELETE FROM CENTRE_COUTS_PERSONNES WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let deleteCommandes = await db.query(`
            DELETE FROM COMMANDES_AFFECTEES WHERE idAffectee = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });
        deleteCommandes = await db.query(`
            DELETE FROM COMMANDES_OBSERVATEURS WHERE idObservateur = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });
        deleteCommandes = await db.query(`
            DELETE FROM COMMANDES_DEMANDEURS WHERE idDemandeur = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let deleteTDLaffectations = await db.query(`
            DELETE FROM TODOLIST_PERSONNES WHERE idExecutant = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let deleteProfils = await db.query(`
            DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let delteAbonnements = await db.query(`
            DELETE FROM NOTIFICATIONS_ABONNEMENTS WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let deleteResetPasswords = await db.query(`
            DELETE FROM RESETPASSWORD WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        await fonctionsMetiers.deconnecterUtilisateur(idPersonne);

        let delteMailQueue = await db.query(`
            DELETE FROM MAIL_QUEUE WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne
        ;`,{
            idPersonne : idPersonne,
        });

        logger.info("Suppression réussie de l'utilisateur "+idPersonne, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'utilisateur "+idPersonne, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const alerteBenevoleLotDelete = async (idLogger, idAlerte) => {
    try {
        logger.info("Suppression de l'alerte bénévoles sur les lots "+idAlerte, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM LOTS_ALERTES WHERE idAlerte = :idAlerte
        ;`,{
            idAlerte : idAlerte,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM LOTS_ALERTES WHERE idAlerte = :idAlerte
        ;`,{
            idAlerte : idAlerte,
        });

        logger.info("Suppression réussie de l'alerte bénévoles sur les lots "+idAlerte, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'alerte bénévoles sur les lots "+idAlerte, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const alerteBenevoleVehiculeDelete = async (idLogger, idAlerte) => {
    try {
        logger.info("Suppression de l'alerte bénévoles sur les véhicules "+idAlerte, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_ALERTES WHERE idAlerte = :idAlerte
        ;`,{
            idAlerte : idAlerte,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_ALERTES WHERE idAlerte = :idAlerte
        ;`,{
            idAlerte : idAlerte,
        });

        logger.info("Suppression réussie de l'alerte bénévoles sur les véhicules "+idAlerte, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'alerte bénévoles sur les véhicules "+idAlerte, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const alerteBenevoleVHFDelete = async (idLogger, idAlerte) => {
    try {
        logger.info("Suppression de l'alerte bénévoles sur les transmissions "+idAlerte, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_ALERTES WHERE idAlerte = :idAlerte
        ;`,{
            idAlerte : idAlerte,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_ALERTES WHERE idAlerte = :idAlerte
        ;`,{
            idAlerte : idAlerte,
        });

        logger.info("Suppression réussie de l'alerte bénévoles sur les transmissions "+idAlerte, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'alerte bénévoles sur les transmissions "+idAlerte, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const catalogueDelete = async (idLogger, idMaterielCatalogue) => {
    try {
        logger.info("Suppression de l'entrée du catalogue matériel "+idMaterielCatalogue, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        await commandeItemDelete('SYSTEM', null, idMaterielCatalogue, null);

        let materielADelete = await db.query(`
            SELECT * FROM MATERIEL_ELEMENT WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });
        for(const matos of materielADelete){await materielsDelete('SYSTEM', matos.idElement);}

        let deleteFromReferentiel = await db.query(`
            DELETE FROM REFERENTIELS WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });

        let deleteFromInventaires = await db.query(`
            DELETE FROM INVENTAIRES_CONTENUS WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });

        let deleteFromReservesInventaires = await db.query(`
            DELETE FROM RESERVES_INVENTAIRES_CONTENUS WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });

        let reserveADelete = await db.query(`
            SELECT * FROM RESERVES_MATERIEL WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });
        for(const matos of reserveADelete){await reserveMaterielDelete('SYSTEM', matos.idReserveElement);}

        let codeBarreADelete = await db.query(`
            SELECT * FROM CODES_BARRE WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });
        for(const matos of codeBarreADelete){await codesBarreDelete('SYSTEM', matos.idCode);}

        let consommationsADelete = await db.query(`
            DELETE FROM LOTS_CONSOMMATION_MATERIEL WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });

        let affectationsTenuesADelete = await db.query(`
            SELECT * FROM TENUES_AFFECTATION WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });
        for(const matos of affectationsTenuesADelete){await tenuesAffectationsDelete('SYSTEM', matos.idTenue, false);}

        let catalogueTenuesADelete = await db.query(`
            SELECT * FROM TENUES_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });
        for(const matos of catalogueTenuesADelete){await tenuesCatalogueDelete('SYSTEM', matos.idCatalogueTenue);}

        let stockVehiculesADelete = await db.query(`
            SELECT * FROM VEHICULES_STOCK WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });
        for(const matos of stockVehiculesADelete){await vehiculesStockDelete('SYSTEM', matos.idVehiculesStock);}

        let stockVhfADelete = await db.query(`
            SELECT * FROM VHF_STOCK WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });
        for(const matos of stockVhfADelete){await vhfStockDelete('SYSTEM', matos.idVhfStock);}

        let finalDeleteQuery = await db.query(`
            DELETE FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue : idMaterielCatalogue,
        });

        logger.info("Suppression réussie de l'entrée du catalogue matériel "+idMaterielCatalogue, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'entrée du catalogue matériel "+idMaterielCatalogue, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const categoriesDelete = async (idLogger, idCategorie) => {
    try {
        logger.info("Suppression de la catégorie "+idCategorie, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM MATERIEL_CATEGORIES WHERE idCategorie = :idCategorie
        ;`,{
            idCategorie : idCategorie,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;

        updateQuery = await db.query(`
            UPDATE MATERIEL_CATALOGUE SET idCategorie = Null WHERE idCategorie = :idCategorie
        ;`,{
            idCategorie : idCategorie,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM MATERIEL_CATEGORIES WHERE idCategorie = :idCategorie
        ;`,{
            idCategorie : idCategorie,
        });

        logger.info("Suppression réussie de la catégorie "+idCategorie, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de la catégorie "+idCategorie, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const cautionsDelete = async (idLogger, idCaution) => {
    try {
        logger.info("Suppression de la caution "+idCaution, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM CAUTIONS WHERE idCaution = :idCaution
        ;`,{
            idCaution : idCaution,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM CAUTIONS WHERE idCaution = :idCaution
        ;`,{
            idCaution : idCaution,
        });

        logger.info("Suppression réussie de la caution "+idCaution, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de la caution "+idCaution, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const centreCoutsDelete = async (idLogger, idCentreDeCout) => {
    try {
        logger.info("Suppression du centre de couts "+idCentreDeCout, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout : idCentreDeCout,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let gerantsToDelete = await db.query(`
            DELETE FROM CENTRE_COUTS_PERSONNES WHERE idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout : idCentreDeCout,
        });

        let annuleCommandeIntegration = await db.query(`
            UPDATE COMMANDES SET integreCentreCouts = 0, idCentreDeCout = Null WHERE idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout : idCentreDeCout,
        });

        let operationsToDelete = await db.query(`
            SELECT * FROM CENTRE_COUTS_OPERATIONS WHERE idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout : idCentreDeCout,
        });
        for(const ope of operationsToDelete){await centreCoutsOperationsDelete('SYSTEM', ope.idOperations);}

        let documentsToDrop = await db.query(`
            SELECT * FROM DOCUMENTS_CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout : idCentreDeCout,
        });
        for(const doc of documentsToDrop){await centreCoutsDocDelete('SYSTEM', doc.idDocCouts);}

        let finalDeleteQuery = await db.query(`
            DELETE FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout : idCentreDeCout,
        });

        logger.info("Suppression réussie du centre de couts "+idCentreDeCout, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du centre de couts "+idCentreDeCout, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const centreCoutsDocDelete = async (idLogger, idDocCouts) => {
    try {
        logger.info("Suppression du document de centre de couts "+idDocCouts, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM DOCUMENTS_CENTRE_COUTS WHERE idDocCouts = :idDocCouts
        ;`,{
            idDocCouts : idDocCouts,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        if(getInitialData[0].urlFichierDocCouts != null)
        {
            const { unlink } = require('fs');
            unlink('uploads/centresCouts/'+getInitialData[0].urlFichierDocCouts, (err)=>{if(err){logger.error(err)}});
        }
        else
        {
            logger.warn("Suppression du fichier physique impossible pour le document de centre de couts "+idDocCouts+" car URL manquante en DB", {idPersonne: idLogger})
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM DOCUMENTS_CENTRE_COUTS WHERE idDocCouts = :idDocCouts
        ;`,{
            idDocCouts : idDocCouts,
        });

        logger.info("Suppression réussie du document de centre de couts "+idDocCouts, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du document de centre de couts "+idDocCouts, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const centreCoutsGerantDelete = async (idLogger, idGerant) => {
    try {
        logger.info("Suppression du gérant "+idGerant, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM CENTRE_COUTS_PERSONNES WHERE idGerant = :idGerant
        ;`,{
            idGerant : idGerant,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM CENTRE_COUTS_PERSONNES WHERE idGerant = :idGerant
        ;`,{
            idGerant : idGerant,
        });

        logger.info("Suppression réussie du gérant "+idGerant, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du gérant "+idGerant, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const centreCoutsOperationsDelete = async (idLogger, idOperations) => {
    try {
        logger.info("Suppression de l'opération "+idOperations, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM CENTRE_COUTS_OPERATIONS WHERE idOperations = :idOperations
        ;`,{
            idOperations : idOperations,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        if(getInitialData[0].idCommande > 0)
        {
            updateQuery = await db.query(`
                UPDATE COMMANDES SET integreCentreCouts = 0 WHERE idCommande = :idCommande
            ;`,{
                idCommande : getInitialData[0].idCommande,
            });
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM CENTRE_COUTS_OPERATIONS WHERE idOperations = :idOperations
        ;`,{
            idOperations : idOperations,
        });

        await fonctionsMetiers.calculerTotalCentreDeCouts(getInitialData[0].idCentreDeCout);

        logger.info("Suppression réussie l'opération "+idOperations, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'opération "+idOperations, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const codesBarreDelete = async (idLogger, idCode) => {
    try {
        logger.info("Suppression du code barre "+idCode, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM CODES_BARRE WHERE idCode = :idCode
        ;`,{
            idCode : idCode,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM CODES_BARRE WHERE idCode = :idCode
        ;`,{
            idCode : idCode,
        });

        logger.info("Suppression réussie du code barre "+idCode, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du code barre "+idCode, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const commandeDocDelete = async (idLogger, idDocCommande) => {
	try {
        logger.info("Suppression du document de commande "+idDocCommande, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM DOCUMENTS_COMMANDES WHERE idDocCommande = :idDocCommande
        ;`,{
            idDocCommande : idDocCommande,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        if(getInitialData[0].urlFichierDocCommande != null)
        {
            const { unlink } = require('fs');
            unlink('uploads/commandes/'+getInitialData[0].urlFichierDocCommande, (err)=>{if(err){logger.error(err)}});
        }
        else
        {
            logger.warn("Suppression du fichier physique impossible pour le document de commande "+idDocCommande+" car URL manquante en DB", {idPersonne: idLogger})
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM DOCUMENTS_COMMANDES WHERE idDocCommande = :idDocCommande
        ;`,{
            idDocCommande : idDocCommande,
        });

        logger.info("Suppression réussie du document de commande "+idDocCommande, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du document de commande "+idDocCommande, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const commandeItemDelete = async (idLogger, idCommande, idMaterielCatalogue, idCommandeMateriel) => {
    try {
        logger.info("Suppression de l'entrée commande idCommande: "+idCommande+" idMaterielCatalogue:"+idMaterielCatalogue, {idPersonne: idLogger})
        if(idCommandeMateriel != null && idCommandeMateriel > 0)
        {
            let finalDeleteQuery = await db.query(`
                DELETE FROM COMMANDES_MATERIEL WHERE idCommandeMateriel = :idCommandeMateriel;
            ;`,{
                idCommandeMateriel : idCommandeMateriel,
            });
        }else{
            if(idMaterielCatalogue == -1)
            {
                let getInitialData = await db.query(`
                    SELECT * FROM COMMANDES_MATERIEL WHERE idMaterielCatalogue Is Null AND idCommande = :idCommande
                ;`,{
                    idCommande : idCommande,
                });
                logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

                let finalDeleteQuery = await db.query(`
                    DELETE FROM COMMANDES_MATERIEL WHERE idMaterielCatalogue Is Null AND idCommande = :idCommande;
                ;`,{
                    idCommande : idCommande,
                });
            }
            else
            {
                if(idCommande == null)
                {
                    let finalDeleteQuery = await db.query(`
                        DELETE FROM COMMANDES_MATERIEL WHERE idMaterielCatalogue = :idMaterielCatalogue;
                    ;`,{
                        idMaterielCatalogue: idMaterielCatalogue,
                    });
                }
                else
                {
                    let getInitialData = await db.query(`
                    SELECT * FROM COMMANDES_MATERIEL WHERE idMaterielCatalogue = :idMaterielCatalogue AND idCommande = :idCommande
                    ;`,{
                        idCommande : idCommande,
                        idMaterielCatalogue: idMaterielCatalogue,
                    });
                    logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

                    let finalDeleteQuery = await db.query(`
                        DELETE FROM COMMANDES_MATERIEL WHERE idMaterielCatalogue = :idMaterielCatalogue AND idCommande = :idCommande;
                    ;`,{
                        idCommande : idCommande,
                        idMaterielCatalogue: idMaterielCatalogue,
                    });
                }
            }
        }

        logger.info("Suppression réussie de l'entrée commande idCommande: "+idCommande+" idMaterielCatalogue:"+idMaterielCatalogue+"  idCommandeMateriel:"+idCommandeMateriel, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'entrée commande idCommande: "+idCommande+" idMaterielCatalogue:"+idMaterielCatalogue+"  idCommandeMateriel:"+idCommandeMateriel, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const commandesDelete = async (idLogger, idCommande) => {
    try {
        logger.info("Suppression de la commande "+idCommande, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM COMMANDES WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        let deleteQuery;
        let materielADetacher = await db.query(`
            SELECT * FROM COMMANDES_MATERIEL WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });
        for(const materiel of materielADetacher){await commandeItemDelete('SYSTEM', null, null, materiel.idCommandeMateriel);}
        
        deleteQuery = await db.query(`
            DELETE FROM COMMANDES_TIMELINE WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });

        let documentsADrop = await db.query(`
            SELECT * FROM DOCUMENTS_COMMANDES WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });
        for(const document of documentsADrop){await commandeDocDelete('SYSTEM', document.idDocCommande);}

        deleteQuery = await db.query(`
            DELETE FROM COMMANDES_AFFECTEES WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });
        deleteQuery = await db.query(`
            DELETE FROM COMMANDES_OBSERVATEURS WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });
        deleteQuery = await db.query(`
            DELETE FROM COMMANDES_DEMANDEURS WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });

        let operationsADrop = await db.query(`
            SELECT * FROM CENTRE_COUTS_OPERATIONS WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });
        for(const operation of operationsADrop){await centreCoutsOperationsDelete('SYSTEM', operation.idOperations);}

        let finalDeleteQuery = await db.query(`
            DELETE FROM COMMANDES WHERE idCommande = :idCommande
        ;`,{
            idCommande : idCommande,
        });

        logger.info("Suppression réussie de la commande "+idCommande, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de la commande "+idCommande, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const emplacementsDelete = async (idLogger, idEmplacement) => {
    try {
        logger.info("Suppression de l'emplacement "+idEmplacement, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM MATERIEL_EMPLACEMENT WHERE idEmplacement = :idEmplacement
        ;`,{
            idEmplacement : idEmplacement,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE MATERIEL_ELEMENT SET idEmplacement = Null WHERE idEmplacement = :idEmplacement
        ;`,{
            idEmplacement : idEmplacement,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM MATERIEL_EMPLACEMENT WHERE idEmplacement = :idEmplacement
        ;`,{
            idEmplacement : idEmplacement,
        });

        logger.info("Suppression réussie de l'emplacement "+idEmplacement, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'emplacement "+idEmplacement, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const etatsLotsDelete = async (idLogger, idLotsEtat) => {
    try {
        logger.info("Suppression de l'état de lot "+idLotsEtat, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM LOTS_ETATS WHERE idLotsEtat = :idLotsEtat
        ;`,{
            idLotsEtat : idLotsEtat,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE LOTS_LOTS SET idLotsEtat = Null WHERE idLotsEtat = :idLotsEtat
        ;`,{
            idLotsEtat : idLotsEtat,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM LOTS_ETATS WHERE idLotsEtat = :idLotsEtat
        ;`,{
            idLotsEtat : idLotsEtat,
        });

        logger.info("Suppression réussie de l'état de lot "+idLotsEtat, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'état de lot "+idLotsEtat, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const etatsMaterielsDelete = async (idLogger, idMaterielsEtat) => {
    try {
        logger.info("Suppression de l'état matériel "+idMaterielsEtat, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM MATERIEL_ETATS WHERE idMaterielsEtat = :idMaterielsEtat
        ;`,{
            idMaterielsEtat : idMaterielsEtat,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE MATERIEL_ELEMENT SET idMaterielsEtat = Null WHERE idMaterielsEtat = :idMaterielsEtat
        ;`,{
            idMaterielsEtat : idMaterielsEtat,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM MATERIEL_ETATS WHERE idMaterielsEtat = :idMaterielsEtat
        ;`,{
            idMaterielsEtat : idMaterielsEtat,
        });

        logger.info("Suppression réussie de l'état matériel "+idMaterielsEtat, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'état matériel "+idMaterielsEtat, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const etatsVehiculesDelete = async (idLogger, idVehiculesEtat) => {
    try {
        logger.info("Suppression de l'état de véhicule "+idVehiculesEtat, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_ETATS WHERE idVehiculesEtat = :idVehiculesEtat
        ;`,{
            idVehiculesEtat : idVehiculesEtat,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE VEHICULES SET idVehiculesEtat = Null WHERE idVehiculesEtat = :idVehiculesEtat
        ;`,{
            idVehiculesEtat : idVehiculesEtat,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_ETATS WHERE idVehiculesEtat = :idVehiculesEtat
        ;`,{
            idVehiculesEtat : idVehiculesEtat,
        });

        logger.info("Suppression réussie de l'état de véhicule "+idVehiculesEtat, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'état de véhicule "+idVehiculesEtat, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const fournisseursDelete = async (idLogger, idFournisseur) => {
    try {
        logger.info("Suppression du fournisseur "+idFournisseur, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM FOURNISSEURS WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE MATERIEL_SAC SET idFournisseur = Null WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });
        updateQuery = await db.query(`
            UPDATE MATERIEL_ELEMENT SET idFournisseur = Null WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });
        updateQuery = await db.query(`
            UPDATE COMMANDES SET idFournisseur = Null WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });
        updateQuery = await db.query(`
            UPDATE RESERVES_MATERIEL SET idFournisseur = Null WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });
        updateQuery = await db.query(`
            UPDATE TENUES_CATALOGUE SET idFournisseur = Null WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });
        updateQuery = await db.query(`
            UPDATE MATERIEL_CATALOGUE SET idFournisseur = Null WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });
        updateQuery = await db.query(`
            UPDATE VEHICULES_STOCK SET idFournisseur = Null WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });
        updateQuery = await db.query(`
            UPDATE VHF_STOCK SET idFournisseur = Null WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM FOURNISSEURS WHERE idFournisseur = :idFournisseur
        ;`,{
            idFournisseur : idFournisseur,
        });

        logger.info("Suppression réussie du fournisseur "+idFournisseur, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du fournisseur "+idFournisseur, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const lieuxDelete = async (idLogger, idLieu) => {
    try {
        logger.info("Suppression du lieu "+idLieu, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM LIEUX WHERE idLieu = :idLieu
        ;`,{
            idLieu : idLieu,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE LOTS_LOTS SET idLieu = Null WHERE idLieu = :idLieu
        ;`,{
            idLieu : idLieu,
        });
        updateQuery = await db.query(`
            UPDATE COMMANDES SET idLieuLivraison = Null WHERE idLieuLivraison = :idLieu
        ;`,{
            idLieu : idLieu,
        });
        updateQuery = await db.query(`
            UPDATE RESERVES_CONTENEUR SET idLieu = Null WHERE idLieu = :idLieu
        ;`,{
            idLieu : idLieu,
        });
        updateQuery = await db.query(`
            UPDATE VEHICULES SET idLieu = Null WHERE idLieu = :idLieu
        ;`,{
            idLieu : idLieu,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM LIEUX WHERE idLieu = :idLieu
        ;`,{
            idLieu : idLieu,
        });

        logger.info("Suppression réussie du lieu "+idLieu, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du lieu "+idLieu, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const lotsConsommationsDelete = async (idLogger, idConsommation) => {
    try {
        logger.info("Suppression du rapport de consommation de consommables bénévoles "+idConsommation, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM LOTS_CONSOMMATION WHERE idConsommation = :idConsommation
        ;`,{
            idConsommation : idConsommation,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let deleteContent = await db.query(`
            DELETE FROM LOTS_CONSOMMATION_MATERIEL WHERE idConsommation = :idConsommation
        ;`,{
            idConsommation : idConsommation,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM LOTS_CONSOMMATION WHERE idConsommation = :idConsommation
        ;`,{
            idConsommation : idConsommation,
        });

        logger.info("Suppression réussie du rapport de consommation de consommables bénévoles "+idConsommation, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du rapport de consommation de consommables bénévoles "+idConsommation, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const lotsDelete = async (idLogger, idLot) => {
    try {
        logger.info("Suppression du lot "+idLot, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM LOTS_LOTS WHERE idLot = :idLot
        ;`,{
            idLot : idLot,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        let inventairesADrop = await db.query(`
            SELECT * FROM INVENTAIRES WHERE idLot = :idLot
        ;`,{
            idLot : idLot,
        });
        for(const inventaire of inventairesADrop){await lotsInventaireDelete('SYSTEM', inventaire.idInventaire);}

        let alertesADrop = await db.query(`
            SELECT * FROM LOTS_ALERTES WHERE idLot = :idLot
        ;`,{
            idLot : idLot,
        });
        for(const alerte of alertesADrop){await alerteBenevoleLotDelete('SYSTEM', alerte.idAlerte);}

        updateQuery = await db.query(`
            UPDATE MATERIEL_SAC SET idLot = Null WHERE idLot = :idLot
        ;`,{
            idLot : idLot,
        });

        let updateConsoMateriel = await db.query(`
            UPDATE LOTS_CONSOMMATION_MATERIEL SET idLot = Null WHERE idLot = :idLot
        ;`,{
            idLot : idLot,
        });


        let finalDeleteQuery = await db.query(`
            DELETE FROM LOTS_LOTS WHERE idLot = :idLot
        ;`,{
            idLot : idLot,
        });

        logger.info("Suppression réussie du lot "+idLot, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du lot "+idLot, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const lotsInventaireDelete = async (idLogger, idInventaire) => {
    try {
        logger.info("Suppression de l'inventaire de lot "+idInventaire, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM INVENTAIRES WHERE idInventaire = :idInventaire
        ;`,{
            idInventaire : idInventaire,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let deleteQuery = await db.query(`
            DELETE FROM INVENTAIRES_CONTENUS WHERE idInventaire = :idInventaire
        ;`,{
            idInventaire : idInventaire,
        });

        deleteQuery = await db.query(`
            DELETE FROM LOTS_INVENTAIRES_TEMP WHERE idInventaire = :idInventaire
        ;`,{
            idInventaire : idInventaire,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM INVENTAIRES WHERE idInventaire = :idInventaire
        ;`,{
            idInventaire : idInventaire,
        });

        logger.info("Suppression réussie de l'inventaire de lot "+idInventaire, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'inventaire de lot "+idInventaire, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const materielsDelete = async (idLogger, idElement) => {
    try {
        logger.info("Suppression de l'élément matériel "+idElement, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue WHERE idElement = :idElement
        ;`,{
            idElement : idElement,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM MATERIEL_ELEMENT WHERE idElement = :idElement
        ;`,{
            idElement : idElement,
        });

        if(getInitialData[0].idLot > 0)
        {
            await fonctionsMetiers.checkOneConf(getInitialData[0].idLot)
        }

        logger.info("Suppression réussie de l'élément matériel "+idElement, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'élément matériel "+idElement, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const messagesDelete = async (idLogger, idMessage) => {
    try {
        logger.info("Suppression du message "+idMessage, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM MESSAGES WHERE idMessage = :idMessage
        ;`,{
            idMessage : idMessage,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM MESSAGES WHERE idMessage = :idMessage
        ;`,{
            idMessage : idMessage,
        });

        logger.info("Suppression réussie du message "+idMessage, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du message "+idMessage, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const profilsDelete = async (idLogger, idProfil) => {
    try {
        logger.info("Suppression du profil "+idProfil, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM PROFILS WHERE idProfil = :idProfil
        ;`,{
            idProfil : idProfil,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let personnesImpactees = await db.query(`
            SELECT * FROM PROFILS_PERSONNES WHERE idProfil = :idProfil
        ;`,{
            idProfil : idProfil,
        });
        let suppressionLiens = await db.query(`
            DELETE FROM PROFILS_PERSONNES WHERE idProfil = :idProfil
        ;`,{
            idProfil : idProfil,
        });
        await fonctionsMetiers.deconnecterProfil(idProfil);
        for(const personne of personnesImpactees)
        {
            await fonctionsMetiers.majIndicateursPersonne(personne.idPersonne, true);
            await fonctionsMetiers.majNotificationsPersonne(personne.idPersonne, true);
            await fonctionsMetiers.majValideursPersonne(true);
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM PROFILS WHERE idProfil = :idProfil
        ;`,{
            idProfil : idProfil,
        });

        logger.info("Suppression réussie du profil "+idProfil, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du profil "+idProfil, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const referentielsDelete = async (idLogger, idTypeLot) => {
    try {
        logger.info("Suppression du référentiel "+idTypeLot, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot
        ;`,{
            idTypeLot : idTypeLot,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let detacherLots = await db.query(`
            UPDATE LOTS_LOTS SET idTypeLot = Null WHERE idTypeLot = :idTypeLot
        ;`,{
            idTypeLot : idTypeLot,
        });

        let deleteContenuRef = await db.query(`
            DELETE FROM REFERENTIELS WHERE idTypeLot = :idTypeLot
        ;`,{
            idTypeLot : idTypeLot,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot
        ;`,{
            idTypeLot : idTypeLot,
        });

        logger.info("Suppression réussie du référentiel "+idTypeLot, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du référentiel "+idTypeLot, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const reserveConteneurDelete = async (idLogger, idConteneur) => {
    try {
        logger.info("Suppression du conteneur de réserve "+idConteneur, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM RESERVES_CONTENEUR WHERE idConteneur = :idConteneur
        ;`,{
            idConteneur : idConteneur,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let inventairesADrop = await db.query(`
            SELECT * FROM RESERVES_INVENTAIRES WHERE idConteneur = :idConteneur
        ;`,{
            idConteneur : idConteneur,
        });
        for(const inventaire of inventairesADrop){await reserveInventaireDelete('SYSTEM', inventaire.idReserveInventaire);}

        let updateMateriel = await db.query(`
            UPDATE RESERVES_MATERIEL SET idConteneur = Null WHERE idConteneur = :idConteneur
        ;`,{
            idConteneur : idConteneur,
        });

        let updateConsoMateriel = await db.query(`
            UPDATE LOTS_CONSOMMATION_MATERIEL SET idConteneur = Null WHERE idConteneur = :idConteneur
        ;`,{
            idConteneur : idConteneur,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM RESERVES_CONTENEUR WHERE idConteneur = :idConteneur
        ;`,{
            idConteneur : idConteneur,
        });

        logger.info("Suppression réussie du conteneur de réserve "+idConteneur, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du conteneur de réserve "+idConteneur, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const reserveInventaireDelete = async (idLogger, idReserveInventaire) => {
    try {
        logger.info("Suppression de l'inventaire de reserve "+idReserveInventaire, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM RESERVES_INVENTAIRES WHERE idReserveInventaire = :idReserveInventaire
        ;`,{
            idReserveInventaire : idReserveInventaire,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let deleteQuery = await db.query(`
            DELETE FROM RESERVES_INVENTAIRES_CONTENUS WHERE idReserveInventaire = :idReserveInventaire
        ;`,{
            idReserveInventaire : idReserveInventaire,
        });

        deleteQuery = await db.query(`
            DELETE FROM RESERVES_INVENTAIRES_TEMP WHERE idReserveInventaire = :idReserveInventaire
        ;`,{
            idReserveInventaire : idReserveInventaire,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM RESERVES_INVENTAIRES WHERE idReserveInventaire = :idReserveInventaire
        ;`,{
            idReserveInventaire : idReserveInventaire,
        });

        logger.info("Suppression réussie de l'inventaire de reserve "+idReserveInventaire, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'inventaire de reserve "+idReserveInventaire, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const reserveMaterielDelete = async (idLogger, idReserveElement) => {
    try {
        logger.info("Suppression du matériel de reserve "+idReserveElement, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM RESERVES_MATERIEL WHERE idReserveElement = :idReserveElement
        ;`,{
            idReserveElement : idReserveElement,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM RESERVES_MATERIEL WHERE idReserveElement = :idReserveElement
        ;`,{
            idReserveElement : idReserveElement,
        });

        logger.info("Suppression réussie du matériel de reserve "+idReserveElement, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du matériel de reserve "+idReserveElement, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const sacsDelete = async (idLogger, idSac) => {
    try {
        logger.info("Suppression du sac "+idSac, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM MATERIEL_SAC WHERE idSac = :idSac
        ;`,{
            idSac : idSac,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});


        let emplacementsASupprimer = await db.query(`
            SELECT * FROM MATERIEL_EMPLACEMENT WHERE idSac = :idSac
        ;`,{
            idSac : idSac,
        });
        for(const emp of emplacementsASupprimer){await emplacementsDelete('SYSTEM', emp.idEmplacement);}

        let finalDeleteQuery = await db.query(`
            DELETE FROM MATERIEL_SAC WHERE idSac = :idSac
        ;`,{
            idSac : idSac,
        });

        logger.info("Suppression réussie du sac "+idSac, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du sac "+idSac, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const tenuesAffectationsDelete = async (idLogger, idTenue, reintegration) => {
    try {
        logger.info("Suppression de l'affecation de la tenue "+idTenue, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM TENUES_AFFECTATION WHERE idTenue = :idTenue
        ;`,{
            idTenue : idTenue,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        if(reintegration == true)
        {
            updateQuery = await db.query(`
                UPDATE TENUES_CATALOGUE SET stockCatalogueTenue = stockCatalogueTenue + 1 WHERE idMaterielCatalogue = :idMaterielCatalogue
            ;`,{
                idMaterielCatalogue : getInitialData[0].idMaterielCatalogue,
            });
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM TENUES_AFFECTATION WHERE idTenue = :idTenue
        ;`,{
            idTenue : idTenue,
        });

        logger.info("Suppression réussie de l'affecation de la tenue "+idTenue, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'affecation de la tenue "+idTenue, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const tenuesCatalogueDelete = async (idLogger, idCatalogueTenue) => {
    try {
        logger.info("Suppression d'une entrée dans le stock des tenues "+idCatalogueTenue, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM TENUES_CATALOGUE WHERE idCatalogueTenue = :idCatalogueTenue
        ;`,{
            idCatalogueTenue : idCatalogueTenue,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM TENUES_CATALOGUE WHERE idCatalogueTenue = :idCatalogueTenue
        ;`,{
            idCatalogueTenue : idCatalogueTenue,
        });

        logger.info("Suppression réussie d'une entrée dans le stock des tenues "+idCatalogueTenue, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec d'une entrée dans le stock des tenues "+idCatalogueTenue, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const todolistDelete = async (idLogger, idTache) => {
    try {
        logger.info("Suppression de la tache "+idTache, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM TODOLIST WHERE idTache = :idTache
        ;`,{
            idTache : idTache,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let deleteQuery = await db.query(`
            DELETE FROM TODOLIST_PERSONNES WHERE idTache = :idTache
        ;`,{
            idTache : idTache,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM TODOLIST WHERE idTache = :idTache
        ;`,{
            idTache : idTache,
        });

        logger.info("Suppression réussie de la tache "+idTache, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de la tache "+idTache, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesCarburantsDelete = async (idLogger, idCarburant) => {
    try {
        logger.info("Suppression du carburant "+idCarburant, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM CARBURANTS WHERE idCarburant = :idCarburant
        ;`,{
            idCarburant : idCarburant,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE VEHICULES SET idCarburant = Null WHERE idCarburant = :idCarburant
        ;`,{
            idCarburant : idCarburant,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM CARBURANTS WHERE idCarburant = :idCarburant
        ;`,{
            idCarburant : idCarburant,
        });

        logger.info("Suppression réussie du carburant "+idCarburant, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du carburant "+idCarburant, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesPneumatiquesDelete = async (idLogger, idPneumatique) => {
    try {
        logger.info("Suppression du type de pneumatique "+idPneumatique, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_PNEUMATIQUES WHERE idPneumatique = :idPneumatique
        ;`,{
            idPneumatique : idPneumatique,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE VEHICULES SET idPneumatiqueAvant = Null WHERE idPneumatiqueAvant = :idPneumatique
        ;`,{
            idPneumatique : idPneumatique,
        });
        updateQuery = await db.query(`
            UPDATE VEHICULES SET idPneumatiqueArriere = Null WHERE idPneumatiqueArriere = :idPneumatique
        ;`,{
            idPneumatique : idPneumatique,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_PNEUMATIQUES WHERE idPneumatique = :idPneumatique
        ;`,{
            idPneumatique : idPneumatique,
        });

        logger.info("Suppression réussie du type de pneumatique "+idPneumatique, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du type de pneumatique "+idPneumatique, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesDelete = async (idLogger, idVehicule) => {
    try {
        logger.info("Suppression du véhicule "+idVehicule, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery = await db.query(`
            UPDATE LOTS_LOTS SET idVehicule = Null WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });

        let documentsToDrop = await db.query(`
            SELECT * FROM DOCUMENTS_VEHICULES WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });
        for(const document of documentsToDrop){await vehiculesDocDelete('SYSTEM', document.idDocVehicules);}

        let maintenancesToDrop = await db.query(`
            SELECT * FROM VEHICULES_MAINTENANCE WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });
        for(const mnt of maintenancesToDrop){await vehiculesMaintenanceDelete('SYSTEM', mnt.idMaintenance);}

        let releveKMToDrop = await db.query(`
            SELECT * FROM VEHICULES_RELEVES WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });
        for(const releve of releveKMToDrop){await vehiculesReleveDelete('SYSTEM', releve.idReleve);}

        let desinfectionsToDrop = await db.query(`
            SELECT * FROM VEHICULES_DESINFECTIONS WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });
        for(const desinf of desinfectionsToDrop){await vehiculesDesinfectionsDelete('SYSTEM', desinf.idVehiculesDesinfection);}

        let dropDesinfectionsAlertes = await db.query(`
            DELETE FROM VEHICULES_DESINFECTIONS_ALERTES WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });

        let healthToDrop = await db.query(`
            SELECT * FROM VEHICULES_HEALTH WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });
        for(const health of healthToDrop){await vehiculesHealthDelete('SYSTEM', health.idVehiculeHealth);}

        let dropHealthAlertes = await db.query(`
            DELETE FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });

        let dropCarteGriseDetails = await db.query(`
            DELETE FROM VEHICULES_CHAMPS_CG WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });

        let alertesBenevolesToDop = await db.query(`
            SELECT * FROM VEHICULES_ALERTES WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });
        for(const alerte of alertesBenevolesToDop){await alerteBenevoleVehiculeDelete('SYSTEM', alerte.idAlerte);}

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES WHERE idVehicule = :idVehicule
        ;`,{
            idVehicule : idVehicule,
        });

        logger.info("Suppression réussie du véhicule "+idVehicule, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du véhicule "+idVehicule, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesDesinfectionsDelete = async (idLogger, idVehiculesDesinfection) => {
    try {
        logger.info("Suppression d'une itération de désinfection de véhicule "+idVehiculesDesinfection, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_DESINFECTIONS WHERE idVehiculesDesinfection = :idVehiculesDesinfection
        ;`,{
            idVehiculesDesinfection : idVehiculesDesinfection,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_DESINFECTIONS WHERE idVehiculesDesinfection = :idVehiculesDesinfection
        ;`,{
            idVehiculesDesinfection : idVehiculesDesinfection,
        });

        logger.info("Suppression réussie d'une itération de désinfection de véhicule "+idVehiculesDesinfection, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec d'une itération de désinfection de véhicule "+idVehiculesDesinfection, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesDocDelete = async (idLogger, idDocVehicules) => {
	try {
        logger.info("Suppression du document de véhicule "+idDocVehicules, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM DOCUMENTS_VEHICULES WHERE idDocVehicules = :idDocVehicules
        ;`,{
            idDocVehicules : idDocVehicules,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        if(getInitialData[0].urlFichierDocVehicule != null)
        {
            const { unlink } = require('fs');
            unlink('uploads/vehicules/'+getInitialData[0].urlFichierDocVehicule, (err)=>{if(err){logger.error(err)}});
        }
        else
        {
            logger.warn("Suppression du fichier physique impossible pour le document de véhicule "+idDocVehicules+" car URL manquante en DB", {idPersonne: idLogger})
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM DOCUMENTS_VEHICULES WHERE idDocVehicules = :idDocVehicules
        ;`,{
            idDocVehicules : idDocVehicules,
        });

        logger.info("Suppression réussie du document de véhicule "+idDocVehicules, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du document de véhicule "+idDocVehicules, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesHealthDelete = async (idLogger, idVehiculeHealth) => {
    try {
        logger.info("Suppression de maintenance régulière de véhicule "+idVehiculeHealth, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_HEALTH WHERE idVehiculeHealth = :idVehiculeHealth
        ;`,{
            idVehiculeHealth : idVehiculeHealth,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let deleteQuery = await db.query(`
            DELETE FROM VEHICULES_HEALTH_CHECKS WHERE idVehiculeHealth = :idVehiculeHealth
        ;`,{
            idVehiculeHealth : idVehiculeHealth,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_HEALTH WHERE idVehiculeHealth = :idVehiculeHealth
        ;`,{
            idVehiculeHealth : idVehiculeHealth,
        });

        logger.info("Suppression réussie de maintenance régulière de véhicule "+idVehiculeHealth, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de maintenance régulière de véhicule "+idVehiculeHealth, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesMaintenanceDelete = async (idLogger, idMaintenance) => {
    try {
        logger.info("Suppression d'une maintenance "+idMaintenance, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_MAINTENANCE WHERE idMaintenance = :idMaintenance
        ;`,{
            idMaintenance : idMaintenance,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_MAINTENANCE WHERE idMaintenance = :idMaintenance
        ;`,{
            idMaintenance : idMaintenance,
        });

        logger.info("Suppression réussie d'une maintenance "+idMaintenance, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec d'une maintenance "+idMaintenance, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesReleveDelete = async (idLogger, idReleve) => {
    try {
        logger.info("Suppression d'un relevé kilométrique "+idReleve, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_RELEVES WHERE idReleve = :idReleve
        ;`,{
            idReleve : idReleve,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_RELEVES WHERE idReleve = :idReleve
        ;`,{
            idReleve : idReleve,
        });

        logger.info("Suppression réussie d'un relevé kilométrique "+idReleve, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec d'un relevé kilométrique "+idReleve, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesTypesDelete = async (idLogger, idVehiculesType) => {
    try {
        logger.info("Suppression du type de véhicule "+idVehiculesType, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_TYPES WHERE idVehiculesType = :idVehiculesType
        ;`,{
            idVehiculesType : idVehiculesType,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateQuery;
        updateQuery = await db.query(`
            UPDATE VEHICULES SET idVehiculesType = Null WHERE idVehiculesType = :idVehiculesType
        ;`,{
            idVehiculesType : idVehiculesType,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_TYPES WHERE idVehiculesType = :idVehiculesType
        ;`,{
            idVehiculesType : idVehiculesType,
        });

        logger.info("Suppression réussie du type de véhicule "+idVehiculesType, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du type de véhicule "+idVehiculesType, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesTypesDesinfectionsDelete = async (idLogger, idVehiculesDesinfectionsType) => {
    try {
        logger.info("Suppression du type de désinfection "+idVehiculesDesinfectionsType, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_DESINFECTIONS_TYPES WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
        ;`,{
            idVehiculesDesinfectionsType : idVehiculesDesinfectionsType,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let desinfectionsADelete = await db.query(`
            SELECT * FROM VEHICULES_DESINFECTIONS WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
        ;`,{
            idVehiculesDesinfectionsType : idVehiculesDesinfectionsType,
        });
        for(const desinf of desinfectionsADelete){await vehiculesDesinfectionsDelete('SYSTEM', desinf.idVehiculesDesinfection);}

        let alertesADelete = await db.query(`
            DELETE FROM VEHICULES_DESINFECTIONS_ALERTES WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
        ;`,{
            idVehiculesDesinfectionsType : idVehiculesDesinfectionsType,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_DESINFECTIONS_TYPES WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
        ;`,{
            idVehiculesDesinfectionsType : idVehiculesDesinfectionsType,
        });

        logger.info("Suppression réussie du type de désinfection "+idVehiculesDesinfectionsType, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du type de désinfection "+idVehiculesDesinfectionsType, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesTypesMaintenanceReguliereDelete = async (idLogger, idHealthType) => {
    try {
        logger.info("Suppression du type de maintenance régulière "+idHealthType, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_HEALTH_TYPES WHERE idHealthType = :idHealthType
        ;`,{
            idHealthType : idHealthType,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let healthCheckToDrop = await db.query(`
            SELECT * FROM VEHICULES_HEALTH_CHECKS WHERE idHealthType = :idHealthType
        ;`,{
            idHealthType : idHealthType,
        });
        for(const health of healthCheckToDrop){await vehiculesHealthDelete('SYSTEM', health.idVehiculeHealth);}

        let alertesADelete = await db.query(`
            DELETE FROM VEHICULES_HEALTH_ALERTES WHERE idHealthType = :idHealthType
        ;`,{
            idHealthType : idHealthType,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_HEALTH_TYPES WHERE idHealthType = :idHealthType
        ;`,{
            idHealthType : idHealthType,
        });

        logger.info("Suppression réussie du type de maintenance régulière "+idHealthType, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du type de maintenance régulière "+idHealthType, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesTypesMaintenancesPonctuellesDelete = async (idLogger, idTypeMaintenance) => {
    try {
        logger.info("Suppression du type de maintenance ponctuelle "+idTypeMaintenance, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_MAINTENANCE_TYPES WHERE idTypeMaintenance = :idTypeMaintenance
        ;`,{
            idTypeMaintenance : idTypeMaintenance,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let updateMaintenances = await db.query(`
            UPDATE VEHICULES_MAINTENANCE SET idTypeMaintenance = null WHERE idTypeMaintenance = :idTypeMaintenance
        ;`,{
            idTypeMaintenance : idTypeMaintenance,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_MAINTENANCE_TYPES WHERE idTypeMaintenance = :idTypeMaintenance
        ;`,{
            idTypeMaintenance : idTypeMaintenance,
        });

        logger.info("Suppression réussie du type de maintenance ponctuelle "+idTypeMaintenance, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du type de maintenance ponctuelle "+idTypeMaintenance, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesDetailCGDelete = async (idLogger, idVehiculeChampCG) => {
    try {
        logger.info("Suppression d'un champ carte grise "+idVehiculeChampCG, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_CHAMPS_CG WHERE idVehiculeChampCG = :idVehiculeChampCG
        ;`,{
            idVehiculeChampCG : idVehiculeChampCG,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_CHAMPS_CG WHERE idVehiculeChampCG = :idVehiculeChampCG
        ;`,{
            idVehiculeChampCG : idVehiculeChampCG,
        });

        logger.info("Suppression réussie d'un champ carte grise "+idVehiculeChampCG, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec d'un champ carte grise "+idVehiculeChampCG, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vehiculesStockDelete = async (idLogger, idVehiculesStock) => {
    try {
        logger.info("Suppression d'un élément de stock véhicules "+idVehiculesStock, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VEHICULES_STOCK WHERE idVehiculesStock = :idVehiculesStock
        ;`,{
            idVehiculesStock : idVehiculesStock,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VEHICULES_STOCK WHERE idVehiculesStock = :idVehiculesStock
        ;`,{
            idVehiculesStock : idVehiculesStock,
        });

        logger.info("Suppression réussie d'un élément de stock véhicules "+idVehiculesStock, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec d'un élément de stock véhicules "+idVehiculesStock, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfCanauxDelete = async (idLogger, idVhfCanal) => {
    try {
        logger.info("Suppression de canal du fréquence VHF "+idVhfCanal, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_CANAL WHERE idVhfCanal = :idVhfCanal
        ;`,{
            idVhfCanal : idVhfCanal,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let documentsToDrop = await db.query(`
            SELECT * FROM DOCUMENTS_CANAL_VHF WHERE idVhfCanal = :idVhfCanal
        ;`,{
            idVhfCanal : idVhfCanal,
        });
        for(const document of documentsToDrop){await vhfCanauxDocDelete('SYSTEM', document.idDocCanalVHF);}

        let associations = await db.query(`
            SELECT * FROM VHF_PLAN_CANAL WHERE idVhfCanal = :idVhfCanal
        ;`,{
            idVhfCanal : idVhfCanal,
        });
        for(const assoc of associations){await vhfPlansCanauxDelete('SYSTEM', assoc.idVhfPlan, idVhfCanal);}

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_CANAL WHERE idVhfCanal = :idVhfCanal
        ;`,{
            idVhfCanal : idVhfCanal,
        });

        logger.info("Suppression réussie du canal de fréquence VHF "+idVhfCanal, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du canal de fréquence VHF "+idVhfCanal, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfCanauxDocDelete = async (idLogger, idDocCanalVHF) => {
	try {
        logger.info("Suppression du document de canal VHF "+idDocCanalVHF, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM DOCUMENTS_CANAL_VHF WHERE idDocCanalVHF = :idDocCanalVHF
        ;`,{
            idDocCanalVHF : idDocCanalVHF,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        if(getInitialData[0].urlFichierDocCanalVHF != null)
        {
            const { unlink } = require('fs');
            unlink('uploads/vhfCanaux/'+getInitialData[0].urlFichierDocCanalVHF, (err)=>{if(err){logger.error(err)}});
        }
        else
        {
            logger.warn("Suppression du fichier physique impossible pour le document de canal VHF "+idDocCanalVHF+" car URL manquante en DB", {idPersonne: idLogger})
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM DOCUMENTS_CANAL_VHF WHERE idDocCanalVHF = :idDocCanalVHF
        ;`,{
            idDocCanalVHF : idDocCanalVHF,
        });

        logger.info("Suppression réussie du document de canal VHF "+idDocCanalVHF, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du document de canal VHF "+idDocCanalVHF, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfEquipementsAccessoiresDelete = async (idLogger, idVhfAccessoire) => {
    try {
        logger.info("Suppression de l'accessoire VHF "+idVhfAccessoire, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_ACCESSOIRES WHERE idVhfAccessoire = :idVhfAccessoire
        ;`,{
            idVhfAccessoire : idVhfAccessoire,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_ACCESSOIRES WHERE idVhfAccessoire = :idVhfAccessoire
        ;`,{
            idVhfAccessoire : idVhfAccessoire,
        });

        logger.info("Suppression réussie de l'accessoire VHF "+idVhfAccessoire, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'accessoire VHF "+idVhfAccessoire, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfEquipementsDelete = async (idLogger, idVhfEquipement) => {
    try {
        logger.info("Suppression de l'accessoire VHF "+idVhfEquipement, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_EQUIPEMENTS WHERE idVhfEquipement = :idVhfEquipement
        ;`,{
            idVhfEquipement : idVhfEquipement,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let documentsToDrop = await db.query(`
            SELECT * FROM DOCUMENTS_VHF WHERE idVhfEquipement = :idVhfEquipement
        ;`,{
            idVhfEquipement : idVhfEquipement,
        });
        for(const document of documentsToDrop){await vhfEquipementsDocDelete('SYSTEM', document.idDocVHF);}

        let accessoiresToDelete = await db.query(`
            SELECT * FROM VHF_ACCESSOIRES WHERE idVhfEquipement = :idVhfEquipement
        ;`,{
            idVhfEquipement : idVhfEquipement,
        });
        for(const accessoire of accessoiresToDelete){await vhfEquipementsAccessoiresDelete('SYSTEM', accessoire.idVhfAccessoire);}

        let alertesADrop = await db.query(`
            SELECT * FROM VHF_ALERTES WHERE idVhfEquipement = :idVhfEquipement
        ;`,{
            idVhfEquipement : idVhfEquipement,
        });
        for(const alerte of alertesADrop){await alerteBenevoleVHFDelete('SYSTEM', alerte.idAlerte);}

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_EQUIPEMENTS WHERE idVhfEquipement = :idVhfEquipement
        ;`,{
            idVhfEquipement : idVhfEquipement,
        });

        logger.info("Suppression réussie de l'accessoire VHF "+idVhfEquipement, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'accessoire VHF "+idVhfEquipement, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfEquipementsDocDelete = async (idLogger, idDocVHF) => {
	try {
        logger.info("Suppression du document d'équipement VHF "+idDocVHF, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM DOCUMENTS_VHF WHERE idDocVHF = :idDocVHF
        ;`,{
            idDocVHF : idDocVHF,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        if(getInitialData[0].urlFichierDocVHF != null)
        {
            const { unlink } = require('fs');
            unlink('uploads/vhfEquipements/'+getInitialData[0].urlFichierDocVHF, (err)=>{if(err){logger.error(err)}});
        }
        else
        {
            logger.warn("Suppression du fichier physique impossible pour le document d'équipement VHF "+idDocVHF+" car URL manquante en DB", {idPersonne: idLogger})
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM DOCUMENTS_VHF WHERE idDocVHF = :idDocVHF
        ;`,{
            idDocVHF : idDocVHF,
        });

        logger.info("Suppression réussie du document d'équipement VHF "+idDocVHF, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du document d'équipement VHF "+idDocVHF, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfPlansCanauxDelete = async (idLogger, idVhfPlan, idVhfCanal) => {
    try {
        logger.info("Suppression de l'association du plan "+idVhfPlan+" au canal "+idVhfCanal, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_PLAN_CANAL WHERE idVhfPlan = :idVhfPlan AND idVhfCanal = :idVhfCanal
        ;`,{
            idVhfPlan : idVhfPlan,
            idVhfCanal : idVhfCanal,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_PLAN_CANAL WHERE idVhfPlan = :idVhfPlan AND idVhfCanal = :idVhfCanal
        ;`,{
            idVhfPlan : idVhfPlan,
            idVhfCanal : idVhfCanal,
        });

        logger.info("Suppression réussie de l'association du plan "+idVhfPlan+" au canal "+idVhfCanal, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'association du plan "+idVhfPlan+" au canal "+idVhfCanal, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfPlansDelete = async (idLogger, idVhfPlan) => {
    try {
        logger.info("Suppression du plan de fréquence "+idVhfPlan, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_PLAN WHERE idVhfPlan = :idVhfPlan
        ;`,{
            idVhfPlan : idVhfPlan,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let documentsToDrop = await db.query(`
            SELECT * FROM DOCUMENTS_PLAN_VHF WHERE idVhfPlan = :idVhfPlan
        ;`,{
            idVhfPlan : idVhfPlan,
        });
        for(const document of documentsToDrop){await vhfPlansDocDelete('SYSTEM', document.idDocPlanVHF);}

        let associations = await db.query(`
            SELECT * FROM VHF_PLAN_CANAL WHERE idVhfPlan = :idVhfPlan
        ;`,{
            idVhfPlan : idVhfPlan,
        });
        for(const assoc of associations){await vhfPlansCanauxDelete('SYSTEM', idVhfPlan, assoc.idVhfCanal);}

        let unlinckEquipements = await db.query(`
            UPDATE VHF_EQUIPEMENTS SET idVhfPlan = Null WHERE idVhfPlan = :idVhfPlan
        ;`,{
            idVhfPlan : idVhfPlan,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_PLAN WHERE idVhfPlan = :idVhfPlan
        ;`,{
            idVhfPlan : idVhfPlan,
        });

        logger.info("Suppression réussie du plan de fréquence "+idVhfPlan, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du plan de fréquence "+idVhfPlan, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfPlansDocDelete = async (idLogger, idDocPlanVHF) => {
	try {
        logger.info("Suppression du document de plans "+idDocPlanVHF, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM DOCUMENTS_PLAN_VHF WHERE idDocPlanVHF = :idDocPlanVHF
        ;`,{
            idDocPlanVHF : idDocPlanVHF,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        if(getInitialData[0].urlFichierDocPlanVHF != null)
        {
            const { unlink } = require('fs');
            unlink('uploads/vhfPlans/'+getInitialData[0].urlFichierDocPlanVHF, (err)=>{if(err){logger.error(err)}});
        }
        else
        {
            logger.warn("Suppression du fichier physique impossible pour le document de plans "+idDocPlanVHF+" car URL manquante en DB", {idPersonne: idLogger})
        }

        let finalDeleteQuery = await db.query(`
            DELETE FROM DOCUMENTS_PLAN_VHF WHERE idDocPlanVHF = :idDocPlanVHF
        ;`,{
            idDocPlanVHF : idDocPlanVHF,
        });

        logger.info("Suppression réussie du document de plans "+idDocPlanVHF, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du document de plans "+idDocPlanVHF, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfTypesAccessoiresDelete = async (idLogger, idVhfAccessoireType) => {
    try {
        logger.info("Suppression du type d'accessoire VHF "+idVhfAccessoireType, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_ACCESSOIRES_TYPES WHERE idVhfAccessoireType = :idVhfAccessoireType
        ;`,{
            idVhfAccessoireType : idVhfAccessoireType,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let unlink = await db.query(`
            UPDATE VHF_ACCESSOIRES SET idVhfAccessoireType = Null WHERE idVhfAccessoireType = :idVhfAccessoireType
        ;`,{
            idVhfAccessoireType : idVhfAccessoireType,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_ACCESSOIRES_TYPES WHERE idVhfAccessoireType = :idVhfAccessoireType
        ;`,{
            idVhfAccessoireType : idVhfAccessoireType,
        });

        logger.info("Suppression réussie du type d'accessoire VHF "+idVhfAccessoireType, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du type d'accessoire VHF "+idVhfAccessoireType, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfEtatsDelete = async (idLogger, idVhfEtat) => {
    try {
        logger.info("Suppression de l'état d'équipement VHF "+idVhfEtat, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_ETATS WHERE idVhfEtat = :idVhfEtat
        ;`,{
            idVhfEtat : idVhfEtat,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let unlink = await db.query(`
            UPDATE VHF_EQUIPEMENTS SET idVhfEtat = Null WHERE idVhfEtat = :idVhfEtat
        ;`,{
            idVhfEtat : idVhfEtat,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_ETATS WHERE idVhfEtat = :idVhfEtat
        ;`,{
            idVhfEtat : idVhfEtat,
        });

        logger.info("Suppression réussie de l'état d'équipement VHF "+idVhfEtat, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de l'état d'équipement VHF "+idVhfEtat, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfTechnologiesDelete = async (idLogger, idVhfTechno) => {
    try {
        logger.info("Suppression de la technologie VHF "+idVhfTechno, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_TECHNOLOGIES WHERE idVhfTechno = :idVhfTechno
        ;`,{
            idVhfTechno : idVhfTechno,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let unlink = await db.query(`
            UPDATE VHF_EQUIPEMENTS SET idVhfTechno = Null WHERE idVhfTechno = :idVhfTechno
        ;`,{
            idVhfTechno : idVhfTechno,
        });

        unlink = await db.query(`
            UPDATE VHF_CANAL SET idVhfTechno = Null WHERE idVhfTechno = :idVhfTechno
        ;`,{
            idVhfTechno : idVhfTechno,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_TECHNOLOGIES WHERE idVhfTechno = :idVhfTechno
        ;`,{
            idVhfTechno : idVhfTechno,
        });

        logger.info("Suppression réussie de la technologie VHF "+idVhfTechno, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec de la technologie VHF "+idVhfTechno, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfTypesEquipementsDelete = async (idLogger, idVhfType) => {
    try {
        logger.info("Suppression du type d'equipements VHF "+idVhfType, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_TYPES_EQUIPEMENTS WHERE idVhfType = :idVhfType
        ;`,{
            idVhfType : idVhfType,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let unlink = await db.query(`
            UPDATE VHF_EQUIPEMENTS SET idVhfType = Null WHERE idVhfType = :idVhfType
        ;`,{
            idVhfType : idVhfType,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_TYPES_EQUIPEMENTS WHERE idVhfType = :idVhfType
        ;`,{
            idVhfType : idVhfType,
        });

        logger.info("Suppression réussie du type d'equipements VHF "+idVhfType, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du type d'equipements VHF "+idVhfType, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const vhfStockDelete = async (idLogger, idVhfStock) => {
    try {
        logger.info("Suppression d'un élément de stock vhf "+idVhfStock, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM VHF_STOCK WHERE idVhfStock = :idVhfStock
        ;`,{
            idVhfStock : idVhfStock,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let finalDeleteQuery = await db.query(`
            DELETE FROM VHF_STOCK WHERE idVhfStock = :idVhfStock
        ;`,{
            idVhfStock : idVhfStock,
        });

        logger.info("Suppression réussie d'un élément de stock vhf "+idVhfStock, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec d'un élément de stock vhf "+idVhfStock, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

const typeDocumentsDelete = async (idLogger, idTypeDocument) => {
    try {
        logger.info("Suppression du type de document "+idTypeDocument, {idPersonne: idLogger})

        let getInitialData = await db.query(`
            SELECT * FROM DOCUMENTS_TYPES WHERE idTypeDocument = :idTypeDocument
        ;`,{
            idTypeDocument : idTypeDocument,
        });
        logger.info("Sauvegarde avant suppression", {idPersonne: idLogger, backupBeforeDrop: getInitialData[0]});

        let unlink = await db.query(`
            UPDATE DOCUMENTS_CANAL_VHF SET idTypeDocument = Null WHERE idTypeDocument = :idTypeDocument
        ;`,{
            idTypeDocument : idTypeDocument,
        });
        unlink = await db.query(`
            UPDATE DOCUMENTS_CENTRE_COUTS SET idTypeDocument = Null WHERE idTypeDocument = :idTypeDocument
        ;`,{
            idTypeDocument : idTypeDocument,
        });
        unlink = await db.query(`
            UPDATE DOCUMENTS_COMMANDES SET idTypeDocument = Null WHERE idTypeDocument = :idTypeDocument
        ;`,{
            idTypeDocument : idTypeDocument,
        });
        unlink = await db.query(`
            UPDATE DOCUMENTS_VEHICULES SET idTypeDocument = Null WHERE idTypeDocument = :idTypeDocument
        ;`,{
            idTypeDocument : idTypeDocument,
        });
        unlink = await db.query(`
            UPDATE DOCUMENTS_VHF SET idTypeDocument = Null WHERE idTypeDocument = :idTypeDocument
        ;`,{
            idTypeDocument : idTypeDocument,
        });
        unlink = await db.query(`
            UPDATE DOCUMENTS_PLAN_VHF SET idTypeDocument = Null WHERE idTypeDocument = :idTypeDocument
        ;`,{
            idTypeDocument : idTypeDocument,
        });

        let finalDeleteQuery = await db.query(`
            DELETE FROM DOCUMENTS_TYPES WHERE idTypeDocument = :idTypeDocument
        ;`,{
            idTypeDocument : idTypeDocument,
        });

        logger.info("Suppression réussie du type de document "+idTypeDocument, {idPersonne: idLogger})
        return true;
    } catch (error) {
        logger.info("Suppression en échec du type de document "+idTypeDocument, {idPersonne: idLogger})
        logger.error(error)
        return false;
    }
}

module.exports = {
    annuaireDelete,
    alerteBenevoleLotDelete,
    alerteBenevoleVehiculeDelete,
    alerteBenevoleVHFDelete,
    catalogueDelete,
    categoriesDelete,
    cautionsDelete,
    centreCoutsDelete,
    centreCoutsDocDelete,
    centreCoutsGerantDelete,
    centreCoutsOperationsDelete,
    codesBarreDelete,
    commandeDocDelete,
    commandeItemDelete,
    commandesDelete,
    emplacementsDelete,
    etatsLotsDelete,
    etatsMaterielsDelete,
    etatsVehiculesDelete,
    fournisseursDelete,
    lieuxDelete,
    lotsConsommationsDelete,
    lotsDelete,
    lotsInventaireDelete,
    materielsDelete,
    messagesDelete,
    profilsDelete,
    referentielsDelete,
    reserveConteneurDelete,
    reserveInventaireDelete,
    reserveMaterielDelete,
    sacsDelete,
    tenuesAffectationsDelete,
    tenuesCatalogueDelete,
    todolistDelete,
    vehiculesCarburantsDelete,
    vehiculesPneumatiquesDelete,
    vehiculesDelete,
    vehiculesDesinfectionsDelete,
    vehiculesDocDelete,
    vehiculesHealthDelete,
    vehiculesMaintenanceDelete,
    vehiculesReleveDelete,
    vehiculesTypesDelete,
    vehiculesTypesDesinfectionsDelete,
    vehiculesTypesMaintenanceReguliereDelete,
    vehiculesTypesMaintenancesPonctuellesDelete,
    vehiculesDetailCGDelete,
    vehiculesStockDelete,
    vhfCanauxDelete,
    vhfCanauxDocDelete,
    vhfEquipementsAccessoiresDelete,
    vhfEquipementsDelete,
    vhfEquipementsDocDelete,
    vhfPlansCanauxDelete,
    vhfPlansDelete,
    vhfPlansDocDelete,
    vhfTypesAccessoiresDelete,
    vhfEtatsDelete,
    vhfTechnologiesDelete,
    vhfTypesEquipementsDelete,
    vhfStockDelete,
    typeDocumentsDelete,
};