const db = require('../db');
const dotenv = require('dotenv').config();
const logger = require('../winstonLogger');
const fonctionsMetiers = require('../fonctionsMetiers');
const fonctionsDelete = require('../fonctionsDelete');

exports.getReferentiels = async (req, res)=>{
    try {
        let results = await db.query(
            'SELECT * FROM LOTS_TYPES ORDER BY libelleTypeLot;'
        );
        
        for(const referentiel of results)
        {
            let lots = await db.query(
                'SELECT libelleLot, alerteConfRef FROM LOTS_LOTS WHERE idTypeLot = :idTypeLot ORDER BY libelleLot;',{
                    idTypeLot: referentiel.idTypeLot,
                }
            );
            referentiel.lotsRattaches = lots;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneReferentiel = async (req, res)=>{
    try {
        let results = await db.query(
            'SELECT * FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot;',
            {
                idTypeLot: req.body.idTypeLot,
            }
        );
        
        for(const referentiel of results)
        {
            let lots = await db.query(
                'SELECT libelleLot, alerteConfRef FROM LOTS_LOTS WHERE idTypeLot = :idTypeLot ORDER BY libelleLot;',{
                    idTypeLot: referentiel.idTypeLot,
                }
            );
            referentiel.lotsRattaches = lots;

            let contenus = await db.query(`
                SELECT
                    ref.*,
                    mc.libelleMateriel,
                    mc.sterilite,
                    mc.idCategorie,
                    cat.libelleCategorie
                FROM
                    REFERENTIELS ref
                    LEFT OUTER JOIN MATERIEL_CATALOGUE mc ON ref.idMaterielCatalogue = mc.idMaterielCatalogue
                    LEFT OUTER JOIN MATERIEL_CATEGORIES cat ON mc.idCategorie = cat.idCategorie
                WHERE
                    idTypeLot = :idTypeLot
                ;`,{
                    idTypeLot: referentiel.idTypeLot,
                }
            );
            referentiel.contenu = contenus;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addReferentiel = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                LOTS_TYPES
            SET
                libelleTypeLot = :libelleTypeLot
        `,{
            libelleTypeLot: req.body.libelleTypeLot || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idTypeLot) as idTypeLot FROM LOTS_TYPES;'
        );

        res.status(201);
        res.json({idTypeLot: selectLast[0].idTypeLot});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCatalogueForReferentielForm = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                mc.*,
                cat.libelleCategorie,
                ref.quantiteReferentiel,
                ref.obligatoire,
                ref.commentairesReferentiel
            FROM
                MATERIEL_CATALOGUE mc
                LEFT OUTER JOIN MATERIEL_CATEGORIES cat ON mc.idCategorie = cat.idCategorie
                LEFT OUTER JOIN (SELECT * FROM REFERENTIELS WHERE idTypeLot = :idTypeLot) ref ON mc.idMaterielCatalogue = ref.idMaterielCatalogue
            ORDER BY
                cat.libelleCategorie ASC,
                mc.libelleMateriel ASC
            ;`,
            {
                idTypeLot: req.body.idTypeLot,
            }
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateReferentiel = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                LOTS_TYPES
            SET
                libelleTypeLot = :libelleTypeLot
            WHERE
                idTypeLot = :idTypeLot
        `,{
            libelleTypeLot: req.body.libelleTypeLot || null,
            idTypeLot : req.body.idTypeLot || null,
        });

        const cleanQuery = await db.query(`
            DELETE FROM
                REFERENTIELS
            WHERE
                idTypeLot = :idTypeLot
        `,{
            idTypeLot : req.body.idTypeLot || null,
        });

        for(const item of req.body.catalogue)
        {
            const insertQuery = await db.query(`
                INSERT INTO
                    REFERENTIELS
                SET
                    idTypeLot               = :idTypeLot,
                    idMaterielCatalogue     = :idMaterielCatalogue,
                    quantiteReferentiel     = :quantiteReferentiel,
                    obligatoire             = :obligatoire,
                    commentairesReferentiel = :commentairesReferentiel
            `,{
                idTypeLot              : req.body.idTypeLot || null,
                idMaterielCatalogue    : item.idMaterielCatalogue || null,
                quantiteReferentiel    : item.quantiteReferentiel || null,
                obligatoire            : item.obligatoire == 1 ? 1 : 0,
                commentairesReferentiel: item.commentairesReferentiel || null,
            });
        }

        await fonctionsMetiers.checkAllConf();
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteReferentiel = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.referentielsDelete(req.verifyJWTandProfile.idPersonne , req.body.idTypeLot);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}