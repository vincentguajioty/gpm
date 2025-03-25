const db = require('../db');
const fonctionsDelete = require('../helpers/fonctionsDelete');
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
exports.getAffectations = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT DISTINCT
                personneNonGPM,
                mailPersonneNonGPM
            FROM
                TENUES_AFFECTATION ta
            ORDER BY
                personneNonGPM
        ;`);

        for(const personne of results)
        {
            if(personne.mailPersonneNonGPM != null)
            {
                let affectations = await db.query(`
                    SELECT
                        ta.*,
                        tc.libelleMateriel,
                        tc.taille
                    FROM
                        TENUES_AFFECTATION ta
                        LEFT OUTER JOIN MATERIEL_CATALOGUE tc ON ta.idMaterielCatalogue = tc.idMaterielCatalogue
                    WHERE
                        ta.personneNonGPM = :personneNonGPM
                        AND
                        ta.mailPersonneNonGPM = :mailPersonneNonGPM
                    ORDER BY
                        tc.libelleMateriel,
                        tc.taille
                ;`,{
                    personneNonGPM: personne.personneNonGPM,
                    mailPersonneNonGPM: personne.mailPersonneNonGPM,
                });
                personne.affectations = affectations;
            }else{
                let affectations = await db.query(`
                    SELECT
                        ta.*,
                        tc.libelleMateriel,
                        tc.taille
                    FROM
                        TENUES_AFFECTATION ta
                        LEFT OUTER JOIN MATERIEL_CATALOGUE tc ON ta.idMaterielCatalogue = tc.idMaterielCatalogue
                    WHERE
                        ta.personneNonGPM = :personneNonGPM
                        AND
                        ta.mailPersonneNonGPM IS NULL
                    ORDER BY
                        tc.libelleMateriel,
                        tc.taille
                ;`,{
                    personneNonGPM: personne.personneNonGPM,
                });
                personne.affectations = affectations;
            }
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
                ta.*,
                ta.personneNonGPM as nomPrenom,
                ta.mailPersonneNonGPM as mailPersonne,
                cat.libelleMateriel,
                cat.taille
            FROM
                TENUES_AFFECTATION ta
                LEFT OUTER JOIN MATERIEL_CATALOGUE cat ON ta.idMaterielCatalogue = cat.idMaterielCatalogue
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
                allRecords.*
            FROM    
                ((
                    SELECT DISTINCT
                        personneNonGPM,
                        mailPersonneNonGPM
                    FROM
                        TENUES_AFFECTATION
                    WHERE
                        personneNonGPM IS NOT NULL
                )
                UNION
                (
                    SELECT DISTINCT
                        personneNonGPM,
                        mailPersonneNonGPM
                    FROM
                        CAUTIONS
                    WHERE
                        personneNonGPM IS NOT NULL
                )) allRecords
            ORDER BY
                allRecords.personneNonGPM,
                allRecords.mailPersonneNonGPM
        ;`);

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addAffectations = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                TENUES_AFFECTATION
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                personneNonGPM = :personneNonGPM,
                mailPersonneNonGPM = :mailPersonneNonGPM,
                dateAffectation = :dateAffectation,
                dateRetour = :dateRetour,
                notifPersonne = :notifPersonne
        `,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            personneNonGPM: req.body.personneNonGPM || null,
            mailPersonneNonGPM: req.body.mailPersonneNonGPM || null,
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
        const result = await db.query(`
            UPDATE
                TENUES_AFFECTATION
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                personneNonGPM = :personneNonGPM,
                mailPersonneNonGPM = :mailPersonneNonGPM,
                dateAffectation = :dateAffectation,
                dateRetour = :dateRetour,
                notifPersonne = :notifPersonne
            WHERE
                idTenue = :idTenue
        `,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            personneNonGPM: req.body.personneNonGPM || null,
            mailPersonneNonGPM: req.body.mailPersonneNonGPM || null,
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
        if(!req.body.mailPersonneNonGPM || req.body.mailPersonneNonGPM == null || req.body.mailPersonneNonGPM == "")
        {
            const result = await db.query(`
                UPDATE
                    TENUES_AFFECTATION
                SET
                    dateRetour = :dateRetour,
                    notifPersonne = true
                WHERE
                    personneNonGPM = :personneNonGPM
            `,{
                personneNonGPM: req.body.personneNonGPM || null,
                dateRetour: req.body.dateRetour || null,
            });
        }else{
            const result = await db.query(`
                UPDATE
                    TENUES_AFFECTATION
                SET
                    dateRetour = :dateRetour,
                    notifPersonne = true
                WHERE
                    personneNonGPM = :personneNonGPM
                    AND
                    mailPersonneNonGPM = :mailPersonneNonGPM
            `,{
                personneNonGPM: req.body.personneNonGPM || null,
                mailPersonneNonGPM: req.body.mailPersonneNonGPM || null,
                dateRetour: req.body.dateRetour || null,
            });
        }

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
        let tenuesAretourner = [];

        if(!req.body.mailPersonneNonGPM || req.body.mailPersonneNonGPM == null || req.body.mailPersonneNonGPM == "")
        {
            tenuesAretourner = await db.query(`
                SELECT
                    idTenue
                FROM    
                    TENUES_AFFECTATION
                WHERE
                    personneNonGPM = :personneNonGPM
            `,{
                personneNonGPM: req.body.personneNonGPM || null,
            });
        }else{
            tenuesAretourner = await db.query(`
                SELECT
                    idTenue
                FROM    
                    TENUES_AFFECTATION
                WHERE
                    personneNonGPM = :personneNonGPM
                    AND
                    mailPersonneNonGPM = :mailPersonneNonGPM
            `,{
                personneNonGPM: req.body.personneNonGPM || null,
                mailPersonneNonGPM: req.body.mailPersonneNonGPM || null,
            });
        }

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
                ta.*,
                c.libelleMateriel,
                c.taille
            FROM
                TENUES_AFFECTATION ta
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON ta.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN TENUES_CATALOGUE tc ON ta.idMaterielCatalogue = tc.idMaterielCatalogue
            ORDER BY
                ta.personneNonGPM,
                ta.mailPersonneNonGPM,
                c.libelleMateriel,
                c.taille
        ;`);

        const workbook = new excelJS.Workbook();
        
        const worksheetAffectations = workbook.addWorksheet('Affectations');
        worksheetAffectations.columns = [
            { header: "Numéro interne",                key: "idTenue",                   width: 15 },
            { header: "Identité",                      key: "personneNonGPM",            width: 30 },
            { header: "Mail",                          key: "mailPersonneNonGPM",        width: 30 },
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

// CAUTIONS
exports.getCautions = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT DISTINCT
                personneNonGPM,
                mailPersonneNonGPM
            FROM
                CAUTIONS ta
            ORDER BY
                personneNonGPM
        ;`);

        for(const personne of results)
        {
            if(personne.mailPersonneNonGPM != null && personne.personneNonGPM != null)
            {
                let cautions = await db.query(`
                    SELECT
                        ta.*
                    FROM
                        CAUTIONS ta
                    WHERE
                        ta.personneNonGPM = :personneNonGPM
                        AND
                        ta.mailPersonneNonGPM = :mailPersonneNonGPM
                ;`,{
                    personneNonGPM: personne.personneNonGPM,
                    mailPersonneNonGPM: personne.mailPersonneNonGPM,
                });
                personne.cautions = cautions;
            }else{
                if(personne.personneNonGPM != null)
                {
                    let cautions = await db.query(`
                        SELECT
                            ta.*
                        FROM
                            CAUTIONS ta
                        WHERE
                            ta.personneNonGPM = :personneNonGPM
                            AND
                            ta.mailPersonneNonGPM IS NULL
                    ;`,{
                        personneNonGPM: personne.personneNonGPM,
                    });
                    personne.cautions = cautions;
                }else{
                    
                    let cautions = await db.query(`
                        SELECT
                            ta.*
                        FROM
                            CAUTIONS ta
                        WHERE
                            ta.personneNonGPM IS NULL
                            AND
                            ta.mailPersonneNonGPM IS NULL
                    ;`);
                    personne.cautions = cautions;
                }
            }
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
                CAUTIONS
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addCautions = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                CAUTIONS
            SET
                personneNonGPM = :personneNonGPM,
                mailPersonneNonGPM = :mailPersonneNonGPM,
                montantCaution = :montantCaution,
                dateEmissionCaution = :dateEmissionCaution,
                dateExpirationCaution = :dateExpirationCaution,
                detailsMoyenPaiement = :detailsMoyenPaiement
        `,{
            personneNonGPM: req.body.personneNonGPM || null,
            mailPersonneNonGPM: req.body.mailPersonneNonGPM || null,
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
        const result = await db.query(`
            UPDATE
                CAUTIONS
            SET
                personneNonGPM = :personneNonGPM,
                mailPersonneNonGPM = :mailPersonneNonGPM,
                montantCaution = :montantCaution,
                dateEmissionCaution = :dateEmissionCaution,
                dateExpirationCaution = :dateExpirationCaution,
                detailsMoyenPaiement = :detailsMoyenPaiement
            WHERE
                idCaution = :idCaution
        `,{
            personneNonGPM: req.body.personneNonGPM || null,
            mailPersonneNonGPM: req.body.mailPersonneNonGPM || null,
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