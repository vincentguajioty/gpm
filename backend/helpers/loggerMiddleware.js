const logger = require('../winstonLogger');

const httpLogger = () => {
    return function(req, res, next) {
        logger.http(req.body);
        next();
    }
}

const modificationLogger = () => {
    return function(req, res, next) {
        logger.info('Edition de données via ' + req.originalUrl, {idPersonne: req.verifyJWTandProfile ? req.verifyJWTandProfile.idPersonne  : -1});
        next();
    }
}

const suppressionLogger = () => {
    return function(req, res, next) {
        logger.info('Suppression de données via ' + req.originalUrl, {idPersonne: req.verifyJWTandProfile ? req.verifyJWTandProfile.idPersonne  : -1});
        next();
    }
}

module.exports = {
    httpLogger,
    modificationLogger,
    suppressionLogger,
};