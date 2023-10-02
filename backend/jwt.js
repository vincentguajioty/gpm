const express = require('express');
const app = express();
const dotenv = require('dotenv').config();
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
        console.log(error)
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
                jwt.verify(token, process.env.JWT_TOKEN, (err, decoded) => {
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
                            logger.http('Requete authentifiée ' + req.originalUrl + ' de l\'utilisateur ' + decoded.identifiant)
                            req.idPersonne = decoded.idPersonne;
                            req.verifyJWTandProfile = decoded;
                            next();
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

const cleanSessionTable = async () => {
    try {
        const deleteQuery = await db.query(
            'DELETE FROM JWT_SESSIONS WHERE refreshValidity < CURRENT_TIMESTAMP');
    } catch (error) {
        console.log(error)
    }
}

const cleanOldBlacklist = async () => {
    try {
        const deleteQuery = await db.query(
            'DELETE FROM JWT_SESSIONS_BLACKLIST WHERE blockedDateTime < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 365 DAY)');
    } catch (error) {
        console.log(error)
    }
}

module.exports = {
    verifyJWTandProfile,
    tokenNotInBlacklist,
    cleanSessionTable,
    cleanOldBlacklist,
};