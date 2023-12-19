const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

exports.getOneConso = async (req, res)=>{
    try {
        const consommation = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION
            WHERE
                idConsommation = :idConsommation
        `,{
            idConsommation: req.body.idConsommation || null
        });

        const elements = await db.query(`
            SELECT
                m.*,
                l.libelleLot,
                c.libelleMateriel,
                res.libelleConteneur
            FROM
                LOTS_CONSOMMATION_MATERIEL m
                LEFT OUTER JOIN LOTS_LOTS l ON m.idLot = l.idLot
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN RESERVES_CONTENEUR res ON m.idConteneur = res.idConteneur
            WHERE
                idConsommation = :idConsommation
        `,{
            idConsommation: req.body.idConsommation || null
        });
        
        res.json({consommation: consommation[0], elements: elements});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.createConso = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                LOTS_CONSOMMATION
            SET
                nomDeclarantConsommation = :nomDeclarantConsommation,
                dateConsommation = :dateConsommation,
                evenementConsommation = :evenementConsommation,
                declarationEnCours = true,
                reapproEnCours = false
        `,{
            nomDeclarantConsommation: req.body.nomDeclarantConsommation || null,
            dateConsommation: req.body.dateConsommation || null,
            evenementConsommation: req.body.evenementConsommation || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idConsommation) as idConsommation FROM LOTS_CONSOMMATION;'
        );

        res.status(201);
        res.json({idConsommation: selectLast[0].idConsommation});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}
