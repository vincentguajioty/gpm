const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');

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