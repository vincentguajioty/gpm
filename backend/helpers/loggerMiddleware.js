const logger = require('../winstonLogger');

const httpLogger = () => {
    return function(req, res, next) {
        logger.http(JSON.stringify(req.body));
        next();
    }
}

const modificationLogger = () => {
    return function(req, res, next) {
        logger.info('Edition de données via ' + req.originalUrl, {
            idPersonne: req.verifyJWTandProfile ? req.verifyJWTandProfile.idPersonne  : -1,
            identifiant: req.verifyJWTandProfile ? req.verifyJWTandProfile.identifiant  : -1,
            tokenDelegationActive: req.verifyJWTandProfile.tokenDelegationActive ? req.verifyJWTandProfile.tokenDelegationActive : null,
            tokenDelegationInitialIdPersonne: req.verifyJWTandProfile.tokenDelegationInitialIdPersonne ? req.verifyJWTandProfile.tokenDelegationInitialIdPersonne : null,
            tokenDelegationInitialIdentifiant: req.verifyJWTandProfile.tokenDelegationInitialIdentifiant ? req.verifyJWTandProfile.tokenDelegationInitialIdentifiant : null,
        });
        next();
    }
}

const suppressionLogger = () => {
    return function(req, res, next) {
        logger.info('Suppression de données via ' + req.originalUrl, {
            idPersonne: req.verifyJWTandProfile ? req.verifyJWTandProfile.idPersonne  : -1,
            identifiant: req.verifyJWTandProfile ? req.verifyJWTandProfile.identifiant  : -1,
            tokenDelegationActive: req.verifyJWTandProfile.tokenDelegationActive ? req.verifyJWTandProfile.tokenDelegationActive : null,
            tokenDelegationInitialIdPersonne: req.verifyJWTandProfile.tokenDelegationInitialIdPersonne ? req.verifyJWTandProfile.tokenDelegationInitialIdPersonne : null,
            tokenDelegationInitialIdentifiant: req.verifyJWTandProfile.tokenDelegationInitialIdentifiant ? req.verifyJWTandProfile.tokenDelegationInitialIdentifiant : null,
        });
        next();
    }
}

module.exports = {
    httpLogger,
    modificationLogger,
    suppressionLogger,
};