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
                e.libelleEtat,
                cdc.libelleCentreDecout
            FROM
                COMMANDES c
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat
                LEFT OUTER JOIN CENTRE_COUTS cdc ON c.idCentreDeCout = cdc.idCentreDeCout
            ORDER BY
                dateCreation DESC
        ;`);

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCommandesToApprouveOnePerson = async (req, res)=>{
    try {
        let allCmdPending = await db.query(`
            SELECT
                c.*,
                cdc.libelleCentreDecout
            FROM
                COMMANDES c
                LEFT OUTER JOIN CENTRE_COUTS cdc ON c.idCentreDeCout = cdc.idCentreDeCout
            WHERE
                c.idEtat = 2
            ORDER BY
                dateDemandeValidation DESC
        ;`);

        let results = [];
        for(const cmd of allCmdPending)
        {
            let valideursCmd = await fonctionsMetiers.getValideurs(cmd.idCommande);
            for(const valideur of valideursCmd)
            {
                if(valideur.idPersonne == req.verifyJWTandProfile.idPersonne){
                    let verificationContraintes = await fonctionsMetiers.verificationContraintesCmd(cmd.idCommande);
                    cmd.verificationContraintes = verificationContraintes;
                    results.push(cmd)
                }
            }
        }

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
            ORDER BY
                p.identifiant
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
            ORDER BY
                p.identifiant
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
            ORDER BY
                p.identifiant
        ;`,{
            idCommande: req.body.idCommande,
        });

        let valideurs = await fonctionsMetiers.getValideurs(req.body.idCommande);

        let materiels = await db.query(`
            SELECT
                m.*,
                CONCAT_WS(' > ',c.libelleMateriel,c.taille) as libelleMateriel,
                c.idFournisseur as idFournisseurReference,
                c.modules_ope,
                c.modules_vehicules,
                c.modules_tenues,
                c.modules_vhf,
                f.nomFournisseur as nomFournisseurReference
            FROM
                COMMANDES_MATERIEL m
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
            WHERE
                m.idCommande = :idCommande
            ORDER BY
                c.libelleMateriel
        ;`,{
            idCommande: req.body.idCommande,
        });

        let timeLine = await db.query(`
            SELECT
                t.idEvtCommande as id,
                null as title,
                detailsEvtCommande as text,
                i.iconFontAsw as icon,
                i.iconColor as iconColor,
                t.dateEvtCommande as time,
                'completed' as status,
                null as link
            FROM
                COMMANDES_TIMELINE t
                LEFT OUTER JOIN COMMANDES_TIMELINE_ICON i ON t.idComIcon = i.idComIcon
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
            ORDER BY
                nomDocCommande
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

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(selectLast[0].idCommande, req.verifyJWTandProfile.identifiant+" crée la commande.", 1);

        res.status(201);
        res.json({idCommande: selectLast[0].idCommande});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addComment = async (req, res)=>{
    try {
        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" ajoute le commentaire: "+req.body.comment, 36);
        res.sendStatus(201);
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

        await fonctionsMetiers.envoyerNotifAuChangementStatutCommande(req.body.idCommande, 8);

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" abandonne la commande.", 7);
        
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

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" modifie la commande.", 12);
        
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

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" ajoute un article à la commande.", 12);

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

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" modifie un article de la commande.", 12);

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

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" retire un article de la commande.", 12);

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

        await fonctionsMetiers.envoyerNotifAuChangementStatutCommande(req.body.idCommande, 2);

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" envoie la demande de validation.", 14);

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

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" édite les remarques de validation: "+req.body.remarquesValidation, 12);

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

        await fonctionsMetiers.envoyerNotifAuChangementStatutCommande(req.body.idCommande, 1);

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" refuse la commande.", 19);

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

        await fonctionsMetiers.envoyerNotifAuChangementStatutCommande(req.body.idCommande, 3);

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" valide la commande.", 13);

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

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" modifie les informations de commande données par le fournisseur.", 12);

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

        const updateMaterielCommande = await db.query(`
            UPDATE
                COMMANDES_MATERIEL
            SET
                quantiteAtransferer = quantiteCommande
            WHERE
                idCommande = :idCommande
                AND
                idMaterielCatalogue IS NOT NULL
        `,{
            idCommande: req.body.idCommande || null,
        });

        await fonctionsMetiers.envoyerNotifAuChangementStatutCommande(req.body.idCommande, 4);

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" a lancer la commande chez le fournisseur.", 21);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateInfosLivraison = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                dateLivraisoneffective = :dateLivraisoneffective,
                remarquesLivraison = :remarquesLivraison
            WHERE
                idCommande = :idCommande
        `,{
            dateLivraisoneffective: req.body.dateLivraisoneffective || null,
            remarquesLivraison: req.body.remarquesLivraison || null,
            idCommande: req.body.idCommande || null,
        });

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" modifie les informations de livraison.", 12);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.livraisonOKCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                idEtat = 5
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });

        await fonctionsMetiers.envoyerNotifAuChangementStatutCommande(req.body.idCommande, 5);

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" réceptionne la commande sans déclencher de SAV, ou cloture le SAV le cas échéant.", 25);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.livraisonSAVCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                idEtat = 6,
                savHistorique = true
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });

        await fonctionsMetiers.envoyerNotifAuChangementStatutCommande(req.body.idCommande, 6);

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" réceptionne la commande et engage un SAV.", 30);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.transfertManuel = async (req, res)=>{
    try {
        const updateMaterielCommande = await db.query(`
            UPDATE
                COMMANDES_MATERIEL
            SET
                quantiteAtransferer = 0
            WHERE
                idCommandeMateriel = :idCommandeMateriel
        `,{
            idCommandeMateriel: req.body.idCommandeMateriel || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.cloreCommande = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                COMMANDES
            SET
                idEtat = 7,
                dateCloture = CURRENT_TIMESTAMP
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });

        await fonctionsMetiers.envoyerNotifAuChangementStatutCommande(req.body.idCommande, 7);

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.body.idCommande, req.verifyJWTandProfile.identifiant+" clôture la commande.", 16);

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

        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(req.query.idCommande, req.verifyJWTandProfile.identifiant+" ajoute une pièce jointe.", 9);

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
        const getCommandeID = await db.query(`
            SELECT
                idCommande
            FROM
                DOCUMENTS_COMMANDES
            WHERE
                idDocCommande = :idDocCommande
        `,{
            idDocCommande: req.body.idDocCommande || null,
        });
        await fonctionsMetiers.ajouterCommentaireTimeLineCommande(getCommandeID[0].idCommande, req.verifyJWTandProfile.identifiant+" supprime une pièce jointe.", 11);

        const deleteResult = await fonctionsDelete.commandeDocDelete(req.verifyJWTandProfile.idPersonne , req.body.idDocCommande);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Transferts
exports.getReservesOpeForOneIntegration = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                rm.*,
                rm.idReserveElement as value,
                CONCAT_WS(' > ',c.libelleConteneur,cat.libelleMateriel) as label,
                c.libelleConteneur,
                c.inventaireEnCours,
                cat.libelleMateriel
            FROM
                RESERVES_MATERIEL rm
                LEFT OUTER JOIN RESERVES_CONTENEUR c ON rm.idConteneur = c.idConteneur
                LEFT OUTER JOIN MATERIEL_CATALOGUE cat ON rm.idMaterielCatalogue = cat.idMaterielCatalogue
            WHERE
                rm.idMaterielCatalogue = :idMaterielCatalogue
            ORDER BY
                c.libelleConteneur,
                cat.libelleMateriel
        ;`,{
            idMaterielCatalogue: req.body.idMaterielCatalogue
        });

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.enregistrerTransfertOPE = async (req, res)=>{
    try {
        const downgradeCmd = await db.query(`
            UPDATE
                COMMANDES_MATERIEL
            SET
                quantiteAtransferer = quantiteAtransferer - :qttTransfert
            WHERE
                idCommandeMateriel = :idCommandeMateriel
        `,{
            idCommandeMateriel: req.body.idCommandeMateriel || null,
            qttTransfert: req.body.qttTransfert || 0,
        });

        const upgradeReserve = await db.query(`
            UPDATE
                RESERVES_MATERIEL
            SET
                quantiteReserve = quantiteReserve + :qttTransfert
            WHERE
                idReserveElement = :idReserveElement
        `,{
            idReserveElement: req.body.idReserveElement || null,
            qttTransfert: req.body.qttTransfert || 0,
        });

        if(req.body.peremptionCmd && req.body.peremptionCmd != null)
        {
            let currentReserve = await db.query(`
                SELECT
                    *
                FROM
                    RESERVES_MATERIEL
                WHERE
                    idReserveElement = :idReserveElement
            `,{
                idReserveElement: req.body.idReserveElement || null,
            });
            currentReserve = currentReserve[0];

            if(currentReserve.peremptionReserve == null || new Date(currentReserve.peremptionReserve) > new Date(req.body.peremptionCmd))
            {
                const upgradeReserve = await db.query(`
                    UPDATE
                        RESERVES_MATERIEL
                    SET
                        peremptionReserve = :peremptionReserve
                    WHERE
                        idReserveElement = :idReserveElement
                `,{
                    idReserveElement: req.body.idReserveElement || null,
                    peremptionReserve: req.body.peremptionCmd || null,
                });
            }
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getReservesTenForOneIntegration = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                rm.*,
                rm.idCatalogueTenue as value,
                CONCAT_WS(' > ',cat.libelleMateriel,cat.taille) as label,
                cat.libelleMateriel
            FROM
                TENUES_CATALOGUE rm
                LEFT OUTER JOIN MATERIEL_CATALOGUE cat ON rm.idMaterielCatalogue = cat.idMaterielCatalogue
            WHERE
                rm.idMaterielCatalogue = :idMaterielCatalogue
            ORDER BY
                cat.libelleMateriel
        ;`,{
            idMaterielCatalogue: req.body.idMaterielCatalogue
        });

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.enregistrerTransfertTEN = async (req, res)=>{
    try {
        const downgradeCmd = await db.query(`
            UPDATE
                COMMANDES_MATERIEL
            SET
                quantiteAtransferer = quantiteAtransferer - :qttTransfert
            WHERE
                idCommandeMateriel = :idCommandeMateriel
        `,{
            idCommandeMateriel: req.body.idCommandeMateriel || null,
            qttTransfert: req.body.qttTransfert || 0,
        });

        const upgradeReserve = await db.query(`
            UPDATE
                TENUES_CATALOGUE
            SET
                stockCatalogueTenue = stockCatalogueTenue + :qttTransfert
            WHERE
                idCatalogueTenue = :idCatalogueTenue
        `,{
            idCatalogueTenue: req.body.idCatalogueTenue || null,
            qttTransfert: req.body.qttTransfert || 0,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}