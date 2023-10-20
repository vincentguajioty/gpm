const db = require('../db');
const brcypt = require('bcryptjs');
const dotenv = require('dotenv').config();
const logger = require('../winstonLogger');
const axios = require('axios');
const fonctionsLDAP = require('../helpers/fonctionsLDAP');

const ldapUserLogin = async (identifiant, motDePasse) => {
    try {
        const LDAP_DOMAIN = process.env.LDAP_DOMAIN;
        const LDAP_BASEDN = process.env.LDAP_BASEDN;
        const LDAP_ISWINAD = process.env.LDAP_ISWINAD;

        let username;

        if(LDAP_ISWINAD == 1)
        { username = identifiant+"@"+LDAP_DOMAIN}
        else
        { username = "uid="+identifiant+",cn=users,"+LDAP_BASEDN }

        logger.debug("ldapUserLogin - LDAP_DOMAIN : "+LDAP_DOMAIN);
        logger.debug("ldapUserLogin - LDAP_BASEDN : "+LDAP_BASEDN);
        logger.debug("ldapUserLogin - LDAP_ISWINAD : "+LDAP_ISWINAD);
        logger.debug("ldapUserLogin - username : "+username);

        let client = await fonctionsLDAP.createClient();

        logger.debug("ldapUserLogin - Client: ");
        logger.debug(client);
        client = await fonctionsLDAP.bindLdapClient(client, username, motDePasse);
        logger.debug(client);
        if(client === false)
        {
            logger.debug('Retour de la connexion client en FALSE');
            return false;
        }
        else
        {
            logger.debug('Retour de la connexion client en TRUE');
            return true;
        }
    } catch (error) {
        logger.error(error)
        return false;
    }
}

const localUserLogin = async (motDePasse, selectedUser) => {
    const mdpAvecSels = process.env.LOCAL_SELS_PRE + motDePasse + process.env.LOCAL_SELS_POST;
    const check = await brcypt.compare(mdpAvecSels, selectedUser.motDePasse);
    if(check){return true};

    return false;
}

const checkCaptcha = async (reCaptchaToken) => {
    const URL = process.env.RECAPTCHA_CHECKURL + '?secret='+process.env.RECAPTCHA_SECRET+'&response='+reCaptchaToken;
            
    const checkGoogle = await axios.post(URL);

    if(checkGoogle.data.success != true)
    {
        return false;
    }
    return true;
}

module.exports = {
    ldapUserLogin,
    localUserLogin,
    checkCaptcha,
};