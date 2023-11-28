const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

//sacs
exports.getSacs = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                s.*,
                l.libelleLot,
                l.inventaireEnCours,
                f.nomFournisseur,
                COUNT(DISTINCT(emp.idEmplacement)) as quantiteEmplacements,
                COUNT(DISTINCT(matos.idElement)) as quantiteMateriels
            FROM
                MATERIEL_SAC s
                LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                LEFT OUTER JOIN FOURNISSEURS f ON f.idFournisseur = s.idFournisseur
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT emp ON s.idSac = emp.idSac
                LEFT OUTER JOIN MATERIEL_ELEMENT matos ON emp.idEmplacement = matos.idEmplacement
            GROUP BY
                s.idSac
            ORDER BY
                libelleSac ASC
        ;`);

        if(req.body.filterIdLot > 0)
        {
            results = results.filter(item => item.idLot == filterIdLot)
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneSac = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                s.*,
                l.libelleLot,
                l.inventaireEnCours,
                f.nomFournisseur,
                COUNT(emp.idEmplacement) as quantiteEmplacements,
                COUNT(matos.idElement) as quantiteMateriels
            FROM
                MATERIEL_SAC s
                LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                LEFT OUTER JOIN FOURNISSEURS f ON s.idFournisseur = s.idFournisseur
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT emp ON s.idSac = emp.idSac
                LEFT OUTER JOIN MATERIEL_ELEMENT matos ON emp.idEmplacement = matos.idEmplacement
            WHERE
                s.idSac = :idSac
            GROUP BY
                s.idSac
        ;`,{
            idSac: req.body.idSac
        });

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addSacs = async (req, res)=>{
    try {
        let insert = await db.query(`
            INSERT INTO
                MATERIEL_SAC
            SET
                libelleSac = :libelleSac,
                idLot = :idLot,
                taille = :taille,
                couleur = :couleur,
                idFournisseur = :idFournisseur
        ;`,{
            libelleSac: req.body.libelleSac || null,
            idLot: req.body.idLot || null,
            taille: req.body.taille || null,
            couleur: req.body.couleur || null,
            idFournisseur: req.body.idFournisseur || null,
        });

        let selectLast = await db.query(
            'SELECT MAX(idSac) as idSac FROM MATERIEL_SAC;'
        );

        let defaultEmpl = await db.query(`
            INSERT INTO
                MATERIEL_EMPLACEMENT
            SET
                libelleEmplacement = "Défaut",
                idSac = :idSac
        ;`,{
            idSac: selectLast[0].idSac,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateSacs = async (req, res)=>{
    try {
        let oldRecord = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_SAC
            WHERE
                idSac = :idSac
        ;`,{
            idSac: req.body.idSac
        });
        oldRecord = oldRecord[0];
        
        let update = await db.query(`
            UPDATE
                MATERIEL_SAC
            SET
                libelleSac = :libelleSac,
                idLot = :idLot,
                taille = :taille,
                couleur = :couleur,
                idFournisseur = :idFournisseur
            WHERE
                idSac = :idSac
        ;`,{
            libelleSac: req.body.libelleSac || null,
            idLot: req.body.idLot || null,
            taille: req.body.taille || null,
            couleur: req.body.couleur || null,
            idFournisseur: req.body.idFournisseur || null,
            idSac: req.body.idSac || null,
        });

        if(oldRecord.idLot != req.body.idLot)
        {
            if(oldRecord.idLot != null)
            {
                await fonctionsMetiers.checkOneConf(oldRecord.idLot);
            }
            if(req.body.idLot != null)
            {
                await fonctionsMetiers.checkOneConf(req.body.idLot);
            }
        }
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.sacDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.sacsDelete(req.verifyJWTandProfile.idPersonne , req.body.idSac);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//emplacements
exports.getEmplacementsOneSac = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_EMPLACEMENT
            WHERE
                idSac = :idSac
            ORDER BY
                libelleEmplacement ASC
        ;`,{
            idSac: req.body.idSac,
        });

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addEmplacement = async (req, res)=>{
    try {
        let insert = await db.query(`
            INSERT INTO
                MATERIEL_EMPLACEMENT
            SET
                libelleEmplacement = :libelleEmplacement,
                idSac = :idSac
        ;`,{
            libelleEmplacement: req.body.libelleEmplacement || null,
            idSac: req.body.idSac || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateEmplacement = async (req, res)=>{
    try {
        let oldRecord = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_EMPLACEMENT e
                LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
            WHERE
                idEmplacement = :idEmplacement
        ;`,{
            idEmplacement: req.body.idEmplacement
        });
        oldRecord = oldRecord[0];
        
        let update = await db.query(`
            UPDATE
                MATERIEL_EMPLACEMENT
            SET
                libelleEmplacement = :libelleEmplacement,
                idSac = :idSac
            WHERE
                idEmplacement = :idEmplacement
        ;`,{
            libelleEmplacement: req.body.libelleEmplacement || null,
            idSac: req.body.idSac || null,
            idEmplacement: req.body.idEmplacement || null,
        });

        if(oldRecord.idSac != req.body.idSac)
        {
            let newRecord = await db.query(`
                SELECT
                    *
                FROM
                    MATERIEL_EMPLACEMENT e
                    LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                WHERE
                    idEmplacement = :idEmplacement
            ;`,{
                idEmplacement: req.body.idEmplacement
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

exports.emplacementDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.emplacementsDelete(req.verifyJWTandProfile.idPersonne , req.body.idEmplacement);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}