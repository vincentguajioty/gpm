const logger = require('./winstonLogger');
const db = require('./db');
const jwt = require('jsonwebtoken');

const tokenNotInBlacklist = async (token) => {
    try {
        const results = await db.query(
            'SELECT * FROM JWT_SESSIONS_BLACKLIST WHERE jwtToken = :jwtToken;',
            {
                jwtToken : token,
            }
        );
        if(results.length === 0)
        {
            return true;
        }
        return false;
    } catch (error) {
        logger.error(error)
    }
    return (true);
}

const verifyJWTandProfile = (role) => {
    return async function(req, res, next) {
        const token = req.headers["x-access-token"];
        if(!token){
            logger.http('Connexion sans token');
            res.status(401);
            res.json({auth: false, message: "We need a token, please send it next time !"});
        }
        else
        {
            const tokenBlackListeCheck = await tokenNotInBlacklist(token);
    
            if(tokenBlackListeCheck)
            {
                jwt.verify(token, process.env.JWT_TOKEN, async (err, decoded) => {
                    if(err){
                        logger.http('Connexion avec un mauvais token');
                        res.status(401);
                        res.json({auth: false, message: "You failed to authenticate with your rubish token"});
                    }
                    else
                    {
                        var acl = true;
                        role.forEach(prerequis => {
                            acl = acl && decoded[prerequis];
                        });
                        if(!acl)
                        {
                            logger.info('Accès refusé par ACL', {idPersonne: 'SYSTEM'});
                            res.status(403);
                            res.send('Accès refusé par le contrôle de profile');
                        }
                        else
                        {
                            const maintenanceConfig = await db.query(
                                'SELECT maintenance FROM CONFIG;'
                            );
                            if(maintenanceConfig[0].maintenance && !decoded['maintenance'])
                            {
                                logger.info('Accès refusé par le dispositif de maintenance', {idPersonne: 'SYSTEM'});
                                res.status(403);
                                res.send('Accès refusé par le dispositif de maintenance');
                            }
                            else
                            {
                                logger.http('Requete authentifiée ' + req.originalUrl + ' de l\'utilisateur ' + decoded.identifiant)
                                req.idPersonne = decoded.idPersonne;
                                req.verifyJWTandProfile = decoded;
                                next();
                            }
                        }
                    }
                });
            }
            else
            {
                logger.http('Connexion avec un token blacklisté');
                res.status(401);
                res.json({auth: false, message: "You failed to authenticate with your token"});
            }
           
        }
    }
}

const decryptAesToken = () => {
    return async function(req, res, next) {
        const token = req.body.aesToken;
        if(!token){
            next();
        }
        else
        {
            jwt.verify(token, process.env.JWT_AESFOURNISSEURS_TOKEN, async (err, decoded) => {
                if(err){
                    logger.http('Demande de decodage AES avec un mauvais token');
                    res.status(403);
                    res.json({auth: false, message: "You failed to authenticate with your rubish token"});
                }
                else
                {
                    logger.http('Demande de decodage AES réussi');
                    req.aesKey = decoded.aesKey;
                    next();
                }
            });
        }
    }
}

const decryptAMToken = () => {
    return async function(req, res, next) {
        const token = req.body.amToken;
        if(!token){
            next();
        }
        else
        {
            jwt.verify(token, process.env.JWT_ACTIONSMASSIVES_TOKEN, async (err, decoded) => {
                if(err){
                    logger.http('Demande de decodage ActionsMassives avec un mauvais token');
                    res.status(403);
                    res.json({auth: false, message: "You failed to authenticate with your rubish token"});
                }
                else
                {
                    logger.http('Demande de decodage ActionsMassives réussi');
                    next();
                }
            });
        }
    }
}

const cleanSessionTable = async () => {
    try {
        const deleteQuery = await db.query(
            'DELETE FROM JWT_SESSIONS WHERE refreshValidity < CURRENT_TIMESTAMP');
    } catch (error) {
        logger.error(error)
    }
}

const cleanOldBlacklist = async () => {
    try {
        const deleteQuery = await db.query(
            'DELETE FROM JWT_SESSIONS_BLACKLIST WHERE blockedDateTime < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 365 DAY)');
    } catch (error) {
        logger.error(error)
    }
}

const verifyJWTforSocketIO = async (token, role) => {
    try {
        const tokenBlackListeCheck = await tokenNotInBlacklist(token);
        
        if(tokenBlackListeCheck)
        {
            try {
                var decoded = jwt.verify(token, process.env.JWT_TOKEN);
            } catch (error) {
                return(false);
            }

            var acl = true;
            role.forEach(prerequis => {
                acl = acl && decoded[prerequis];
            });
            if(!acl)
            {
                logger.info('Accès refusé par ACL', {idPersonne: 'SYSTEM'});
                return(false);
            }
            else
            {
                const maintenanceConfig = await db.query(
                    'SELECT maintenance FROM CONFIG;'
                );
                if(maintenanceConfig[0].maintenance && !decoded['maintenance'])
                {
                    logger.info('Accès refusé par le dispositif de maintenance', {idPersonne: 'SYSTEM'});
                    return(false);
                }
                else
                {
                    logger.http('Requete authentifiée sur SOKETIO de l\'utilisateur ' + decoded.identifiant)
                    return(true);
                }
            }
        }
        else
        {
            logger.http('Connexion avec un token blacklisté');
            return(false);
        }
    } catch (error) {
        logger.error(error);
    }
}

module.exports = {
    verifyJWTandProfile,
    tokenNotInBlacklist,
    cleanSessionTable,
    cleanOldBlacklist,
    decryptAesToken,
    decryptAMToken,
    verifyJWTforSocketIO,
};