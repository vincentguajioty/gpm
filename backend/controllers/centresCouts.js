const db = require('../db');
const logger = require('../winstonLogger');
const multer = require('multer');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsDelete = require('../helpers/fonctionsDelete');

exports.getCentres = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                cc.*,
                COUNT(co.idCommande) as commandesAintegrer
            FROM
                CENTRE_COUTS cc
                LEFT OUTER JOIN (SELECT * FROM COMMANDES WHERE integreCentreCouts = false AND idEtat > 3 AND idEtat < 8) co ON cc.idCentreDeCout = co.idCentreDeCout
            GROUP BY
                cc.idCentreDeCout
            ORDER BY
                libelleCentreDecout
        ;`);

        for(const centre of results)
        {
            let statutOuverture = '1 - Ouvert';
            if(centre.dateOuverture && centre.dateOuverture != null && new Date(centre.dateOuverture) > new Date())
            {
                statutOuverture = '2 - Futur';
            }else if(centre.dateFermeture && centre.dateFermeture != null && new Date(centre.dateFermeture) < new Date())
            {
                statutOuverture = '3 - Clos';
            }
            centre.statutOuverture = statutOuverture;

            let gestionnaires = await db.query(`
                SELECT
                    cp.*,
                    p.nomPersonne,
                    p.prenomPersonne,
                    p.identifiant
                FROM
                    CENTRE_COUTS_PERSONNES cp
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON cp.idPersonne = p.idPersonne
                WHERE
                    idCentreDeCout = :idCentreDeCout
            ;`,{
                idCentreDeCout: centre.idCentreDeCout
            });
            centre.gestionnaires = gestionnaires;
            for(const gestionnaire of gestionnaires)
            {
                gestionnaire.actif = fonctionsMetiers.checkGestionnaireStatut(centre, gestionnaire);
            }
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneCentre = async (req, res)=>{
    try {
        let centreDetails = await db.query(`
            SELECT
                *
            FROM
                CENTRE_COUTS
            WHERE
                idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout || null,
        });
        centreDetails = centreDetails[0];

        let statutOuverture = '1 - Ouvert';
        if(centreDetails.dateOuverture && centreDetails.dateOuverture != null && new Date(centreDetails.dateOuverture) > new Date())
        {
            statutOuverture = '2 - Futur';
        }else if(centreDetails.dateFermeture && centreDetails.dateFermeture != null && new Date(centreDetails.dateFermeture) < new Date())
        {
            statutOuverture = '3 - Clos';
        }
        centreDetails.statutOuverture = statutOuverture;

        let gestionnaires = await db.query(`
            SELECT
                cp.*,
                p.nomPersonne,
                p.prenomPersonne,
                p.identifiant
            FROM
                CENTRE_COUTS_PERSONNES cp
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON cp.idPersonne = p.idPersonne
            WHERE
                idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });
        for(const gestionnaire of gestionnaires)
        {
            gestionnaire.actif = fonctionsMetiers.checkGestionnaireStatut(centreDetails, gestionnaire);
        }

        let operations = await db.query(`
            SELECT
                o.*,
                p.nomPersonne,
                p.prenomPersonne,
                p.identifiant
            FROM
                CENTRE_COUTS_OPERATIONS o
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON o.idPersonne = p.idPersonne
            WHERE
                idCentreDeCout = :idCentreDeCout
            ORDER BY
                dateOperation DESC
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });

        let commandesAIntegrer = await db.query(`
            SELECT
                *
            FROM
                COMMANDES
            WHERE
                idCentreDeCout = :idCentreDeCout
                AND integreCentreCouts = false
                AND idEtat > 3
                AND idEtat < 8
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });

        let commandesRefusees = await db.query(`
            SELECT
                *
            FROM
                COMMANDES c
                LEFT OUTER JOIN CENTRE_COUTS_OPERATIONS op ON c.idCommande = op.idCommande
            WHERE
                c.idCentreDeCout = :idCentreDeCout
                AND c.integreCentreCouts = true
                AND op.idOperations IS NULL
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });

        let commandesEnApproche = await db.query(`
            SELECT
                *
            FROM
                COMMANDES
            WHERE
                idCentreDeCout = :idCentreDeCout
                AND idEtat <= 3
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });

        let documents = await db.query(`
            SELECT
                *
            FROM
                VIEW_DOCUMENTS_CENTRE_COUTS_COMMANDES
            WHERE
                idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });

        res.send({
            centreDetails: centreDetails,
            gestionnaires: gestionnaires,
            operations: operations,
            commandesAIntegrer: commandesAIntegrer,
            commandesRefusees: commandesRefusees,
            commandesEnApproche: commandesEnApproche,
            documents: documents,
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addCentre = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                CENTRE_COUTS
            SET
                libelleCentreDecout = :libelleCentreDecout,
                commentairesCentreCout = :commentairesCentreCout,
                dateOuverture = :dateOuverture,
                dateFermeture = :dateFermeture,
                soldeActuel = 0
        `,{
            libelleCentreDecout: req.body.libelleCentreDecout || null,
            commentairesCentreCout: req.body.commentairesCentreCout || null,
            dateOuverture: req.body.dateOuverture || null,
            dateFermeture: req.body.dateFermeture || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idCentreDeCout) as idCentreDeCout FROM CENTRE_COUTS;'
        );

        res.status(201);
        res.json({idCentreDeCout: selectLast[0].idCentreDeCout});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateCentre = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                CENTRE_COUTS
            SET
                libelleCentreDecout = :libelleCentreDecout,
                commentairesCentreCout = :commentairesCentreCout,
                dateOuverture = :dateOuverture,
                dateFermeture = :dateFermeture
            WHERE
                idCentreDeCout = :idCentreDeCout
        `,{
            libelleCentreDecout: req.body.libelleCentreDecout || null,
            commentairesCentreCout: req.body.commentairesCentreCout || null,
            dateOuverture: req.body.dateOuverture || null,
            dateFermeture: req.body.dateFermeture || null,
            idCentreDeCout: req.body.idCentreDeCout || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addOperation = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                CENTRE_COUTS_OPERATIONS
            SET
                idCentreDeCout = :idCentreDeCout,
                dateOperation = :dateOperation,
                libelleOperation = :libelleOperation,
                montantEntrant = :montantEntrant,
                montantSortant = :montantSortant,
                detailsMoyenTransaction = :detailsMoyenTransaction,
                idPersonne = :idPersonne
        `,{
            idCentreDeCout: req.body.idCentreDeCout || null,
            dateOperation: req.body.dateOperation || null,
            libelleOperation: req.body.libelleOperation || null,
            montantEntrant: req.body.montantEntrant || null,
            montantSortant: req.body.montantSortant || null,
            detailsMoyenTransaction: req.body.detailsMoyenTransaction || null,
            idPersonne: req.body.idPersonne || null,
        });
        
        await fonctionsMetiers.calculerTotalCentreDeCouts(req.body.idCentreDeCout);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateOperation = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                CENTRE_COUTS_OPERATIONS
            SET
                dateOperation = :dateOperation,
                libelleOperation = :libelleOperation,
                montantEntrant = :montantEntrant,
                montantSortant = :montantSortant,
                detailsMoyenTransaction = :detailsMoyenTransaction,
                idPersonne = :idPersonne
            WHERE
                idOperations = :idOperations
        `,{
            idOperations: req.body.idOperations || null,
            dateOperation: req.body.dateOperation || null,
            libelleOperation: req.body.libelleOperation || null,
            montantEntrant: req.body.montantEntrant || null,
            montantSortant: req.body.montantSortant || null,
            detailsMoyenTransaction: req.body.detailsMoyenTransaction || null,
            idPersonne: req.body.idPersonne || null,
        });
        
        await fonctionsMetiers.calculerTotalCentreDeCouts(req.body.idCentreDeCout);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.centreCoutsOperationsDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.centreCoutsOperationsDelete(req.verifyJWTandProfile.idPersonne , req.body.idOperations);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addGerant = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                CENTRE_COUTS_PERSONNES
            SET
                idCentreDeCout = :idCentreDeCout,
                idPersonne = :idPersonne,
                montantMaxValidation = :montantMaxValidation,
                depasseBudget = :depasseBudget,
                validerClos = :validerClos
        `,{
            idCentreDeCout: req.body.idCentreDeCout || null,
            idPersonne: req.body.idPersonne || null,
            montantMaxValidation: req.body.montantMaxValidation || null,
            depasseBudget: req.body.depasseBudget || false,
            validerClos: req.body.validerClos || false,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateGerant = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                CENTRE_COUTS_PERSONNES
            SET
                idPersonne = :idPersonne,
                montantMaxValidation = :montantMaxValidation,
                depasseBudget = :depasseBudget,
                validerClos = :validerClos
            WHERE
                idGerant = :idGerant
        `,{
            idGerant: req.body.idGerant || null,
            idPersonne: req.body.idPersonne || null,
            montantMaxValidation: req.body.montantMaxValidation || null,
            depasseBudget: req.body.depasseBudget || false,
            validerClos: req.body.validerClos || false,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.centreCoutsGerantDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.centreCoutsGerantDelete(req.verifyJWTandProfile.idPersonne , req.body.idGerant);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}