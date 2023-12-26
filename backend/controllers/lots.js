const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsMail = require('../helpers/fonctionsMail');

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
            let conso = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    LOTS_CONSOMMATION_MATERIEL mat
                    LEFT OUTER JOIN LOTS_CONSOMMATION c ON mat.idConsommation = c.idConsommation
                WHERE
                    mat.idLot = :idLot
                    AND
                    mat.traiteOperateur = false
                    AND
                    c.declarationEnCours = true
            ;`,{
                idLot: lot.idLot,
            });
            lot.consoEnCours = conso[0].nb > 0 ? true : false;
            
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

        let conso = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                LOTS_CONSOMMATION_MATERIEL mat
            WHERE
                mat.idLot = :idLot
                AND
                mat.traiteOperateur = false
        ;`,{
            idLot: lot.idLot,
        });
        lot.consoEnCours = conso[0].nb > 0 ? true : false;

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
                frequenceInventaire = :frequenceInventaire,
                dispoBenevoles = false
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

exports.duplicateLot = async (req, res)=>{
    try {
        const getInitialLot = await db.query(`
            SELECT
                *
            FROM
                LOTS_LOTS
            WHERE
                idLot = :idLot
        `,{
            idLot: req.body.idLot || null,
        });
        let lotSource = getInitialLot[0];
        
        const result = await db.query(`
            INSERT INTO LOTS_LOTS (
                libelleLot,
                idTypeLot,
                idNotificationEnabled,
                idLieu,
                idPersonne,
                dateDernierInventaire,
                frequenceInventaire,
                commentairesLots,
                idVehicule,
                idLotsEtat,
                dispoBenevoles
            )
            SELECT
                :libelleLot as libelleLot,
                idTypeLot,
                idNotificationEnabled,
                idLieu,
                idPersonne,
                dateDernierInventaire,
                frequenceInventaire,
                commentairesLots,
                idVehicule,
                idLotsEtat,
                dispoBenevoles
            FROM
                LOTS_LOTS
            WHERE
                idLot = :idLot
        `,{
            idLot     : req.body.idLot || null,
            libelleLot: req.body.libelleLot || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idLot) as idLot FROM LOTS_LOTS;'
        );
        let idLotDuplicate = selectLast[0].idLot

        const sacs = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_SAC
            WHERE
                idLot = :idLot;
        `,{
            idLot: lotSource.idLot,
        });
        for(const sac of sacs)
        {
            let createSac = await db.query(`
                INSERT INTO
                    MATERIEL_SAC
                SET
                    libelleSac    = :libelleSac,
                    idLot         = :idLot,
                    taille        = :taille,
                    couleur       = :couleur,
                    idFournisseur = :idFournisseur
            `,{
                libelleSac: sac.libelleSac,
                idLot: idLotDuplicate,
                taille: sac.taille,
                couleur: sac.couleur,
                idFournisseur: sac.idFournisseur,
            });

            let createdSac = await db.query(`
                SELECT MAX(idSac) as idSac FROM MATERIEL_SAC;
            `);
            let idSacDuplicate = createdSac[0].idSac;

            const emplacements = await db.query(`
                SELECT
                    *
                FROM
                    MATERIEL_EMPLACEMENT
                WHERE
                    idSac = :idSac;
            `,{
                idSac: sac.idSac,
            });
            for(const emplacement of emplacements)
            {
                let createEmplacement = await db.query(`
                    INSERT INTO
                        MATERIEL_EMPLACEMENT
                    SET
                        libelleEmplacement = :libelleEmplacement,
                        idSac = :idSac
                `,{
                    libelleEmplacement: emplacement.libelleEmplacement,
                    idSac: idSacDuplicate,
                });

                let createdEmplacement = await db.query(`
                    SELECT MAX(idEmplacement) as idEmplacement FROM MATERIEL_EMPLACEMENT;
                `);
                let idEmplacementDuplicate = createdEmplacement[0].idEmplacement;

                const elements = await db.query(`
                    SELECT
                        *
                    FROM
                        MATERIEL_ELEMENT
                    WHERE
                        idEmplacement = :idEmplacement;
                `,{
                    idEmplacement: emplacement.idEmplacement,
                });
                for(const element of elements)
                {
                    let createElement = await db.query(`
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
                            commentairesElement = :commentairesElement,
                            idMaterielsEtat = :idMaterielsEtat
                    `,{
                        idMaterielCatalogue: element.idMaterielCatalogue,
                        idEmplacement: idEmplacementDuplicate,
                        idFournisseur: element.idFournisseur,
                        quantite: element.quantite,
                        quantiteAlerte: element.quantiteAlerte,
                        peremption: element.peremption,
                        peremptionAnticipation: element.peremptionAnticipation,
                        commentairesElement: element.commentairesElement,
                        idMaterielsEtat: element.idMaterielsEtat,
                    });
                }
            }
        }

        await fonctionsMetiers.checkOneConf(idLotDuplicate);

        res.status(201);
        res.json({idLot: idLotDuplicate});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.importRef = async (req, res)=>{
    try {
        for(const materiel of req.body.importArray)
        {
            if(materiel.idEmplacement != null && materiel.quantite != null)
            {
                let insert = await db.query(`
                    INSERT INTO
                        MATERIEL_ELEMENT
                    SET
                        idMaterielCatalogue = :idMaterielCatalogue,
                        idEmplacement = :idEmplacement,
                        quantite = :quantite,
                        quantiteAlerte = :quantiteAlerte,
                        peremption = :peremption
                ;`,{
                    idMaterielCatalogue: materiel.idMaterielCatalogue || null,
                    idEmplacement: materiel.idEmplacement || null,
                    quantite: materiel.quantite || 0,
                    quantiteAlerte: materiel.quantiteAlerte || 0,
                    peremption: materiel.peremption || null,
                });

                let selectLast = await db.query(
                    'SELECT MAX(idElement) as idElement FROM MATERIEL_ELEMENT;'
                );
                await fonctionsMetiers.updateConformiteMaterielOpe(selectLast[0].idElement);
            }
        }

        await fonctionsMetiers.checkOneConf(req.body.idLot);
        res.sendStatus(201);
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
                idLotsEtat = :idLotsEtat,
                dispoBenevoles = :dispoBenevoles
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
            dispoBenevoles: req.body.dispoBenevoles || false,
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
                idLotsAlertesEtat = 2,
                datePriseEnCompteAlerte = CURRENT_TIMESTAMP
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
                idLotsAlertesEtat = 2,
                datePriseEnCompteAlerte = CURRENT_TIMESTAMP
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

        if(req.body.idLotsAlertesEtat == 4 || req.body.idLotsAlertesEtat == 5)
        {
            const result = await db.query(`
                UPDATE
                    LOTS_ALERTES
                SET
                    dateResolutionAlerte = CURRENT_TIMESTAMP
                WHERE
                    idAlerte = :idAlerte
            `,{
                idAlerte: req.body.idAlerte,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//LOTS - Alertes bénévoles Création publique
exports.createAlerte = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                LOTS_ALERTES
            SET
                idLotsAlertesEtat = 1,
                dateCreationAlerte = CURRENT_TIMESTAMP,
                nomDeclarant = :nomDeclarant,
                mailDeclarant = :mailDeclarant,
                idLot = :idLot,
                messageAlerteLot = :messageAlerteLot
        `,{
            nomDeclarant: req.body.nomDeclarant || null,
            mailDeclarant: req.body.mailDeclarant || null,
            idLot: req.body.idLot || null,
            messageAlerteLot: req.body.messageAlerteLot || null,
        });

        let selectLast = await db.query(
            'SELECT MAX(idAlerte) as idAlerte FROM LOTS_ALERTES;'
        );

        if(req.body.mailDeclarant && req.body.mailDeclarant != null && req.body.mailDeclarant != "")
        {
            await fonctionsMail.registerToMailQueue({
                typeMail: 'confirmationAlerteLot',
                idObject: selectLast[0].idAlerte,
                otherMail: req.body.mailDeclarant,
            });
        }
        
        const usersToNotify = await db.query(`
            SELECT
                idPersonne
            FROM
                VIEW_HABILITATIONS
            WHERE
                notif_benevoles_lots = true
                AND
                notifications = true
                AND
                mailPersonne IS NOT NULL
                AND
                mailPersonne <> ""
        `);
        for(const personne of usersToNotify)
        {
            await fonctionsMail.registerToMailQueue({
                typeMail: 'alerteBenevolesLot',
                idPersonne: personne.idPersonne,
                idObject: selectLast[0].idAlerte,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//LOTS - Inventaires
exports.getOneInventaireForDisplay = async (req, res)=>{
    try {
        let inventaire = await db.query(`
            SELECT
                i.*,
                l.libelleLot,
                p.prenomPersonne,
                p.nomPersonne,
                p.identifiant
            FROM
                INVENTAIRES i
                LEFT OUTER JOIN LOTS_LOTS l ON i.idLot = l.idLot
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne
            WHERE
                idInventaire = :idInventaire
        ;`,{
            idInventaire: req.body.idInventaire,
        });

        let contenu = await db.query(`
            SELECT
                i.*,
                c.libelleMateriel,
                cat.libelleCategorie
            FROM
                INVENTAIRES_CONTENUS i
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON i.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN MATERIEL_CATEGORIES cat ON c.idCategorie = cat.idCategorie
            WHERE
                idInventaire = :idInventaire
            ORDER BY
                cat.libelleCategorie,
                c.libelleMateriel
        ;`,{
            idInventaire: req.body.idInventaire,
        });

        res.send({
            inventaire: inventaire[0],
            contenu: contenu,
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.startInventaire = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                INVENTAIRES
            SET
                idLot = :idLot,
                dateInventaire = :dateInventaire,
                idPersonne = :idPersonne,
                inventaireEnCours = true
        `,{
            idLot: req.body.idLot || null,
            dateInventaire: req.body.dateInventaire || null,
            idPersonne: req.body.idPersonne || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idInventaire) as idInventaire FROM INVENTAIRES;'
        );

        const updateLotInv = await db.query(`
            UPDATE
                LOTS_LOTS
            SET
                inventaireEnCours = true
            WHERE
                idLot = :idLot
        `,{
            idLot: req.body.idLot || null,
        });

        if(req.body.isLastInventaire && req.body.isLastInventaire == true)
        {
            const updateLotDate = await db.query(`
                UPDATE
                    LOTS_LOTS
                SET
                    dateDernierInventaire = :dateInventaire
                WHERE
                    idLot = :idLot
            `,{
                idLot: req.body.idLot || null,
                dateInventaire: req.body.dateInventaire || null,
            });
        }

        const intialisationInventaire = await db.query(`
            INSERT INTO
                LOTS_INVENTAIRES_TEMP
            (
                idInventaire,
                idEmplacement,
                idElement,
                idMaterielCatalogue,
                libelleMateriel,
                quantiteAvantInventaire,
                quantiteInventoriee,
                quantiteAlerte,
                peremptionAvantInventaire,
                peremptionInventoriee
            )
            SELECT
                :idInventaire as idInventaire,
                e.idEmplacement,
                e.idElement,
                e.idMaterielCatalogue,
                cat.libelleMateriel,
                e.quantite as quantiteAvantInventaire,
                0 as quantiteInventoriee,
                e.quantiteAlerte,
                e.peremption as peremptionAvantInventaire,
                null as peremptionInventoriee
            FROM
                MATERIEL_ELEMENT e
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT emp ON e.idEmplacement = emp.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON emp.idSac = s.idSac
                LEFT OUTER JOIN MATERIEL_CATALOGUE cat ON e.idMaterielCatalogue = cat.idMaterielCatalogue
            WHERE
                s.idLot = :idLot
        `,{
            idInventaire: selectLast[0].idInventaire,
            idLot: req.body.idLot,
        });

        res.status(201);
        res.json({idInventaire: selectLast[0].idInventaire});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getArborescenceSacs = async (req, res)=>{
    try {
        let idLot = await db.query(`
            SELECT
                idLot
            FROM
                INVENTAIRES
            WHERE
                idInventaire = :idInventaire
        ;`,{
            idInventaire: req.body.idInventaire || null,
        });
        idLot = idLot[0].idLot

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
            idLot: idLot || null,
        });
        for(const sac of sacs)
        {
            let emplacements = await db.query(`
                SELECT
                    *
                FROM
                    MATERIEL_EMPLACEMENT
                WHERE
                    idSac = :idSac
                ORDER BY
                    libelleEmplacement
            ;`,{
                idSac: sac.idSac || null,
            });
            sac.emplacements = emplacements;
        }

        res.send(sacs);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getAllElementsInventaireEnCours = async (req, res)=>{
    try {
        let elements = await db.query(`
            SELECT
                *
            FROM
                LOTS_INVENTAIRES_TEMP
            WHERE
                idInventaire = :idInventaire
        ;`,{
            idInventaire: req.body.idInventaire || null,
        });

        res.send(elements);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.lotsInventaireCancel = async (req, res)=>{
    try {
        let idLot = await db.query(
            'SELECT idLot FROM INVENTAIRES WHERE idInventaire = :idInventaire;'
        ,{
            idInventaire: req.body.idInventaire,
        });
        idLot = idLot[0].idLot;

        const deleteResult = await fonctionsDelete.lotsInventaireDelete(req.verifyJWTandProfile.idPersonne , req.body.idInventaire);
        
        if(deleteResult){
            const updateLotInv = await db.query(`
                UPDATE
                    LOTS_LOTS
                SET
                    inventaireEnCours = false
                WHERE
                    idLot = :idLot
            `,{
                idLot: idLot || null,
            });
            
            res.status(201);
            res.json({idLot: idLot});

        }else{
            res.sendStatus(500);
        }

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.lotsInventaireDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.lotsInventaireDelete(req.verifyJWTandProfile.idPersonne , req.body.idInventaire);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}