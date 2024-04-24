const db = require('../db');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const logger = require('../winstonLogger');

const retraitItemStock = async (idCatalogueTenue) => {
    try {
        const result = await db.query(`
            UPDATE
                TENUES_CATALOGUE
            SET
                stockCatalogueTenue = stockCatalogueTenue - 1
            WHERE
                idCatalogueTenue = :idCatalogueTenue
        `,{
            idCatalogueTenue: idCatalogueTenue,
        });
    } catch (error) {
        logger.error(error)
    }
}

const ajoutItemStock = async (idCatalogueTenue) => {
    try {
        const result = await db.query(`
            UPDATE
                TENUES_CATALOGUE
            SET
                stockCatalogueTenue = stockCatalogueTenue + 1
            WHERE
                idCatalogueTenue = :idCatalogueTenue
        `,{
            idCatalogueTenue: idCatalogueTenue,
        });
    } catch (error) {
        logger.error(error)
    }
}

exports.getCatalogue = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                tc.*,
                f.nomFournisseur
            FROM
                TENUES_CATALOGUE tc
                LEFT OUTER JOIN FOURNISSEURS f ON tc.idFournisseur = f.idFournisseur
        ;`);
        for(const tenue of results)
        {
            let affectations = await db.query(`
                SELECT
                    ta.*,
                    pr.identifiant
                FROM
                    TENUES_AFFECTATION ta
                    LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ta.idPersonne = pr.idPersonne
                WHERE
                    ta.idCatalogueTenue = :idCatalogueTenue
            ;`,{
                idCatalogueTenue: tenue.idCatalogueTenue,
            });
            tenue.affectations = affectations;
        }
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
                libelleCatalogueTenue = :libelleCatalogueTenue,
                tailleCatalogueTenue = :tailleCatalogueTenue,
                serigraphieCatalogueTenue = :serigraphieCatalogueTenue,
                idFournisseur = :idFournisseur,
                stockCatalogueTenue = :stockCatalogueTenue,
                stockAlerteCatalogueTenue = :stockAlerteCatalogueTenue
        `,{
            libelleCatalogueTenue: req.body.libelleCatalogueTenue || null,
            tailleCatalogueTenue: req.body.tailleCatalogueTenue || null,
            serigraphieCatalogueTenue: req.body.serigraphieCatalogueTenue || null,
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
                libelleCatalogueTenue = :libelleCatalogueTenue,
                tailleCatalogueTenue = :tailleCatalogueTenue,
                serigraphieCatalogueTenue = :serigraphieCatalogueTenue,
                idFournisseur = :idFournisseur,
                stockCatalogueTenue = :stockCatalogueTenue,
                stockAlerteCatalogueTenue = :stockAlerteCatalogueTenue
            WHERE
                idCatalogueTenue = :idCatalogueTenue
        `,{
            libelleCatalogueTenue: req.body.libelleCatalogueTenue || null,
            tailleCatalogueTenue: req.body.tailleCatalogueTenue || null,
            serigraphieCatalogueTenue: req.body.serigraphieCatalogueTenue || null,
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

exports.getAffectations = async (req, res)=>{
    try {
        let results = await db.query(`
            (
                SELECT DISTINCT
                    'interne' as type,
                    pr.idPersonne as idPersonne,
                    CONCAT(nomPersonne, " ", prenomPersonne) as nomPrenom,
                    mailPersonne as mailPersonne
                FROM
                    PERSONNE_REFERENTE pr
                    LEFT OUTER JOIN TENUES_AFFECTATION ta ON pr.idPersonne = ta.idPersonne
                WHERE
                    ta.idTenue IS NOT NULL
            )
            UNION
            (
                SELECT DISTINCT
                    'externe' as type,
                    null as idPersonne,
                    personneNonGPM as nomPrenom,
                    mailPersonneNonGPM as mailPersonne
                FROM
                    TENUES_AFFECTATION ta
                WHERE
                    personneNonGPM IS NOT NULL
                    AND
                    idPersonne IS NULL
            )
        ;`);

        for(const personne of results)
        {
            if(personne.idPersonne > 0)
            {
                let affectations = await db.query(`
                    SELECT
                        ta.*,
                        tc.libelleCatalogueTenue,
                        tc.tailleCatalogueTenue
                    FROM
                        TENUES_AFFECTATION ta
                        LEFT OUTER JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue
                    WHERE
                        ta.idPersonne = :idPersonne
                ;`,{
                    idPersonne: personne.idPersonne,
                });
                personne.affectations = affectations;
            }
            else
            {
                if(personne.mailPersonne != null)
                {
                    let affectations = await db.query(`
                        SELECT
                            ta.*,
                            tc.libelleCatalogueTenue,
                            tc.tailleCatalogueTenue
                        FROM
                            TENUES_AFFECTATION ta
                            LEFT OUTER JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue
                        WHERE
                            ta.personneNonGPM = :personneNonGPM
                            AND
                            ta.mailPersonneNonGPM = :mailPersonneNonGPM
                    ;`,{
                        personneNonGPM: personne.nomPrenom,
                        mailPersonneNonGPM: personne.mailPersonne,
                    });
                    personne.affectations = affectations;
                }else{
                    let affectations = await db.query(`
                        SELECT
                            ta.*,
                            tc.libelleCatalogueTenue,
                            tc.tailleCatalogueTenue
                        FROM
                            TENUES_AFFECTATION ta
                            LEFT OUTER JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue
                        WHERE
                            ta.personneNonGPM = :personneNonGPM
                            AND
                            ta.mailPersonneNonGPM IS NULL
                    ;`,{
                        personneNonGPM: personne.nomPrenom,
                    });
                    personne.affectations = affectations;
                }
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
                *
            FROM
                TENUES_AFFECTATION
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
            (
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
            )
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
                idCatalogueTenue = :idCatalogueTenue,
                idPersonne = :idPersonne,
                personneNonGPM = :personneNonGPM,
                mailPersonneNonGPM = :mailPersonneNonGPM,
                dateAffectation = :dateAffectation,
                dateRetour = :dateRetour,
                notifPersonne = :notifPersonne
        `,{
            idCatalogueTenue: req.body.idCatalogueTenue || null,
            idPersonne: req.body.idPersonne || null,
            personneNonGPM: req.body.personneNonGPM || null,
            mailPersonneNonGPM: req.body.mailPersonneNonGPM || null,
            dateAffectation: req.body.dateAffectation || null,
            dateRetour: req.body.dateRetour || null,
            notifPersonne: req.body.notifPersonne || false,
        });

        await retraitItemStock(req.body.idCatalogueTenue);
        
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
                idCatalogueTenue = :idCatalogueTenue,
                idPersonne = :idPersonne,
                personneNonGPM = :personneNonGPM,
                mailPersonneNonGPM = :mailPersonneNonGPM,
                dateAffectation = :dateAffectation,
                dateRetour = :dateRetour,
                notifPersonne = :notifPersonne
            WHERE
                idTenue = :idTenue
        `,{
            idCatalogueTenue: req.body.idCatalogueTenue || null,
            idPersonne: req.body.idPersonne || null,
            personneNonGPM: req.body.personneNonGPM || null,
            mailPersonneNonGPM: req.body.mailPersonneNonGPM || null,
            dateAffectation: req.body.dateAffectation || null,
            dateRetour: req.body.dateRetour || null,
            notifPersonne: req.body.notifPersonne || false,
            idTenue: req.body.idTenue,
        });

        if(req.body.idCatalogueTenueInitial != req.body.idCatalogueTenue)
        {
            await ajoutItemStock(req.body.idCatalogueTenueInitial);
            await retraitItemStock(req.body.idCatalogueTenue);
        }
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteAffectations = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.tenuesAffectationsDelete(req.verifyJWTandProfile.idPersonne , req.body.idTenue);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCautions = async (req, res)=>{
    try {
        let results = await db.query(`
            (
                SELECT DISTINCT
                    'interne' as type,
                    pr.idPersonne as idPersonne,
                    CONCAT(nomPersonne, " ", prenomPersonne) as nomPrenom,
                    mailPersonne as mailPersonne
                FROM
                    PERSONNE_REFERENTE pr
                    LEFT OUTER JOIN CAUTIONS ta ON pr.idPersonne = ta.idPersonne
                WHERE
                    ta.idCaution IS NOT NULL
            )
            UNION
            (
                SELECT DISTINCT
                    'externe' as type,
                    null as idPersonne,
                    personneNonGPM as nomPrenom,
                    mailPersonneNonGPM as mailPersonne
                FROM
                    CAUTIONS ta
                WHERE
                    idPersonne IS NULL
            )
        ;`);

        for(const personne of results)
        {
            if(personne.idPersonne > 0)
            {
                let cautions = await db.query(`
                    SELECT
                        ta.*
                    FROM
                        CAUTIONS ta
                    WHERE
                        ta.idPersonne = :idPersonne
                ;`,{
                    idPersonne: personne.idPersonne,
                });
                personne.cautions = cautions;
            }
            else
            {
                if(personne.mailPersonne != null && personne.nomPrenom != null)
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
                        personneNonGPM: personne.nomPrenom,
                        mailPersonneNonGPM: personne.mailPersonne,
                    });
                    personne.cautions = cautions;
                }else{
                    if(personne.nomPrenom != null)
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
                            personneNonGPM: personne.nomPrenom,
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
                idPersonne = :idPersonne,
                personneNonGPM = :personneNonGPM,
                mailPersonneNonGPM = :mailPersonneNonGPM,
                montantCaution = :montantCaution,
                dateEmissionCaution = :dateEmissionCaution,
                dateExpirationCaution = :dateExpirationCaution,
                detailsMoyenPaiement = :detailsMoyenPaiement
        `,{
            idPersonne: req.body.idPersonne || null,
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
                idPersonne = :idPersonne,
                personneNonGPM = :personneNonGPM,
                mailPersonneNonGPM = :mailPersonneNonGPM,
                montantCaution = :montantCaution,
                dateEmissionCaution = :dateEmissionCaution,
                dateExpirationCaution = :dateExpirationCaution,
                detailsMoyenPaiement = :detailsMoyenPaiement
            WHERE
                idCaution = :idCaution
        `,{
            idPersonne: req.body.idPersonne || null,
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