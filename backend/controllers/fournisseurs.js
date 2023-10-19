const db = require('../db');
const brcypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const jwtFunctions = require('../jwt');
const dotenv = require('dotenv').config();
const logger = require('../winstonLogger');
const axios = require('axios');
const moment = require('moment');
const fonctionsDelete = require('../helpers/fonctionsDelete')

exports.getFournisseurs = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;`
        );

        for(const fournisseur of results)
        {
            if(req.aesKey)
            {
                let uncrypt = await db.query(
                    `SELECT CAST(AES_DECRYPT(aesFournisseur, :aesKey) AS CHAR) as aesFournisseur FROM FOURNISSEURS WHERE idFournisseur = :idFournisseur;`,
                    {
                        idFournisseur: fournisseur.idFournisseur,
                        aesKey: req.aesKey,
                    }
                );
                fournisseur.aesFournisseur = uncrypt[0].aesFournisseur;
            }
            else
            {
                fournisseur.aesFournisseur = null;
            }
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneFournisseur = async (req, res)=>{
    try {
        let results = await db.query(
            `SELECT * FROM FOURNISSEURS WHERE idFournisseur = :idFournisseur;`,
            {
                idFournisseur: req.body.idFournisseur,
            }
        );

        for(const fournisseur of results)
        {
            if(req.aesKey)
            {
                let uncrypt = await db.query(
                    `SELECT CAST(AES_DECRYPT(aesFournisseur, :aesKey) AS CHAR) as aesFournisseur FROM FOURNISSEURS WHERE idFournisseur = :idFournisseur;`,
                    {
                        idFournisseur: fournisseur.idFournisseur,
                        aesKey: req.aesKey,
                    }
                );
                fournisseur.aesFournisseur = uncrypt[0].aesFournisseur;
            }
            else
            {
                fournisseur.aesFournisseur = null;
            }

            const commandes = await db.query(`
                SELECT
                    c.dateCreation,
                    c.nomCommande,
                    ce.libelleEtat,
                    c.numCommandeFournisseur
                FROM
                    COMMANDES c
                    LEFT OUTER JOIN COMMANDES_ETATS ce ON c.idEtat = ce.idEtat
                WHERE
                    idFournisseur = :idFournisseur
                ;`,
                {
                    idFournisseur: fournisseur.idFournisseur,
                }
            );
            fournisseur.commandes = commandes;

            const catalogue = await db.query(`
                SELECT
                    mc.libelleMateriel,
                    cat.libelleCategorie,
                    mc.commentairesMateriel
                FROM
                    MATERIEL_CATALOGUE mc
                    LEFT OUTER JOIN MATERIEL_CATEGORIES cat ON mc.idCategorie = cat.idCategorie
                WHERE
                    idFournisseur = :idFournisseur
                ;`,
                {
                    idFournisseur: fournisseur.idFournisseur,
                }
            );
            fournisseur.catalogue = catalogue;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addFournisseur = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                FOURNISSEURS
            SET
                nomFournisseur = :nomFournisseur
        `,{
            nomFournisseur: req.body.nomFournisseur || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idFournisseur) as idFournisseur FROM FOURNISSEURS;'
        );

        res.status(201);
        res.json({idFournisseur: selectLast[0].idFournisseur});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateFournisseur = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                FOURNISSEURS
            SET
                nomFournisseur = :nomFournisseur,
                adresseFournisseur = :adresseFournisseur,
                telephoneFournisseur = :telephoneFournisseur,
                mailFournisseur = :mailFournisseur,
                siteWebFournisseur = :siteWebFournisseur
            WHERE
                idFournisseur = :idFournisseur
        `,{
            idFournisseur: req.body.idFournisseur || null,
            nomFournisseur: req.body.nomFournisseur || null,
            adresseFournisseur: req.body.adresseFournisseur || null,
            telephoneFournisseur: req.body.telephoneFournisseur || null,
            mailFournisseur: req.body.mailFournisseur || null,
            siteWebFournisseur: req.body.siteWebFournisseur || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteFournisseur = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.fournisseursDelete(req.verifyJWTandProfile.idPersonne , req.body.idFournisseur);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}