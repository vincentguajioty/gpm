const db = require('./db');
const logger = require('./winstonLogger');
const dotenv = require('dotenv').config();
const fonctionsLDAP = require('./fonctionsLDAP');
const jwtFunctions = require('./jwt');
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
                    MAX(dateHealth) as dateHealth
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
            });

            if(last.length > 0 && last[0].dateHealth != null)
            {
                let lastDateHealt = new Date(last.dateHealth);
                let alertDate = lastDateHealt.getDate() + alerte.frequenceHealth;
                if(alertDate <= new Date())
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
                    MAX(dateDesinfection) as dateDesinfection
                FROM
                    VEHICULES_DESINFECTIONS
                WHERE
                    idVehicule = :idVehicule
                    AND
                    idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
            `,{
                idVehicule: idVehicule,
                idVehiculesDesinfectionsType: alerte.idVehiculesDesinfectionsType,
            });

            if(last.length > 0 && last[0].dateDesinfection != null)
            {
                let lastDateHealt = new Date(last.dateDesinfection);
                let alertDate = lastDateHealt.getDate() + alerte.frequenceDesinfection;
                if(alertDate <= new Date())
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

        // TODO
        // majIndicateursPersonne($idPersonne,1);
        // majNotificationsPersonne($idPersonne,1);
        // majValideursPersonne(1);

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
    cnilAnonyme,
    cnilAnonymeCron,
};