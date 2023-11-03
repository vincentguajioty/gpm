const db = require('../db');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsLDAP = require('../helpers/fonctionsLDAP');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const dotenv = require('dotenv').config();
const authenticator = require('authenticator');
const logger = require('../winstonLogger');
const brcypt = require('bcryptjs');

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