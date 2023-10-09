const db = require('../db');
const dotenv = require('dotenv').config();
const logger = require('../winstonLogger');

/*--- Catégories de matériels ---*/
exports.getFournisseurs = async (req, res)=>{
    try {
        let results = await db.query(
            'SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;'
        );
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}