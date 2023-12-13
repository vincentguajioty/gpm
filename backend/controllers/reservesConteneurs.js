const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

//Réserves - Gestion générale
exports.getConteneurs = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                l.*,
                DATE_ADD(l.dateDernierInventaire, INTERVAL frequenceInventaire DAY) as prochainInventaire,
                li.libelleLieu
            FROM
                RESERVES_CONTENEUR l
                LEFT OUTER JOIN LIEUX li ON li.idLieu = l.idLieu
            ORDER BY
                l.libelleConteneur ASC
        ;`);

        for(const conteneur of results)
        {
            let materielsOK = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    RESERVES_MATERIEL e
                WHERE
                    idConteneur = :idConteneur
                    AND
                    (
                        quantiteReserve > quantiteAlerteReserve AND
                        (
                            peremptionNotificationReserve > CURRENT_DATE
                            OR
                            peremptionNotificationReserve IS NULL
                        )
                    )
            ;`,{
                idConteneur: conteneur.idConteneur,
            });
            let materielsLimites = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    RESERVES_MATERIEL e
                WHERE
                    idConteneur = :idConteneur
                    AND
                    (
                        (
                            quantiteReserve = quantiteAlerteReserve
                            AND
                            peremptionNotificationReserve = CURRENT_DATE
                        )
                        OR
                        (
                            quantiteReserve = quantiteAlerteReserve
                            AND
                            (
                                peremptionNotificationReserve > CURRENT_DATE
                                OR
                                peremptionNotificationReserve IS NULL
                            )
                        )
                        OR
                        (
                            quantiteReserve > quantiteAlerteReserve
                            AND
                            peremptionNotificationReserve = CURRENT_DATE
                        )
                    )
            ;`,{
                idConteneur: conteneur.idConteneur,
            });
            let materielsAlerte = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    RESERVES_MATERIEL e
                WHERE
                    idConteneur = :idConteneur
                    AND
                    (
                        quantiteReserve < quantiteAlerteReserve OR
                        peremptionNotificationReserve < CURRENT_DATE
                    )
            ;`,{
                idConteneur: conteneur.idConteneur,
            });

            conteneur.materielsOK = materielsOK[0].nb;
            conteneur.materielsLimites = materielsLimites[0].nb;
            conteneur.materielsAlerte = materielsAlerte[0].nb;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneConteneur = async (req, res)=>{
    try {
        let conteneur = await db.query(`
            SELECT
                l.*,
                DATE_ADD(l.dateDernierInventaire, INTERVAL frequenceInventaire DAY) as prochainInventaire,
                li.libelleLieu
            FROM
                RESERVES_CONTENEUR l
                LEFT OUTER JOIN LIEUX li ON li.idLieu = l.idLieu
            WHERE
                idConteneur = :idConteneur
        ;`,{
            idConteneur: req.body.idConteneur || null,
        });
        conteneur = conteneur[0];

        let materielsOK = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                RESERVES_MATERIEL e
            WHERE
                idConteneur = :idConteneur
                AND
                (
                    quantiteReserve > quantiteAlerteReserve AND
                    (
                        peremptionNotificationReserve > CURRENT_DATE
                        OR
                        peremptionNotificationReserve IS NULL
                    )
                )
        ;`,{
            idConteneur: conteneur.idConteneur,
        });
        let materielsLimites = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                RESERVES_MATERIEL e
            WHERE
                idConteneur = :idConteneur
                AND
                (
                    (
                        quantiteReserve = quantiteAlerteReserve
                        AND
                        peremptionNotificationReserve = CURRENT_DATE
                    )
                    OR
                    (
                        quantiteReserve = quantiteAlerteReserve
                        AND
                        (
                            peremptionNotificationReserve > CURRENT_DATE
                            OR
                            peremptionNotificationReserve IS NULL
                        )
                    )
                    OR
                    (
                        quantiteReserve > quantiteAlerteReserve
                        AND
                        peremptionNotificationReserve = CURRENT_DATE
                    )
                )
        ;`,{
            idConteneur: conteneur.idConteneur,
        });
        let materielsAlerte = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                RESERVES_MATERIEL e
            WHERE
                idConteneur = :idConteneur
                AND
                (
                    quantiteReserve < quantiteAlerteReserve OR
                    peremptionNotificationReserve < CURRENT_DATE
                )
        ;`,{
            idConteneur: conteneur.idConteneur,
        });

        conteneur.materielsOK = materielsOK[0].nb;
        conteneur.materielsLimites = materielsLimites[0].nb;
        conteneur.materielsAlerte = materielsAlerte[0].nb;

        let inventaires = await db.query(`
            SELECT
                i.*,
                p.identifiant,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                RESERVES_INVENTAIRES i
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne
            WHERE
                idConteneur = :idConteneur
            ORDER BY
                dateInventaire DESC
        ;`,{
            idConteneur: req.body.idConteneur || null,
        });

        res.send({
            conteneur: conteneur,
            inventaires: inventaires,
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addConteneur = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                RESERVES_CONTENEUR
            SET
                libelleConteneur = :libelleConteneur,
                dateDernierInventaire = :dateDernierInventaire,
                frequenceInventaire = :frequenceInventaire,
                dispoBenevoles = false
        `,{
            libelleConteneur: req.body.libelleConteneur || null,
            dateDernierInventaire: req.body.dateDernierInventaire || null,
            frequenceInventaire: req.body.frequenceInventaire || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idConteneur) as idConteneur FROM RESERVES_CONTENEUR;'
        );

        res.status(201);
        res.json({idConteneur: selectLast[0].idConteneur});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateConteneur = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                RESERVES_CONTENEUR
            SET
                idLieu = :idLieu,
                libelleConteneur = :libelleConteneur,
                dateDernierInventaire = :dateDernierInventaire,
                frequenceInventaire = :frequenceInventaire,
                dispoBenevoles = :dispoBenevoles
            WHERE
                idConteneur = :idConteneur
        `,{
            idLieu: req.body.idLieu || null,
            libelleConteneur: req.body.libelleConteneur || null,
            dateDernierInventaire: req.body.dateDernierInventaire || null,
            frequenceInventaire: req.body.frequenceInventaire || null,
            dispoBenevoles: req.body.dispoBenevoles || null,
            idConteneur: req.body.idConteneur || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.reserveConteneurDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.reserveConteneurDelete(req.verifyJWTandProfile.idPersonne , req.body.idConteneur);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}