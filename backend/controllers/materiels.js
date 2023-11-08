const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete')

exports.getMateriels = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_ELEMENT m
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN MATERIEL_ETATS me ON m.idMaterielsEtat = me.idMaterielsEtat
            ORDER BY
                libelleMateriel ASC
        ;`);

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}