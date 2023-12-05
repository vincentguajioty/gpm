const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

//LOTS - Gestion générale
exports.getLots = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                l.*,
                DATE_ADD(l.dateDernierInventaire, INTERVAL frequenceInventaire DAY) as prochainInventaire,
                t.libelleTypeLot,
                n.libelleNotificationEnabled,
                n.notifiationEnabled,
                li.libelleLieu,
                p.identifiant,
                p.prenomPersonne,
                p.nomPersonne,
                v.libelleVehicule,
                e.libelleLotsEtat,
                COUNT(a.idAlerte) as nbAlertesEnCours
            FROM
                LOTS_LOTS l
                LEFT OUTER JOIN LOTS_TYPES t ON t.idTypeLot = l.idTypeLot
                LEFT OUTER JOIN NOTIFICATIONS_ENABLED n ON l.idNotificationEnabled = n.idNotificationEnabled
                LEFT OUTER JOIN LIEUX li ON li.idLieu = l.idLieu
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne
                LEFT OUTER JOIN VEHICULES v ON l.idVehicule = v.idVehicule
                LEFT OUTER JOIN LOTS_ETATS e ON e.idLotsEtat = l.idLotsEtat
                LEFT OUTER JOIN (SELECT * FROM LOTS_ALERTES WHERE dateResolutionAlerte IS NULL) a ON a.idLot = l.idLot
            GROUP BY
                l.idLot
            ORDER BY
                l.libelleLot ASC
        ;`);

        for(const lot of results)
        {
            let materielsOK = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    MATERIEL_ELEMENT e
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                WHERE
                    idLot = :idLot
                    AND
                    (
                        quantite > quantiteAlerte AND
                        (
                            peremptionNotification > CURRENT_DATE
                            OR
                            peremptionNotification IS NULL
                        )
                    )
            ;`,{
                idLot: lot.idLot,
            });
            let materielsLimites = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    MATERIEL_ELEMENT e
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                WHERE
                    idLot = :idLot
                    AND
                    (
                        (
                            quantite = quantiteAlerte
                            AND
                            peremptionNotification = CURRENT_DATE
                        )
                        OR
                        (
                            quantite = quantiteAlerte
                            AND
                            (
                                peremptionNotification > CURRENT_DATE
                                OR
                                peremptionNotification IS NULL
                            )
                        )
                        OR
                        (
                            quantite > quantiteAlerte
                            AND
                            peremptionNotification = CURRENT_DATE
                        )
                    )
            ;`,{
                idLot: lot.idLot,
            });
            let materielsAlerte = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    MATERIEL_ELEMENT e
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                WHERE
                    idLot = :idLot
                    AND
                    (
                        quantite < quantiteAlerte OR
                        peremptionNotification < CURRENT_DATE
                    )
            ;`,{
                idLot: lot.idLot,
            });

            lot.materielsOK = materielsOK[0].nb;
            lot.materielsLimites = materielsLimites[0].nb;
            lot.materielsAlerte = materielsAlerte[0].nb;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneLot = async (req, res)=>{
    try {
        let lot = await db.query(`
            SELECT
                l.*,
                DATE_ADD(l.dateDernierInventaire, INTERVAL frequenceInventaire DAY) as prochainInventaire,
                t.libelleTypeLot,
                n.libelleNotificationEnabled,
                n.notifiationEnabled,
                li.libelleLieu,
                p.identifiant,
                p.prenomPersonne,
                p.nomPersonne,
                v.libelleVehicule,
                e.libelleLotsEtat,
                COUNT(a.idAlerte) as nbAlertesEnCours
            FROM
                LOTS_LOTS l
                LEFT OUTER JOIN LOTS_TYPES t ON t.idTypeLot = l.idTypeLot
                LEFT OUTER JOIN NOTIFICATIONS_ENABLED n ON l.idNotificationEnabled = n.idNotificationEnabled
                LEFT OUTER JOIN LIEUX li ON li.idLieu = l.idLieu
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne
                LEFT OUTER JOIN VEHICULES v ON l.idVehicule = v.idVehicule
                LEFT OUTER JOIN LOTS_ETATS e ON e.idLotsEtat = l.idLotsEtat
                LEFT OUTER JOIN (SELECT * FROM LOTS_ALERTES WHERE dateResolutionAlerte IS NULL) a ON a.idLot = l.idLot
            WHERE
                l.idLot = :idLot
            GROUP BY
                l.idLot
        ;`,{
            idLot: req.body.idLot || null,
        });
        lot = lot[0];

        let materielsOK = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                MATERIEL_ELEMENT e
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
            WHERE
                idLot = :idLot
                AND
                (
                    quantite > quantiteAlerte AND
                    (
                        peremptionNotification > CURRENT_DATE
                        OR
                        peremptionNotification IS NULL
                    )
                )
        ;`,{
            idLot: lot.idLot,
        });
        let materielsLimites = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                MATERIEL_ELEMENT e
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
            WHERE
                idLot = :idLot
                AND
                (
                    (
                        quantite = quantiteAlerte
                        AND
                        peremptionNotification = CURRENT_DATE
                    )
                    OR
                    (
                        quantite = quantiteAlerte
                        AND
                        (
                            peremptionNotification > CURRENT_DATE
                            OR
                            peremptionNotification IS NULL
                        )
                    )
                    OR
                    (
                        quantite > quantiteAlerte
                        AND
                        peremptionNotification = CURRENT_DATE
                    )
                )
        ;`,{
            idLot: lot.idLot,
        });
        let materielsAlerte = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                MATERIEL_ELEMENT e
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
            WHERE
                idLot = :idLot
                AND
                (
                    quantite < quantiteAlerte OR
                    peremptionNotification < CURRENT_DATE
                )
        ;`,{
            idLot: lot.idLot,
        });

        lot.materielsOK = materielsOK[0].nb;
        lot.materielsLimites = materielsLimites[0].nb;
        lot.materielsAlerte = materielsAlerte[0].nb;

        let sacs = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_SAC
            WHERE
                idLot = :idLot
            ORDER BY
                libelleSac
        ;`,{
            idLot: req.body.idLot || null,
        });

        let inventaires = await db.query(`
            SELECT
                i.*,
                p.identifiant,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                INVENTAIRES i
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne
            WHERE
                idLot = :idLot
            ORDER BY
                dateInventaire DESC
        ;`,{
            idLot: req.body.idLot || null,
        });

        let analyseRef = [];
        if(lot.idTypeLot != null && lot.idTypeLot > 0)
        {
            analyseRef = await db.query(`
                SELECT
                    r.idMaterielCatalogue,
                    c.libelleMateriel,
                    c.sterilite,
                    r.quantiteReferentiel
                FROM
                    REFERENTIELS r
                    LEFT OUTER JOIN MATERIEL_CATALOGUE c ON r.idMaterielCatalogue = c.idMaterielCatalogue
                WHERE
                    r.idTypeLot = :idTypeLot
                    AND
                    r.obligatoire = 1
            ;`,{
                idTypeLot: lot.idTypeLot,
            });

            for(const refItem of analyseRef)
            {
                let qttPresentDansLot = await db.query(`
                    SELECT
                        SUM(quantite) as nb
                    FROM
                        MATERIEL_ELEMENT m
                        LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement
                        LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                    WHERE
                        s.idLot = :idLot
                        AND
                        idMaterielCatalogue = :idMaterielCatalogue
                ;`,{
                    idLot: req.body.idLot,
                    idMaterielCatalogue: refItem.idMaterielCatalogue,
                });
                refItem.qttLot = qttPresentDansLot[0].nb;

                let peremptionPresentDansLot = await db.query(`
                    SELECT
                        MIN(peremption) as peremption
                    FROM
                        MATERIEL_ELEMENT m
                        LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement
                        LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                    WHERE
                        s.idLot = :idLot
                        AND
                        idMaterielCatalogue = :idMaterielCatalogue
                ;`,{
                    idLot: req.body.idLot,
                    idMaterielCatalogue: refItem.idMaterielCatalogue,
                });
                refItem.peremptionLot = peremptionPresentDansLot[0].peremption;
            }
        }

        let alertesBenevoles = await db.query(`
                SELECT
                    a.*,
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne,
                    e.libelleLotsAlertesEtat,
                    e.couleurLotsAlertesEtat
                FROM
                    LOTS_ALERTES a
                    LEFT OUTER JOIN LOTS_ALERTES_ETATS e ON a.idLotsAlertesEtat = e.idLotsAlertesEtat
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
                WHERE
                    idLot = :idLot
                ORDER BY
                    a.dateCreationAlerte DESC
            ;`,{
                idLot: req.body.idLot
            });

        res.send({
            lot: lot,
            sacs: sacs,
            inventaires: inventaires,
            analyseRef: analyseRef,
            alertesBenevoles: alertesBenevoles,
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addLot = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                LOTS_LOTS
            SET
                libelleLot = :libelleLot,
                idNotificationEnabled = :idNotificationEnabled,
                idLotsEtat = :idLotsEtat,
                dateDernierInventaire = :dateDernierInventaire,
                frequenceInventaire = :frequenceInventaire
        `,{
            libelleLot: req.body.libelleLot || null,
            idNotificationEnabled: req.body.idNotificationEnabled || null,
            idLotsEtat: req.body.idLotsEtat || null,
            dateDernierInventaire: req.body.dateDernierInventaire || null,
            frequenceInventaire: req.body.frequenceInventaire || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idLot) as idLot FROM LOTS_LOTS;'
        );

        res.status(201);
        res.json({idLot: selectLast[0].idLot});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateLot = async (req, res)=>{
    try {
        let oldRecord = await db.query(`
            SELECT
                *
            FROM
                LOTS_LOTS
            WHERE
                idLot = :idLot
        ;`,{
            idLot: req.body.idLot
        });
        oldRecord = oldRecord[0];
        
        const result = await db.query(`
            UPDATE
                LOTS_LOTS
            SET
                libelleLot = :libelleLot,
                idTypeLot = :idTypeLot,
                idNotificationEnabled = :idNotificationEnabled,
                idLieu = :idLieu,
                idPersonne = :idPersonne,
                dateDernierInventaire = :dateDernierInventaire,
                frequenceInventaire = :frequenceInventaire,
                commentairesLots = :commentairesLots,
                idVehicule = :idVehicule,
                idLotsEtat = :idLotsEtat
            WHERE
                idLot = :idLot
        `,{
            libelleLot: req.body.libelleLot || null,
            idTypeLot: req.body.idTypeLot || null,
            idNotificationEnabled: req.body.idNotificationEnabled || null,
            idLieu: req.body.idLieu || null,
            idPersonne: req.body.idPersonne || null,
            dateDernierInventaire: req.body.dateDernierInventaire || null,
            frequenceInventaire: req.body.frequenceInventaire || null,
            commentairesLots: req.body.commentairesLots || null,
            idVehicule: req.body.idVehicule || null,
            idLotsEtat: req.body.idLotsEtat || null,
            idLot: req.body.idLot || null,
        });

        if(oldRecord.idTypeLot != req.body.idTypeLot)
        {
            await fonctionsMetiers.checkOneConf(req.body.idLot);
        }
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.lotsDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.lotsDelete(req.verifyJWTandProfile.idPersonne , req.body.idLot);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//LOTS - Alertes bénévoles
exports.getLotsAlertes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                a.*,
                e.libelleLotsAlertesEtat,
                e.couleurLotsAlertesEtat,
                v.libelleLot,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                LOTS_ALERTES a
                LEFT OUTER JOIN LOTS_ALERTES_ETATS e ON a.idLotsAlertesEtat = e.idLotsAlertesEtat
                LEFT OUTER JOIN LOTS_LOTS v ON a.idLot = v.idLot
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
            ORDER BY
                a.dateCreationAlerte DESC
        ;`);

        if(req.body.idLot && req.body.idLot != null && req.body.idLot > 0)
        {
            results = results.filter(alerte => alerte.idLot == req.body.idLot)
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.autoAffect = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                LOTS_ALERTES
            SET
                idTraitant = :idTraitant,
                idLotsAlertesEtat = 2
            WHERE
                idAlerte = :idAlerte
        `,{
            idTraitant : req.verifyJWTandProfile.idPersonne,
            idAlerte: req.body.idAlerte,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.affectationTier = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                LOTS_ALERTES
            SET
                idTraitant = :idTraitant,
                idLotsAlertesEtat = 2
            WHERE
                idAlerte = :idAlerte
        `,{
            idTraitant : req.body.idTraitant,
            idAlerte: req.body.idAlerte,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.udpateStatut = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                LOTS_ALERTES
            SET
                idLotsAlertesEtat = :idLotsAlertesEtat
            WHERE
                idAlerte = :idAlerte
        `,{
            idAlerte: req.body.idAlerte,
            idLotsAlertesEtat: req.body.idLotsAlertesEtat,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}