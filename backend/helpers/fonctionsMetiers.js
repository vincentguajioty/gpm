const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsLDAP = require('./fonctionsLDAP');
const fonctionsMail = require('./fonctionsMail');
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
        logger.info("Début de la mise à jour de la table de matériel opérationnel pour mise à jour du champ d'anticipation des péremption conformément au catalogue.")
        const updateOpe = await db.query(`UPDATE MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue SET e.peremptionAnticipation = c.peremptionAnticipationOpe WHERE c.peremptionAnticipationOpe IS NOT NULL;`);
        logger.info("Fin de la mise à jour de la table de matériel opérationnel pour mise à jour du champ d'anticipation des péremption conformément au catalogue.")

        logger.info("Début de la mise à jour de la table de matériel en reserve pour mise à jour du champ d'anticipation des péremption conformément au catalogue.")
        const updateRes = await db.query(`UPDATE RESERVES_MATERIEL e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue SET e.peremptionReserveAnticipation = c.peremptionAnticipationRes WHERE c.peremptionAnticipationRes IS NOT NULL;`);
        logger.info("Fin de la mise à jour de la table de matériel en reserve pour mise à jour du champ d'anticipation des péremption conformément au catalogue.")
    } catch (error) {
        logger.error(error)
    }
}

const notificationsMAJpersonne = async () => {
    try {
        logger.info("Début de la mise à jour des conditions de notification dans la table des personnes.")

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
                actifCeJour: personne.actifCeJour &&  personne.actifCeJour == true ? true : false,
                idPersonne: personne.idPersonne,
            });
        }

        logger.info("Fin de la mise à jour des conditions de notification dans la table des personnes.")
    } catch (error) {
        logger.error(error)
    }
}

const notificationsConditionsMAJ = async () => {
    try {
        logger.info("Début de la mise à jour de la table des conditions de notification.")

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
        
        logger.info("Fin de la mise à jour de la table des conditions de notification.")
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
        logger.info("Lancement de la vérification des maintenance de tous les véhicules.")

        let allVehicules = await db.query(`
            SELECT * FROM VEHICULES;
        `);
        for(const vehicule of allVehicules)
        {
            await checkOneMaintenance(vehicule.idVehicule)
        }
        
        logger.info("Fin de la vérification des maintenance de tous les véhicules.")
    } catch (error) {
        logger.error(error)
    }
}

const checkOneMaintenance = async (idVehicule) => {
    try {
        logger.info("Lancement de la vérification des maintenance du véhicule "+idVehicule)

        let alertes = await db.query(`
            SELECT COUNT(*) as nb FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule;
        `,{
            idVehicule: idVehicule,
        });

        if(alertes[0].nb > 0)
        {
            if (await checkVehiculeMaintenance(idVehicule) == 1)
            {
                logger.info("Véhicule "+idVehicule+" contrôlé en erreur de maintenance.")
                let update = await db.query(`
                    UPDATE VEHICULES SET alerteMaintenance = True WHERE idVehicule = :idVehicule;
                `,{
                    idVehicule: idVehicule,
                });
            }
            else
            {
                logger.info("Véhicule "+idVehicule+" contrôlé en maintenance ok.")
                let update = await db.query(`
                    UPDATE VEHICULES SET alerteMaintenance = false WHERE idVehicule = :idVehicule;
                `,{
                    idVehicule: idVehicule,
                });
            }
        }
        else
        {
            logger.warn("Vérification des maintenance du vehicule "+idVehicule+" impossible car aucune alerte paramétrée.")
            let update = await db.query(`
                UPDATE VEHICULES SET alerteMaintenance = null WHERE idVehicule = :idVehicule;
            `,{
                idVehicule: idVehicule,
            });
        }
        
        logger.info("Fin de la vérification des maintenance du véhicule "+idVehicule)
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
        logger.info("Lancement de la vérification des désinfections de tous les véhicules.")

        let allVehicules = await db.query(`
            SELECT * FROM VEHICULES;
        `);
        for(const vehicule of allVehicules)
        {
            await checkOneDesinfection(vehicule.idVehicule)
        }
        
        logger.info("Fin de la vérification des désinfections de tous les véhicules.")
    } catch (error) {
        logger.error(error)
    }
}

const checkOneDesinfection = async (idVehicule) => {
    try {
        logger.info("Lancement de la vérification des désinfections du véhicule "+idVehicule)

        let alertes = await db.query(`
            SELECT COUNT(*) as nb FROM VEHICULES_DESINFECTIONS_ALERTES WHERE idVehicule = :idVehicule;
        `,{
            idVehicule: idVehicule,
        });

        if(alertes[0].nb > 0)
        {
            if (await checkVehiculeDesinfection(idVehicule) == 1)
            {
                logger.info("Véhicule "+idVehicule+" contrôlé en erreur de désinfections.")
                let update = await db.query(`
                    UPDATE VEHICULES SET alerteDesinfection = True WHERE idVehicule = :idVehicule;
                `,{
                    idVehicule: idVehicule,
                });
            }
            else
            {
                logger.info("Véhicule "+idVehicule+" contrôlé en désinfections ok.")
                let update = await db.query(`
                    UPDATE VEHICULES SET alerteDesinfection = false WHERE idVehicule = :idVehicule;
                `,{
                    idVehicule: idVehicule,
                });
            }
        }
        else
        {
            logger.warn("Vérification des désinfections du vehicule "+idVehicule+" impossible car aucune alerte paramétrée.")
            let update = await db.query(`
                UPDATE VEHICULES SET alerteDesinfection = null WHERE idVehicule = :idVehicule;
            `,{
                idVehicule: idVehicule,
            });
        }
        
        logger.info("Fin de la vérification des désinfections du véhicule "+idVehicule)
    } catch (error) {
        logger.error(error)
    }
}

const checkLotsConf = async (idLot) => {
    try {
        logger.info("Vérification de conformité du lot "+idLot)
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
            logger.info("Lot "+idLot+" vérifié non-conforme")
            return 1;
        }
        else
        {
            logger.info("Lot "+idLot+" vérifié conforme")
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
        logger.info("Lancement de la vérification de conformité de tous les lots.")

        let lots = await db.query(`
            SELECT * FROM LOTS_LOTS;
        `);
        for(const lot of lots)
        {
            await checkOneConf(lot.idLot)
        }
        
        logger.info("Fin de la vérification de conformité de tous les lots.")
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
            logger.info("CRON - Anonymisation de l'utilisateur "+user.idPersonne+" - "+user.identifiant)
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

const initiateOldValuesInventaireLot = async (idInventaire) => {
    try {
        let updateQuery = await db.query(`
            UPDATE
                LOTS_INVENTAIRES_TEMP
            SET
                quantiteInventoriee = quantiteAvantInventaire,
                peremptionInventoriee = peremptionAvantInventaire
            WHERE
                idInventaire = :idInventaire
        `,{
            idInventaire : idInventaire || null,
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

const initiateOldValuesInventaireReserve = async (idReserveInventaire) => {
    try {
        let updateQuery = await db.query(`
            UPDATE
                RESERVES_INVENTAIRES_TEMP
            SET
                quantiteInventoriee = quantiteAvantInventaire,
                peremptionInventoriee = peremptionAvantInventaire
            WHERE
                idReserveInventaire = :idReserveInventaire
        `,{
            idReserveInventaire : idReserveInventaire || null,
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
            logger.info("Revue des indicateurs d'accueil pour la personne "+idPersonne)
        }
    } catch (error) {
        logger.error(error)
    }
}

const majIndicateursProfil = async (idProfil) => {
    try {
        logger.info("Revue des indicateurs d'accueil pour le profil "+idProfil)

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
        notif_consommations_lots = personne.notifications && (personne.lots_lecture || personne.sac_lecture || personne.materiel_lecture) && (personne.notif_consommations_lots);

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
                notif_benevoles_vehicules     = :notif_benevoles_vehicules,
                notif_consommations_lots      = :notif_consommations_lots
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
            notif_consommations_lots     : notif_consommations_lots,
        });

        if(enableLog)
        {
            logger.info("Revue des notifications pour la personne "+idPersonne)
        }
    } catch (error) {
        logger.error(error)
    }
}

const majNotificationsProfil = async (idProfil) => {
    try {
        logger.info("Revue des notifications pour le profil "+idProfil)

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
            logger.info("Nettoyage des personnes des valideurs par défaut des commandes")
        }
    } catch (error) {
        logger.error(error)
    }
}

const checkFunctionnalityBenevolesEnabled = async () => {
    try {
        const getConfig = await db.query(`
            SELECT
                consommation_benevoles,
                alertes_benevoles_lots,
                alertes_benevoles_vehicules
            FROM
                CONFIG
        `);
        if(getConfig[0].consommation_benevoles == true || getConfig[0].alertes_benevoles_lots == true || getConfig[0].alertes_benevoles_vehicules == true)
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
        let verifFonctionnalite = await checkFunctionnalityBenevolesEnabled();
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
        let verifFonctionnalite = await checkFunctionnalityBenevolesEnabled();
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
        let verifFonctionnalite = await checkFunctionnalityBenevolesEnabled();
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
        let verifFonctionnalite = await checkFunctionnalityBenevolesEnabled();
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
        let verifFonctionnalite = await checkFunctionnalityBenevolesEnabled();
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
        let verifFonctionnalite = await checkFunctionnalityBenevolesEnabled();
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
        
        const usersToNotify = await db.query(`
            SELECT
                idPersonne
            FROM
                VIEW_HABILITATIONS
            WHERE
                notif_consommations_lots = true
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
                typeMail: 'finDeclarationConso',
                idPersonne: personne.idPersonne,
                idObject: data.idConsommation,
            });
        }
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

const queueNotificationJournaliere = async () => {
    try {
        const getUsersToNotify = await db.query(`
            SELECT
                idPersonne
            FROM
                VIEW_HABILITATIONS
            WHERE
                notifications_abo_cejour = true
                AND
                notifications = true
                AND
                mailPersonne IS NOT NULL
                AND
                mailPersonne <> ""
        `);
        for(const personne of getUsersToNotify)
        {
            await fonctionsMail.registerToMailQueue({
                typeMail: 'notifJournaliere',
                idPersonne: personne.idPersonne,
            });
        }
    } catch (error) {
        logger.error(error)
    }
}

const calculerTotalCommande = async (idCommande) => {
    try {
        const updateTotal = await db.query(`
            UPDATE
                COMMANDES
            SET
                montantTotal = (
                    SELECT
                        CAST(IFNULL(SUM(prixProduitTTC*quantiteCommande),0) AS DECIMAL(10,2)) AS total
                    FROM
                        COMMANDES_MATERIEL c
                        LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue 
                    WHERE
                        idCommande = :idCommande
                )
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: idCommande
        });
    } catch (error) {
        logger.error(error)
    }
}

const calculerTousTotauxCommandes = async () => {
    try {
        let getAllCommandes = await db.query(`
            SELECT
                idCommande
            FROM
                COMMANDES
        `);
        for(const commande of getAllCommandes)
        {
            await calculerTotalCommande(commande.idCommande);
        }
    } catch (error) {
        logger.error(error)
    }
}

const getValideurs = async (idCommande) => {
    try {
        let commande = await db.query(`
            SELECT
                idCentreDeCout
            FROM
                COMMANDES
            WHERE
                idCommande = :idCommande
        ;`,{
            idCommande: idCommande,
        });
        commande = commande[0];

        const getValideursUniversels = await db.query(`
            SELECT
                idPersonne,    
                idPersonne as value,
                identifiant as label
            FROM
                VIEW_HABILITATIONS
            WHERE
                commande_valider_delegate = 1
        ;`);

        if(!commande.idCentreDeCout || commande.idCentreDeCout == null)
        {
            return(getValideursUniversels);
        }
        
        const getValideursCentreDeCout = await db.query(`
            SELECT
                c.idPersonne,
                p.identifiant
            FROM
                CENTRE_COUTS_PERSONNES c
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne
            WHERE
                c.idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout: commande.idCentreDeCout,
        });
        let valideursPotentiels = [];
        for(const personne of getValideursCentreDeCout)
        {
            if(await cmdEstValideur(personne.idPersonne, idCommande) == true)
            {
                valideursPotentiels.push({
                    idPersonne: personne.idPersonne,
                    value: personne.idPersonne,
                    label: personne.identifiant,
                })
            }
        }

        if(valideursPotentiels.length > 0)
        {
            return valideursPotentiels;
        }

        return getValideursUniversels;
        
    } catch (error) {
        logger.error(error)
        return([]);
    }
}

const cmdEstValideur = async(idPersonne, idCommande) => {
    try {
        let commande = await db.query(`
            SELECT
                *
            FROM
                COMMANDES
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: idCommande,
        })
        commande = commande[0];
    
        if(!commande.idCentreDeCout || commande.idCentreDeCout == null)
        {
            return false;
        }

        let centreDeCout = await db.query(`
            SELECT
                *
            FROM
                CENTRE_COUTS
            WHERE
                idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout: commande.idCentreDeCout,
        });
        centreDeCout = centreDeCout[0];
        
        let pouvoirsvalidation = await db.query(`
            SELECT
                idPersonne,
                montantMaxValidation,
                depasseBudget,
                validerClos
            FROM
                CENTRE_COUTS_PERSONNES
            WHERE
                idCentreDeCout = :idCentreDeCout
                AND idPersonne = :idPersonne
        ;`,{
            idCentreDeCout: commande.idCentreDeCout,
            idPersonne: idPersonne,
        });

        let enApprocheSurCentreCout = await db.query(`
            SELECT
                SUM(montantTotal) as enApproche
            FROM
                COMMANDES
            WHERE
                idCentreDeCout = :idCentreDeCout
                AND integreCentreCouts = 0
                AND idEtat != 8
                AND idEtat != 1
                AND idEtat != 2
        ;`,{
            idCentreDeCout: commande.idCentreDeCout,
        });
        enApprocheSurCentreCout = enApprocheSurCentreCout[0].enApproche;

        let encoursCentreCout = await db.query(`
            SELECT
                COALESCE(SUM(montantEntrant),0)-COALESCE(SUM(montantSortant),0) as encours
            FROM
                CENTRE_COUTS_OPERATIONS
            WHERE
                idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout: commande.idCentreDeCout,
        });
        encoursCentreCout = encoursCentreCout[0].encours

        let disponible = encoursCentreCout - enApprocheSurCentreCout;
        
        let centreOuvert;
        if(centreDeCout.dateFermeture == null)
        {
            if(new Date(centreDeCout.dateOuverture) <= new Date())
            {
                centreOuvert = true;
            }
            else
            {
                centreOuvert = false;
            }
        }
        else
        {
            if(new Date(centreDeCout.dateFermeture) < new Date())
            {
                centreOuvert = false;
            }
            else
            {
                if(new Date(centreDeCout.dateOuverture) <= new Date())
                {
                    centreOuvert = true;
                }
                else
                {
                    centreOuvert = false;
                }
            }
        }

        for(const pouvoir of pouvoirsvalidation)
        {
            if(pouvoir.montantMaxValidation == null)
            {
                if(centreOuvert == false && pouvoir.validerClos == false)
                {
                    return false;
                }

                if(commande.montantTotal >= disponible && pouvoir.depasseBudget == false)
                {
                    return false;
                }

                return true;
            }else{
                if(pouvoir.montantMaxValidation >= commande.montantTotal)
                {
                    if(centreOuvert == false && pouvoir.validerClos == false)
                    {
                        return false;
                    }
    
                    if(commande.montantTotal > disponible && pouvoir.depasseBudget == false)
                    {
                        return false;
                    }
    
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }

        return false;
    } catch (error) {
        logger.error(error)
        return false;
    }
}

const cmdEstAffectee = async(idPersonne, idCommande) => {
    try {
        let affectees = await db.query(`
            SELECT
                *
            FROM
                COMMANDES_AFFECTEES
            WHERE
                idCommande = :idCommande
                AND
                idAffectee = :idPersonne
        `,{
            idCommande: idCommande,
            idPersonne: idPersonne,
        })
        if(affectees.length == 1){return true;}else{return false;}
    } catch (error) {
        logger.error(error)
    }
}

const cmdEstValideurUniversel = async(idPersonne, idCommande) => {
    try {
        let profilPersonne = await db.query(`
            SELECT
                commande_valider_delegate
            FROM
                VIEW_HABILITATIONS
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: idPersonne,
        });

        if(profilPersonne.length != 1 || profilPersonne[0].commande_valider_delegate == false)
        {return false;}

        let commande = await db.query(`
            SELECT
                *
            FROM
                COMMANDES
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: idCommande,
        });
        commande = commande[0];

        let nbValideursStandards = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                CENTRE_COUTS_PERSONNES c
                INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne
            WHERE
                idCentreDeCout = :idCentreDeCout
                AND
                (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL)
        `,{
            idCentreDeCout: commande.idCentreDeCout,
            montantTotalCommande: commande.montantTotal
        });
        nbValideursStandards = nbValideursStandards[0].nb;

        if(nbValideursStandards == 0){return true}else{return false}

    } catch (error) {
        logger.error(error)
    }
}

const cmdEstObservateur = async(idPersonne, idCommande) => {
    try {
        let obs = await db.query(`
            SELECT
                *
            FROM
                COMMANDES_OBSERVATEURS
            WHERE
                idCommande = :idCommande
                AND
                idObservateur = :idPersonne
        `,{
            idCommande: idCommande,
            idPersonne: idPersonne,
        })
        if(obs.length == 1){return true;}else{return false;}
    } catch (error) {
        logger.error(error)
    }
}

const cmdEstDemandeur = async(idPersonne, idCommande) => {
    try {
        let dem = await db.query(`
            SELECT
                *
            FROM
                COMMANDES_DEMANDEURS
            WHERE
                idCommande = :idCommande
                AND
                idDemandeur = :idPersonne
        `,{
            idCommande: idCommande,
            idPersonne: idPersonne,
        })
        if(dem.length == 1){return true;}else{return false;}
    } catch (error) {
        logger.error(error)
    }
}

const centreCoutsEstCharge = async(idPersonne, idCentreDeCout) => {
    try {
        let centreDeCout = await db.query(`
            SELECT
                *
            FROM
                CENTRE_COUTS
            WHERE
                idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout: idCentreDeCout,
        });
        centreDeCout = centreDeCout[0];

        let encoursCentreCout = await db.query(`
            SELECT
                COALESCE(SUM(montantEntrant),0)-COALESCE(SUM(montantSortant),0) as encours
            FROM
                CENTRE_COUTS_OPERATIONS
            WHERE
                idCentreDeCout = :idCentreDeCout
        ;`,{
            idCentreDeCout: idCentreDeCout,
        });
        encoursCentreCout = encoursCentreCout[0].encours

        let pouvoirs = await db.query(`
            SELECT
                c.*
            FROM
                CENTRE_COUTS_PERSONNES c
                LEFT OUTER JOIN VIEW_HABILITATIONS v ON c.idPersonne = v.idPersonne
            WHERE
                c.idCentreDeCout = :idCentreDeCout
                AND v.cout_etreEnCharge=1
                AND c.idPersonne = :idPersonne
        ;`,{
            idCentreDeCout: idCentreDeCout,
            idPersonne: idPersonne,
        });

        let centreOuvert;
        if(centreDeCout.dateFermeture == null)
        {
            if(new Date(centreDeCout.dateOuverture) <= new Date())
            {
                centreOuvert = true;
            }
            else
            {
                centreOuvert = false;
            }
        }
        else
        {
            if(new Date(centreDeCout.dateFermeture) < new Date())
            {
                centreOuvert = false;
            }
            else
            {
                if(new Date(centreDeCout.dateOuverture) <= new Date())
                {
                    centreOuvert = true;
                }
                else
                {
                    centreOuvert = false;
                }
            }
        }

        for(const pouvoir of pouvoirs)
        {
            if(centreOuvert == false && pouvoir.validerClos == false)
            {
                return false;
            }

            if(encoursCentreCout < 0 && pouvoir.depasseBudget == false)
            {
                return false;
            }

            return true;
        }

        return false;

    } catch (error) {
        logger.error(error)
    }
}

const verificationContraintesCmd = async (idCommande) => {
    try {
        let commande = await db.query(`
            SELECT
                *
            FROM
                COMMANDES
            WHERE
                idCommande = :idCommande
        ;`,{
            idCommande: idCommande,
        });
        commande = commande[0];

        let contraintes = await db.query(`
            SELECT
                *
            FROM
                COMMANDES_CONTRAINTES
        ;`);
        for(const contrainte of contraintes)
        {
            let contrainteRespectee = false;

            // vérification des possibles contraintes une à une
            if(contrainte.fournisseurObligatoire == true)
            {
                if(commande.idFournisseur != null && commande.idFournisseur > 0)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.minDemandeurs && contrainte.minDemandeurs > 0)
            {
                let personnes = await db.query(`
                    SELECT
                        p.idPersonne as value,
                        p.identifiant as label
                    FROM
                        COMMANDES_DEMANDEURS c
                        LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idDemandeur
                    WHERE
                        c.idCommande = :idCommande
                ;`,{
                    idCommande: idCommande,
                });
                if(personnes.length >= contrainte.minDemandeurs)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.minAffectees && contrainte.minAffectees > 0)
            {
                let personnes = await db.query(`
                    SELECT
                        p.idPersonne as value,
                        p.identifiant as label
                    FROM
                        COMMANDES_AFFECTEES c
                        LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idAffectee
                    WHERE
                        c.idCommande = :idCommande
                ;`,{
                    idCommande: idCommande,
                });
                if(personnes.length >= contrainte.minAffectees)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.minObservateurs && contrainte.minObservateurs > 0)
            {
                let personnes = await db.query(`
                    SELECT
                        p.idPersonne as value,
                        p.identifiant as label
                    FROM
                        COMMANDES_OBSERVATEURS c
                        LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idObservateur
                    WHERE
                        c.idCommande = :idCommande
                ;`,{
                    idCommande: idCommande,
                });
                if(personnes.length >= contrainte.minObservateurs)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.minValideurs && contrainte.minValideurs > 0)
            {
                let personnes = await getValideurs(idCommande);
                if(personnes.length >= contrainte.minValideurs)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.idTypeDocumentObligatoire != null && contrainte.idTypeDocumentObligatoire > 0 && contrainte.nbTypeDocumentObligatoire > 0)
            {
                let documents = await db.query(`
                    SELECT
                        *
                    FROM
                        DOCUMENTS_COMMANDES
                    WHERE
                        idCommande = :idCommande
                        AND
                        idTypeDocument = :idTypeDocument
                ;`,{
                    idCommande: idCommande,
                    idTypeDocument: idTypeDocumentObligatoire,
                });
                if(documents.length >= contrainte.nbTypeDocumentObligatoire)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.minMontant != null)
            {
                if(commande.montantTotal >= contrainte.minMontant)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.maxMontant != null)
            {
                if(commande.montantTotal <= contrainte.maxMontant)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.minQttMateriel != null && contrainte.minQttMateriel > 0)
            {
                let materiels = await db.query(`
                    SELECT
                        m.*,
                        c.libelleMateriel
                    FROM
                        COMMANDES_MATERIEL m
                        LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                    WHERE
                        m.idCommande = :idCommande
                ;`,{
                    idCommande: idCommande,
                });

                if(materiels.length >= contrainte.minQttMateriel)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.integrationStockObligatoire == true)
            {
                let totalATransferer = await db.query(`
                    SELECT
                        SUM(quantiteAtransferer) as totalATransferer
                    FROM
                        COMMANDES_MATERIEL
                    WHERE
                        idCommande = :idCommande
                ;`,{
                    idCommande: idCommande,
                });
                totalATransferer = totalATransferer[0].totalATransferer;

                if(totalATransferer > 0)
                {
                    contrainteRespectee = false;
                }else{
                    contrainteRespectee = true;
                }
            }

            if(contrainte.centreCoutsObligatoire == true)
            {
                if(commande.idCentreDeCout != null && commande.idCentreDeCout > 0)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.lieuLivraisonObligatoire == true)
            {
                if(commande.idLieuLivraison != null && commande.idLieuLivraison > 0)
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.remarquesGeneralesObligatoires == true)
            {
                if(commande.remarquesGenerales != null && commande.remarquesGenerales != "")
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.remarquesValidationObligatoire == true)
            {
                if(commande.remarquesValidation != null && commande.remarquesValidation != "")
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.remarquesLivraisonsObligatoire == true)
            {
                if(commande.remarquesLivraison != null && commande.remarquesLivraison != "")
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.referenceCommandeFournisseurObligatoire == true)
            {
                if(commande.numCommandeFournisseur != null && commande.numCommandeFournisseur != "")
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.datePassageCommandeObligatoire == true)
            {
                if(commande.datePassage != null && commande.datePassage != "")
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.datePrevueLivraisonObligatoire == true)
            {
                if(commande.dateLivraisonPrevue != null && commande.dateLivraisonPrevue != "")
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            if(contrainte.dateLivraisonEffectiveObligatoire == true)
            {
                if(commande.dateLivraisoneffective != null && commande.dateLivraisoneffective != "")
                {
                    contrainteRespectee = true;
                }else{
                    contrainteRespectee = false;
                }
            }

            contrainte.contrainteRespectee = contrainteRespectee;
        }

        let etatsCommandes = await db.query(`
            SELECT
                *
            FROM
                COMMANDES_ETATS
            WHERE
                idEtat <> :etatActuel
        ;`,{
            etatActuel: commande.idEtat,
        });
        for(const futurEtat of etatsCommandes)
        {
            let passagePossible = false;
            let contraintesQuiAppliquent = contraintes.filter(ctr => (ctr.idEtatInitial == commande.idEtat && ctr.idEtatFinal == futurEtat.idEtat))

            if(contraintesQuiAppliquent.length == 0)
            {
                passagePossible = true
            }else{
                let contraintesKO = contraintesQuiAppliquent.filter(ctr => ctr.contrainteRespectee != true);
                passagePossible = contraintesKO.length == 0 ? true : false;
            }

            futurEtat.passagePossible = passagePossible;
        }

        return({
            contraintes: contraintes.filter(ctr => ctr.idEtatInitial == commande.idEtat),
            possiblesMovesTo: etatsCommandes,
        })
    } catch (error) {
        logger.error(error)
    }
}

const verificationAvantChangementEtatCmd = async (idCommande, idEtatCible) => {
    try {
        let verificationContraintes = await verificationContraintesCmd(idCommande);

        let move = verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == idEtatCible);
        if(move.length != 1)
        {
            return false;
        }else{
            return move[0].passagePossible;
        }

    } catch (error) {
        logger.error(error)
    }
}

const envoyerNotifAuChangementStatutCommande = async (idCommande, idEtatCible) => {
    try {
        let configForNotif = await db.query(
            'SELECT * FROM CONFIG;'
        );
        configForNotif = configForNotif[0];

        let commande = await db.query(`
            SELECT
                savHistorique
            FROM
                COMMANDES
            WHERE
                idCommande = :idCommande
            ;
        `,{
            idCommande: idCommande || null,
        });
        let savHistorique = commande[0].savHistorique

        const demandeurs = await db.query(`
            SELECT
                pr.idPersonne
            FROM
                COMMANDES_DEMANDEURS ca
                LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne
            WHERE
                idCommande = :idCommande
                AND mailPersonne != ''
                AND mailPersonne IS NOT NULL
            ;
        `,{
            idCommande: idCommande || null,
        });

        const affectees = await db.query(`
            SELECT
                pr.idPersonne
            FROM
                COMMANDES_AFFECTEES ca
                LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne
            WHERE
                idCommande = :idCommande
                AND mailPersonne != ''
                AND mailPersonne IS NOT NULL
            ;
        `,{
            idCommande: idCommande || null,
        });

        const observateurs = await db.query(`
            SELECT
                pr.idPersonne
            FROM
                COMMANDES_OBSERVATEURS ca
                LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne
            WHERE
                idCommande = :idCommande
                AND mailPersonne != ''
                AND mailPersonne IS NOT NULL
            ;
        `,{
            idCommande: idCommande || null,
        });

        const valideurs = await getValideurs(idCommande);

        const responsablesCentreCouts = await db.query(`
            SELECT
                p.idPersonne
            FROM
                CENTRE_COUTS_PERSONNES c
                INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne
            WHERE
                idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande);
        `,{
            idCommande: idCommande || null,
        });

        switch (idEtatCible) {
            case 1:
                logger.debug('Envoi de notifications pour la commande '+idCommande+' qui passe au statut 1')
                if(configForNotif.notifications_commandes_demandeur_validationNOK)
                {
                    for(const personne of demandeurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande refusée',
                            otherContent: "La commande dont vous êtes le demandeur a été refusée et est à nouveau disponible pour édition."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_valideur_validationNOK)
                {
                    for(const personne of valideurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande refusée',
                            otherContent: "La commande dont vous êtes le valideur a été refusée."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_affectee_validationNOK)
                {
                    for(const personne of affectees)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande refusée',
                            otherContent: "La commande qui vous est affectée a été refusée et est à nouveau disponible pour édition."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_observateur_validationNOK)
                {
                    for(const personne of observateurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande refusée',
                            otherContent: "Pour information, la commande dont vous êtes observateur a été refusée."
                        })
                    }
                }
            break;

            case 2:
                logger.debug('Envoi de notifications pour la commande '+idCommande+' qui passe au statut 2')
                if(configForNotif.notifications_commandes_demandeur_validation)
                {
                    for(const personne of demandeurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Validation demandée',
                            otherContent: "La commande dont vous êtes le demandeur a bien été soumise à validation. Elle sera traitée par les personnes affectées dès lors que les valideurs auront approuvé la commande."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_valideur_validation)
                {
                    for(const personne of valideurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Demande de validation en cours chez vous',
                            otherContent: "La commande dont vous êtes le valideur a été soumise à validation. Merci de vous connecter à l'application pour valider ou refuser la commande."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_affectee_validation)
                {
                    for(const personne of affectees)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Validation demandée',
                            otherContent: "La commande qui vous est affectée a été soumise à validation. Vous serez en mesure de travailler dessus dès que celle-ci sera validée."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_observateur_validation)
                {
                    for(const personne of observateurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Validation demandée',
                            otherContent: "Pour information, la commande dont vous êtes observateur a été soumise à validation."
                        })
                    }
                }
            break;

            case 3:
                logger.debug('Envoi de notifications pour la commande '+idCommande+' qui passe au statut 3')
                if(configForNotif.notifications_commandes_demandeur_validationOK)
                {
                    for(const personne of demandeurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande validée',
                            otherContent: "La commande dont vous êtes le demandeur a été validée. Elle est désormais entre les mains des personnes affectées à son traitement afin qu'elle soit passée chez le fournisseur."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_valideur_validationOK)
                {
                    for(const personne of valideurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande validée',
                            otherContent: "La commande dont vous êtes le valideur a bien été validée."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_affectee_validationOK)
                {
                    for(const personne of affectees)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande validée',
                            otherContent: "La commande qui vous est affectée a été validée. Elle est désormais entre vos main pour être passée auprès du fournisseur."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_observateur_validationOK)
                {
                    for(const personne of observateurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande validée',
                            otherContent: "Pour information, la commande dont vous êtes observateur a été validée."
                        })
                    }
                }
            break;

            case 4:
                logger.debug('Envoi de notifications pour la commande '+idCommande+' qui passe au statut 4')
                if(configForNotif.notifications_commandes_demandeur_passee)
                {
                    for(const personne of demandeurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande passée',
                            otherContent: "La commande dont vous êtes le demandeur a été passée auprès du fournisseur et est désormais en attente de livraison."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_valideur_passee)
                {
                    for(const personne of valideurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande passée',
                            otherContent: "La commande dont vous êtes le valideur a été passée auprès du fournisseur et est désormais en attente de livraison."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_affectee_passee)
                {
                    for(const personne of affectees)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande passée',
                            otherContent: "La commande qui vous est affectée a été passée auprès du fournisseur et est désormais en attente de livraison."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_observateur_passee)
                {
                    for(const personne of observateurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande passée',
                            otherContent: "Pour information, la commande dont vous êtes observateur a été passée auprès du fournisseur et est désormais en attente de livraison."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_valideur_centreCout)
                {
                    for(const personne of responsablesCentreCouts)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Intégration d\'une commande au centre de couts',
                            otherContent: "Une commande rattachée à l'un de vos centres de couts vient d'être passée auprès du fournisseur. Dès à présent et dès que celle-ci aura été payée au fournisseur, nous vous invitons à l'intégrer dans le livre de comptes de votre centre de couts."
                        })
                    }
                }
            break;

            case 5:
                logger.debug('Envoi de notifications pour la commande '+idCommande+' qui passe au statut 5')
                if(savHistorique)
                {
                    if(configForNotif.notifications_commandes_demandeur_savOK)
                    {
                        for(const personne of demandeurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Fin du SAV',
                                otherContent: "La commande dont vous êtes le demandeur a terminé son traitement SAV et est passée à un stade de livraison acceptée. Elle doit maintenant être intégrée au stock."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_valideur_savOK)
                    {
                        for(const personne of valideurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Fin du SAV',
                                otherContent: "La commande dont vous êtes le valideur a terminé son traitement SAV et est passée à un stade de livraison acceptée. Elle doit maintenant être intégrée au stock."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_affectee_savOK)
                    {
                        for(const personne of affectees)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Fin du SAV',
                                otherContent: "La commande qui vous est affectée a terminé son traitement SAV et est passée à un stade de livraison acceptée. Vous devez maintenance l'intégrer au stock."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_observateur_savOK)
                    {
                        for(const personne of observateurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Fin du SAV',
                                otherContent: "Pour information, la commande dont vous êtes observateur a terminé son traitement SAV et est passée à un stade de livraison acceptée. Elle doit maintenant être intégrée au stock."
                            })
                        }
                    }
                }else{
                    if(configForNotif.notifications_commandes_demandeur_livraisonOK)
                    {
                        for(const personne of demandeurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Livraison OK',
                                otherContent: "La commande dont vous êtes le demandeur a été livrée correctement sans déclenchement de SAV. Elle doit maintenant être intégrée au stock."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_valideur_livraisonOK)
                    {
                        for(const personne of valideurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Livraison OK',
                                otherContent: "La commande dont vous êtes le valideur a été livrée correctement sans déclenchement de SAV. Elle doit maintenant être intégrée au stock."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_affectee_livraisonOK)
                    {
                        for(const personne of affectees)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Livraison OK',
                                otherContent: "La commande qui vous est affectée a été livrée correctement sans déclenchement de SAV. Vous devez maintenance l'intégrer au stock."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_observateur_livraisonOK)
                    {
                        for(const personne of observateurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Livraison OK',
                                otherContent: "Pour information, la commande dont vous êtes observateur a été livrée correctement sans déclenchement de SAV. Elle doit maintenant être intégrée au stock."
                            })
                        }
                    }
                }
            break;

            case 6:
                logger.debug('Envoi de notifications pour la commande '+idCommande+' qui passe au statut 6')
                if(configForNotif.notifications_commandes_demandeur_livraisonNOK)
                    {
                        for(const personne of demandeurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Livraison défaillante - Engagement SAV',
                                otherContent: "La commande dont vous êtes le demandeur a été livrée avec un défaut nécessitant une démarche SAV."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_valideur_livraisonNOK)
                    {
                        for(const personne of valideurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Livraison défaillante - Engagement SAV',
                                otherContent: "La commande dont vous êtes le valideur a été livrée avec un défaut nécessitant une démarche SAV."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_affectee_livraisonNOK)
                    {
                        for(const personne of affectees)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Livraison défaillante - Engagement SAV',
                                otherContent: "La commande qui vous est affectée a été livrée avec un défaut nécessitant une démarche SAV. Le suivi du SAV est a effecté dans le champ des commentaires de livraison."
                            })
                        }
                    }
                    if(configForNotif.notifications_commandes_observateur_livraisonNOK)
                    {
                        for(const personne of observateurs)
                        {
                            await fonctionsMail.registerToMailQueue({
                                typeMail: 'commandeNotif',
                                idObject: idCommande,
                                idPersonne: personne.idPersonne,
                                otherSubject: 'Livraison défaillante - Engagement SAV',
                                otherContent: "Pour information, la commande dont vous êtes observateur a été livrée avec un défaut nécessitant une démarche SAV."
                            })
                        }
                    }
            break;

            case 7:
                logger.debug('Envoi de notifications pour la commande '+idCommande+' qui passe au statut 7')
                if(configForNotif.notifications_commandes_demandeur_cloture)
                {
                    for(const personne of demandeurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande cloturée',
                            otherContent: "La commande dont vous êtes le demandeur a été cloturée. Son traitement prend donc fin."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_valideur_cloture)
                {
                    for(const personne of valideurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande cloturée',
                            otherContent: "La commande dont vous êtes le valideur a été cloturée. Son traitement prend donc fin."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_affectee_cloture)
                {
                    for(const personne of affectees)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande cloturée',
                            otherContent: "La commande qui vous est affectée a été cloturée. Son traitement prend donc fin."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_observateur_cloture)
                {
                    for(const personne of observateurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande cloturée',
                            otherContent: "Pour information, la commande dont vous êtes observateur a été cloturée. Son traitement prend donc fin."
                        })
                    }
                }
            break;

            case 8:
                logger.debug('Envoi de notifications pour la commande '+idCommande+' qui passe au statut 8')
                if(configForNotif.notifications_commandes_demandeur_abandon)
                {
                    for(const personne of demandeurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande abandonnée',
                            otherContent: "La commande dont vous êtes le demandeur a été abandonnée. Son traitement prend donc fin sans aucune autre action nécessaire."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_valideur_abandon)
                {
                    for(const personne of valideurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande abandonnée',
                            otherContent: "La commande dont vous êtes le valideur a été abandonnée. Son traitement prend donc fin sans aucune autre action nécessaire."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_affectee_abandon)
                {
                    for(const personne of affectees)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande abandonnée',
                            otherContent: "La commande qui vous est affectée a été abandonnée. Son traitement prend donc fin sans aucune autre action nécessaire."
                        })
                    }
                }
                if(configForNotif.notifications_commandes_observateur_abandon)
                {
                    for(const personne of observateurs)
                    {
                        await fonctionsMail.registerToMailQueue({
                            typeMail: 'commandeNotif',
                            idObject: idCommande,
                            idPersonne: personne.idPersonne,
                            otherSubject: 'Commande abandonnée',
                            otherContent: "Pour information, la commande dont vous êtes observateur a été abandonnée. Son traitement prend donc fin sans aucune autre action nécessaire."
                        })
                    }
                }
            break;
        
            default:
            break;
        }

    } catch (error) {
        logger.error(error)
    }
}

const ajouterCommentaireTimeLineCommande = async (idCommande, commentaire, idComIcon) => {
    try {
        let commande = await db.query(`
            INSERT INTO
                COMMANDES_TIMELINE
            SET
                idCommande = :idCommande,
                detailsEvtCommande = :commentaire,
                idComIcon = :idComIcon,
                dateEvtCommande = CURRENT_TIMESTAMP
            ;
        `,{
            idCommande: idCommande || null,
            commentaire: commentaire || null,
            idComIcon: idComIcon || null,
        });
    } catch (error) {
        logger.error(error)
    }
}

const calculerTotalCentreDeCouts = async (idCentreDeCout) => {
    try {
        const updateTotal = await db.query(`
            UPDATE
                CENTRE_COUTS
            SET
                soldeActuel = IFNULL((
                    SELECT
                        SUM(CAST(IFNULL(montantEntrant,0) - IFNULL(montantSortant,0) AS DECIMAL(10,2)))
                    FROM
                        CENTRE_COUTS_OPERATIONS c
                    WHERE
                        idCentreDeCout = :idCentreDeCout
                ),0)
            WHERE
                idCentreDeCout = :idCentreDeCout
        `,{
            idCentreDeCout: idCentreDeCout
        });
    } catch (error) {
        logger.error(error)
    }
}

const calculerTousTotauxCentreDeCouts = async () => {
    try {
        let getAllCentres = await db.query(`
            SELECT
                idCentreDeCout
            FROM
                CENTRE_COUTS
        `);
        for(const centre of getAllCentres)
        {
            await calculerTotalCentreDeCouts(centre.idCentreDeCout);
        }
    } catch (error) {
        logger.error(error)
    }
}

const checkGestionnaireStatut = (centreDeCout, gestionnaire) => {
    try {
        let estActif = true;

        if(centreDeCout.statutOuverture != '1 - Ouvert' && gestionnaire.validerClos != true)
        {
            estActif = false;
        }

        if(centreDeCout.soldeActuel < 0 && gestionnaire.depasseBudget != true)
        {
            estActif = false;
        }
        return estActif;
    } catch (error) {
        logger.log(error)
    }
}

const cleanTempFolder = () => {
    try {
        const fs = require("fs");
        const path = require("path");

        const directory = "temp";

        fs.readdir(directory, (err, files) => {
            if (err) throw err;

            for (const file of files) {
                if(file != 'do_not_delete.txt')
                {
                    fs.unlink(path.join(directory, file), (err) => {
                        if (err) throw err;
                    });
                }
            }
        });
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
    initiateOldValuesInventaireLot,
    validerInventaireLot,
    figeInventaireLot,
    updateInventaireReserveItem,
    initiateOldValuesInventaireReserve,
    validerInventaireReserve,
    figeInventaireReserve,
    majIndicateursPersonne,
    majIndicateursProfil,
    majNotificationsPersonne,
    majNotificationsProfil,
    majValideursPersonne,
    checkFunctionnalityBenevolesEnabled,
    ajouterItemConsommation,
    updateItemConsommation,
    supprimerItemConsommation,
    terminerSaisieConsommation,
    updateReconditionnementConsommation,
    terminerReconditionnementConsommation,
    validerUnElementConsomme,
    comptabiliserToutesConsommations,
    queueNotificationJournaliere,
    calculerTotalCommande,
    calculerTousTotauxCommandes,
    getValideurs,
    cmdEstValideur,
    cmdEstAffectee,
    cmdEstValideurUniversel,
    cmdEstObservateur,
    cmdEstDemandeur,
    centreCoutsEstCharge,
    verificationContraintesCmd,
    verificationAvantChangementEtatCmd,
    envoyerNotifAuChangementStatutCommande,
    ajouterCommentaireTimeLineCommande,
    calculerTotalCentreDeCouts,
    calculerTousTotauxCentreDeCouts,
    checkGestionnaireStatut,
    cleanTempFolder,
};