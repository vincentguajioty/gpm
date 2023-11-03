const db = require('../db');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsLDAP = require('../helpers/fonctionsLDAP');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const dotenv = require('dotenv').config();
const authenticator = require('authenticator');
const logger = require('../winstonLogger');
const brcypt = require('bcryptjs');

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
            stockCatalogueTenue: req.body.stockCatalogueTenue || null,
            stockAlerteCatalogueTenue: req.body.stockAlerteCatalogueTenue || null,
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
            stockCatalogueTenue: req.body.stockCatalogueTenue || null,
            stockAlerteCatalogueTenue: req.body.stockAlerteCatalogueTenue || null,
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
                SELECT
                    'interne' as type,
                    pr.idPersonne as idPersonne,
                    CONCAT(nomPersonne, " ", prenomPersonne) as nomPrenom
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
                    personneNonGPM as nomPrenom
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
                ;`,{
                    personneNonGPM: personne.nomPrenom,
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
                    personneNonGPM
                FROM
                    TENUES_AFFECTATION
                WHERE
                    personneNonGPM IS NOT NULL
            )
            UNION
            (
                SELECT DISTINCT
                    personneNonGPM
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
                dateAffectation = :dateAffectation,
                dateRetour = :dateRetour
        `,{
            idCatalogueTenue: req.body.idCatalogueTenue || null,
            idPersonne: req.body.idPersonne || null,
            personneNonGPM: req.body.personneNonGPM || null,
            dateAffectation: req.body.dateAffectation || null,
            dateRetour: req.body.dateRetour || null,
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
                dateAffectation = :dateAffectation,
                dateRetour = :dateRetour
            WHERE
                idTenue = :idTenue
        `,{
            idCatalogueTenue: req.body.idCatalogueTenue || null,
            idPersonne: req.body.idPersonne || null,
            personneNonGPM: req.body.personneNonGPM || null,
            dateAffectation: req.body.dateAffectation || null,
            dateRetour: req.body.dateRetour || null,
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