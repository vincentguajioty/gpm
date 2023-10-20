const db = require('../db');
const ldap = require('ldapjs');
const dotenv = require('dotenv').config();
const logger = require('../winstonLogger');

const createClient = async () => {
    return new Promise((resolve, reject) => {
        try {
            const LDAP_URL = process.env.LDAP_URL;
            const LDAP_SSL = process.env.LDAP_SSL;

            let client = ldap.createClient({
                url: [LDAP_URL]
            });

            logger.debug('createClient:');
            logger.debug(client);

            if(LDAP_SSL == 1)
            {
                logger.debug('SSL nécessaire');
                client.starttls({}, (err, res) => {
                    if(err)
                    {logger.debug('Erreur au step SSL');
                    logger.error(err);}
                });
            }

            logger.debug(client);

            resolve(client);
        } catch (error) {
            logger.error(error)
            reject();
        }
    })
}

const bindLdapClient = async (client, username, password) => {
    return new Promise((resolve, reject) => {
        try {
            client.bind(username, password, async (err, result) => {
                if(err){
                    logger.error("Erreur de connexion AD avec l'utilisateur "+username)
                    reject(false);
                }
                if(result)
                {
                    logger.debug("Connexion AD en succès avec l'utilisateur "+username)
                    resolve(client);
                }
            })
        } catch (error) {
            logger.error(error)
            reject();
        }
    })
}

const seachLdapClient = async (client, usernameUser, opts) => {
    return new Promise((resolve, reject) => {
        try {
            client.search(usernameUser, opts, async (err, res) => {
                if(err){
                    logger.error(err);
                    reject(false);
                }
                else
                {
                    logger.debug("Recherche dans l'AD réussie")
                    
                    res.on('error', async function() {
                        logger.debug("Utilisateur non-trouvé dans l'AD.")
                        reject();
                    });

                    res.on('searchEntry', async function (data) {
                        logger.debug("Utilisateur trouvé dans l'AD.")
                        resolve(data);
                    })
                }
            })
        } catch (error) {
            logger.error(error)
            reject();
        }
    })
}

const updateProfilsFromAd = async (idPersonne) => {
    const LDAP_DOMAIN = process.env.LDAP_DOMAIN;
    const LDAP_BASEDN = process.env.LDAP_BASEDN;
    const LDAP_ISWINAD = process.env.LDAP_ISWINAD;
    const LDAP_USER = process.env.LDAP_USER;
    const LDAP_PASSWORD = process.env.LDAP_PASSWORD;

    const userToHandle = await db.query(
        'SELECT idPersonne, identifiant FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;',
        {
            idPersonne : idPersonne,
        }
    );

    const profilsListe = await db.query(
        'SELECT * FROM PROFILS WHERE LDAP_BINDDN IS NOT NULL;');

    let usernameAD;
    let usernameUser;

    if(LDAP_ISWINAD == 1)
    {
        usernameAD = LDAP_USER+"@"+LDAP_DOMAIN;
        usernameUser = userToHandle[0].identifiant+"@"+LDAP_DOMAIN;
    }
    else
    {
        usernameAD = "uid="+LDAP_USER+",cn=users,"+LDAP_BASEDN;
        usernameUser = "uid="+userToHandle[0].identifiant+",cn=users,"+LDAP_BASEDN;
    }

    logger.debug('updateProfilsFromAd - LDAP_DOMAIN : '+LDAP_DOMAIN);
    logger.debug('updateProfilsFromAd - LDAP_BASEDN : '+LDAP_BASEDN);
    logger.debug('updateProfilsFromAd - LDAP_ISWINAD : '+LDAP_ISWINAD);
    logger.debug('updateProfilsFromAd - usernameAD : '+usernameAD);

    let client = await createClient();

    logger.debug("updateProfilsFromAd - Client: "+client);

    client = await bindLdapClient(client, usernameAD, LDAP_PASSWORD);

    logger.debug("updateProfilsFromAd - Nettoyage des profils existants");
    const cleanProfiles = await db.query(
        'DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne',
        {
            idPersonne : idPersonne,
        }
    );
    
    const opts = {};
    let searchRes = await seachLdapClient(client, usernameUser, opts);

    for(const oneGroupFromAd of searchRes.object.memberOf)
    {
        logger.debug('Groupe AD trouvé: '+oneGroupFromAd);
        for(const profil of profilsListe)
        {
            if(oneGroupFromAd.includes(profil.LDAP_BINDDN))
            {
                logger.debug('Match entre le groupe AD trouvé: '+oneGroupFromAd+' et le BIND_DN du profil '+profil.LDAP_BINDDN);
                const addProfile = await db.query(
                    'INSERT INTO PROFILS_PERSONNES SET idPersonne = :idPersonne, idProfil = :idProfil',
                    {
                        idPersonne : idPersonne,
                        idProfil: profil.idProfil,
                    }
                );
                logger.debug('Match inséré en base');
            }
            else
            {
                logger.debug('Pas de match entre le groupe AD '+oneGroupFromAd+' et le BIND_DN du profil '+profil.LDAP_BINDDN);
            }
        }
    }
    return true;
}

const updateAllUsersFromAD = async () => {
    try {
        let allPersonnes = await db.query(`
            SELECT * FROM PERSONNE_REFERENTE WHERE isActiveDirectory = 1 AND cnil_anonyme = 0;
        `);
        for(const personne of allPersonnes)
        {
            update = await updateProfilsFromAd(personne.idPersonne);
            if(update)
            {
                logger.info("Mise à jour LDAP avec succès de l'utilisateur "+personne.idPersonne);
            }
            else
            {
                logger.error("Mise à jour LDAP en echec de l'utilisateur "+personne.idPersonne);
            }
        }
    } catch (error) {
        logger.error(error);
    }
}

const killTokensForNoProfils = async () => {
    try {
        const utilisateursToBeKilled = await db.query(`
            SELECT
                pr.idPersonne
            FROM
                PERSONNE_REFERENTE pr
                LEFT OUTER JOIN VIEW_HABILITATIONS vh ON pr.idPersonne = vh.idPersonne
            WHERE
                vh.connexion_connexion = false
        `);
        for(const user of utilisateursToBeKilled)
        {
            const sessionToBlacklist = await db.query(
                'SELECT * FROM JWT_SESSIONS WHERE idPersonne = :idPersonne',
            {
                idPersonne : user.idPersonne,
            });
    
            for(const toBlackList of sessionToBlacklist)
            {
                let insertRequest = await db.query(
                    `INSERT INTO JWT_SESSIONS_BLACKLIST SET 
                        blockedDateTime = CURRENT_TIMESTAMP,
                        jwtToken = :token
                    ;`,
                {
                    token        : toBlackList.jwtToken,
                });
            }
            
            for(const toBlackList of sessionToBlacklist)
            {
                insertRequest = await db.query(
                    `INSERT INTO JWT_SESSIONS_BLACKLIST SET 
                        blockedDateTime = CURRENT_TIMESTAMP,
                        jwtToken = :refreshtoken
                    ;`,
                {
                    refreshtoken : toBlackList.jwtRefreshToken,
                });
            }
    
            const deleteFromCurrentList = await db.query(
                'DELETE FROM JWT_SESSIONS WHERE idPersonne = :idPersonne',
            {
                idPersonne : user.idPersonne,
            });
        }
    } catch (error) {
        logger.error(error)
    }
}

module.exports = {
    createClient,
    bindLdapClient,
    seachLdapClient,
    updateProfilsFromAd,
    updateAllUsersFromAD,
    killTokensForNoProfils,
};