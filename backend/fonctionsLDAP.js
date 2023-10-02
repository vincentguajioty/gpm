const db = require('./db');
const ldap = require('ldapjs');
const dotenv = require('dotenv').config();
const logger = require('./winstonLogger');

const createClient = async () => {
    return new Promise((resolve, reject) => {
        try {
            const LDAP_DOMAIN = process.env.LDAP_DOMAIN;
            const LDAP_SSL = process.env.LDAP_SSL;

            let client = ldap.createClient({
                url: [LDAP_DOMAIN]
            });

            if(LDAP_SSL == 1)
            {
                client.starttls({}, (err, res) => {
                    logger.error(err);
                });
            }

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

module.exports = {
    createClient,
    bindLdapClient,
    seachLdapClient,
    updateProfilsFromAd,
};