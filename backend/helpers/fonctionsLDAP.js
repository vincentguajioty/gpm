const db = require('../db');
const ldap = require('ldapjs');
const logger = require('../winstonLogger');

const createClient = async () => {
    return new Promise(async (resolve, reject) => {
        try {
            const LDAP_DOMAIN = process.env.LDAP_DOMAIN;
            const LDAP_URL = process.env.LDAP_URL;
            const LDAP_SSL = process.env.LDAP_SSL;
            
            let opts = {
                rejectUnauthorized: false,
                hostname: LDAP_DOMAIN,
            }

            let client = ldap.createClient({
                url: LDAP_URL,
                reconnect: true,
                tlsOptions: opts,
            });

            client.on('error', err=>{
                logger.error('Connexion en échec');
                logger.error(err);
            });

            logger.debug('createClient:');
            logger.debug(client);

            if(LDAP_SSL == 1)
            {
                logger.debug('SSL demandé par la configuration LDAP_SSL: '+LDAP_SSL);

                client = await enableSSL(client);
                
                logger.debug("Client en sortie de Starttls:");
                logger.debug(client);
            }

            logger.debug(client);
            logger.debug('Log avant la sortie de createClient')
            resolve(client);
        } catch (error) {
            logger.error(error)
            reject();
        }
    })
}

const enableSSL = async (client) => {
    return new Promise((resolve, reject) => {
        try {
            const LDAP_DOMAIN = process.env.LDAP_DOMAIN;
            
            let opts = {
                rejectUnauthorized: false,
                hostname: LDAP_DOMAIN,
            }

            client.starttls(opts, undefined, (err, res) => {
                logger.debug("Callback de starttls");

                resolve(client);
            });
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
                    logger.debug(err);
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

const seachLdapClientAD = async (client, opts, LDAP_BASEDN) => {
    return new Promise((resolve, reject) => {
        try {
            client.search(LDAP_BASEDN, opts,(err,res) => {
                if(err)
                {
                    logger.error("Recherche dans l'AD version WINAD en echec")
                    logger.error(err);
                    reject(false);
                }
                else
                {
                    logger.debug("Recherche dans l'AD version AD réussie")
                    
                    res.on('searchEntry', entry => {
                        logger.debug("Utilisateur trouvé dans l'AD version WINAD.")
                        resolve(entry);
                    });
                    res.on('error', err => {
                        logger.debug("Utilisateur non-trouvé dans l'AD version WINAD.")
                            reject();
                    });
                }
            });
        } catch (error) {
            logger.error("Erreur dans la fonction seachLdapClientAD")
            logger.error(error)
            reject();
        }
    })
}

const seachLdapClientLDAP = async (client, usernameUser, opts) => {
    return new Promise((resolve, reject) => {
        try {
            client.search(usernameUser, opts, async (err, res) => {
                if(err){
                    logger.error("Recherche dans l'AD version LDAP en echec")
                    logger.error(err);
                    reject(false);
                }
                else
                {
                    logger.debug("Recherche dans l'AD version LDAP réussie")
                    
                    res.on('error', async function() {
                        logger.debug("Utilisateur non-trouvé dans l'AD version LDAP.")
                        reject();
                    });

                    res.on('searchEntry', async function (data) {
                        logger.debug("Utilisateur trouvé dans l'AD version LDAP.")
                        resolve(data);
                    })
                }
            })
        } catch (error) {
            logger.error("Erreur dans la fonction seachLdapClientLDAP")
            logger.error(error)
            reject();
        }
    })
}

const updateProfilsFromAd = async (idPersonne) => {
    try {
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
        let opts = {};

        if(LDAP_ISWINAD == 1)
        {
            usernameAD = LDAP_USER+"@"+LDAP_DOMAIN;
            usernameUser = userToHandle[0].identifiant+"@"+LDAP_DOMAIN;
            opts = {
                scope: "sub",
                filter: `(userPrincipalName=${usernameUser})`

            };
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

        logger.debug("updateProfilsFromAd - Client: ");
        logger.debug(client);
        
        client = await bindLdapClient(client, usernameAD, LDAP_PASSWORD);

        logger.debug('Client après bindLDAP demandé par la fonction updateProfilsFromAd');
        logger.debug(client);

        logger.debug("updateProfilsFromAd - Nettoyage des profils existants");
        const cleanProfiles = await db.query(
            'DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne',
            {
                idPersonne : idPersonne,
            }
        );
        
        
        logger.debug('seachLdapClient - usernameUser : '+usernameUser);
        let searchRes;
        if(LDAP_ISWINAD == 1)
        {
            searchRes = await seachLdapClientAD(client, opts, LDAP_BASEDN);
        }else{
            searchRes = await seachLdapClientLDAP(client, usernameUser, opts);
        }
        
        logger.debug("Résultat du searchLDAPClient:");
        logger.debug(searchRes);

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

        client.destroy();
        return true;
    } catch (error) {
        logger.error(error)
        return(false);
    }
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
    seachLdapClientAD,
    seachLdapClientLDAP,
    updateProfilsFromAd,
    updateAllUsersFromAD,
    killTokensForNoProfils,
};