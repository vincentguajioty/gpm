const db = require('../db');
const logger = require('../winstonLogger');
const multer = require('multer');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const excelJS = require("exceljs");

//CENTRE
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
            ORDER BY
                p.identifiant
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
                c.*,
                f.nomFournisseur,
                e.libelleEtat
            FROM
                COMMANDES c
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat
            WHERE
                c.idCentreDeCout = :idCentreDeCout
                AND c.integreCentreCouts = false
                AND c.idEtat > 3
                AND c.idEtat < 8
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });
        for(const cmd of commandesAIntegrer)
        {
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
                idCommande: cmd.idCommande,
            });
            cmd.affectees = affectees;
        }

        let commandesRefusees = await db.query(`
            SELECT
                c.*,
                f.nomFournisseur,
                e.libelleEtat
            FROM
                COMMANDES c
                LEFT OUTER JOIN CENTRE_COUTS_OPERATIONS op ON c.idCommande = op.idCommande
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat
            WHERE
                c.idCentreDeCout = :idCentreDeCout
                AND c.integreCentreCouts = true
                AND op.idOperations IS NULL
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });
        for(const cmd of commandesRefusees)
        {
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
                idCommande: cmd.idCommande,
            });
            cmd.affectees = affectees;
        }
        
        let commandesValideesNonIntegrees = await db.query(`
            SELECT
                *
            FROM
                COMMANDES
            WHERE
                idCentreDeCout = :idCentreDeCout
                AND integreCentreCouts = false
                AND idEtat > 2
                AND idEtat < 8
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });

        let commandesNonValidees = await db.query(`
            SELECT
                *
            FROM
                COMMANDES
            WHERE
                idCentreDeCout = :idCentreDeCout
                AND idEtat <= 2
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
            ORDER BY
                nomDocCouts
        ;`,{
            idCentreDeCout: req.body.idCentreDeCout
        });

        res.send({
            centreDetails: centreDetails,
            gestionnaires: gestionnaires,
            operations: operations,
            commandesAIntegrer: commandesAIntegrer,
            commandesRefusees: commandesRefusees,
            commandesValideesNonIntegrees: commandesValideesNonIntegrees,
            commandesNonValidees: commandesNonValidees,
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

exports.centreCoutsDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.centreCoutsDelete(req.verifyJWTandProfile.idPersonne , req.body.idCentreDeCout);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//OPERATIONS
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

//GESTIONNAIRES
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

//COMMANDES
exports.integrerCommande = async (req, res)=>{
    try {
        let commande = await db.query(`
            SELECT
                c.*,
                f.nomFournisseur
            FROM
                COMMANDES c
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });
        commande = commande[0]

        const result = await db.query(`
            INSERT INTO
                CENTRE_COUTS_OPERATIONS
            SET
                idCentreDeCout = :idCentreDeCout,
                dateOperation = CURRENT_TIMESTAMP,
                libelleOperation = :libelleOperation,
                montantSortant = :montantSortant,
                detailsMoyenTransaction = :detailsMoyenTransaction,
                idPersonne = :idPersonne,
                idCommande = :idCommande
        `,{
            idCentreDeCout: req.body.idCentreDeCout || null,
            libelleOperation: 'Commande '+req.body.idCommande+' ('+commande.nomFournisseur+')',
            montantSortant: commande.montantTotal || null,
            detailsMoyenTransaction: commande.nomCommande || null,
            idPersonne: req.verifyJWTandProfile.idPersonne,
            idCommande: req.body.idCommande || null,
        });

        const updateCMD = await db.query(`
            UPDATE
                COMMANDES
            SET
                integreCentreCouts = 1
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });
        
        await fonctionsMetiers.calculerTotalCentreDeCouts(req.body.idCentreDeCout);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.refuserCommande = async (req, res)=>{
    try {
        const updateCMD = await db.query(`
            UPDATE
                COMMANDES
            SET
                integreCentreCouts = 1
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });
        
        await fonctionsMetiers.calculerTotalCentreDeCouts(req.body.idCentreDeCout);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.recyclerCommande = async (req, res)=>{
    try {
        const updateCMD = await db.query(`
            UPDATE
                COMMANDES
            SET
                integreCentreCouts = 0
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: req.body.idCommande || null,
        });
        
        await fonctionsMetiers.calculerTotalCentreDeCouts(req.body.idCentreDeCout);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//PJ
const multerConfigCDC = multer.diskStorage({
    destination: (req, file, callback) => {
        callback(null, 'uploads/centresCouts');
    },
    filename: (req, file, callback) => {
        const ext = file.mimetype.split('/')[1];
        callback(null, `cdc-${Date.now()}.${ext}`);
    }
});

const uploadCDC = multer({
    storage: multerConfigCDC,
});

exports.uploadCDCAttachedMulter = uploadCDC.single('file');

exports.uploadCentreCoutsAttached = async (req, res, next)=>{
    try {
        const newFileToDB = await db.query(
            `INSERT INTO
                DOCUMENTS_CENTRE_COUTS
            SET
                urlFichierDocCouts = :filename,
                idCentreDeCout     = :idCentreDeCout
        `,{
            filename : req.file.filename,
            idCentreDeCout : req.query.idCentreDeCout,
        });

        const lastSelect = await db.query(`SELECT MAX(idDocCouts) as idDocCouts FROM DOCUMENTS_CENTRE_COUTS`);

        res.status(200);
        res.json({idDocCouts: lastSelect[0].idDocCouts})
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMetaDataCentreCouts = async (req, res, next)=>{
    try {
        const document = await db.query(
            `SELECT
                *
            FROM
                DOCUMENTS_CENTRE_COUTS
            WHERE
                idDocCouts = :idDocCouts
        `,{
            idDocCouts : req.body.idDocCouts,
        });

        const update = await db.query(
            `UPDATE
                DOCUMENTS_CENTRE_COUTS
            SET
                nomDocCouts   = :nomDocCouts,
                formatDocCouts = :formatDocCouts,
                dateDocCouts   = :dateDocCouts,
                idTypeDocument = :idTypeDocument
            WHERE
                idDocCouts        = :idDocCouts
        `,{
            nomDocCouts    : req.body.nomDocCouts || null,
            formatDocCouts : document[0].urlFichierDocCouts.split('.')[1],
            dateDocCouts   : req.body.dateDocCouts || new Date(),
            idDocCouts     : req.body.idDocCouts,
            idTypeDocument    : req.body.idTypeDocument || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.dropCentreCoutsDocument = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.centreCoutsDocDelete(req.verifyJWTandProfile.idPersonne , req.body.idDocCouts);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Export
exports.genererExport = async (req, res)=>{
    try {
        const operations = await db.query(`
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

        const workbook = new excelJS.Workbook();
        const worksheet = workbook.addWorksheet('Export CDC');

        worksheet.columns = [
            { header: "Référence opération",   key: "idOperations",            width: 10 },
            { header: "Date",                  key: "dateOperation",           width: 20 }, 
            { header: "Libellé",               key: "libelleOperation",        width: 60 },
            { header: "Montant Entrant",       key: "montantEntrant",          width: 15 },
            { header: "Montant Sortant",       key: "montantSortant",          width: 15 },
            { header: "Détails",               key: "detailsMoyenTransaction", width: 60 },
            { header: "Personnes responsable", key: "identifiant",             width: 20 },
            { header: "Référence commande",    key: "idCommande",              width: 20 },
        ];

        for(const ope of operations)
        {
            worksheet.addRow(ope);
        }

        worksheet.getRow(1).eachCell((cell) => {
            cell.font = { bold: true };
        });

        let fileName = Date.now() + '-ExportCDC.xlsx';

        const saveFile = await workbook.xlsx.writeFile('temp/'+fileName);

        res.send({fileName: fileName});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}