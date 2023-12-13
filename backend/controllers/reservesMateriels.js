const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');

exports.getReservesMateriels = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                m.*,
                e.libelleConteneur,
                e.inventaireEnCours,
                c.libelleMateriel,
                c.idCategorie,
                c.sterilite,
                c.peremptionAnticipationRes
            FROM
                RESERVES_MATERIEL m
                LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
            ORDER BY
                libelleMateriel ASC
        ;`);

        if(req.body.filterIdConteneur > 0)
        {
            results = results.filter(item => item.idConteneur == req.body.filterIdConteneur)
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneReservesMateriel = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                m.*,
                e.libelleConteneur,
                e.inventaireEnCours,
                c.libelleMateriel,
                c.idCategorie,
                c.sterilite,
                c.peremptionAnticipationRes
            FROM
                RESERVES_MATERIEL m
                LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
            WHERE
                idReserveElement = :idReserveElement
        ;`,{
            idReserveElement: req.body.idReserveElement
        });

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addReservesMateriels = async (req, res)=>{
    try {
        let insert = await db.query(`
            INSERT INTO
                RESERVES_MATERIEL
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idConteneur = :idConteneur,
                idFournisseur = :idFournisseur,
                quantiteReserve = :quantiteReserve,
                quantiteAlerteReserve = :quantiteAlerteReserve,
                peremptionReserve = :peremptionReserve,
                peremptionReserveAnticipation = :peremptionReserveAnticipation,
                commentairesReserveElement = :commentairesReserveElement
        ;`,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idConteneur: req.body.idConteneur || null,
            idFournisseur: req.body.idFournisseur || null,
            quantiteReserve: req.body.quantiteReserve || 0,
            quantiteAlerteReserve: req.body.quantiteAlerteReserve || 0,
            peremptionReserve: req.body.peremptionReserve || null,
            peremptionReserveAnticipation: req.body.peremptionReserveAnticipation || null,
            commentairesReserveElement: req.body.commentairesReserveElement || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateReservesMateriels = async (req, res)=>{
    try {
        let update = await db.query(`
            UPDATE
                RESERVES_MATERIEL
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idConteneur = :idConteneur,
                idFournisseur = :idFournisseur,
                quantiteReserve = :quantiteReserve,
                quantiteAlerteReserve = :quantiteAlerteReserve,
                peremptionReserve = :peremptionReserve,
                peremptionReserveAnticipation = :peremptionReserveAnticipation,
                commentairesReserveElement = :commentairesReserveElement
            WHERE
                idReserveElement = :idReserveElement
        ;`,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idConteneur: req.body.idConteneur || null,
            idFournisseur: req.body.idFournisseur || null,
            quantiteReserve: req.body.quantiteReserve || 0,
            quantiteAlerteReserve: req.body.quantiteAlerteReserve || 0,
            peremptionReserve: req.body.peremptionReserve || null,
            peremptionReserveAnticipation: req.body.peremptionReserveAnticipation || null,
            commentairesReserveElement: req.body.commentairesReserveElement || null,
            idReserveElement: req.body.idReserveElement || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.materielsReservesDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.reserveMaterielDelete(req.verifyJWTandProfile.idPersonne , req.body.idReserveElement);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}