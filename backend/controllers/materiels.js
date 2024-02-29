const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

exports.getMateriels = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                m.*,
                e.libelleEmplacement,
                e.idSac,
                s.libelleSac,
                s.idLot,
                l.libelleLot,
                l.idTypeLot,
                l.idNotificationEnabled,
                l.inventaireEnCours,
                c.libelleMateriel,
                c.idCategorie,
                c.sterilite,
                c.peremptionAnticipationOpe,
                me.libelleMaterielsEtat
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

        if(req.body.filterIdEmplacement > 0)
        {
            results = results.filter(item => item.idEmplacement == req.body.filterIdEmplacement)
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneMateriel = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                m.*,
                e.libelleEmplacement,
                e.idSac,
                s.libelleSac,
                s.idLot,
                l.libelleLot,
                l.idTypeLot,
                l.idNotificationEnabled,
                l.inventaireEnCours,
                c.libelleMateriel,
                c.idCategorie,
                c.sterilite,
                c.peremptionAnticipationOpe,
                me.libelleMaterielsEtat
            FROM
                MATERIEL_ELEMENT m
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN MATERIEL_ETATS me ON m.idMaterielsEtat = me.idMaterielsEtat
            WHERE
                idElement = :idElement
        ;`,{
            idElement: req.body.idElement
        });

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addMateriels = async (req, res)=>{
    try {
        let insert = await db.query(`
            INSERT INTO
                MATERIEL_ELEMENT
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idEmplacement = :idEmplacement,
                idFournisseur = :idFournisseur,
                quantite = :quantite,
                quantiteAlerte = :quantiteAlerte,
                peremption = :peremption,
                peremptionAnticipation = :peremptionAnticipation,
                numeroSerie = :numeroSerie,
                commentairesElement = :commentairesElement,
                idMaterielsEtat = :idMaterielsEtat
        ;`,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idEmplacement: req.body.idEmplacement || null,
            idFournisseur: req.body.idFournisseur || null,
            quantite: req.body.quantite || 0,
            quantiteAlerte: req.body.quantiteAlerte || 0,
            peremption: req.body.peremption || null,
            peremptionAnticipation: req.body.peremptionAnticipation || null,
            numeroSerie: req.body.numeroSerie || null,
            commentairesElement: req.body.commentairesElement || null,
            idMaterielsEtat: req.body.idMaterielsEtat || null,
        });

        let selectLast = await db.query(
            'SELECT MAX(idElement) as idElement FROM MATERIEL_ELEMENT;'
        );
        await fonctionsMetiers.updateConformiteMaterielOpe(selectLast[0].idElement);

        let newRecord = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_ELEMENT m
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
            WHERE
                idElement = :idElement
        ;`,{
            idElement: selectLast[0].idElement
        });
        newRecord = newRecord[0];

        if(newRecord.idLot != null)
        {
            await fonctionsMetiers.checkOneConf(newRecord.idLot);
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMateriels = async (req, res)=>{
    try {
        let oldRecord = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_ELEMENT m
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
            WHERE
                idElement = :idElement
        ;`,{
            idElement: req.body.idElement
        });
        oldRecord = oldRecord[0];

        let update = await db.query(`
            UPDATE
                MATERIEL_ELEMENT
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idEmplacement = :idEmplacement,
                idFournisseur = :idFournisseur,
                quantite = :quantite,
                quantiteAlerte = :quantiteAlerte,
                peremption = :peremption,
                peremptionAnticipation = :peremptionAnticipation,
                numeroSerie = :numeroSerie,
                commentairesElement = :commentairesElement,
                idMaterielsEtat = :idMaterielsEtat
            WHERE
                idElement = :idElement
        ;`,{
            idMaterielCatalogue: req.body.idMaterielCatalogue || null,
            idEmplacement: req.body.idEmplacement || null,
            idFournisseur: req.body.idFournisseur || null,
            quantite: req.body.quantite || 0,
            quantiteAlerte: req.body.quantiteAlerte || 0,
            peremption: req.body.peremption || null,
            peremptionAnticipation: req.body.peremptionAnticipation || null,
            numeroSerie: req.body.numeroSerie || null,
            commentairesElement: req.body.commentairesElement || null,
            idMaterielsEtat: req.body.idMaterielsEtat || null,
            idElement: req.body.idElement || null,
        });
        await fonctionsMetiers.updateConformiteMaterielOpe(req.body.idElement);

        if(oldRecord.idEmplacement != req.body.idEmplacement)
        {
            let newRecord = await db.query(`
                SELECT
                    *
                FROM
                    MATERIEL_ELEMENT m
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                WHERE
                    idElement = :idElement
            ;`,{
                idElement: req.body.idElement
            });
            newRecord = newRecord[0];

            if(oldRecord.idLot != null && oldRecord.idLot != newRecord.idLot)
            {
                await fonctionsMetiers.checkOneConf(oldRecord.idLot);
            }
            if(newRecord.idLot != null)
            {
                await fonctionsMetiers.checkOneConf(newRecord.idLot);
            }
        }
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.materielsDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.materielsDelete(req.verifyJWTandProfile.idPersonne , req.body.idElement);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}