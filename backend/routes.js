const express = require('express');
const app = express();

const logger = require('./winstonLogger');

const jwtFunctions = require('./jwt');

const router = express.Router();

const connexionCtrl            = require('./controllers/connexion');

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

//authentification
router.post('/login',                                                httpLogger(),                                                                                        connexionCtrl.login );
router.post('/mfaNeeded',                                            httpLogger(),                                                                                        connexionCtrl.mfaNeeded );
router.post('/pwdReinitRequest',                                     httpLogger(),                                                                                        connexionCtrl.pwdReinitRequest );
router.post('/pwdReinitValidate',                                    httpLogger(),                                                                                        connexionCtrl.pwdReinitValidate );
router.post('/refreshToken',                                         httpLogger(),                                                                                        connexionCtrl.refreshToken );
router.post('/dropSession',                                          httpLogger(),                                                                                        connexionCtrl.dropSession );
router.get('/getConfig',                                             httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                           connexionCtrl.getConfig );
router.get('/checkLogin',                                            httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                           connexionCtrl.checkLogin );
router.post('/updatePassword',                                       httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,     modificationLogger(), connexionCtrl.updatePassword );
router.post('/updatePasswordWithoutCheck',                           httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,     modificationLogger(), connexionCtrl.updatePasswordWithoutCheck );
router.post('/getCurrentSessionsOneUser',                            httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                           connexionCtrl.getCurrentSessionsOneUser );
router.post('/blackListSession',                                     httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,     suppressionLogger(),  connexionCtrl.blackListSession );

router.get('/', connexionCtrl.alive );

module.exports = router;