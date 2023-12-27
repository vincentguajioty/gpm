const db = require('../db');
const logger = require('../winstonLogger');

exports.getCommandes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                c.*,
                f.nomFournisseur,
                e.libelleEtat
            FROM
                COMMANDES c
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur
                LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat
            ORDER BY
                dateCreation DESC
        ;`);

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}