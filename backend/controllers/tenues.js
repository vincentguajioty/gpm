const db = require('../db');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMail = require('../helpers/fonctionsMail');
const logger = require('../winstonLogger');
const excelJS = require("exceljs");

const retraitItemStock = async (idMaterielCatalogue) => {
    try {
        const result = await db.query(`
            UPDATE
                TENUES_CATALOGUE
            SET
                stockCatalogueTenue = stockCatalogueTenue - 1
            WHERE
                idMaterielCatalogue = :idMaterielCatalogue
        `,{
            idMaterielCatalogue: idMaterielCatalogue,
        });
    } catch (error) {
        logger.error(error)
    }
}

const ajoutItemStock = async (idMaterielCatalogue) => {
    try {
        const result = await db.query(`
            UPDATE
                TENUES_CATALOGUE
            SET
                stockCatalogueTenue = stockCatalogueTenue + 1
            WHERE
                idMaterielCatalogue = :idMaterielCatalogue
        `,{
            idMaterielCatalogue: idMaterielCatalogue,
        });
    } catch (error) {
        logger.error(error)
    }
}

// CATALOGUE
exports.getCatalogue = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                tc.*,
                cat.*,
                f.nomFournisseur
            FROM
                TENUES_CATALOGUE tc
                LEFT OUTER JOIN MATERIEL_CATALOGUE cat ON tc.idMaterielCatalogue = cat.idMaterielCatalogue
                LEFT OUTER JOIN FOURNISSEURS f ON tc.idFournisseur = f.idFournisseur
            ORDER BY
                cat.libelleMateriel,
                cat.taille
        ;`);
        
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addCatalogue = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                TENUES_CATALOGUE
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idFournisseur = :idFournisseur,
                stockCatalogueTenue = :stockCatalogueTenue,
                stockAlerteCatalogueTenue = :stockAlerteCatalogueTenue
        `,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idFournisseur: req.body.idFournisseur || null,
            stockCatalogueTenue: req.body.stockCatalogueTenue || 0,
            stockAlerteCatalogueTenue: req.body.stockAlerteCatalogueTenue || 0,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateCatalogue = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                TENUES_CATALOGUE
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idFournisseur = :idFournisseur,
                stockCatalogueTenue = :stockCatalogueTenue,
                stockAlerteCatalogueTenue = :stockAlerteCatalogueTenue
            WHERE
                idCatalogueTenue = :idCatalogueTenue
        `,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idFournisseur: req.body.idFournisseur || null,
            stockCatalogueTenue: req.body.stockCatalogueTenue || 0,
            stockAlerteCatalogueTenue: req.body.stockAlerteCatalogueTenue || 0,
            idCatalogueTenue: req.body.idCatalogueTenue,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteCatalogue = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.tenuesCatalogueDelete(req.verifyJWTandProfile.idPersonne , req.body.idCatalogueTenue);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.exporterCatalogue = async (req, res)=>{
    try {
        const elements = await db.query(`
            SELECT
                *
            FROM
                TENUES_CATALOGUE c
                LEFT OUTER JOIN MATERIEL_CATALOGUE cat ON c.idMaterielCatalogue = cat.idMaterielCatalogue
            ORDER BY
                libelleMateriel,
                taille
        ;`);

        const workbook = new excelJS.Workbook();
        
        const worksheetCatalogue = workbook.addWorksheet('Catalogue et stock');
        worksheetCatalogue.columns = [
            { header: "Numéro interne",   key: "idCatalogueTenue",          width: 15 },
            { header: "Element de tenue", key: "libelleMateriel",           width: 40 },
            { header: "Taille",           key: "taille",                    width: 20 },
            { header: "Stock",            key: "stockCatalogueTenue",       width: 15 },
            { header: "Stock d'alerte",   key: "stockAlerteCatalogueTenue", width: 15 },
        ];

        for(const element of elements)
        {
            worksheetCatalogue.addRow(element);
        }

        worksheetCatalogue.getRow(1).eachCell((cell) => {
            cell.font = { bold: true };
        });

        let fileName = Date.now() + '-ExportCatalogueTenues.xlsx';

        const saveFile = await workbook.xlsx.writeFile('temp/'+fileName);

        res.send({fileName: fileName});

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

// AFFECTATIONS
const creerExterne = async (nomPrenomExterne, mailExterne) => {
    try {
        const result = await db.query(`
            INSERT INTO
                PERSONNE_EXTERNE
            SET
                nomPrenomExterne = :nomPrenomExterne,
                mailExterne = :mailExterne
        `,{
            nomPrenomExterne: nomPrenomExterne || null,
            mailExterne: mailExterne || null,
        });

        let selectLast = await db.query(
            'SELECT MAX(idExterne) as idExterne FROM PERSONNE_EXTERNE;'
        );

        return selectLast[0].idExterne;
        
    } catch (error) {
        logger.error(error);
        return null;
    }
}

exports.getAffectations = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT DISTINCT
                pe.*
            FROM
                PERSONNE_EXTERNE pe
                LEFT OUTER JOIN VIEW_TENUES_AFFECTATION v ON pe.idExterne = v.idExterne
            WHERE
                v.idTenue IS NOT NULL
            ORDER BY
                nomPrenomExterne
        ;`);

        for(const personne of results)
        {
            let affectations = await db.query(`
                SELECT
                    *
                FROM
                    VIEW_TENUES_AFFECTATION
                WHERE
                    idExterne = :idExterne
                ORDER BY
                    libelleMateriel,
                    taille
            ;`,{
                idExterne: personne.idExterne,
            });
            personne.affectations = affectations;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getAffectationsRow = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                *
            FROM
                VIEW_TENUES_AFFECTATION
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getPersonnesSuggested = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                *,
                idExterne as value,
                CONCAT_WS(' / ', nomPrenomExterne, mailExterne) as label
            FROM    
                PERSONNE_EXTERNE
            ORDER BY
                nomPrenomExterne,
                mailExterne
        ;`);

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addAffectations = async (req, res)=>{
    try {
        let idExterne = null;
        if(req.body.idExterne && req.body.idExterne != null && req.body.idExterne > 0)
        {
            idExterne = req.body.idExterne;
        }else{
            idExterne = await creerExterne(req.body.nomPrenomExterne, req.body.mailExterne);
        }
        
        const result = await db.query(`
            INSERT INTO
                TENUES_AFFECTATION
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idExterne = :idExterne,
                dateAffectation = :dateAffectation,
                dateRetour = :dateRetour,
                notifPersonne = :notifPersonne
        `,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idExterne: idExterne,
            dateAffectation: req.body.dateAffectation || null,
            dateRetour: req.body.dateRetour || null,
            notifPersonne: req.body.notifPersonne || false,
        });

        await retraitItemStock(req.body.idMaterielCatalogue);
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateAffectations = async (req, res)=>{
    try {
        let idExterne = null;
        if(req.body.idExterne && req.body.idExterne != null && req.body.idExterne > 0)
        {
            idExterne = req.body.idExterne;
        }else{
            idExterne = await creerExterne(req.body.nomPrenomExterne, req.body.mailExterne);
        }
        
        const result = await db.query(`
            UPDATE
                TENUES_AFFECTATION
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idExterne = :idExterne,
                dateAffectation = :dateAffectation,
                dateRetour = :dateRetour,
                notifPersonne = :notifPersonne
            WHERE
                idTenue = :idTenue
        `,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idExterne: idExterne,
            dateAffectation: req.body.dateAffectation || null,
            dateRetour: req.body.dateRetour || null,
            notifPersonne: req.body.notifPersonne || false,
            idTenue: req.body.idTenue,
        });

        if(req.body.idMaterielCatalogueInitial != req.body.idMaterielCatalogue)
        {
            await ajoutItemStock(req.body.idMaterielCatalogueInitial);
            await retraitItemStock(req.body.idMaterielCatalogue);
        }
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.plannifierRetourMassifTenue = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                TENUES_AFFECTATION
            SET
                dateRetour = :dateRetour,
                notifPersonne = true
            WHERE
                idExterne = :idExterne
        `,{
            idExterne: req.body.idExterne || null,
            dateRetour: req.body.dateRetour || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteAffectations = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.tenuesAffectationsDelete(req.verifyJWTandProfile.idPersonne , req.body.idTenue, req.body.reintegration);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteMassifAffectations = async (req, res)=>{
    try {
        let tenuesAretourner = await db.query(`
            SELECT
                idTenue
            FROM    
                TENUES_AFFECTATION
            WHERE
                idExterne = :idExterne
        `,{
            idExterne: req.body.idExterne || null,
        });

        for(const tenue of tenuesAretourner)
        {
            const deleteResult = await fonctionsDelete.tenuesAffectationsDelete(req.verifyJWTandProfile.idPersonne , tenue.idTenue, true);
        }
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.exporterAffectations = async (req, res)=>{
    try {
        const affectations = await db.query(`
            SELECT
                *
            FROM
                VIEW_TENUES_AFFECTATION
            ORDER BY
                nomPrenomExterne,
                mailExterne,
                libelleMateriel,
                taille
        ;`);

        const workbook = new excelJS.Workbook();
        
        const worksheetAffectations = workbook.addWorksheet('Affectations');
        worksheetAffectations.columns = [
            { header: "Numéro interne",                key: "idTenue",                   width: 15 },
            { header: "Identité",                      key: "nomPrenomExterne",          width: 30 },
            { header: "Mail",                          key: "mailExterne",               width: 30 },
            { header: "Notification par mail active",  key: "notifPersonne",             width: 15 },
            { header: "Element de tenue",              key: "libelleMateriel",           width: 30 },
            { header: "Taille",                        key: "taille",                    width: 20 },
            { header: "Date d'affectation",            key: "dateAffectation",           width: 15 },
            { header: "Date de retour prévisionnelle", key: "dateRetour",                width: 15 },
        ];

        for(const affectation of affectations)
        {
            worksheetAffectations.addRow(affectation);
        }

        worksheetAffectations.getRow(1).eachCell((cell) => {
            cell.font = { bold: true };
        });

        let fileName = Date.now() + '-ExportAffectationsTenues.xlsx';

        const saveFile = await workbook.xlsx.writeFile('temp/'+fileName);

        res.send({fileName: fileName});

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

// DEMANDES DE REMPLACEMENT et PRETS
exports.reponseDemandeRemplacement = async (req, res)=>{
    try {
        const getDemandeur = await db.query(`
            SELECT
                *
            FROM
                VIEW_TENUES_AFFECTATION
            WHERE
                idTenue = :idTenue
        `,{
            idTenue: req.body.idTenue,
        });
        
        if(getDemandeur[0].mailExterne && getDemandeur[0].mailExterne != null && getDemandeur[0].mailExterne != "")
        {
            await fonctionsMail.registerToMailQueue({
                typeMail: req.body.reponseBinaire == true ? 'acceptationDemandeRemplacementTenue' : 'rejetDemandeRemplacementTenue',
                idObject: req.body.idTenue,
                otherMail: getDemandeur[0].mailExterne,
                otherContent: req.body.reponseDetails,
            });
        }
        
        const result = await db.query(`
            UPDATE
                TENUES_AFFECTATION
            SET
                demandeBenevoleRemplacement = false,
                demandeBenevoleRemplacementMotif = null
            WHERE
                idTenue = :idTenue
        `,{
            idTenue: req.body.idTenue,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.reponseDemandePret = async (req, res)=>{
    try {
        const getDemandeur = await db.query(`
            SELECT
                *
            FROM
                VIEW_TENUES_AFFECTATION
            WHERE
                idTenue = :idTenue
        `,{
            idTenue: req.body.idTenue,
        });
        
        if(getDemandeur[0].mailExterne && getDemandeur[0].mailExterne != null && getDemandeur[0].mailExterne != "")
        {
            await fonctionsMail.registerToMailQueue({
                typeMail: req.body.reponseBinaire == true ? 'acceptationDemandePretTenue' : 'rejetDemandePretTenue',
                idObject: req.body.reponseBinaire == true ? req.body.idTenue : null,
                otherMail: getDemandeur[0].mailExterne,
                otherContent: req.body.reponseDetails,
            });
        }

        if(req.body.reponseBinaire == true)
        {
            const result = await db.query(`
                UPDATE
                    TENUES_AFFECTATION
                SET
                    demandeBenevolePret = false,
                    demandeBenevolePretMotif = null,
                    dateAffectation = :dateAffectation,
                    dateRetour = :dateRetour
                WHERE
                    idTenue = :idTenue
            `,{
                idTenue: req.body.idTenue,
                dateAffectation: req.body.dateAffectation || null,
                dateRetour: req.body.dateRetour || null,
            });
            
            if(req.body.decompteStock == true)
            {
                let updateQuery = await db.query(`
                    UPDATE TENUES_CATALOGUE SET stockCatalogueTenue = stockCatalogueTenue - 1 WHERE idMaterielCatalogue = :idMaterielCatalogue
                ;`,{
                    idMaterielCatalogue : getDemandeur[0].idMaterielCatalogue,
                });
            }

        }else{
            const deleteResult = await fonctionsDelete.tenuesAffectationsDelete(req.verifyJWTandProfile.idPersonne , req.body.idTenue, false);
        }
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

// CAUTIONS
exports.getCautions = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT DISTINCT
                pe.*
            FROM
                PERSONNE_EXTERNE pe
                LEFT OUTER JOIN VIEW_CAUTIONS v ON pe.idExterne = v.idExterne
            WHERE
                v.idCaution IS NOT NULL
            ORDER BY
                nomPrenomExterne
        ;`);

        for(const personne of results)
        {
            let cautions = await db.query(`
                SELECT
                    *
                FROM
                    VIEW_CAUTIONS
                WHERE
                    idExterne = :idExterne
            ;`,{
                idExterne: personne.idExterne,
            });
            personne.cautions = cautions;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCautionsRow = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                *
            FROM
                VIEW_CAUTIONS
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addCautions = async (req, res)=>{
    try {
        let idExterne = null;
        if(req.body.idExterne && req.body.idExterne != null && req.body.idExterne > 0)
        {
            idExterne = req.body.idExterne;
        }else{
            idExterne = await creerExterne(req.body.nomPrenomExterne, req.body.mailExterne);
        }
        
        const result = await db.query(`
            INSERT INTO
                CAUTIONS
            SET
                idExterne = :idExterne,
                montantCaution = :montantCaution,
                dateEmissionCaution = :dateEmissionCaution,
                dateExpirationCaution = :dateExpirationCaution,
                detailsMoyenPaiement = :detailsMoyenPaiement
        `,{
            idExterne: idExterne,
            montantCaution: req.body.montantCaution || null,
            dateEmissionCaution: req.body.dateEmissionCaution || null,
            dateExpirationCaution: req.body.dateExpirationCaution || null,
            detailsMoyenPaiement: req.body.detailsMoyenPaiement || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateCautions = async (req, res)=>{
    try {
        let idExterne = null;
        if(req.body.idExterne && req.body.idExterne != null && req.body.idExterne > 0)
        {
            idExterne = req.body.idExterne;
        }else{
            idExterne = await creerExterne(req.body.nomPrenomExterne, req.body.mailExterne);
        }

        const result = await db.query(`
            UPDATE
                CAUTIONS
            SET
                idExterne = :idExterne,
                montantCaution = :montantCaution,
                dateEmissionCaution = :dateEmissionCaution,
                dateExpirationCaution = :dateExpirationCaution,
                detailsMoyenPaiement = :detailsMoyenPaiement
            WHERE
                idCaution = :idCaution
        `,{
            idExterne: idExterne,
            montantCaution: req.body.montantCaution || null,
            dateEmissionCaution: req.body.dateEmissionCaution || null,
            dateExpirationCaution: req.body.dateExpirationCaution || null,
            detailsMoyenPaiement: req.body.detailsMoyenPaiement || null,
            idCaution: req.body.idCaution,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteCautions = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.cautionsDelete(req.verifyJWTandProfile.idPersonne , req.body.idCaution);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}