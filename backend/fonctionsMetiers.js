const db = require('./db');
const logger = require('./winstonLogger');
const dotenv = require('dotenv').config();
const fonctionsLDAP = require('./fonctionsLDAP');
const jwtFunctions = require('./jwt');

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

module.exports = {
    majLdapOneUser,
    majLdapAllUsers,
    deconnecterUtilisateur,
    deconnecterProfil,
    deconnecterToutLeMonde,
};