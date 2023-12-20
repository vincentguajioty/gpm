const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsLDAP = require('./fonctionsLDAP');
const moment = require('moment');

const majLdapOneUser = async (idPersonne) => {
    try {
        logger.debug('Fonction majLdapOneUser pour idPersonne: '+idPersonne);
        
        let oneUser = await db.query(
            `SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;`,
        {
            idPersonne : idPersonne
        });
        if(oneUser[0].isActiveDirectory)
        {
            await fonctionsLDAP.updateProfilsFromAd(idPersonne);
        }
    } catch (error) {
        logger.error(error);
    }
}

const majLdapAllUsers = async () => {
    try {
        logger.debug('Fonction majLdapAllUsers');
        if(process.env.LDAP_ENABLED == "1")
        {
            logger.debug("LDAP activé, lancement de l'update")

            let utilisateursLDAP = await db.query(`SELECT idPersonne FROM PERSONNE_REFERENTE WHERE isActiveDirectory = 1;`);
            for (const user of utilisateursLDAP) {
                let updateUnitaire = await majLdapOneUser(user.idPersonne);
            }
        }
        else
        {
            logger.debug("LDAP désactivé, pas d'action faite")
        }
    } catch (error) {
        logger.error(error);
    }
}

const deconnecterUtilisateur = async (idPersonne) => {
    try {
        const revoquerRefreshToken = await db.query(`
            INSERT INTO
                JWT_SESSIONS_BLACKLIST (blockedDateTime, jwtToken)
            SELECT
                CURRENT_TIMESTAMP as blockedDateTime,
                jwtRefreshToken as jwtToken
            FROM
                JWT_SESSIONS
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: idPersonne
        });

        const revoquerToken = await db.query(`
            INSERT INTO
                JWT_SESSIONS_BLACKLIST (blockedDateTime, jwtToken)
            SELECT
                CURRENT_TIMESTAMP as blockedDateTime,
                jwtToken
            FROM
                JWT_SESSIONS
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: idPersonne
        });

        const supprimerListeActive = await db.query(`
            DELETE FROM
                JWT_SESSIONS
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: idPersonne
        });

    } catch (error) {
        logger.error(error);
    }
}

const deconnecterProfil = async (idProfil) => {
    try {
        const getAllUsers = await db.query(`
            SELECT DISTINCT
                idPersonne
            FROM
                PROFILS_PERSONNES
            WHERE
                idProfil = :idProfil
        `,{
            idProfil: idProfil
        });

        for(const user of getAllUsers)
        {
            let idPersonne = user.idPersonne;
            await deconnecterUtilisateur(idPersonne);
        }

    } catch (error) {
        logger.error(error);
    }
}

const deconnecterToutLeMonde = async () => {
    try {
        const revoquerRefreshToken = await db.query(`
            INSERT INTO
                JWT_SESSIONS_BLACKLIST (blockedDateTime, jwtToken)
            SELECT
                CURRENT_TIMESTAMP as blockedDateTime,
                jwtRefreshToken as jwtToken
            FROM
                JWT_SESSIONS
        `);

        const revoquerToken = await db.query(`
            INSERT INTO
                JWT_SESSIONS_BLACKLIST (blockedDateTime, jwtToken)
            SELECT
                CURRENT_TIMESTAMP as blockedDateTime,
                jwtToken
            FROM
                JWT_SESSIONS
        `);

        const supprimerListeActive = await db.query(`
            TRUNCATE JWT_SESSIONS
        `);
    } catch (error) {
        logger.error(error);
    }
}

const updateLastConnexion = async (idPersonne) => {
    try {
        const update = await db.query(`
            UPDATE
                PERSONNE_REFERENTE
            SET
                derniereConnexion = CURRENT_TIMESTAMP
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne : idPersonne,
        });
    } catch (error) {
        logger.error(error);
    }
}

const checkPwdReinitEnable = () => {
    return async function(req, res, next) {
        const result = await db.query(
            `SELECT
                resetPassword
            FROM
                CONFIG
        `);
        if(result[0].resetPassword == true)
        {
            next();
        }
        else
        {
            res.status(401);
            res.json({auth: false, message: "La fonction est désactivée"});
        }
    }
}

const clearPwdReinitTable = async () => {
    try {
        const revoquerRefreshToken = await db.query(`
            TRUNCATE TABLE RESETPASSWORD;
        `);

    } catch (error) {
        logger.error(error);
    }
}

const unlockLotsInventaires = async () => {
    try {
        const update = await db.query(`UPDATE LOTS_LOTS SET inventaireEnCours = Null;`);
    } catch (error) {
        logger.error(error)
    }
}

const unlockReservesInventaires = async () => {
    try {
        const update = await db.query(`UPDATE RESERVES_CONTENEUR SET inventaireEnCours = Null;`);
    } catch (error) {
        logger.error(error)
    }
}

const updatePeremptionsAnticipations = async () => {
    try {
        logger.info("Début de la mise à jour de la table de matériel opérationnel pour mise à jour du champ d'anticipation des péremption conformément au catalogue.", {idPersonne: 'SYSTEM'})
        const updateOpe = await db.query(`UPDATE MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue SET e.peremptionAnticipation = c.peremptionAnticipationOpe WHERE c.peremptionAnticipationOpe IS NOT NULL;`);
        logger.info("Fin de la mise à jour de la table de matériel opérationnel pour mise à jour du champ d'anticipation des péremption conformément au catalogue.", {idPersonne: 'SYSTEM'})

        logger.info("Début de la mise à jour de la table de matériel en reserve pour mise à jour du champ d'anticipation des péremption conformément au catalogue.", {idPersonne: 'SYSTEM'})
        const updateRes = await db.query(`UPDATE RESERVES_MATERIEL e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue SET e.peremptionReserveAnticipation = c.peremptionAnticipationRes WHERE c.peremptionAnticipationRes IS NOT NULL;`);
        logger.info("Fin de la mise à jour de la table de matériel en reserve pour mise à jour du champ d'anticipation des péremption conformément au catalogue.", {idPersonne: 'SYSTEM'})
    } catch (error) {
        logger.error(error)
    }
}

const notificationsMAJpersonne = async () => {
    try {
        logger.info("Début de la mise à jour des conditions de notification dans la table des personnes.", {idPersonne: 'SYSTEM'})

        const personnes = await db.query(`
            SELECT
                p.idPersonne,
                MAX(actifCeJour) as actifCeJour
            FROM
                PERSONNE_REFERENTE p
                LEFT OUTER JOIN NOTIFICATIONS_ABONNEMENTS n ON p.idPersonne = n.idPersonne
                LEFT OUTER JOIN NOTIFICATIONS_CONDITIONS c ON n.idCondition = c.idCondition
            GROUP BY
                p.idPersonne
        `);

        for(const personne of personnes)
        {
            const update = await db.query(`
            UPDATE
                PERSONNE_REFERENTE
            SET
                notifications_abo_cejour = :actifCeJour
            WHERE
                idPersonne = :idPersonne
            `,{
                actifCeJour: personne.actifCeJour,
                idPersonne: personne.idPersonne,
            });
        }

        logger.info("Fin de la mise à jour des conditions de notification dans la table des personnes.", {idPersonne: 'SYSTEM'})
    } catch (error) {
        logger.error(error)
    }
}

const notificationsConditionsMAJ = async () => {
    try {
        logger.info("Début de la mise à jour de la table des conditions de notification.", {idPersonne: 'SYSTEM'})

        const currentDate = new Date();

        let update = await db.query(`
            UPDATE NOTIFICATIONS_CONDITIONS SET actifCeJour = 0;
        `);

        update = await db.query(`
            UPDATE NOTIFICATIONS_CONDITIONS SET actifCeJour = 1 WHERE idCondition = 1;
        `);

        update = await db.query(`
            UPDATE
                NOTIFICATIONS_CONDITIONS
            SET
                actifCeJour = 1
            WHERE
                codeCondition = :numJour
                OR
                UPPER(codeCondition) = UPPER(:nomJour)
        `,{
            numJour: moment(currentDate).format("D"),
            nomJour: moment(currentDate).format("dddd"),
        });
        
        logger.info("Fin de la mise à jour de la table des conditions de notification.", {idPersonne: 'SYSTEM'})
    } catch (error) {
        logger.error(error)
    }
}

const checkVehiculeMaintenance = async (idVehicule) => {
    try {
        let erreurs = 0;
        
        let alertes = await db.query(`
            SELECT
                *
            FROM
                VEHICULES_HEALTH_ALERTES
            WHERE
                idVehicule = :idVehicule
        `,{
            idVehicule : idVehicule,
        });

        for (const alerte of alertes)
        {
            let last = await db.query(`
                SELECT
                    MAX(dateHealth) as dateHealth,
                    DATE_ADD(MAX(dateHealth), INTERVAL :frequenceHealth DAY) as nextMaintenance
                FROM
                    VEHICULES_HEALTH_CHECKS c
                    LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth
                WHERE
                    idVehicule = :idVehicule
                    AND
                    idHealthType = :idHealthType
            `,{
                idVehicule: idVehicule,
                idHealthType: alerte.idHealthType,
                frequenceHealth: alerte.frequenceHealth,
            });

            if(last.length > 0 && last[0].dateHealth != null)
            {
                if(new Date(last[0].nextMaintenance) <= new Date())
                {
                    erreurs += 1;
                }
            }else{
                erreurs += 1;
            }
        }

        if(erreurs == 0)
        {
            return 0;
        }else{
            return 1;
        }

    } catch (error) {
        logger.error(error)
    }
}

const checkAllMaintenance = async () => {
    try {
        logger.info("Lancement de la vérification des maintenance de tous les véhicules.", {idPersonne: 'SYSTEM'})

        let allVehicules = await db.query(`
            SELECT * FROM VEHICULES;
        `);
        for(const vehicule of allVehicules)
        {
            await checkOneMaintenance(vehicule.idVehicule)
        }
        
        logger.info("Fin de la vérification des maintenance de tous les véhicules.", {idPersonne: 'SYSTEM'})
    } catch (error) {
        logger.error(error)
    }
}

const checkOneMaintenance = async (idVehicule) => {
    try {
        logger.info("Lancement de la vérification des maintenance du véhicule "+idVehicule, {idPersonne: 'SYSTEM'})

        let alertes = await db.query(`
            SELECT COUNT(*) as nb FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule;
        `,{
            idVehicule: idVehicule,
        });

        if(alertes[0].nb > 0)
        {
            if (await checkVehiculeMaintenance(idVehicule) == 1)
            {
                logger.info("Véhicule "+idVehicule+" contrôlé en erreur de maintenance.", {idPersonne: 'SYSTEM'})
                let update = await db.query(`
                    UPDATE VEHICULES SET alerteMaintenance = True WHERE idVehicule = :idVehicule;
                `,{
                    idVehicule: idVehicule,
                });
            }
            else
            {
                logger.info("Véhicule "+idVehicule+" contrôlé en maintenance ok.", {idPersonne: 'SYSTEM'})
                let update = await db.query(`
                    UPDATE VEHICULES SET alerteMaintenance = false WHERE idVehicule = :idVehicule;
                `,{
                    idVehicule: idVehicule,
                });
            }
        }
        else
        {
            logger.warn("Vérification des maintenance du vehicule "+idVehicule+" impossible car aucune alerte paramétrée.", {idPersonne: 'SYSTEM'})
            let update = await db.query(`
                UPDATE VEHICULES SET alerteMaintenance = null WHERE idVehicule = :idVehicule;
            `,{
                idVehicule: idVehicule,
            });
        }
        
        logger.info("Fin de la vérification des maintenance du véhicule "+idVehicule, {idPersonne: 'SYSTEM'})
    } catch (error) {
        logger.error(error)
    }
}

const checkVehiculeDesinfection = async (idVehicule) => {
    try {
        let erreurs = 0;
        
        let alertes = await db.query(`
            SELECT
                *
            FROM
                VEHICULES_DESINFECTIONS_ALERTES
            WHERE
                idVehicule = :idVehicule
        `,{
            idVehicule : idVehicule,
        });

        for (const alerte of alertes)
        {
            let last = await db.query(`
                SELECT
                    MAX(dateDesinfection) as dateDesinfection,
                    DATE_ADD(MAX(dateDesinfection), INTERVAL :frequenceDesinfection DAY) as nextDesinfection
                FROM
                    VEHICULES_DESINFECTIONS
                WHERE
                    idVehicule = :idVehicule
                    AND
                    idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
            `,{
                idVehicule: idVehicule,
                idVehiculesDesinfectionsType: alerte.idVehiculesDesinfectionsType,
                frequenceDesinfection: alerte.frequenceDesinfection,
            });

            if(last.length > 0 && last[0].dateDesinfection != null)
            {
                if(new Date(last[0].nextDesinfection) <= new Date())
                {
                    erreurs += 1;
                }
            }else{
                erreurs += 1;
            }
        }

        if(erreurs == 0)
        {
            return 0;
        }else{
            return 1;
        }

    } catch (error) {
        logger.error(error)
    }
}

const checkAllDesinfection = async () => {
    try {
        logger.info("Lancement de la vérification des désinfections de tous les véhicules.", {idPersonne: 'SYSTEM'})

        let allVehicules = await db.query(`
            SELECT * FROM VEHICULES;
        `);
        for(const vehicule of allVehicules)
        {
            await checkOneDesinfection(vehicule.idVehicule)
        }
        
        logger.info("Fin de la vérification des désinfections de tous les véhicules.", {idPersonne: 'SYSTEM'})
    } catch (error) {
        logger.error(error)
    }
}

const checkOneDesinfection = async (idVehicule) => {
    try {
        logger.info("Lancement de la vérification des désinfections du véhicule "+idVehicule, {idPersonne: 'SYSTEM'})

        let alertes = await db.query(`
            SELECT COUNT(*) as nb FROM VEHICULES_DESINFECTIONS_ALERTES WHERE idVehicule = :idVehicule;
        `,{
            idVehicule: idVehicule,
        });

        if(alertes[0].nb > 0)
        {
            if (await checkVehiculeDesinfection(idVehicule) == 1)
            {
                logger.info("Véhicule "+idVehicule+" contrôlé en erreur de désinfections.", {idPersonne: 'SYSTEM'})
                let update = await db.query(`
                    UPDATE VEHICULES SET alerteDesinfection = True WHERE idVehicule = :idVehicule;
                `,{
                    idVehicule: idVehicule,
                });
            }
            else
            {
                logger.info("Véhicule "+idVehicule+" contrôlé en désinfections ok.", {idPersonne: 'SYSTEM'})
                let update = await db.query(`
                    UPDATE VEHICULES SET alerteDesinfection = false WHERE idVehicule = :idVehicule;
                `,{
                    idVehicule: idVehicule,
                });
            }
        }
        else
        {
            logger.warn("Vérification des désinfections du vehicule "+idVehicule+" impossible car aucune alerte paramétrée.", {idPersonne: 'SYSTEM'})
            let update = await db.query(`
                UPDATE VEHICULES SET alerteDesinfection = null WHERE idVehicule = :idVehicule;
            `,{
                idVehicule: idVehicule,
            });
        }
        
        logger.info("Fin de la vérification des désinfections du véhicule "+idVehicule, {idPersonne: 'SYSTEM'})
    } catch (error) {
        logger.error(error)
    }
}

const checkLotsConf = async (idLot) => {
    try {
        logger.info("Vérification de conformité du lot "+idLot, {idPersonne: 'SYSTEM'})
        let errorsConf = 0;

        let lot = await db.query(`
            SELECT idTypeLot FROM LOTS_LOTS WHERE idLot = :idLot;
        `,{
            idLot: idLot,
        });

        let referentiels = await db.query(`
            SELECT
                *
            FROM
                REFERENTIELS r
                LEFT OUTER JOIN LOTS_TYPES t on r.idTypeLot=t.idTypeLot
                LEFT OUTER JOIN MATERIEL_CATALOGUE m on r.idMaterielCatalogue = m.idMaterielCatalogue
            WHERE
                r.idTypeLot = :idTypeLot
        `,{
            idTypeLot: lot[0].idTypeLot,
        });
        for(const referentiel of referentiels)
        {
            let dateMateriel = await db.query(`
                SELECT
                    MIN(peremption) as peremption
                FROM
                    MATERIEL_ELEMENT e
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac = s.idSac
                    LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
                    AND
                    s.idLot = :idLot;
            `,{
                idMaterielCatalogue: referentiel.idMaterielCatalogue,
                idLot: idLot,
            });
            dateMateriel = dateMateriel[0];

            if ((referentiel.obligatoire) && (referentiel.sterilite) && (new Date(dateMateriel.peremption) < new Date()))
            {
                errorsConf += 1;
            }
            if ((referentiel.obligatoire) && (referentiel.sterilite) && (dateMateriel.peremption == 0))
            {
                errorsConf += 1;
            }
            if ((referentiel.obligatoire) && (referentiel.sterilite) && (dateMateriel.peremption == '0000-00-00'))
            {
                errorsConf += 1;
            }
            if ((referentiel.obligatoire) && (referentiel.sterilite) && (dateMateriel.peremption == null))
            {
                errorsConf += 1;
            }


            let quantiteMateriel = await db.query(`
                SELECT
                    SUM(quantite) as nb
                FROM
                    MATERIEL_ELEMENT e
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac = s.idSac
                    LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                WHERE
                    idMaterielCatalogue = :idMaterielCatalogue
                    AND
                    s.idLot = :idLot
                ;
            `,{
                idMaterielCatalogue: referentiel.idMaterielCatalogue,
                idLot: idLot,
            });
            quantiteMateriel = quantiteMateriel[0];

            if ((referentiel.obligatoire) && (quantiteMateriel.nb < referentiel.quantiteReferentiel))
            {
                errorsConf += 1;
            }
        }

        if(errorsConf > 0)
        {
            logger.info("Lot "+idLot+" vérifié non-conforme", {idPersonne: 'SYSTEM'})
            return 1;
        }
        else
        {
            logger.info("Lot "+idLot+" vérifié conforme", {idPersonne: 'SYSTEM'})
            return 0;
        }
        
        
    } catch (error) {
        logger.error(error)
    }
}

const checkOneConf = async (idLot) => {
    try {
        let lots = await db.query(`
            SELECT * FROM LOTS_LOTS WHERE idLot = :idLot;
        `,{
            idLot: idLot
        });

        for(const lot of lots)
        {
            if(lot.idTypeLot > 0)
            {
                if(await checkLotsConf(lot.idLot) == 1)
                {
                    let update = await db.query(`
                        UPDATE LOTS_LOTS SET alerteConfRef = True WHERE idLot = :idLot;
                    `,{
                        idLot: lot.idLot
                    });
                }
                else
                {
                    let update = await db.query(`
                        UPDATE LOTS_LOTS SET alerteConfRef = False WHERE idLot = :idLot;
                    `,{
                        idLot: lot.idLot
                    });
                }
            }
            else
            {
                let update = await db.query(`
                    UPDATE LOTS_LOTS SET alerteConfRef = null WHERE idLot = :idLot;
                `,{
                    idLot: lot.idLot
                });
            }
        }
    } catch (error) {
        logger.error(error)
    }
}

const checkAllConf = async () => {
    try {
        logger.info("Lancement de la vérification de conformité de tous les lots.", {idPersonne: 'SYSTEM'})

        let lots = await db.query(`
            SELECT * FROM LOTS_LOTS;
        `);
        for(const lot of lots)
        {
            await checkOneConf(lot.idLot)
        }
        
        logger.info("Fin de la vérification de conformité de tous les lots.", {idPersonne: 'SYSTEM'})
    } catch (error) {
        logger.error(error)
    }
}

const updateConformiteMaterielOpe = async (idElement) => {
    try {
        let materiel = await db.query(`
            SELECT
                *
            FROM
                MATERIEL_ELEMENT m
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
            WHERE
                idElement = :idElement
        ;`,{
            idElement : idElement
        });
        materiel = materiel[0];

        if(materiel.peremptionAnticipationOpe != null)
        {
            let updateAnticipation = await db.query(`
                UPDATE
                    MATERIEL_ELEMENT
                SET
                    peremptionAnticipation = :peremptionAnticipation
                WHERE
                    idElement = :idElement
            ;`,{
                idElement : idElement,
                peremptionAnticipation: materiel.peremptionAnticipationOpe,
            });
        }
        
    } catch (error) {
        logger.error(error)
    }
}

const cnilAnonyme = async (idPersonne) => {
    try {
        let personne = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_REFERENTE
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: idPersonne
        });
        personne = personne[0];

        let disableAD = await db.query(`
            UPDATE
                PERSONNE_REFERENTE
            SET
                isActiveDirectory = false
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: idPersonne
        });

        let profilCleaning = await db.query(`
            DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne
        `,{
            idPersonne: idPersonne
        });

        let cmdTimeLineCleaning = await db.query(`
            UPDATE
                COMMANDES_TIMELINE
            SET
                detailsEvtCommande = REPLACE(detailsEvtCommande, :identifiant, :anonyme)
        `,{
            identifiant: personne.identifiant,
            anonyme: "ANONYME "+personne.idPersonne,
        });

        let updateUser = await db.query(`
            UPDATE
                PERSONNE_REFERENTE
            SET
                identifiant = CONCAT("ANONYME ", idPersonne),
                nomPersonne = "ANONYME",
                prenomPersonne = "ANONYME",
                mailPersonne = Null,
                telPersonne = Null,
                cnil_anonyme = true
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: idPersonne
        });

        await majIndicateursPersonne(idPersonne,true);
        await majNotificationsPersonne(idPersonne,true);
        await majValideursPersonne(true);

    } catch (error) {
        logger.error(error)
    }
}

const cnilAnonymeCron = async () => {
    try {

        let users = await db.query(`
            SELECT
                vue.*
            FROM
                (SELECT
                    p.idPersonne,
                    p.identifiant,
                    p.derniereConnexion,
                    COUNT(pp.idProfil) as nbProfil
                FROM
                    PERSONNE_REFERENTE p
                    LEFT OUTER JOIN PROFILS_PERSONNES pp ON p.idPersonne = pp.idPersonne
                WHERE
                    p.cnil_anonyme = false
                GROUP BY
                    p.idPersonne
                ) vue
            WHERE
                vue.nbProfil = 0
                AND
                DATE_ADD(vue.derniereConnexion, INTERVAL 3 YEAR) <= NOW()
        `);
        for(const user of users)
        {
            logger.info("CRON - Anonymisation de l'utilisateur "+user.idPersonne+" - "+user.identifiant, {idPersonne: 'SYSTEM'})
            await cnilAnonyme(user.idPersonne);
        }
        
    } catch (error) {
        logger.error(error)
    }
}

const replaceString = async (string, replacementArray) => {
    try {
        Object.keys(replacementArray).forEach(key => {
            let toSearch = ":"+key;
            string = string.replaceAll(toSearch, replacementArray[key]);
        })
        return string;
    } catch (error) {
        logger.error(error)
    }
}

const updateInventaireLotItem = async (element) => {
    try {
        let updateQuery = await db.query(`
            UPDATE
                LOTS_INVENTAIRES_TEMP
            SET
                quantiteInventoriee = :quantiteInventoriee,
                peremptionInventoriee = :peremptionInventoriee
            WHERE
                idElement = :idElement
        `,{
            quantiteInventoriee : element.quantiteInventoriee || null,
            peremptionInventoriee : element.peremptionInventoriee || null,
            idElement : element.idElement || null,
        });

    } catch (error) {
        logger.error(error)
    }
}

const validerInventaireLot = async (idInventaire, commentairesInventaire) => {
    try {
        let getLot = await db.query(`
            SELECT
                idLot
            FROM
                INVENTAIRES
            WHERE
                idInventaire = :idInventaire
        `,{
            idInventaire: idInventaire
        });
        idLot = getLot[0].idLot;
        
        let elementsDeInventaire = await db.query(`
            SELECT
                *
            FROM
                LOTS_INVENTAIRES_TEMP
            WHERE
                idInventaire = :idInventaire
        `,{
            idInventaire: idInventaire
        });
        for(const element of elementsDeInventaire)
        {
            let updateMateriel = await db.query(`
                UPDATE
                    MATERIEL_ELEMENT
                SET
                    quantite = :quantite,
                    peremption = :peremption
                WHERE
                    idElement = :idElement
            `,{
                idElement: element.idElement,
                quantite: element.quantiteInventoriee || 0,
                peremption: element.peremptionInventoriee || null,
            });
        }

        await figeInventaireLot(idLot, idInventaire);

        let cleanInventaireTemp = await db.query(`
            DELETE FROM
                LOTS_INVENTAIRES_TEMP
            WHERE
                idInventaire = :idInventaire
        `,{
            idInventaire: idInventaire
        });

        let unlockInventaire = await db.query(`
            UPDATE
                INVENTAIRES
            SET
                inventaireEnCours = false,
                commentairesInventaire = :commentairesInventaire
            WHERE
                idInventaire = :idInventaire
        `,{
            idInventaire: idInventaire,
            commentairesInventaire: commentairesInventaire,
        });

        let unlockLot = await db.query(`
            UPDATE
                LOTS_LOTS
            SET
                inventaireEnCours = false
            WHERE
                idLot = :idLot
        `,{
            idLot: idLot,
        });

        await checkOneConf(idLot);

    } catch (error) {
        logger.error(error)
    }
}

const figeInventaireLot = async (idLot, idInventaire) => {
    try {
        let fige = await db.query(`
            INSERT INTO
                INVENTAIRES_CONTENUS(idInventaire, idMaterielCatalogue, quantiteInventaire, peremptionInventaire)
            SELECT
                :idInventaire as idInventaire,
                e.idMaterielCatalogue as idMaterielCatalogue,
                SUM(e.quantite) as quantiteInventaire,
                MIN(e.peremption) as peremptionInventaire
            FROM
                MATERIEL_ELEMENT e
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT emp ON e.idEmplacement = emp.idEmplacement
                LEFT OUTER JOIN MATERIEL_SAC s ON emp.idSac = s.idSac
            WHERE
                s.idLot = :idLot
            GROUP BY
                e.idMaterielCatalogue
        `,{
            idInventaire: idInventaire,
            idLot:        idLot,
        });
                
    } catch (error) {
        logger.error(error)
    }
}

const updateInventaireReserveItem = async (element) => {
    try {
        let updateQuery = await db.query(`
            UPDATE
                RESERVES_INVENTAIRES_TEMP
            SET
                quantiteInventoriee = :quantiteInventoriee,
                peremptionInventoriee = :peremptionInventoriee
            WHERE
                idReserveElement = :idReserveElement
        `,{
            quantiteInventoriee : element.quantiteInventoriee || null,
            peremptionInventoriee : element.peremptionInventoriee || null,
            idReserveElement : element.idReserveElement || null,
        });

    } catch (error) {
        logger.error(error)
    }
}

const validerInventaireReserve = async (idReserveInventaire, commentairesInventaire) => {
    try {
        let getLot = await db.query(`
            SELECT
                idConteneur
            FROM
                RESERVES_INVENTAIRES
            WHERE
                idReserveInventaire = :idReserveInventaire
        `,{
            idReserveInventaire: idReserveInventaire
        });
        idConteneur = getLot[0].idConteneur;
        
        let elementsDeInventaire = await db.query(`
            SELECT
                *
            FROM
                RESERVES_INVENTAIRES_TEMP
            WHERE
                idReserveInventaire = :idReserveInventaire
        `,{
            idReserveInventaire: idReserveInventaire
        });
        for(const element of elementsDeInventaire)
        {
            let updateMateriel = await db.query(`
                UPDATE
                    RESERVES_MATERIEL
                SET
                    quantiteReserve = :quantiteReserve,
                    peremptionReserve = :peremptionReserve
                WHERE
                    idReserveElement = :idReserveElement
            `,{
                idReserveElement: element.idReserveElement,
                quantiteReserve: element.quantiteInventoriee || 0,
                peremptionReserve: element.peremptionInventoriee || null,
            });
        }

        await figeInventaireReserve(idConteneur, idReserveInventaire);

        let cleanInventaireTemp = await db.query(`
            DELETE FROM
                RESERVES_INVENTAIRES_TEMP
            WHERE
                idReserveInventaire = :idReserveInventaire
        `,{
            idReserveInventaire: idReserveInventaire
        });

        let unlockInventaire = await db.query(`
            UPDATE
                RESERVES_INVENTAIRES
            SET
                inventaireEnCours = false,
                commentairesInventaire = :commentairesInventaire
            WHERE
                idReserveInventaire = :idReserveInventaire
        `,{
            idReserveInventaire: idReserveInventaire,
            commentairesInventaire: commentairesInventaire,
        });

        let unlockLot = await db.query(`
            UPDATE
                RESERVES_CONTENEUR
            SET
                inventaireEnCours = false
            WHERE
                idConteneur = :idConteneur
        `,{
            idConteneur: idConteneur,
        });

    } catch (error) {
        logger.error(error)
    }
}

const figeInventaireReserve = async (idConteneur, idReserveInventaire) => {
    try {
        let fige = await db.query(`
            INSERT INTO
                RESERVES_INVENTAIRES_CONTENUS(idReserveInventaire, idMaterielCatalogue, quantiteInventaire, peremptionInventaire)
            SELECT
                :idReserveInventaire as idReserveInventaire,
                idMaterielCatalogue as idMaterielCatalogue,
                SUM(quantiteReserve) as quantiteInventaire,
                MIN(peremptionReserve) as peremptionInventaire
            FROM
                RESERVES_MATERIEL
            WHERE
                idConteneur = :idConteneur
            GROUP BY
                idMaterielCatalogue
        `,{
            idConteneur:         idConteneur,
            idReserveInventaire: idReserveInventaire,
        });
                
    } catch (error) {
        logger.error(error)
    }
}

const majIndicateursPersonne = async (idPersonne, enableLog) => {
    try {
        let personne = await db.query(`
            SELECT * FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;
        `,{
            idPersonne: idPersonne,
        });
        personne = personne[0];

        conf_indicateur1Accueil = (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.conf_indicateur1Accueil);
        conf_indicateur2Accueil = (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.conf_indicateur2Accueil);
        conf_indicateur3Accueil = (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.conf_indicateur3Accueil);
        conf_indicateur4Accueil = (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.conf_indicateur4Accueil);
        conf_indicateur5Accueil = (personne.reserve_lecture) && (personne.conf_indicateur5Accueil);
        conf_indicateur6Accueil = (personne.reserve_lecture) && (personne.conf_indicateur6Accueil);
        conf_indicateur9Accueil = (personne.tenues_lecture || personne.tenuesCatalogue_lecture) && (personne.conf_indicateur9Accueil);
        conf_indicateur10Accueil = (personne.tenues_lecture || personne.tenuesCatalogue_lecture) && (personne.conf_indicateur10Accueil);
        conf_indicateur11Accueil = (personne.desinfections_lecture) && (personne.conf_indicateur11Accueil);
        conf_indicateur12Accueil = (personne.vehiculeHealth_lecture) && (personne.conf_indicateur12Accueil);

        let update = await db.query(`
            UPDATE
                PERSONNE_REFERENTE
            SET
                conf_indicateur1Accueil  = :conf_indicateur1Accueil,
                conf_indicateur2Accueil  = :conf_indicateur2Accueil,
                conf_indicateur3Accueil  = :conf_indicateur3Accueil,
                conf_indicateur4Accueil  = :conf_indicateur4Accueil,
                conf_indicateur5Accueil  = :conf_indicateur5Accueil,
                conf_indicateur6Accueil  = :conf_indicateur6Accueil,
                conf_indicateur9Accueil  = :conf_indicateur9Accueil,
                conf_indicateur10Accueil = :conf_indicateur10Accueil,
                conf_indicateur11Accueil = :conf_indicateur11Accueil,
                conf_indicateur12Accueil = :conf_indicateur12Accueil
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne              : idPersonne,
            conf_indicateur1Accueil : conf_indicateur1Accueil,
            conf_indicateur2Accueil : conf_indicateur2Accueil,
            conf_indicateur3Accueil : conf_indicateur3Accueil,
            conf_indicateur4Accueil : conf_indicateur4Accueil,
            conf_indicateur5Accueil : conf_indicateur5Accueil,
            conf_indicateur6Accueil : conf_indicateur6Accueil,
            conf_indicateur9Accueil : conf_indicateur9Accueil,
            conf_indicateur10Accueil: conf_indicateur10Accueil,
            conf_indicateur11Accueil: conf_indicateur11Accueil,
            conf_indicateur12Accueil: conf_indicateur12Accueil,
        });

        if(enableLog)
        {
            logger.info("Revue des indicateurs d'accueil pour la personne "+idPersonne, {idPersonne: 'SYSTEM'})
        }
    } catch (error) {
        logger.error(error)
    }
}

const majIndicateursProfil = async (idProfil) => {
    try {
        logger.info("Revue des indicateurs d'accueil pour le profil "+idProfil, {idPersonne: 'SYSTEM'})

        let personnes = await db.query(`
            SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;
        `,{
            idProfil: idProfil,
        });
        for(const personne of personnes)
        {
            await majIndicateursPersonne(personne.idPersonne, false);
        }
        
    } catch (error) {
        logger.error(error)
    }
}

const majNotificationsPersonne = async (idPersonne, enableLog) => {
    try {
        let personne = await db.query(`
            SELECT * FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;
        `,{
            idPersonne: idPersonne,
        });
        personne = personne[0];

        notif_lots_manquants = personne.notifications && (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.notif_lots_manquants);
        notif_lots_peremptions = personne.notifications && (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.notif_lots_peremptions);
        notif_lots_inventaires = personne.notifications && (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.notif_lots_inventaires);
        notif_lots_conformites = personne.notifications && (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.notif_lots_conformites);
        notif_reserves_manquants = personne.notifications && (personne.reserve_lecture) && (personne.notif_reserves_manquants);
        notif_reserves_peremptions = personne.notifications && (personne.reserve_lecture) && (personne.notif_reserves_peremptions);
        notif_reserves_inventaires = personne.notifications && (personne.reserve_lecture) && (personne.notif_reserves_inventaires);
        notif_vehicules_desinfections = personne.notifications && (personne.desinfections_lecture) && (personne.notif_vehicules_desinfections);
        notif_vehicules_health = personne.notifications && (personne.vehiculeHealth_lecture) && (personne.notif_vehicules_health);
        notif_tenues_stock = personne.notifications && (personne.tenuesCatalogue_lecture) && (personne.notif_tenues_stock);
        notif_tenues_retours = personne.notifications && (personne.tenues_lecture) && (personne.notif_tenues_retours);
        notif_benevoles_lots = personne.notifications && (personne.alertesBenevolesLots_lecture) && (personne.notif_benevoles_lots);
        notif_benevoles_vehicules = personne.notifications && (personne.alertesBenevolesVehicules_lecture) && (personne.notif_benevoles_vehicules);

        let update = await db.query(`
            UPDATE
                PERSONNE_REFERENTE
            SET
                notif_lots_manquants          = :notif_lots_manquants,
                notif_lots_peremptions        = :notif_lots_peremptions,
                notif_lots_inventaires        = :notif_lots_inventaires,
                notif_lots_conformites        = :notif_lots_conformites,
                notif_reserves_manquants      = :notif_reserves_manquants,
                notif_reserves_peremptions    = :notif_reserves_peremptions,
                notif_reserves_inventaires    = :notif_reserves_inventaires,
                notif_vehicules_health        = :notif_vehicules_health,
                notif_vehicules_desinfections = :notif_vehicules_desinfections,
                notif_tenues_stock            = :notif_tenues_stock,
                notif_tenues_retours          = :notif_tenues_retours,
                notif_benevoles_lots          = :notif_benevoles_lots,
                notif_benevoles_vehicules     = :notif_benevoles_vehicules
            WHERE
                idPersonne                    = :idPersonne
        `,{
            idPersonne                   : idPersonne,
            notif_lots_manquants         : notif_lots_manquants,
            notif_lots_peremptions       : notif_lots_peremptions,
            notif_lots_inventaires       : notif_lots_inventaires,
            notif_lots_conformites       : notif_lots_conformites,
            notif_reserves_manquants     : notif_reserves_manquants,
            notif_reserves_peremptions   : notif_reserves_peremptions,
            notif_reserves_inventaires   : notif_reserves_inventaires,
            notif_vehicules_desinfections: notif_vehicules_desinfections,
            notif_vehicules_health       : notif_vehicules_health,
            notif_tenues_stock           : notif_tenues_stock,
            notif_tenues_retours         : notif_tenues_retours,
            notif_benevoles_lots         : notif_benevoles_lots,
            notif_benevoles_vehicules    : notif_benevoles_vehicules,
        });

        if(enableLog)
        {
            logger.info("Revue des notifications pour la personne "+idPersonne, {idPersonne: 'SYSTEM'})
        }
    } catch (error) {
        logger.error(error)
    }
}

const majNotificationsProfil = async (idProfil) => {
    try {
        logger.info("Revue des notifications pour le profil "+idProfil, {idPersonne: 'SYSTEM'})

        let personnes = await db.query(`
            SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;
        `,{
            idProfil: idProfil,
        });
        for(const personne of personnes)
        {
            await majNotificationsPersonne(personne.idPersonne, false);
        }
        
    } catch (error) {
        logger.error(error)
    }
}

const majValideursPersonne = async (enableLog) => {
    try {

        let cleanQuery = await db.query(`
            DELETE
                CENTRE_COUTS_PERSONNES
            FROM
                CENTRE_COUTS_PERSONNES
                INNER JOIN VIEW_HABILITATIONS ON CENTRE_COUTS_PERSONNES.idPersonne = VIEW_HABILITATIONS.idPersonne
            WHERE
                VIEW_HABILITATIONS.cout_etreEnCharge = 0
                OR
                VIEW_HABILITATIONS.cout_etreEnCharge IS NULL;
        `);
        
        if (enableLog)
        {
            logger.info("Nettoyage des personnes des valideurs par défaut des commandes", {idPersonne: 'SYSTEM'})
        }
    } catch (error) {
        logger.error(error)
    }
}

const checkFunctionnalityRapportConsoEnabled = async () => {
    try {
        const getConfig = await db.query(`
            SELECT
                consommation_benevoles
            FROM
                CONFIG
        `);
        if(getConfig[0].consommation_benevoles == true)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error)
        return false;
    }
}

const ajouterItemConsommation = async (element) => {
    try {
        let verifFonctionnalite = await checkFunctionnalityRapportConsoEnabled();
        if(verifFonctionnalite == false){return;}
        
        const getExistingElement = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idLot = :idLot
                AND
                idMaterielCatalogue = :idMaterielCatalogue
                AND
                idConsommation = :idConsommation
        `,{
            idLot: element.idLot,
            idMaterielCatalogue: element.idMaterielCatalogue,
            idConsommation: element.idConsommation,
        });

        if(getExistingElement.length == 0)
        {
            const addElement = await db.query(`
                INSERT INTO
                    LOTS_CONSOMMATION_MATERIEL
                SET
                    idConsommation = :idConsommation,
                    idMaterielCatalogue = :idMaterielCatalogue,
                    idLot = :idLot,
                    quantiteConsommation = :quantiteConsommation,
                    idConteneur = null,
                    traiteOperateur = false
            `,{
                idConsommation: element.idConsommation || null,
                idMaterielCatalogue: element.idMaterielCatalogue || null,
                idLot: element.idLot || null,
                quantiteConsommation: element.quantiteConsommation || null,
            });

            const getLast = await db.query(`
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
                ORDER BY
                    m.idConsommationMateriel DESC
                LIMIT 1
            `);
            return(getLast[0]);
        }
        else
        {
            const updateElement = await db.query(`
                UPDATE
                    LOTS_CONSOMMATION_MATERIEL
                SET
                    quantiteConsommation = quantiteConsommation + :quantiteConsommation
                WHERE
                    idConsommationMateriel = :idConsommationMateriel
            `,{
                idConsommationMateriel: getExistingElement[0].idConsommationMateriel || null,
                quantiteConsommation: element.quantiteConsommation || null,
            });

            const getUpdated = await db.query(`
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
                    m.idConsommationMateriel = :idConsommationMateriel
            `,{
                idConsommationMateriel: getExistingElement[0].idConsommationMateriel || null,
            });
            return(getUpdated[0]);
        }

    } catch (error) {
        logger.error(error)
    }
}

const updateItemConsommation = async (element) => {
    try {
        let verifFonctionnalite = await checkFunctionnalityRapportConsoEnabled();
        if(verifFonctionnalite == false){return;}

        const getExistingElement = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idLot = :idLot
                AND
                idMaterielCatalogue = :idMaterielCatalogue
                AND
                idConsommation = :idConsommation
        `,{
            idLot: element.idLot,
            idMaterielCatalogue: element.idMaterielCatalogue,
            idConsommation: element.idConsommation,
        });

        if(getExistingElement.length == 0 || (getExistingElement.length == 1 && getExistingElement[0].idConsommationMateriel == element.idConsommationMateriel))
        {
            const updateElement = await db.query(`
                UPDATE
                    LOTS_CONSOMMATION_MATERIEL
                SET
                    idLot = :idLot,
                    quantiteConsommation = :quantiteConsommation
                WHERE
                    idConsommationMateriel = :idConsommationMateriel
            `,{
                idConsommationMateriel: element.idConsommationMateriel || null,
                idLot: element.idLot || null,
                quantiteConsommation: element.quantiteConsommation || null,
            });

            const getItem = await db.query(`
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
                    idConsommationMateriel = :idConsommationMateriel
            `,{
                idConsommationMateriel: element.idConsommationMateriel || null,
            });
            return({toUpdate: getItem[0], toDelete: null});
        }
        else
        {
            const updateElement = await db.query(`
                UPDATE
                    LOTS_CONSOMMATION_MATERIEL
                SET
                    quantiteConsommation = quantiteConsommation + :quantiteConsommation
                WHERE
                    idConsommationMateriel = :idConsommationMateriel
            `,{
                idConsommationMateriel: getExistingElement[0].idConsommationMateriel || null,
                quantiteConsommation: element.quantiteConsommation || null,
            });

            const deleteElement = await db.query(`
                DELETE FROM
                    LOTS_CONSOMMATION_MATERIEL
                WHERE
                    idConsommationMateriel = :idConsommationMateriel
            `,{
                idConsommationMateriel: element.idConsommationMateriel || null,
            });

            const getUpdated = await db.query(`
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
                    m.idConsommationMateriel = :idConsommationMateriel
            `,{
                idConsommationMateriel: getExistingElement[0].idConsommationMateriel || null,
            });
            return({toUpdate: getUpdated[0], toDelete: element.idConsommationMateriel});
        }

    } catch (error) {
        logger.error(error)
    }
}

const supprimerItemConsommation = async (idConsommationMateriel) => {
    try {
        let verifFonctionnalite = await checkFunctionnalityRapportConsoEnabled();
        if(verifFonctionnalite == false){return;}

        const getExistingElement = await db.query(`
            DELETE FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idConsommationMateriel = :idConsommationMateriel
        `,{
            idConsommationMateriel: idConsommationMateriel,
        });
    } catch (error) {
        logger.error(error)
    }
}

const terminerSaisieConsommation = async (data) => {
    try {
        let verifFonctionnalite = await checkFunctionnalityRapportConsoEnabled();
        if(verifFonctionnalite == false){return;}

        const getExistingElement = await db.query(`
            UPDATE
                LOTS_CONSOMMATION
            SET
                commentairesConsommation = :commentairesConsommation,
                declarationEnCours = true,
                reapproEnCours = true
            WHERE
                idConsommation = :idConsommation
        `,{
            idConsommation: data.idConsommation || null,
            commentairesConsommation: data.commentairesConsommation || null,
        });
    } catch (error) {
        logger.error(error)
    }
}

const updateReconditionnementConsommation = async (element) => {
    try {
        let verifFonctionnalite = await checkFunctionnalityRapportConsoEnabled();
        if(verifFonctionnalite == false){return;}

        const updateElement = await db.query(`
            UPDATE
                LOTS_CONSOMMATION_MATERIEL
            SET
                idConteneur = :idConteneur
            WHERE
                idConsommationMateriel = :idConsommationMateriel
        `,{
            idConsommationMateriel: element.idConsommationMateriel || null,
            idConteneur: element.idConteneur > 0 ? element.idConteneur : null,
        });

        const getItem = await db.query(`
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
                idConsommationMateriel = :idConsommationMateriel
        `,{
            idConsommationMateriel: element.idConsommationMateriel || null,
        });
        return({toUpdate: getItem[0], toDelete: null});

    } catch (error) {
        logger.error(error)
    }
}

const terminerReconditionnementConsommation = async (data) => {
    try {
        let verifFonctionnalite = await checkFunctionnalityRapportConsoEnabled();
        if(verifFonctionnalite == false){return;}
        
        const getExistingElement = await db.query(`
            UPDATE
                LOTS_CONSOMMATION
            SET
                commentairesConsommation = :commentairesConsommation,
                declarationEnCours = false,
                reapproEnCours = false
            WHERE
                idConsommation = :idConsommation
        `,{
            idConsommation: data.idConsommation || null,
            commentairesConsommation: data.commentairesConsommation || null,
        });
    } catch (error) {
        logger.error(error)
    }
}

const validerUnElementConsomme = async (idConsommationMateriel) => {
    try {
        let itemFromConso = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idConsommationMateriel = :idConsommationMateriel
        `,{
            idConsommationMateriel: idConsommationMateriel || null,
        });
        itemFromConso = itemFromConso[0];

        if(itemFromConso.traiteOperateur == false)
        {
            if(itemFromConso.idConteneur && itemFromConso.idConteneur != null && itemFromConso.idConteneur > 0)
            {
                let updateReserve = await db.query(`
                    UPDATE
                        RESERVES_MATERIEL
                    SET
                        quantiteReserve = quantiteReserve - :quantiteConsommation
                    WHERE
                        idConteneur = :idConteneur
                        AND
                        idMaterielCatalogue = :idMaterielCatalogue
                `,{
                    quantiteConsommation: itemFromConso.quantiteConsommation || 0,
                    idConteneur: itemFromConso.idConteneur || null,
                    idMaterielCatalogue: itemFromConso.idMaterielCatalogue || null,
                });
            }else{
                //décompte le lot
                let updateLot = await db.query(`
                    UPDATE
                        MATERIEL_ELEMENT e
                        LEFT OUTER JOIN MATERIEL_EMPLACEMENT em ON e.idEmplacement = em.idEmplacement
                        LEFT OUTER JOIN MATERIEL_SAC s ON em.idSac = s.idSac
                    SET
                        e.quantite = e.quantite - :quantiteConsommation
                    WHERE
                        s.idLot = :idLot
                        AND
                        idMaterielCatalogue = :idMaterielCatalogue
                `,{
                    quantiteConsommation: itemFromConso.quantiteConsommation || 0,
                    idLot: itemFromConso.idLot || null,
                    idMaterielCatalogue: itemFromConso.idMaterielCatalogue || null,
                });
                await checkOneConf(itemFromConso.idLot);
            }

            const result = await db.query(`
                UPDATE
                    LOTS_CONSOMMATION_MATERIEL
                SET
                    traiteOperateur = true
                WHERE
                    idConsommationMateriel = :idConsommationMateriel
            `,{
                idConsommationMateriel: idConsommationMateriel || null,
            });
            
            return true;
        }else{
            return false;
        }
    } catch (error) {
        logger.error(error)
    }
}

const comptabiliserToutesConsommations = async () => {
    try {
        const getConfig = await db.query(`
            SELECT
                consommation_benevoles_auto
            FROM
                CONFIG
        `);
        if(getConfig[0].consommation_benevoles_auto == true)
        {
            logger.debug('Lancement du traitement auto des consommations de bénévoles car paramètre actif');
            let elements = await db.query(`
                SELECT
                    idConsommationMateriel
                FROM
                    LOTS_CONSOMMATION_MATERIEL m
                    LEFT OUTER JOIN LOTS_CONSOMMATION c ON m.idConsommation = c.idConsommation
                WHERE
                    c.declarationEnCours = false
                    AND
                    c.reapproEnCours = false
                    AND
                    m.traiteOperateur = false
            `);
            for(const element of elements)
            {
                await validerUnElementConsomme(element.idConsommationMateriel);
            }
        }else{
            logger.debug('Annulation du traitement auto des consommations de bénévoles car paramètre désactivé');
        }
    } catch (error) {
        logger.error(error)
    }
}

module.exports = {
    majLdapOneUser,
    majLdapAllUsers,
    deconnecterUtilisateur,
    deconnecterProfil,
    deconnecterToutLeMonde,
    updateLastConnexion,
    checkPwdReinitEnable,
    clearPwdReinitTable,
    unlockLotsInventaires,
    unlockReservesInventaires,
    updatePeremptionsAnticipations,
    notificationsMAJpersonne,
    notificationsConditionsMAJ,
    checkVehiculeMaintenance,
    checkAllMaintenance,
    checkOneMaintenance,
    checkVehiculeDesinfection,
    checkAllDesinfection,
    checkOneDesinfection,
    checkLotsConf,
    checkOneConf,
    checkAllConf,
    updateConformiteMaterielOpe,
    cnilAnonyme,
    cnilAnonymeCron,
    replaceString,
    updateInventaireLotItem,
    validerInventaireLot,
    figeInventaireLot,
    updateInventaireReserveItem,
    validerInventaireReserve,
    figeInventaireReserve,
    majIndicateursPersonne,
    majIndicateursProfil,
    majNotificationsPersonne,
    majNotificationsProfil,
    majValideursPersonne,
    checkFunctionnalityRapportConsoEnabled,
    ajouterItemConsommation,
    updateItemConsommation,
    supprimerItemConsommation,
    terminerSaisieConsommation,
    updateReconditionnementConsommation,
    terminerReconditionnementConsommation,
    validerUnElementConsomme,
    comptabiliserToutesConsommations,
};