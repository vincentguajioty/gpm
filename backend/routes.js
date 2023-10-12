const express = require('express');
const app = express();
const logger = require('./winstonLogger');
const jwtFunctions = require('./jwt');
const router = express.Router();
const fonctionsMetiers = require('./fonctionsMetiers');

const connexionCtrl = require('./controllers/connexion');
const settingsMetiersCtrl = require('./controllers/settingsMetiers');
const settingsUtilisateursCtrl = require('./controllers/settingsUtilisateurs');

const commandesCtrl = require('./controllers/commandes');

const serveDocumentsCtrl       = require('./controllers/serveDocuments');

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
router.post('/login',                      httpLogger(),                                                                                   connexionCtrl.login );
router.post('/mfaNeeded',                  httpLogger(),                                                                                   connexionCtrl.mfaNeeded );
router.post('/pwdReinitRequest',           httpLogger(), fonctionsMetiers.checkPwdReinitEnable(),                                          connexionCtrl.pwdReinitRequest );
router.post('/pwdReinitValidate',          httpLogger(), fonctionsMetiers.checkPwdReinitEnable(),                                          connexionCtrl.pwdReinitValidate );
router.post('/refreshToken',               httpLogger(),                                                                                   connexionCtrl.refreshToken );
router.post('/dropSession',                httpLogger(),                                                                                   connexionCtrl.dropSession );
router.get('/getConfig',                   httpLogger(),                                                                                   connexionCtrl.getConfig );
router.get('/checkLogin',                  httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                       connexionCtrl.checkLogin );
router.get('/getCGU',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                       connexionCtrl.getCGU );
router.post('/acceptCGU',                  httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                       connexionCtrl.acceptCGU );
router.post('/updatePassword',             httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]), modificationLogger(), connexionCtrl.updatePassword );
router.post('/updatePasswordWithoutCheck', httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]), modificationLogger(), connexionCtrl.updatePasswordWithoutCheck );
router.post('/getCurrentSessionsOneUser',  httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                       connexionCtrl.getCurrentSessionsOneUser );
router.post('/blackListSession',           httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]), suppressionLogger(),  connexionCtrl.blackListSession );

//commandes
router.get('/commandes/getFournisseurs',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_lecture']),                                    commandesCtrl.getFournisseurs);

//settings Métiers
router.get('/settingsMetiers/getCategoriesMateriels',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['categories_lecture']),                                    settingsMetiersCtrl.getCategoriesMateriels);
router.post('/settingsMetiers/addCategoriesMateriels',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['categories_ajout']),                modificationLogger(), settingsMetiersCtrl.addCategoriesMateriels);
router.post('/settingsMetiers/updateCategoriesMateriels',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['categories_modification']),         modificationLogger(), settingsMetiersCtrl.updateCategoriesMateriels);
router.post('/settingsMetiers/deleteCategoriesMateriels',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['categories_suppression']),          suppressionLogger(),  settingsMetiersCtrl.deleteCategoriesMateriels);
router.get('/settingsMetiers/getLieux',                                     httpLogger(), jwtFunctions.verifyJWTandProfile(['lieux_lecture']),                                         settingsMetiersCtrl.getLieux);
router.post('/settingsMetiers/addLieux',                                    httpLogger(), jwtFunctions.verifyJWTandProfile(['lieux_ajout']),                     modificationLogger(), settingsMetiersCtrl.addLieux);
router.post('/settingsMetiers/updateLieux',                                 httpLogger(), jwtFunctions.verifyJWTandProfile(['lieux_modification']),              modificationLogger(), settingsMetiersCtrl.updateLieux);
router.post('/settingsMetiers/deleteLieux',                                 httpLogger(), jwtFunctions.verifyJWTandProfile(['lieux_suppression']),               suppressionLogger(),  settingsMetiersCtrl.deleteLieux);
router.get('/settingsMetiers/getTypesVehicules',                            httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_types_lecture']),                               settingsMetiersCtrl.getTypesVehicules);
router.post('/settingsMetiers/addTypesVehicules',                           httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_types_ajout']),           modificationLogger(), settingsMetiersCtrl.addTypesVehicules);
router.post('/settingsMetiers/updateTypesVehicules',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_types_modification']),    modificationLogger(), settingsMetiersCtrl.updateTypesVehicules);
router.post('/settingsMetiers/deleteTypesVehicules',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_types_suppression']),     suppressionLogger(),  settingsMetiersCtrl.deleteTypesVehicules);
router.get('/settingsMetiers/getTypesDesinfections',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['typesDesinfections_lecture']),                            settingsMetiersCtrl.getTypesDesinfections);
router.post('/settingsMetiers/addTypesDesinfections',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['typesDesinfections_ajout']),        modificationLogger(), settingsMetiersCtrl.addTypesDesinfections);
router.post('/settingsMetiers/updateTypesDesinfections',                    httpLogger(), jwtFunctions.verifyJWTandProfile(['typesDesinfections_modification']), modificationLogger(), settingsMetiersCtrl.updateTypesDesinfections);
router.post('/settingsMetiers/deleteTypesDesinfections',                    httpLogger(), jwtFunctions.verifyJWTandProfile(['typesDesinfections_suppression']),  suppressionLogger(),  settingsMetiersCtrl.deleteTypesDesinfections);
router.get('/settingsMetiers/getTypesMaintenancesRegulieresVehicules',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_lecture']),                            settingsMetiersCtrl.getTypesMaintenancesRegulieresVehicules);
router.post('/settingsMetiers/addTypesMaintenancesRegulieresVehicules',     httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_ajout']),        modificationLogger(), settingsMetiersCtrl.addTypesMaintenancesRegulieresVehicules);
router.post('/settingsMetiers/updateTypesMaintenancesRegulieresVehicules',  httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_modification']), modificationLogger(), settingsMetiersCtrl.updateTypesMaintenancesRegulieresVehicules);
router.post('/settingsMetiers/deleteTypesMaintenancesRegulieresVehicules',  httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_suppression']),  suppressionLogger(),  settingsMetiersCtrl.deleteTypesMaintenancesRegulieresVehicules);
router.get('/settingsMetiers/getTypesMaintenancesPonctuellesVehicules',     httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_lecture']),                            settingsMetiersCtrl.getTypesMaintenancesPonctuellesVehicules);
router.post('/settingsMetiers/addTypesMaintenancesPonctuellesVehicules',    httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_ajout']),        modificationLogger(), settingsMetiersCtrl.addTypesMaintenancesPonctuellesVehicules);
router.post('/settingsMetiers/updateTypesMaintenancesPonctuellesVehicules', httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_modification']), modificationLogger(), settingsMetiersCtrl.updateTypesMaintenancesPonctuellesVehicules);
router.post('/settingsMetiers/deleteTypesMaintenancesPonctuellesVehicules', httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_suppression']),  suppressionLogger(),  settingsMetiersCtrl.deleteTypesMaintenancesPonctuellesVehicules);
router.get('/settingsMetiers/getCarburants',                                httpLogger(), jwtFunctions.verifyJWTandProfile(['carburants_lecture']),                                    settingsMetiersCtrl.getCarburants);
router.post('/settingsMetiers/addCarburants',                               httpLogger(), jwtFunctions.verifyJWTandProfile(['carburants_ajout']),                modificationLogger(), settingsMetiersCtrl.addCarburants);
router.post('/settingsMetiers/updateCarburants',                            httpLogger(), jwtFunctions.verifyJWTandProfile(['carburants_modification']),         modificationLogger(), settingsMetiersCtrl.updateCarburants);
router.post('/settingsMetiers/deleteCarburants',                            httpLogger(), jwtFunctions.verifyJWTandProfile(['carburants_suppression']),          suppressionLogger(),  settingsMetiersCtrl.deleteCarburants);
router.get('/settingsMetiers/getEtatsLots',                                 httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_lecture']),                                         settingsMetiersCtrl.getEtatsLots);
router.post('/settingsMetiers/addEtatsLots',                                httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_ajout']),                     modificationLogger(), settingsMetiersCtrl.addEtatsLots);
router.post('/settingsMetiers/updateEtatsLots',                             httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_modification']),              modificationLogger(), settingsMetiersCtrl.updateEtatsLots);
router.post('/settingsMetiers/deleteEtatsLots',                             httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_suppression']),               suppressionLogger(),  settingsMetiersCtrl.deleteEtatsLots);
router.get('/settingsMetiers/getEtatsMateriels',                            httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_lecture']),                                         settingsMetiersCtrl.getEtatsMateriels);
router.post('/settingsMetiers/addEtatsMateriels',                           httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_ajout']),                     modificationLogger(), settingsMetiersCtrl.addEtatsMateriels);
router.post('/settingsMetiers/updateEtatsMateriels',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_modification']),              modificationLogger(), settingsMetiersCtrl.updateEtatsMateriels);
router.post('/settingsMetiers/deleteEtatsMateriels',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_suppression']),               suppressionLogger(),  settingsMetiersCtrl.deleteEtatsMateriels);
router.get('/settingsMetiers/getEtatsVehicules',                            httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_lecture']),                                         settingsMetiersCtrl.getEtatsVehicules);
router.post('/settingsMetiers/addEtatsVehicules',                           httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_ajout']),                     modificationLogger(), settingsMetiersCtrl.addEtatsVehicules);
router.post('/settingsMetiers/updateEtatsVehicules',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_modification']),              modificationLogger(), settingsMetiersCtrl.updateEtatsVehicules);
router.post('/settingsMetiers/deleteEtatsVehicules',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_suppression']),               suppressionLogger(),  settingsMetiersCtrl.deleteEtatsVehicules);
router.get('/settingsMetiers/getTypesDocuments',                            httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                            settingsMetiersCtrl.getTypesDocuments);
router.post('/settingsMetiers/addTypesDocuments',                           httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.addTypesDocuments);
router.post('/settingsMetiers/updateTypesDocuments',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.updateTypesDocuments);
router.post('/settingsMetiers/deleteTypesDocuments',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      suppressionLogger(),  settingsMetiersCtrl.deleteTypesDocuments);
router.get('/settingsMetiers/getCatalogueMateriel',                         httpLogger(), jwtFunctions.verifyJWTandProfile(['catalogue_lecture']),                                     settingsMetiersCtrl.getCatalogueMateriel);
router.post('/settingsMetiers/addCatalogueMateriel',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['catalogue_ajout']),                 modificationLogger(), settingsMetiersCtrl.addCatalogueMateriel);
router.post('/settingsMetiers/updateCatalogueMateriel',                     httpLogger(), jwtFunctions.verifyJWTandProfile(['catalogue_modification']),          modificationLogger(), settingsMetiersCtrl.updateCatalogueMateriel);
router.post('/settingsMetiers/deleteCatalogueMateriel',                     httpLogger(), jwtFunctions.verifyJWTandProfile(['catalogue_suppression']),           suppressionLogger(),  settingsMetiersCtrl.deleteCatalogueMateriel);
router.get('/settingsMetiers/getVHFTypesAccessoires',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                            settingsMetiersCtrl.getVHFTypesAccessoires);
router.post('/settingsMetiers/addVHFTypesAccessoires',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.addVHFTypesAccessoires);
router.post('/settingsMetiers/updateVHFTypesAccessoires',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.updateVHFTypesAccessoires);
router.post('/settingsMetiers/deleteVHFTypesAccessoires',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      suppressionLogger(),  settingsMetiersCtrl.deleteVHFTypesAccessoires);
router.get('/settingsMetiers/getEtatsVHF',                                  httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                            settingsMetiersCtrl.getEtatsVHF);
router.post('/settingsMetiers/addEtatsVHF',                                 httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.addEtatsVHF);
router.post('/settingsMetiers/updateEtatsVHF',                              httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.updateEtatsVHF);
router.post('/settingsMetiers/deleteEtatsVHF',                              httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      suppressionLogger(),  settingsMetiersCtrl.deleteEtatsVHF);
router.get('/settingsMetiers/getTechnologiesVHF',                           httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                            settingsMetiersCtrl.getTechnologiesVHF);
router.post('/settingsMetiers/addTechnologiesVHF',                          httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.addTechnologiesVHF);
router.post('/settingsMetiers/updateTechnologiesVHF',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.updateTechnologiesVHF);
router.post('/settingsMetiers/deleteTechnologiesVHF',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      suppressionLogger(),  settingsMetiersCtrl.deleteTechnologiesVHF);
router.get('/settingsMetiers/getVHFTypesEquipements',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                            settingsMetiersCtrl.getVHFTypesEquipements);
router.post('/settingsMetiers/addVHFTypesEquipements',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.addVHFTypesEquipements);
router.post('/settingsMetiers/updateVHFTypesEquipements',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      modificationLogger(), settingsMetiersCtrl.updateVHFTypesEquipements);
router.post('/settingsMetiers/deleteVHFTypesEquipements',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      suppressionLogger(),  settingsMetiersCtrl.deleteVHFTypesEquipements);

//settings utilisateurs
router.post('/settingsUtilisateurs/getOneUser',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                           settingsUtilisateursCtrl.getOneUser);
router.post('/settingsUtilisateurs/getProfilsOneUser',               httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                           settingsUtilisateursCtrl.getProfilsOneUser);
router.post('/settingsUtilisateurs/getMfaUrl',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                           settingsUtilisateursCtrl.getMfaUrl);
router.post('/settingsUtilisateurs/enableMfa',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,     modificationLogger(), settingsUtilisateursCtrl.enableMfa);
router.post('/settingsUtilisateurs/disableMfa',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,     modificationLogger(), settingsUtilisateursCtrl.disableMfa);

//get images and documents from secured backend
router.post('/getSecureFile/centresCouts',   httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_lecture',]),           serveDocumentsCtrl.centresCouts);
router.post('/getSecureFile/commandes',      httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_lecture',]),       serveDocumentsCtrl.commandes);
router.post('/getSecureFile/vehicules',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_lecture',]),      serveDocumentsCtrl.vehicules);
router.post('/getSecureFile/vhfCanaux',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_lecture',]),      serveDocumentsCtrl.vhfCanaux);
router.post('/getSecureFile/vhfEquipements', httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_lecture',]), serveDocumentsCtrl.vhfEquipements);
router.post('/getSecureFile/vhfPlans',       httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture',]),       serveDocumentsCtrl.vhfPlans);


router.get('/', connexionCtrl.alive );

module.exports = router;