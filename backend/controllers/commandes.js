const db = require('../db');
const logger = require('../winstonLogger');
const multer = require('multer');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsDelete = require('../helpers/fonctionsDelete');

exports.getCommandes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                c.*,
                f.nomFournisseur,
                e.libelleEtat
            FROM
                COMMANDES c
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat
            ORDER BY
                dateCreation DESC
        ;`);

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneCommande = async (req, res)=>{
    try {
        let commande = await db.query(`
            SELECT
                c.*,
                f.nomFournisseur,
                e.libelleEtat,
                couts.libelleCentreDecout,
                l.libelleLieu
            FROM
                COMMANDES c
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat
                LEFT OUTER JOIN CENTRE_COUTS couts ON couts.idCentreDeCout = c.idCentreDeCout
                LEFT OUTER JOIN LIEUX l ON l.idLieu = c.idLieuLivraison
            WHERE
                c.idCommande = :idCommande
        ;`,{
            idCommande: req.body.idCommande,
        });

        let demandeurs = await db.query(`
            SELECT
                p.idPersonne as value,
                p.identifiant as label
            FROM
                COMMANDES_DEMANDEURS c
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idDemandeur
            WHERE
                c.idCommande = :idCommande
        ;`,{
            idCommande: req.body.idCommande,
        });

        let observateurs = await db.query(`
            SELECT
                p.idPersonne as value,
                p.identifiant as label
            FROM
                COMMANDES_OBSERVATEURS c
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idObservateur
            WHERE
                c.idCommande = :idCommande
        ;`,{
            idCommande: req.body.idCommande,
        });

        let affectees = await db.query(`
            SELECT
                p.idPersonne as value,
                p.identifiant as label
            FROM
                COMMANDES_AFFECTEES c
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idAffectee
            WHERE
                c.idCommande = :idCommande
        ;`,{
            idCommande: req.body.idCommande,
        });

        let valideurs = await fonctionsMetiers.getValideurs(req.body.idCommande);

        let materiels = await db.query(`
            SELECT
                m.*,
                c.libelleMateriel
            FROM
                COMMANDES_MATERIEL m
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
            WHERE
                m.idCommande = :idCommande
        ;`,{
            idCommande: req.body.idCommande,
        });

        let timeLine = await db.query(`
            SELECT
                t.*
            FROM
                COMMANDES_TIMELINE t
            WHERE
                t.idCommande = :idCommande
            ORDER BY
                dateEvtCommande DESC
        ;`,{
            idCommande: req.body.idCommande,
        });

        let documents = await db.query(`
            SELECT
                *
            FROM
                VIEW_DOCUMENTS_COMMANDES
            WHERE
                idCommande = :idCommande
        ;`,{
            idCommande: req.body.idCommande
        });

        let verificationContraintes = await fonctionsMetiers.verificationContraintesCmd(req.body.idCommande);

        res.send({
            detailsCommande: commande[0],
            verificationContraintes: verificationContraintes,
            demandeurs: demandeurs,
            observateurs: observateurs,
            affectees: affectees,
            valideurs: valideurs,
            materiels: materiels,
            timeLine: timeLine,
            documents: documents,
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                COMMANDES
            SET
                nomCommande = :nomCommande,
                idEtat = 1,
                montantTotal = 0,
                dateCreation = CURRENT_TIMESTAMP,
                integreCentreCouts = 0
        `,{
            nomCommande: req.body.nomCommande || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idCommande) as idCommande FROM COMMANDES;'
        );

        const addFirstDemandeur = await db.query(`
            INSERT INTO
                COMMANDES_DEMANDEURS
            SET
                idCommande = :idCommande,
                idDemandeur = :idDemandeur
        `,{
            idCommande: selectLast[0].idCommande,
            idDemandeur: req.verifyJWTandProfile.idPersonne,
        });

        res.status(201);
        res.json({idCommande: selectLast[0].idCommande});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.abandonnerCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                idEtat = 8
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.commandesDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.commandesDelete(req.verifyJWTandProfile.idPersonne , req.body.idCommande);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateInfoGenerales = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                nomCommande = :nomCommande,
                idCentreDeCout = :idCentreDeCout,
                idFournisseur = :idFournisseur,
                idLieuLivraison = :idLieuLivraison,
                remarquesGenerales = :remarquesGenerales
            WHERE
                idCommande = :idCommande
        `,{
            nomCommande: req.body.nomCommande || null,
            idCentreDeCout: req.body.idCentreDeCout || null,
            idFournisseur: req.body.idFournisseur || null,
            idLieuLivraison: req.body.idLieuLivraison || null,
            remarquesGenerales: req.body.remarquesGenerales || null,
            idCommande: req.body.idCommande || null,
        });

        let cleanQuery = await db.query(`
            DELETE FROM
                COMMANDES_DEMANDEURS
            WHERE
                idCommande     = :idCommande
        `,{
            idCommande     : req.body.idCommande || null,
        });
        for(const entry of req.body.idDemandeur)
        {
            const insertQuery = await db.query(`
                INSERT INTO
                    COMMANDES_DEMANDEURS
                SET
                    idCommande     = :idCommande,
                    idDemandeur = :idDemandeur
            `,{
                idCommande     : req.body.idCommande || null,
                idDemandeur : entry.value || null,
            });
        }

        cleanQuery = await db.query(`
            DELETE FROM
                COMMANDES_OBSERVATEURS
            WHERE
                idCommande     = :idCommande
        `,{
            idCommande     : req.body.idCommande || null,
        });
        for(const entry of req.body.idObservateur)
        {
            const insertQuery = await db.query(`
                INSERT INTO
                    COMMANDES_OBSERVATEURS
                SET
                    idCommande     = :idCommande,
                    idObservateur = :idObservateur
            `,{
                idCommande     : req.body.idCommande || null,
                idObservateur : entry.value || null,
            });
        }

        cleanQuery = await db.query(`
            DELETE FROM
                COMMANDES_AFFECTEES
            WHERE
                idCommande     = :idCommande
        `,{
            idCommande     : req.body.idCommande || null,
        });
        for(const entry of req.body.idAffectee)
        {
            const insertQuery = await db.query(`
                INSERT INTO
                    COMMANDES_AFFECTEES
                SET
                    idCommande     = :idCommande,
                    idAffectee = :idAffectee
            `,{
                idCommande     : req.body.idCommande || null,
                idAffectee : entry.value || null,
            });
        }
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addMateriels = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                COMMANDES_MATERIEL
            SET
                idCommande = :idCommande,
                idMaterielCatalogue = :idMaterielCatalogue,
                quantiteCommande = :quantiteCommande,
                referenceProduitFournisseur = :referenceProduitFournisseur,
                remiseProduit = :remiseProduit,
                prixProduitHT = :prixProduitHT,
                taxeProduit = :taxeProduit,
                prixProduitTTC = :prixProduitTTC,
                remarqueArticle = :remarqueArticle
        `,{
            idCommande: req.body.idCommande || null,
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            quantiteCommande: req.body.quantiteCommande || null,
            referenceProduitFournisseur: req.body.referenceProduitFournisseur || null,
            remiseProduit: req.body.remiseProduit || null,
            prixProduitHT: req.body.prixProduitHT || null,
            taxeProduit: req.body.taxeProduit || null,
            prixProduitTTC: req.body.prixProduitTTC || null,
            remarqueArticle: req.body.remarqueArticle || null,
        });

        await fonctionsMetiers.calculerTotalCommande(req.body.idCommande);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMateriels = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES_MATERIEL
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                quantiteCommande = :quantiteCommande,
                referenceProduitFournisseur = :referenceProduitFournisseur,
                remiseProduit = :remiseProduit,
                prixProduitHT = :prixProduitHT,
                taxeProduit = :taxeProduit,
                prixProduitTTC = :prixProduitTTC,
                remarqueArticle = :remarqueArticle
            WHERE
                idCommandeMateriel = :idCommandeMateriel
        `,{
            idCommandeMateriel: req.body.idCommandeMateriel || null,
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            quantiteCommande: req.body.quantiteCommande || null,
            referenceProduitFournisseur: req.body.referenceProduitFournisseur || null,
            remiseProduit: req.body.remiseProduit || null,
            prixProduitHT: req.body.prixProduitHT || null,
            taxeProduit: req.body.taxeProduit || null,
            prixProduitTTC: req.body.prixProduitTTC || null,
            remarqueArticle: req.body.remarqueArticle || null,
        });

        await fonctionsMetiers.calculerTotalCommande(req.body.idCommande);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.removeMateriels = async (req, res)=>{
    try {
        const result = await db.query(`
            DELETE FROM
                COMMANDES_MATERIEL
            WHERE
                idCommandeMateriel = :idCommandeMateriel
        `,{
            idCommandeMateriel: req.body.idCommandeMateriel || null,
        });

        await fonctionsMetiers.calculerTotalCommande(req.body.idCommande);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.demandeValidation = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                idEtat = 2,
                dateDemandeValidation = CURRENT_TIMESTAMP
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateRemarquesValidation = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                remarquesValidation = :remarquesValidation
            WHERE
                idCommande = :idCommande
        `,{
            remarquesValidation: req.body.remarquesValidation || null,
            idCommande: req.body.idCommande || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.rejeterCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                idEtat = 1
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.approuverCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                idEtat = 3,
                dateValidation = CURRENT_TIMESTAMP
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updatePassageCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                numCommandeFournisseur = :numCommandeFournisseur,
                datePassage = :datePassage,
                dateLivraisonPrevue = :dateLivraisonPrevue
            WHERE
                idCommande = :idCommande
        `,{
            numCommandeFournisseur: req.body.numCommandeFournisseur ||null,
            datePassage: req.body.datePassage ||null,
            dateLivraisonPrevue: req.body.dateLivraisonPrevue ||null,
            idCommande: req.body.idCommande || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.passerCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                idEtat = 4
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Commandes PJ
const multerConfigCommandes = multer.diskStorage({
    destination: (req, file, callback) => {
        callback(null, 'uploads/commandes');
    },
    filename: (req, file, callback) => {
        const ext = file.mimetype.split('/')[1];
        callback(null, `commandes-${Date.now()}.${ext}`);
    }
});

const uploadCommandes = multer({
    storage: multerConfigCommandes,
});

exports.uploadCommandesAttachedMulter = uploadCommandes.single('file');

exports.uploadCommandesAttached = async (req, res, next)=>{
    try {
        const newFileToDB = await db.query(
            `INSERT INTO
                DOCUMENTS_COMMANDES
            SET
                urlFichierDocCommande = :filename,
                idCommande                 = :idCommande
        `,{
            filename : req.file.filename,
            idCommande : req.query.idCommande,
        });

        const lastSelect = await db.query(`SELECT MAX(idDocCommande) as idDocCommande FROM DOCUMENTS_COMMANDES`);

        res.status(200);
        res.json({idDocCommande: lastSelect[0].idDocCommande})
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMetaDataCommandes = async (req, res, next)=>{
    try {
        const document = await db.query(
            `SELECT
                *
            FROM
                DOCUMENTS_COMMANDES
            WHERE
                idDocCommande = :idDocCommande
        `,{
            idDocCommande : req.body.idDocCommande,
        });

        const update = await db.query(
            `UPDATE
                DOCUMENTS_COMMANDES
            SET
                nomDocCommande   = :nomDocCommande,
                formatDocCommande = :formatDocCommande,
                dateDocCommande   = :dateDocCommande,
                idTypeDocument = :idTypeDocument
            WHERE
                idDocCommande        = :idDocCommande
        `,{
            nomDocCommande    : req.body.nomDocCommande || null,
            formatDocCommande : document[0].urlFichierDocCommande.split('.')[1],
            dateDocCommande   : req.body.dateDocCommande || new Date(),
            idDocCommande     : req.body.idDocCommande,
            idTypeDocument    : req.body.idTypeDocument || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.dropCommandesDocument = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.commandeDocDelete(req.verifyJWTandProfile.idPersonne , req.body.idDocCommande);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}