const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

//Transferts Réserves -> Lots
exports.getReservesForOneTransfert = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                rm.*,
                rm.idReserveElement as value,
                CONCAT_WS(' > ',c.libelleConteneur,cat.libelleMateriel) as label,
                c.libelleConteneur,
                c.inventaireEnCours,
                cat.libelleMateriel
            FROM
                RESERVES_MATERIEL rm
                LEFT OUTER JOIN RESERVES_CONTENEUR c ON rm.idConteneur = c.idConteneur
                LEFT OUTER JOIN MATERIEL_CATALOGUE cat ON rm.idMaterielCatalogue = cat.idMaterielCatalogue
            WHERE
                rm.idMaterielCatalogue = :idMaterielCatalogue
        ;`,{
            idMaterielCatalogue: req.body.idMaterielCatalogue
        });

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.opererTransfertReserveLot = async (req, res)=>{
    try {
        let element = await db.query(`
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
                m.idElement = :idElement
        `,{
            idElement: req.body.idElement,
        });
        element = element[0];

        let reserve = await db.query(`
            SELECT
                *
            FROM
                RESERVES_MATERIEL
            WHERE
                idReserveElement = :idReserveElement
        `,{
            idReserveElement: req.body.idReserveElement,
        });
        reserve = reserve[0];

        let incrementerLot = await db.query(`
            UPDATE
                MATERIEL_ELEMENT
            SET
                quantite = quantite + :ajout
            WHERE
                idElement = :idElement
        `,{
            idElement: req.body.idElement,
            ajout: req.body.qttTransfert || 0,
        });
        
        let decrementerReserve = await db.query(`
            UPDATE
                RESERVES_MATERIEL
            SET
                quantiteReserve = quantiteReserve - :ajout
            WHERE
                idReserveElement = :idReserveElement
        `,{
            idReserveElement: req.body.idReserveElement,
            ajout: req.body.qttTransfert || 0,
        });

        if(
            (element.peremption == null && reserve.peremptionReserve != null)
            ||
            (
                (element.peremption != null && reserve.peremptionReserve != null)
                &&
                (new Date(element.peremption) > new Date(reserve.peremptionReserve))
            )
        ){
            let keepReservePeremp = await db.query(`
                UPDATE
                    MATERIEL_ELEMENT
                SET
                    peremption = :peremption
                WHERE
                    idElement = :idElement
            `,{
                idElement: req.body.idElement,
                peremption: reserve.peremptionReserve,
            });
        }

        if(parseInt(reserve.quantiteReserve) - parseInt(req.body.qttTransfert) <= 0)
        {
            let resetPeremptionReserve = await db.query(`
                UPDATE
                    RESERVES_MATERIEL
                SET
                    peremptionReserve = null
                WHERE
                    idReserveElement = :idReserveElement
            `,{
                idReserveElement: req.body.idReserveElement,
            });
        }

        if(element.idLot != null)
        {
            await fonctionsMetiers.checkOneConf(element.idLot);
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}