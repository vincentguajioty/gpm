const db = require('../db');
const brcypt = require('bcryptjs');
const authenticator = require('authenticator');
const jwt = require('jsonwebtoken');
const jwtFunctions = require('../jwt');
const logger = require('../winstonLogger');
const moment = require('moment');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsLDAP = require('../helpers/fonctionsLDAP');
const fonctionsAuthentification = require('../helpers/fonctionsAuthentification');
const fonctionsMail = require('../helpers/fonctionsMail');

const jwtExpirySeconds = parseInt(process.env.JWT_EXPIRATION);
const jwtRefreshExpirySeconds = parseInt(process.env.JWT_REFRESH_EXPIRATION);

exports.mfaNeeded = async (req, res)=>{
    try {
        let results = await db.query(
            'SELECT * FROM PERSONNE_REFERENTE WHERE identifiant = :identifiant;',
            {
                identifiant : req.body.identifiant,
            }
        );

        if(results.length == 1 && results[0].mfaEnabled)
        {
            return res.json({mfaNeeded: true});
        }
        else
        {
            return res.json({mfaNeeded: false});
        }

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.login = async (req, res)=>{
    try {
        const identifiant = req.body.identifiant;
        const motDePasse = req.body.motDePasse;
        const reCaptchaToken = req.body.reCaptchaToken;

        let passwordCheckIsOk;

        //ETAPE 1 - On vérifie le recaptcha
        logger.debug("ETAPE 1 - On vérifie le recaptcha");
        if(process.env.RECAPTCHA_ENABLED == "1")
        {
            if(!reCaptchaToken)
            {
                logger.warn('Erreur de recaptcha: token manquant');
                return res.json({auth: false, message:"reCaptcha manquant"});
            }
            
            let googleCheckResult = await fonctionsAuthentification.checkCaptcha(reCaptchaToken);

            if(googleCheckResult === true)
            {
                logger.info('Recaptcha validé');
            }
            else
            {
                logger.warn('Erreur de recaptcha: token invalide');
                return res.json({auth: false, message:"reCaptcha invalide"});
            }
        }

        //ETAPE 2 - On vérifie si l'utilisateur est déjà existant
        logger.debug("ETAPE 2 - On vérifie si l'utilisateur est déjà existant");
        let results = await db.query(
            'SELECT * FROM PERSONNE_REFERENTE WHERE identifiant = :identifiant;',
            {
                identifiant : identifiant,
            }
        );

        if(results.length > 1)
        {
            //Plusieurs utilisateurs existent, il y a une incohérence de données
            logger.debug("Plusieurs utilisateurs existent, il y a une incohérence de données");
            logger.error('Plusieurs utilisateurs avec le même identifiant - Blocage de la connexion');
            return res.json({auth: false, message:"Erreur de données côté serveur"});
        }

        if(results.length == 1)
        {
            //L'utilisateur est déjà existant
            logger.debug("L'utilisateur est déjà existant");
            let selectedUser = results[0];

            //On vérifie son mot de passe
            logger.debug("On vérifie son mot de passe");
            if(selectedUser.isActiveDirectory && process.env.LDAP_ENABLED == "1")
            {
                logger.debug("On lance une vérification LDAP");
                passwordCheckIsOk = await fonctionsAuthentification.ldapUserLogin(identifiant, motDePasse);
            }
            else
            {
                logger.debug("On lance une vérification Locale");
                passwordCheckIsOk = await fonctionsAuthentification.localUserLogin(req.body.motDePasse, selectedUser);
            }

            logger.debug("Retour de la verif mdp: "+passwordCheckIsOk);

            if(passwordCheckIsOk == true)
            {
                //Le mot de passe est bon, on vérifie son MFA
                logger.debug("Le mot de passe est bon, on vérifie son MFA");
                if(selectedUser.mfaEnabled)
                {
                    const mfaCheck = authenticator.verifyToken(selectedUser.mfaSecret, req.body.mfa);
                    if(mfaCheck == null)
                    {
                        logger.warn('Erreur de MFA', {idPersonne: selectedUser.idPersonne});
                        return res.json({auth: false, message:"MFA invalide"});
                    }
                    logger.debug('Vérification du MFA OK');
                }
                
                //Le mot de passe est bon, le contrôle MFA aussi, si c'est un user AD, on met à jour ses groupes
                logger.debug("Le mot de passe est bon, le contrôle MFA aussi, si c'est un user AD, on met à jour ses groupes");
                if(selectedUser.isActiveDirectory && process.env.LDAP_ENABLED == "1")
                {
                    await fonctionsLDAP.updateProfilsFromAd(selectedUser.idPersonne);
                }

                //On récupère l'utilisateur avec ses droits - il existe peut-être mais n'a aucun droit
                logger.debug("On récupère l'utilisateur avec ses droits - il existe peut-être mais n'a aucun droit");
                results = await db.query(
                    'SELECT * FROM VIEW_HABILITATIONS WHERE identifiant = :identifiant AND connexion_connexion = 1;',
                    {
                        identifiant : identifiant,
                    }
                );

                if(results.length == 1)
                {
                    //L'utilisateur a bien ses droits, on le connecte
                    logger.debug("L'utilisateur a bien ses droits, on le connecte");
                    selectedUser = results[0];
                    delete selectedUser.motDePasse;
                    delete selectedUser.mfaSecret;

                    let disclaimerAccept = selectedUser.disclaimerAccept == null ? 'false' : true;

                    const tokenValidUntil = moment(new Date()).add(jwtExpirySeconds, 'seconds');
                    const token = jwt.sign(selectedUser, process.env.JWT_TOKEN, {
                        expiresIn: jwtExpirySeconds,
                    });
                    const refreshTokenValidUntil = moment(new Date()).add(jwtRefreshExpirySeconds, 'seconds');
                    const refreshToken = jwt.sign(selectedUser, process.env.JWT_REFRESH, {
                        expiresIn: jwtRefreshExpirySeconds,
                    });                    
                    req.session.utilisateur = selectedUser;
                    logger.info('Connexion en succès de ' + identifiant, {idPersonne: selectedUser.idPersonne});
                    const sessionInDB = await db.query(`
                        INSERT INTO
                            JWT_SESSIONS
                        SET
                            idPersonne = :idPersonne,
                            createdDateTime = CURRENT_TIMESTAMP,
                            jwtToken = :jwtToken,
                            jwtRefreshToken = :jwtRefreshToken,
                            tokenValidity = :tokenValidity,
                            refreshValidity = :refreshValidity
                    `,
                    {
                        idPersonne: selectedUser.idPersonne,
                        jwtToken: token,
                        jwtRefreshToken: refreshToken,
                        tokenValidity: moment(tokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
                        refreshValidity: moment(refreshTokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
                    });
                    await fonctionsMetiers.updateLastConnexion(selectedUser.idPersonne);
                    res.json({auth: true, disclaimerAccept: disclaimerAccept, token: token, tokenValidUntil: tokenValidUntil, refreshToken: refreshToken, habilitations: selectedUser});
                }
                else
                {
                    //L'utilisateur existe mais n'a pas de droits
                    logger.debug("L'utilisateur existe mais n'a pas de droits");
                    logger.warn('Connexion de l\'utilisateur rejetée car droits insuffisants', {idPersonne: selectedUser.idPersonne});
                    return res.json({auth: false, message:"Droits insuffisants"});
                }

            }
            else
            {
                //L'utilisateur a fait une erreur de mot de passe
                logger.debug("L'utilisateur a fait une erreur de mot de passe");
                logger.warn('Connexion de l\'utilisateur rejetée car erreur de mot de passe');
                return res.json({auth: false, message:"Mauvaise combinaison user/pwd"});
            }
        }
        else
        {
            //L'utilisateur est inconnu, on regarde si une création AD est possible
            logger.debug("L'utilisateur est inconnu, on regarde si une création AD est possible");
            logger.info('Pas de user trouvé pour ' + identifiant)

            if(process.env.LDAP_ENABLED == "1" && process.env.LDAP_AUTOCREATE == "1")
            {
                //Création AD possible
                logger.debug("Création AD possible");
                logger.debug('Tentative d\'autocréation depuis l\'AD pour '+identifiant);

                //Vérification que le mot de passe est bon
                logger.debug("Vérification que le mot de passe est bon");
                passwordCheckIsOk = await fonctionsAuthentification.ldapUserLogin(identifiant, motDePasse);

                if(passwordCheckIsOk == true)
                {
                    //L'authentification est bonne, on crée le user
                    logger.debug("L'authentification est bonne, on crée le user");
                    const createUser = await db.query(`
                        INSERT INTO
                            PERSONNE_REFERENTE
                        SET
                            identifiant       = :identifiant,
                            mfaEnabled        = 0,
                            isActiveDirectory = true
                        `,
                        {
                            identifiant : identifiant,
                        }
                    );
                    const createdUserId = await db.query(`
                        SELECT
                            MAX(idPersonne) as idPersonne
                        FROM
                            PERSONNE_REFERENTE
                        `
                    );

                    logger.debug("Nouvel ID utilisateur créé: "+createdUserId[0].idPersonne);
                    
                    //On met à jour son profil pour charger ses droits
                    logger.debug("On met à jour son profil pour charger ses droits");
                    await fonctionsLDAP.updateProfilsFromAd(createdUserId[0].idPersonne);

                    //On récupère ses droits
                    logger.debug("On récupère ses droits");
                    const newUserSelect = await db.query(
                        'SELECT * FROM VIEW_HABILITATIONS WHERE idPersonne = :idPersonne AND connexion_connexion = 1;',
                        {
                            idPersonne : createdUserId[0].idPersonne,
                        }
                    );
                    logger.debug("Nombre de lignes récupérées depuis la vue: "+newUserSelect.length);
                    if(newUserSelect.length == 1)
                    {
                        //L'utilisateur est créé et a des droits, on le connecte
                        logger.debug("L'utilisateur est créé et a des droits, on le connecte");
                        const newUser = newUserSelect[0];
                        delete newUser.motDePasse;
                        delete newUser.mfaSecret;

                        const tokenValidUntil = moment(new Date()).add(jwtExpirySeconds, 'seconds');
                        const token = jwt.sign(newUser, process.env.JWT_TOKEN, {
                            expiresIn: jwtExpirySeconds,
                        });
                        const refreshTokenValidUntil = moment(new Date()).add(jwtRefreshExpirySeconds, 'seconds');
                        const refreshToken = jwt.sign(newUser, process.env.JWT_REFRESH, {
                            expiresIn: jwtRefreshExpirySeconds,
                        });                    
                        req.session.utilisateur = newUser;
                        logger.info('Connexion en succès de ' + identifiant, {idPersonne: newUser.idPersonne});
                        const sessionInDB = await db.query(`
                            INSERT INTO
                                JWT_SESSIONS
                            SET
                                idPersonne = :idPersonne,
                                createdDateTime = CURRENT_TIMESTAMP,
                                jwtToken = :jwtToken,
                                jwtRefreshToken = :jwtRefreshToken,
                                tokenValidity = :tokenValidity,
                                refreshValidity = :refreshValidity
                        `,
                        {
                            idPersonne: newUser.idPersonne,
                            jwtToken: token,
                            jwtRefreshToken: refreshToken,
                            tokenValidity: moment(tokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
                            refreshValidity: moment(refreshTokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
                        });
                        await fonctionsMetiers.updateLastConnexion(newUser.idPersonne);
                        res.json({auth: true, disclaimerAccept: false, token: token, tokenValidUntil: tokenValidUntil, refreshToken: refreshToken, habilitations: newUser});
                    }
                    else
                    {
                        //L'utilisateur est créé et mais n'a des droits, on refuse la connexion
                        logger.debug("L'utilisateur est créé et mais n'a des droits, on refuse la connexion");
                        logger.warn('Création de l\'utilisateur OK mais connexion de rejetée car droits insuffisants', {idPersonne: createdUserId[0].idPersonne});
                        return res.json({auth: false, message:"Droits insuffisants"});
                    }
                }
                else
                {
                    //L'authentification est KO, on rejette la connexion
                    logger.debug("L'authentification est KO, on rejette la connexion");
                    logger.debug('Refus d\'autocréation depuis l\'AD pour '+identifiant);
                    res.json({auth: false, message:"Mauvaise combinaison user/pwd"})
                }
            }
            else
            {
                //Pas de création AD possible
                logger.debug("Pas de création AD possible");
                logger.debug('Autocréation depuis l\'AD désactivé');
                res.json({auth: false, message:"Mauvaise combinaison user/pwd"})
            }
        }
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }

}

exports.pwdReinitRequest = async (req, res)=>{
    try {
        let cleanUpToken = await db.query(
            'DELETE FROM RESETPASSWORD WHERE dateValidite < CURRENT_TIMESTAMP OR dateValidite Is Null;'
        );

        let getUser = await db.query(
            'SELECT idPersonne, isActiveDirectory, mailPersonne FROM PERSONNE_REFERENTE WHERE identifiant = :identifiant;',
            {
                identifiant : req.body.identifiant,
            }
        );
        if(getUser.length != 1)
        {
            return res.json({handleResult: 'userInconnu'});
        }

        if(getUser[0].isActiveDirectory == true)
        {
            return res.json({handleResult: 'userAD'});
        }
        
        let alreadyExistingRequests = await db.query(
            'SELECT COUNT(*) as nb FROM RESETPASSWORD WHERE idPersonne = :idPersonne AND dateValidite >= CURRENT_TIMESTAMP ;',
            {
                idPersonne : getUser[0].idPersonne,
            }
        );
        if(alreadyExistingRequests[0].nb != 0)
        {
            return res.json({handleResult: 'doublon'});
        }

        const token = jwt.sign(getUser[0].idPersonne, process.env.JWT_TOKEN);

        let generateToken = await db.query(
            `INSERT INTO
                RESETPASSWORD
            SET
                idPersonne = :idPersonne,
                tokenReset = :tokenReset,
                dateDemande = CURRENT_TIMESTAMP,
                dateValidite = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 30 MINUTE)
            ;`,
            {
                idPersonne : getUser[0].idPersonne,
                tokenReset : token,
            }
        );
        generateToken = await db.query(
            `SELECT
                MAX(idReset) as idReset
            FROM
                RESETPASSWORD
            WHERE
                idPersonne = :idPersonne
            ;`,
            {
                idPersonne : getUser[0].idPersonne,
            }
        );

        if(getUser[0].mailPersonne != null && getUser[0].mailPersonne != '' && getUser[0].mailPersonne == req.body.mailPersonne)
        {
            
            await fonctionsMail.registerToMailQueue({
                typeMail: 'autoResetPwd',
                idPersonne: getUser[0].idPersonne,
                idObject: generateToken[0].idReset,
            });
    
            return res.json({handleResult: 'mailEnvoye'});
        }
        else
        {
            return res.json({handleResult: 'resetSansMail'});
        }

        
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.pwdReinitValidate = async (req, res, next)=>{
    try {
        const saltRounds = parseInt(process.env.BCRYPT_SALTROUND);

        const resetRequest = await db.query(`
            SELECT * FROM RESETPASSWORD WHERE tokenReset = :tokenReset;
        `,
        {
            tokenReset : req.body.token,
        });

        if(resetRequest.length != 1)
        {
            return res.json({handleResult: 'echec'});
        }

        let cleanRequest = await db.query(`
            DELETE FROM RESETPASSWORD WHERE tokenReset = :tokenReset;
        `,
        {
            tokenReset : req.body.token,
        });

        const user = await db.query(`
            SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;
        `,
        {
            idPersonne : resetRequest[0].idPersonne,
        });

        let newPasswordWithSalt = process.env.LOCAL_SELS_PRE + user[0].identifiant + process.env.LOCAL_SELS_POST;
        brcypt.hash(newPasswordWithSalt, saltRounds, async (err, hash) => {
            if(err){logger.error(err);}
    
            const update = await db.query(
                'UPDATE PERSONNE_REFERENTE SET motDePasse = :motDePasse WHERE idPersonne = :idPersonne;',
            {
                motDePasse    : hash,
                idPersonne : resetRequest[0].idPersonne,
            });

            await fonctionsMail.registerToMailQueue({
                typeMail: 'resetPassword',
                idPersonne: resetRequest[0].idPersonne,
            });

            return res.json({handleResult: 'reussite'});
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.refreshToken = async (req, res)=>{
    try {
        const token = req.body.refreshToken;
        const expiredToken = req.body.expiredToken;
        if(!token){
            logger.http('Connexion sans token');
            res.status(401);
            res.json({auth: false, message: "We need a token, please send it next time !"});
        }
        else
        {
            const tokenBlackListeCheck = await jwtFunctions.tokenNotInBlacklist(token);

            if(tokenBlackListeCheck)
            {
                jwt.verify(token, process.env.JWT_REFRESH, async (err, decoded) => {
                    if(err){
                        logger.http('Connexion avec un mauvais token');
                        res.status(401);
                        res.json({auth: false, message: "You failed to authenticate with your rubish token"});
                    }
                    else
                    {
                        const tokenInDb = await db.query(`
                            SELECT
                                *
                            FROM
                                JWT_SESSIONS
                            WHERE
                                jwtRefreshToken = :jwtRefreshToken
                        `,
                        {
                            jwtRefreshToken: token,
                        });

                        if(tokenInDb.length > 0)
                        {
                            let personne = await db.query(`
                                SELECT
                                    *
                                FROM
                                    VIEW_HABILITATIONS
                                WHERE
                                    idPersonne = :idPersonne
                            `,
                            {
                                idPersonne: tokenInDb[0].idPersonne,
                            });
                            let disclaimerAccept = personne[0].disclaimerAccept == null ? 'false' : true;
                            
                            const oldTokenContent = jwt.decode(token);
                            delete oldTokenContent.iat;
                            delete oldTokenContent.exp;

                            const tokenValidUntil = moment(new Date()).add(jwtExpirySeconds, 'seconds');
                            const newToken = jwt.sign(oldTokenContent, process.env.JWT_TOKEN, {
                                expiresIn: jwtExpirySeconds,
                            });
                            const refreshTokenValidUntil = moment(new Date()).add(jwtRefreshExpirySeconds, 'seconds');
                            const refreshToken = jwt.sign(oldTokenContent, process.env.JWT_REFRESH, {
                                expiresIn: jwtRefreshExpirySeconds,
                            });                    
                            req.session.utilisateur = oldTokenContent;
                            const sessionInDB = await db.query(`
                                INSERT INTO
                                    JWT_SESSIONS
                                SET
                                    idPersonne = :idPersonne,
                                    createdDateTime = CURRENT_TIMESTAMP,
                                    jwtToken = :jwtToken,
                                    jwtRefreshToken = :jwtRefreshToken,
                                    tokenValidity = :tokenValidity,
                                    refreshValidity = :refreshValidity
                            `,
                            {
                                idPersonne: tokenInDb[0].idPersonne,
                                jwtToken: newToken,
                                jwtRefreshToken: refreshToken,
                                tokenValidity: moment(tokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
                                refreshValidity: moment(refreshTokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
                            });
                            await fonctionsMetiers.updateLastConnexion(tokenInDb[0].idPersonne);
                            res.json({auth: true, disclaimerAccept: disclaimerAccept, token: newToken, tokenValidUntil: tokenValidUntil, refreshToken: refreshToken, habilitations: oldTokenContent});
                        }
                        else
                        {
                            logger.http('Demande de renouvellement de token rejetée car le refreshToken n\'est pas trouvé en DB');
                            res.status(401);
                            res.json({auth: false, message: "Your token is unknown"});
                        }
                    }
                });
            }
            else
            {
                logger.http('Connexion avec un ancien refreshtoken déjà révoqué');
                res.status(401);
                res.json({auth: false, message: "You failed to authenticate with your token"});
            }
        }
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.checkLogin = (req, res) => {
    res.json({auth: true, message: "Token well validated"});
}

exports.updatePassword = async (req, res) => {
    try {
        const idPersonne = req.body.idPersonne;
        const oldPwd = req.body.oldPwd;
        const newPwd = req.body.newPwd;

        const saltRounds = parseInt(process.env.BCRYPT_SALTROUND);

        const results = await db.query(
            'SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;',
        {
            idPersonne : idPersonne,
        });

        if(results.length == 1)
        {
            const result = results[0];
            let oldPasswordWithSalt = process.env.LOCAL_SELS_PRE + oldPwd + process.env.LOCAL_SELS_POST;
            brcypt.compare(oldPasswordWithSalt, result.motDePasse, (err, response) => {
                if(response)
                {
                    let newPasswordWithSalt = process.env.LOCAL_SELS_PRE + newPwd + process.env.LOCAL_SELS_POST;
                    brcypt.hash(newPasswordWithSalt, saltRounds, async (err, hash) => {
                        if(err){logger.error(err);}
                
                        const update = await db.query(
                            'UPDATE PERSONNE_REFERENTE SET motDePasse = :hash WHERE idPersonne = :idPersonne;',
                        {
                            hash : hash,
                            idPersonne : idPersonne,
                        });

                        logger.info('Modification du mot de passe avec succès pour ' + idPersonne, {idPersonne: idPersonne});
                        await fonctionsMetiers.deconnecterUtilisateur(idPersonne);
                        res.sendStatus(201);
                    });
                }
                else
                {
                    logger.warn('Erreur de validation de l\'ancien pwd pour ' + idPersonne, {idPersonne: idPersonne});
                    res.sendStatus(403);
                }
            })
        }
        else
        {
            logger.warn('Erreur de modification de pwd pour ' + idPersonne, {idPersonne: idPersonne});
            res.sendStatus(401);
        }

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updatePasswordWithoutCheck = async (req, res) => {
    try {
        const idPersonne = req.body.idPersonne;
        const newPwd = req.body.newPwd;

        const saltRounds = parseInt(process.env.BCRYPT_SALTROUND);

        let newPasswordWithSalt = process.env.LOCAL_SELS_PRE + newPwd + process.env.LOCAL_SELS_POST;
        brcypt.hash(newPasswordWithSalt, saltRounds, async (err, hash) => {
            if(err){logger.error(err);}
    
            const update = await db.query(
                'UPDATE PERSONNE_REFERENTE SET motDePasse = :hash WHERE idPersonne = :idPersonne;',
            {
                hash : hash,
                idPersonne : idPersonne,
            });

            logger.info('Modification du mot de passe avec succès pour ' + idPersonne, {idPersonne: idPersonne});

            const getUser = await db.query(
                'SELECT * FROM VIEW_HABILITATIONS WHERE idPersonne = :idPersonne;',
            {
                idPersonne : idPersonne,
            });
            if(getUser[0].disclaimerAccept == null)
            {
                res.json({auth: true, disclaimerAccept:false})
            }
            else
            {
                res.sendStatus(201);
            }
        });

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.alive = (req, res) => {
    res.json({message: "Backend serveur is alive"});
}

exports.getConfig = async (req, res) => {
    try {
        const result = await db.query(
            `SELECT
                appname,
                urlsite,
                maintenance,
                resetPassword,
                alertes_benevoles_lots,
                alertes_benevoles_vehicules,
                consommation_benevoles,
                consommation_benevoles_auto,
                mailserver,
                mailcnil
            FROM
                CONFIG
        `);
        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCGU = async (req, res) => {
    try {
        const result = await db.query(
            `SELECT
                cnilDisclaimer
            FROM
                CONFIG
        `);
        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.acceptCGU = async (req, res) => {
    try {
        let results = await db.query(
            `UPDATE
                PERSONNE_REFERENTE
            SET
                disclaimerAccept = CURRENT_TIMESTAMP
            WHERE
                idPersonne = :idPersonne
            ;`,
        {
            idPersonne : req.verifyJWTandProfile.idPersonne
        });
        await fonctionsMetiers.deconnecterUtilisateur(req.verifyJWTandProfile.idPersonne);
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getCurrentSessionsOneUser = async (req, res) => {
    try {
        const result = await db.query(
            `SELECT
                *
            FROM
                JWT_SESSIONS
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne : req.body.idPersonne,
        });
        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.dropSession = async (req, res) => {
    try {
        logger.info('Déconnexion');
        const deleteQuery = await db.query(
            'DELETE FROM JWT_SESSIONS WHERE jwtToken = :jwtToken',
        {
            jwtToken : req.body.jwtToken || null,
        });

        res.sendStatus(200);

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.blackListSession = async (req, res) => {
    try {
        const sessionToBlacklist = await db.query(
            'SELECT * FROM JWT_SESSIONS WHERE idSession = :idSession',
        {
            idSession : req.body.idSession,
        });

        let insertRequest = await db.query(
            `INSERT INTO JWT_SESSIONS_BLACKLIST SET 
                blockedDateTime = CURRENT_TIMESTAMP,
                jwtToken = :token
            ;`,
        {
            token        : sessionToBlacklist[0].jwtToken,
        });

        insertRequest = await db.query(
            `INSERT INTO JWT_SESSIONS_BLACKLIST SET 
                blockedDateTime = CURRENT_TIMESTAMP,
                jwtToken = :refreshtoken
            ;`,
        {
            refreshtoken : sessionToBlacklist[0].jwtRefreshToken,
        });

        const deleteFromCurrentList = await db.query(
            'DELETE FROM JWT_SESSIONS WHERE idSession = :idSession',
        {
            idSession : req.body.idSession,
        });

        res.sendStatus(201);

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.delegate = async (req, res, next)=>{
    try {
        let targetUser = await db.query(
            'SELECT * FROM VIEW_HABILITATIONS WHERE idPersonne = :idPersonne AND connexion_connexion = 1;',
            {
                idPersonne : req.body.idPersonne,
            }
        );

        if(targetUser.length == 1)
        {
            //L'utilisateur a bien ses droits, on le connecte
            logger.debug("DELEGATION - L'utilisateur a bien ses droits, on le connecte");
            selectedUser = targetUser[0];
            delete selectedUser.motDePasse;
            delete selectedUser.mfaSecret;
            selectedUser.tokenDelegationActive = true;
            selectedUser.tokenDelegationInitialIdPersonne = req.verifyJWTandProfile.idPersonne;
            selectedUser.tokenDelegationInitialIdentifiant = req.verifyJWTandProfile.identifiant;

            const tokenValidUntil = moment(new Date()).add(jwtExpirySeconds, 'seconds');
            const token = jwt.sign(selectedUser, process.env.JWT_TOKEN, {
                expiresIn: jwtExpirySeconds,
            });
            const refreshTokenValidUntil = moment(new Date()).add(jwtRefreshExpirySeconds, 'seconds');
            const refreshToken = jwt.sign(selectedUser, process.env.JWT_REFRESH, {
                expiresIn: jwtRefreshExpirySeconds,
            });                    
            req.session.utilisateur = selectedUser;
            logger.info('DELEGATION - Connexion en succès de '+req.verifyJWTandProfile.identifiant+' au compte de ' + selectedUser.identifiant, {idPersonne: selectedUser.idPersonne, identifiant: selectedUser.identifiant});
            const sessionInDB = await db.query(`
                INSERT INTO
                    JWT_SESSIONS
                SET
                    idPersonne = :idPersonne,
                    createdDateTime = CURRENT_TIMESTAMP,
                    jwtToken = :jwtToken,
                    jwtRefreshToken = :jwtRefreshToken,
                    tokenValidity = :tokenValidity,
                    refreshValidity = :refreshValidity
            `,
            {
                idPersonne: selectedUser.idPersonne,
                jwtToken: token,
                jwtRefreshToken: refreshToken,
                tokenValidity: moment(tokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
                refreshValidity: moment(refreshTokenValidUntil).format('YYYY-MM-DD HH:mm:ss'),
            });
            await fonctionsMetiers.updateLastConnexion(selectedUser.idPersonne);
            res.json({auth: true, disclaimerAccept: true, token: token, tokenValidUntil: tokenValidUntil, refreshToken: refreshToken, habilitations: selectedUser});
        }
        else
        {
            //L'utilisateur existe mais n'a pas de droits
            logger.debug("DELEGATION - L'utilisateur existe mais n'a pas de droits");
            logger.warn('DELEGATION - Connexion de l\'utilisateur rejetée car droits insuffisants', {idPersonne: selectedUser.idPersonne});
            return res.json({auth: false, message:"Droits insuffisants"});
        }
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}