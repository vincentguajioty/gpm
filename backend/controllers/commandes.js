const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

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

        res.send({
            detailsCommande: commande[0],
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