const db = require('../db');
const logger = require('../winstonLogger');
const multer = require('multer');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsDelete = require('../helpers/fonctionsDelete');

exports.getCentres = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                cc.*,
                COUNT(co.idCommande) as commandesAintegrer
            FROM
                CENTRE_COUTS cc
                LEFT OUTER JOIN (SELECT * FROM COMMANDES WHERE integreCentreCouts = false AND idEtat > 3) co ON cc.idCentreDeCout = co.idCentreDeCout
            GROUP BY
                cc.idCentreDeCout
            ORDER BY
                libelleCentreDecout
        ;`);

        for(const centre of results)
        {
            let gestionnaires = await db.query(`
                SELECT
                    cp.*,
                    p.nomPersonne,
                    p.prenomPersonne,
                    p.identifiant
                FROM
                    CENTRE_COUTS_PERSONNES cp
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON cp.idPersonne = p.idPersonne
                WHERE
                    idCentreDeCout = :idCentreDeCout
            ;`,{
                idCentreDeCout: centre.idCentreDeCout
            });
            centre.gestionnaires = gestionnaires;

            let statutOuverture = '1 - Ouvert';
            if(centre.dateOuverture && centre.dateOuverture != null && new Date(centre.dateOuverture) > new Date())
            {
                statutOuverture = '2 - Futur';
            }else if(centre.dateFermeture && centre.dateFermeture != null && new Date(centre.dateFermeture) < new Date())
            {
                statutOuverture = '3 - Clos';
            }
            centre.statutOuverture = statutOuverture;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addCentre = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                CENTRE_COUTS
            SET
                libelleCentreDecout = :libelleCentreDecout,
                commentairesCentreCout = :commentairesCentreCout,
                dateOuverture = :dateOuverture,
                dateFermeture = :dateFermeture,
                soldeActuel = 0
        `,{
            libelleCentreDecout: req.body.libelleCentreDecout || null,
            commentairesCentreCout: req.body.commentairesCentreCout || null,
            dateOuverture: req.body.dateOuverture || null,
            dateFermeture: req.body.dateFermeture || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idCentreDeCout) as idCentreDeCout FROM CENTRE_COUTS;'
        );

        res.status(201);
        res.json({idCentreDeCout: selectLast[0].idCentreDeCout});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}