const express = require('express');
const loggerMiddleware = require('./helpers/loggerMiddleware');
const jwtFunctions = require('./jwt');
const middlewaresFunctions = require('./helpers/middlewares');
const router = express.Router();
const fonctionsMetiers = require('./helpers/fonctionsMetiers');

const connexionCtrl = require('./controllers/connexion');
const settingsMetiersCtrl = require('./controllers/settingsMetiers');
const settingsUtilisateursCtrl = require('./controllers/settingsUtilisateurs');
const settingsTechniquesCtrl = require('./controllers/settingsTechniques');
const profilsCtrl = require('./controllers/profils');

const lotsCtrl = require('./controllers/lots');
const sacsCtrl = require('./controllers/sacs');
const materielsCtrl = require('./controllers/materiels');

const lotsConsommationsCtrl = require('./controllers/lotsConsommation');

const reservesConteneursCtrl = require('./controllers/reservesConteneurs');
const reservesMaterielsCtrl = require('./controllers/reservesMateriels');

const transfertsCtrl = require('./controllers/transferts');

const commandesCtrl = require('./controllers/commandes');
const centresCoutsCtrl = require('./controllers/centresCouts');
const fournisseursCtrl = require('./controllers/fournisseurs');
const fournisseursAesCtrl = require('./controllers/fournisseursAes');

const vehiculesCtrl = require('./controllers/vehicules');

const tenuesCtrl = require('./controllers/tenues');
const vhfCtrl = require('./controllers/vhf');

const referentielsCtrl = require('./controllers/referentiels');
const messagesGenerauxCtrl = require('./controllers/messagesGeneraux');
const toDoListCtrl = require('./controllers/toDoList');

const actionsMassivesCtrl = require('./controllers/actionsMassives');

const serveDocumentsCtrl       = require('./controllers/serveDocuments');
const selectForListsCtrl = require('./controllers/selectForLists')
const calendrierCtrl = require('./controllers/calendrier');

//authentification
router.post('/login',                      loggerMiddleware.httpLogger(),                                                                                                    connexionCtrl.login );
router.post('/mfaNeeded',                  loggerMiddleware.httpLogger(),                                                                                                    connexionCtrl.mfaNeeded );
router.post('/pwdReinitRequest',           loggerMiddleware.httpLogger(), fonctionsMetiers.checkPwdReinitEnable(),                                                           connexionCtrl.pwdReinitRequest );
router.post('/pwdReinitValidate',          loggerMiddleware.httpLogger(), fonctionsMetiers.checkPwdReinitEnable(),                                                           connexionCtrl.pwdReinitValidate );
router.post('/refreshToken',               loggerMiddleware.httpLogger(),                                                                                                    connexionCtrl.refreshToken );
router.post('/dropSession',                loggerMiddleware.httpLogger(),                                                                                                    connexionCtrl.dropSession );
router.get('/getConfig',                   loggerMiddleware.httpLogger(),                                                                                                    connexionCtrl.getConfig );
router.get('/checkLogin',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                                        connexionCtrl.checkLogin );
router.get('/getCGU',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                                        connexionCtrl.getCGU );
router.post('/acceptCGU',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                                        connexionCtrl.acceptCGU );
router.post('/updatePassword',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]), loggerMiddleware.modificationLogger(), connexionCtrl.updatePassword );
router.post('/updatePasswordWithoutCheck', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]), loggerMiddleware.modificationLogger(), connexionCtrl.updatePasswordWithoutCheck );
router.post('/getCurrentSessionsOneUser',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                                        connexionCtrl.getCurrentSessionsOneUser );
router.post('/blackListSession',           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]), loggerMiddleware.suppressionLogger(),  connexionCtrl.blackListSession );
router.post('/contactDeveloppeur',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]),                                        connexionCtrl.contactDeveloppeur );

//select routes for forms and lists - AUTHENTICATED
router.get('/select/getNotificationsConditions',               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getNotificationsConditions);
router.get('/select/getPersonnes',                             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getPersonnes);
router.get('/select/getActivePersonnes',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getActivePersonnes);
router.get('/select/getActivePersonnesForCmdAffectation',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getActivePersonnesForCmdAffectation);
router.get('/select/getNonAnonymesPersonnes',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getNonAnonymesPersonnes);
router.get('/select/getPersonnesWithMail',                     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getPersonnesWithMail);
router.get('/select/getProfils',                               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getProfils);
router.get('/select/getNotificationsEnabled',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getNotificationsEnabled);
router.get('/select/getCategoriesMateriels',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getCategoriesMateriels);
router.get('/select/getLieux',                                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getLieux);
router.get('/select/getTypesVehicules',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getTypesVehicules);
router.get('/select/getTypesDesinfections',                    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getTypesDesinfections);
router.get('/select/getTypesMaintenancesRegulieresVehicules',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getTypesMaintenancesRegulieresVehicules);
router.get('/select/getTypesMaintenancesPonctuellesVehicules', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getTypesMaintenancesPonctuellesVehicules);
router.get('/select/getCarburants',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getCarburants);
router.get('/select/getEtatsLots',                             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getEtatsLots);
router.get('/select/getEtatsMateriels',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getEtatsMateriels);
router.get('/select/getEtatsVehicules',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getEtatsVehicules);
router.get('/select/getTypesDocuments',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getTypesDocuments);
router.get('/select/getCatalogueMateriel',                     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getCatalogueMateriel);
router.get('/select/getCatalogueMaterielFull',                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getCatalogueMaterielFull);
router.get('/select/getVHFTypesAccessoires',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getVHFTypesAccessoires);
router.get('/select/getEtatsVHF',                              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getEtatsVHF);
router.get('/select/getTechnologiesVHF',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getTechnologiesVHF);
router.get('/select/getVHFTypesEquipements',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getVHFTypesEquipements);
router.get('/select/getFournisseurs',                          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getFournisseurs);
router.get('/select/getMessagesTypes',                         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getMessagesTypes);
router.get('/select/getPioritesForTDL',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getPioritesForTDL);
router.get('/select/getTenuesCatalogue',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getTenuesCatalogue);
router.get('/select/getVhfPlans',                              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getVhfPlans);
router.get('/select/getVhfFrequences',                         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getVhfFrequences);
router.get('/select/getEmplacements',                          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getEmplacements);
router.get('/select/getEmplacementsFull',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getEmplacementsFull);
router.get('/select/getConteneurs',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getConteneurs);
router.get('/select/getLots',                                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getLots);
router.get('/select/getLotsFull',                              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getLotsFull);
router.get('/select/getSacs',                                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getSacs);
router.get('/select/getSacsFull',                              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getSacsFull);
router.get('/select/getTypesLots',                             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getTypesLots);
router.get('/select/getVehicules',                             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getVehicules);
router.get('/select/getCodesBarreCatalogue',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getCodesBarreCatalogue);
router.get('/select/getEtatsCommandes',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getEtatsCommandes);
router.get('/select/getCentresCouts',                          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),      selectForListsCtrl.getCentresCouts);
//select routes for forms and lists - PUBLIC
router.get('/select/getConsommationsEnCours',                  middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), selectForListsCtrl.getConsommationsEnCours);
router.get('/select/getPublicCatalogueMateriel',               middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), selectForListsCtrl.getPublicCatalogueMateriel);
router.get('/select/getLotsPublics',                           middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), selectForListsCtrl.getLotsPublics);
router.get('/select/getConteneursPublics',                     middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), selectForListsCtrl.getConteneursPublics);
router.get('/select/getVehiculesPublics',                      middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), selectForListsCtrl.getVehiculesPublics);

//Composant Calendrier
router.get('/calendrier/peremptionsLots',               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['materiel_lecture']),       calendrierCtrl.peremptionsLots);
router.get('/calendrier/peremptionsReserves',           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),        calendrierCtrl.peremptionsReserves);
router.get('/calendrier/inventairesPassesLots',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_lecture']),           calendrierCtrl.inventairesPassesLots);
router.get('/calendrier/inventairesPassesReserves',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),        calendrierCtrl.inventairesPassesReserves);
router.get('/calendrier/inventairesFutursLots',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_lecture']),           calendrierCtrl.inventairesFutursLots);
router.get('/calendrier/inventairesFutursReserves',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),        calendrierCtrl.inventairesFutursReserves);
router.get('/calendrier/commandesLivraisons',           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_lecture']),       calendrierCtrl.commandesLivraisons);
router.get('/calendrier/vehiculesMntPonctuelles',       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_lecture']),      calendrierCtrl.vehiculesMntPonctuelles);
router.get('/calendrier/vehiculesMntRegPassees',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealth_lecture']), calendrierCtrl.vehiculesMntRegPassees);
router.get('/calendrier/vehiculesMntRegFutures',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealth_lecture']), calendrierCtrl.vehiculesMntRegFutures);
router.get('/calendrier/vehiculesDesinfectionsPassees', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['desinfections_lecture']), calendrierCtrl.vehiculesDesinfectionsPassees);
router.get('/calendrier/vehiculesDesinfectionsFutures', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['desinfections_lecture']), calendrierCtrl.vehiculesDesinfectionsFutures);
router.get('/calendrier/tenuesAffectations',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_lecture']),         calendrierCtrl.tenuesAffectations);
router.get('/calendrier/tenuesRetours',                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_lecture']),         calendrierCtrl.tenuesRetours);
router.get('/calendrier/cautionsEmissions',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_lecture']),       calendrierCtrl.cautionsEmissions);
router.get('/calendrier/cautionsExpirations',           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_lecture']),       calendrierCtrl.cautionsExpirations);
router.get('/calendrier/toDoListOwn',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),    calendrierCtrl.toDoListOwn);
router.get('/calendrier/toDoListAll',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),       calendrierCtrl.toDoListAll);

//OPERATIONNEL - Lots
router.get('/lots/getLots',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_lecture']),                                             lotsCtrl.getLots);
router.post('/lots/getOneLot',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_lecture']),                                             lotsCtrl.getOneLot);
router.post('/lots/addLot',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_ajout']),        loggerMiddleware.modificationLogger(), lotsCtrl.addLot);
router.post('/lots/duplicateLot',          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_ajout']),        loggerMiddleware.modificationLogger(), lotsCtrl.duplicateLot);
router.post('/lots/importRef',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_modification']), loggerMiddleware.modificationLogger(), lotsCtrl.importRef);
router.post('/lots/updateLot',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_modification']), loggerMiddleware.modificationLogger(), lotsCtrl.updateLot);
router.post('/lots/lotsDelete',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_suppression']),  loggerMiddleware.suppressionLogger(),  lotsCtrl.lotsDelete);
//OPERATIONNEL - Lots - Inventaires
router.post('/lots/getOneInventaireForDisplay',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_lecture']),                                             lotsCtrl.getOneInventaireForDisplay);
router.post('/lots/startInventaire',                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_modification']), loggerMiddleware.modificationLogger(), lotsCtrl.startInventaire);
router.post('/lots/getArborescenceSacs',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_lecture']),                                             lotsCtrl.getArborescenceSacs);
router.post('/lots/getAllElementsInventaireEnCours', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_lecture']),                                             lotsCtrl.getAllElementsInventaireEnCours);
router.post('/lots/lotsInventaireCancel',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_modification']),  loggerMiddleware.suppressionLogger(), lotsCtrl.lotsInventaireCancel);
router.post('/lots/lotsInventaireDelete',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lots_suppression']),  loggerMiddleware.suppressionLogger(),  lotsCtrl.lotsInventaireDelete);
//OPERATIONNEL - Sacs
router.get('/sacs/getSacs',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_lecture']),                                             sacsCtrl.getSacs);
router.post('/sacs/getOneSac',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_lecture']),                                             sacsCtrl.getOneSac);
router.post('/sacs/addSacs',               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_ajout']),        loggerMiddleware.modificationLogger(), sacsCtrl.addSacs);
router.post('/sacs/updateSacs',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_modification']), loggerMiddleware.modificationLogger(), sacsCtrl.updateSacs);
router.post('/sacs/sacDelete',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_suppression']),  loggerMiddleware.suppressionLogger(),  sacsCtrl.sacDelete);
//OPERATIONNEL - Emplacements
router.post('/sacs/getEmplacementsOneSac', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_lecture']),                                             sacsCtrl.getEmplacementsOneSac);
router.post('/sacs/addEmplacement',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_ajout']),        loggerMiddleware.modificationLogger(), sacsCtrl.addEmplacement);
router.post('/sacs/updateEmplacement',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_modification']), loggerMiddleware.modificationLogger(), sacsCtrl.updateEmplacement);
router.post('/sacs/emplacementDelete',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['sac_suppression']),  loggerMiddleware.suppressionLogger(),  sacsCtrl.emplacementDelete);
//OPERATIONNEL - Matériels
router.post('/materiels/getMateriels',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['materiel_lecture']),                                             materielsCtrl.getMateriels);
router.post('/materiels/getOneMateriel',   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['materiel_lecture']),                                             materielsCtrl.getOneMateriel);
router.post('/materiels/addMateriels',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['materiel_ajout']),        loggerMiddleware.modificationLogger(), materielsCtrl.addMateriels);
router.post('/materiels/updateMateriels',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['materiel_modification']), loggerMiddleware.modificationLogger(), materielsCtrl.updateMateriels);
router.post('/materiels/materielsDelete',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['materiel_suppression']),  loggerMiddleware.suppressionLogger(),  materielsCtrl.materielsDelete);
//OPERATIONNEL - Alertes bénévoles
router.post('/lots/getLotsAlertes',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['alertesBenevolesLots_lecture']),                                                                                              lotsCtrl.getLotsAlertes);
router.post('/lots/autoAffect',          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['alertesBenevolesLots_affectation']),                                                 loggerMiddleware.modificationLogger(),   lotsCtrl.autoAffect);
router.post('/lots/affectationTier',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['alertesBenevolesLots_affectationTier']),                                             loggerMiddleware.modificationLogger(),   lotsCtrl.affectationTier);
router.post('/lots/udpateStatut',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),                       middlewaresFunctions.alerteLotOwned(), loggerMiddleware.modificationLogger(),   lotsCtrl.udpateStatut);
//OPERATIONNEL - Alertes bénévoles - PUBLIC
router.post('/lots/createAlerte',     middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(),       lotsCtrl.createAlerte);
//OPERTAIONNEL - Consommation - PUBLIC
router.post('/consommations/getOneConso',     middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(),       lotsConsommationsCtrl.getOneConso);
router.post('/consommations/createConso',     middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(),       lotsConsommationsCtrl.createConso);
//OPERTAIONNEL - Consommation - AUTHENTICATED
router.get('/consommations/getAllConso',                   middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['consommationLots_lecture']),      lotsConsommationsCtrl.getAllConso);
router.post('/consommations/decompterActionDefaut',        middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['consommationLots_affectation']),  lotsConsommationsCtrl.decompterActionDefaut);
router.post('/consommations/annulerTouteAction',           middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['consommationLots_affectation']),  lotsConsommationsCtrl.annulerTouteAction);
router.post('/consommations/decompterToutesActionsDefaut', middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['consommationLots_affectation']),  lotsConsommationsCtrl.decompterToutesActionsDefaut);
router.post('/consommations/lotsConsommationsDelete',      middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['consommationLots_supression']),   lotsConsommationsCtrl.lotsConsommationsDelete);

//RESERVES - Conteneurs
router.get('/reserves/getConteneurs',           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),                                              reservesConteneursCtrl.getConteneurs);
router.post('/reserves/getOneConteneur',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),                                              reservesConteneursCtrl.getOneConteneur);
router.post('/reserves/addConteneur',           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_ajout']),         loggerMiddleware.modificationLogger(), reservesConteneursCtrl.addConteneur);
router.post('/reserves/updateConteneur',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_modification']),  loggerMiddleware.modificationLogger(), reservesConteneursCtrl.updateConteneur);
router.post('/reserves/reserveConteneurDelete', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_suppression']),   loggerMiddleware.suppressionLogger(),  reservesConteneursCtrl.reserveConteneurDelete);
//RESERVES - Conteneurs - Inventaires
router.post('/reserves/getOneInventaireForDisplay',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),                                             reservesConteneursCtrl.getOneInventaireForDisplay);
router.post('/reserves/startInventaire',                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_modification']), loggerMiddleware.modificationLogger(), reservesConteneursCtrl.startInventaire);
router.post('/reserves/getAllElementsInventaireEnCours', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),                                             reservesConteneursCtrl.getAllElementsInventaireEnCours);
router.post('/reserves/reserveInventaireCancel',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_modification']),  loggerMiddleware.suppressionLogger(), reservesConteneursCtrl.reserveInventaireCancel);
router.post('/reserves/reserveInventaireDelete',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_suppression']),  loggerMiddleware.suppressionLogger(),  reservesConteneursCtrl.reserveInventaireDelete);
//RESERVES - Matériels
router.post('/reserves/getReservesMateriels',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),                                             reservesMaterielsCtrl.getReservesMateriels);
router.post('/reserves/getOneReservesMateriel',   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_lecture']),                                             reservesMaterielsCtrl.getOneReservesMateriel);
router.post('/reserves/addReservesMateriels',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_ajout']),        loggerMiddleware.modificationLogger(), reservesMaterielsCtrl.addReservesMateriels);
router.post('/reserves/updateReservesMateriels',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_modification']), loggerMiddleware.modificationLogger(), reservesMaterielsCtrl.updateReservesMateriels);
router.post('/reserves/materielsReservesDelete',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_suppression']),  loggerMiddleware.suppressionLogger(),  reservesMaterielsCtrl.materielsReservesDelete);

//TRANSFERTS - Lots
router.post('/transferts/getReservesForOneTransfert', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_ReserveVersLot']),                                         transfertsCtrl.getReservesForOneTransfert);
router.post('/transferts/opererTransfertReserveLot',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_ReserveVersLot']),  loggerMiddleware.modificationLogger(), transfertsCtrl.opererTransfertReserveLot);

//COMMANDES
router.get('/commandes/getCommandes',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_lecture']),                                                                                                                                                                                            commandesCtrl.getCommandes);
router.post('/commandes/getOneCommande',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_lecture']),                                                                                                                                                                                            commandesCtrl.getOneCommande);
router.post('/commandes/addCommande',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_ajout']),                                                                                                                                                         loggerMiddleware.modificationLogger(), commandesCtrl.addCommande);
router.post('/commandes/addComment',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),                                            middlewaresFunctions.checkCmdRole(['demandeur','affectee', 'valideur', 'observateur']),                 loggerMiddleware.modificationLogger(), commandesCtrl.addComment);
router.post('/commandes/abandonnerCommande',               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_abandonner']),                                                                                                                                                    loggerMiddleware.modificationLogger(), commandesCtrl.abandonnerCommande);
router.post('/commandes/commandesDelete',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_abandonner']),                                                                                                                                                    loggerMiddleware.suppressionLogger(),  commandesCtrl.commandesDelete);
router.post('/commandes/updateInfoGenerales',              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([1]),   middlewaresFunctions.checkCmdRole(['demandeur','affectee']),                                            loggerMiddleware.modificationLogger(), commandesCtrl.updateInfoGenerales);
router.post('/commandes/addMateriels',                     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([1]),   middlewaresFunctions.checkCmdRole(['demandeur','affectee']),                                            loggerMiddleware.modificationLogger(), commandesCtrl.addMateriels);
router.post('/commandes/updateMateriels',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([1]),   middlewaresFunctions.checkCmdRole(['demandeur','affectee']),                                            loggerMiddleware.modificationLogger(), commandesCtrl.updateMateriels);
router.post('/commandes/removeMateriels',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([1]),   middlewaresFunctions.checkCmdRole(['demandeur','affectee']),                                            loggerMiddleware.modificationLogger(), commandesCtrl.removeMateriels);
router.post('/commandes/demandeValidation',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([1]),   middlewaresFunctions.checkCmdRole(['affectee']),             middlewaresFunctions.checkCmdCanMoveTo(2), loggerMiddleware.modificationLogger(), commandesCtrl.demandeValidation);
router.post('/commandes/updateRemarquesValidation',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([2]),   middlewaresFunctions.checkCmdRole(['valideur']),                                                        loggerMiddleware.modificationLogger(), commandesCtrl.updateRemarquesValidation);
router.post('/commandes/rejeterCommande',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([2]),   middlewaresFunctions.checkCmdRole(['valideur']),             middlewaresFunctions.checkCmdCanMoveTo(1), loggerMiddleware.modificationLogger(), commandesCtrl.rejeterCommande);
router.post('/commandes/approuverCommande',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([2]),   middlewaresFunctions.checkCmdRole(['valideur']),             middlewaresFunctions.checkCmdCanMoveTo(3), loggerMiddleware.modificationLogger(), commandesCtrl.approuverCommande);
router.post('/commandes/updatePassageCommande',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([3]),   middlewaresFunctions.checkCmdRole(['affectee']),                                                        loggerMiddleware.modificationLogger(), commandesCtrl.updatePassageCommande);
router.post('/commandes/passerCommande',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([3]),   middlewaresFunctions.checkCmdRole(['affectee']),             middlewaresFunctions.checkCmdCanMoveTo(4), loggerMiddleware.modificationLogger(), commandesCtrl.passerCommande);
router.post('/commandes/updateInfosLivraison',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([4,6]), middlewaresFunctions.checkCmdRole(['affectee']),                                                        loggerMiddleware.modificationLogger(), commandesCtrl.updateInfosLivraison);
router.post('/commandes/livraisonOKCommande',              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([4,6]), middlewaresFunctions.checkCmdRole(['affectee']),             middlewaresFunctions.checkCmdCanMoveTo(5), loggerMiddleware.modificationLogger(), commandesCtrl.livraisonOKCommande);
router.post('/commandes/livraisonSAVCommande',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([4]),   middlewaresFunctions.checkCmdRole(['affectee']),             middlewaresFunctions.checkCmdCanMoveTo(6), loggerMiddleware.modificationLogger(), commandesCtrl.livraisonSAVCommande);
router.post('/commandes/transfertManuel',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([5]),   middlewaresFunctions.checkCmdRole(['affectee']),                                                        loggerMiddleware.modificationLogger(), commandesCtrl.transfertManuel);
router.post('/commandes/cloreCommande',                    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']), middlewaresFunctions.checkCmdStage([5]),   middlewaresFunctions.checkCmdRole(['affectee']),             middlewaresFunctions.checkCmdCanMoveTo(7), loggerMiddleware.modificationLogger(), commandesCtrl.cloreCommande);
//COMMANDES - transferts vers la réserve
router.post('/transferts/getReservesForOneIntegration',    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_cmdVersReserve']), commandesCtrl.getReservesForOneIntegration);
router.post('/transferts/enregistrerTransfert',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['reserve_cmdVersReserve']), commandesCtrl.enregistrerTransfert);

//CENTRES DE COUTS
router.get('/centresCouts/getCentres',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_lecture']),                                             centresCoutsCtrl.getCentres);
router.post('/centresCouts/getOneCentre',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_lecture']),                                             centresCoutsCtrl.getOneCentre);
router.post('/centresCouts/addCentre',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_ajout']),        loggerMiddleware.modificationLogger(), centresCoutsCtrl.addCentre);
router.post('/centresCouts/updateCentre',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_ajout']),        loggerMiddleware.modificationLogger(), centresCoutsCtrl.updateCentre);
router.post('/centresCouts/addOperation',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_etreEnCharge']), loggerMiddleware.modificationLogger(), centresCoutsCtrl.addOperation);
router.post('/centresCouts/updateOperation',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_etreEnCharge']), loggerMiddleware.modificationLogger(), centresCoutsCtrl.updateOperation);
router.post('/centresCouts/centreCoutsOperationsDelete',    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_etreEnCharge']), loggerMiddleware.suppressionLogger(),  centresCoutsCtrl.centreCoutsOperationsDelete);
router.post('/centresCouts/addGerant',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_ajout']),        loggerMiddleware.modificationLogger(), centresCoutsCtrl.addGerant);
router.post('/centresCouts/updateGerant',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_ajout']),        loggerMiddleware.modificationLogger(), centresCoutsCtrl.updateGerant);
router.post('/centresCouts/centreCoutsGerantDelete',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_ajout']),        loggerMiddleware.modificationLogger(), centresCoutsCtrl.centreCoutsGerantDelete);
router.post('/centresCouts/integrerCommande',               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_etreEnCharge']), loggerMiddleware.modificationLogger(), centresCoutsCtrl.integrerCommande);
router.post('/centresCouts/refuserCommande',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_etreEnCharge']), loggerMiddleware.modificationLogger(), centresCoutsCtrl.refuserCommande);
router.post('/centresCouts/recyclerCommande',               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_etreEnCharge']), loggerMiddleware.modificationLogger(), centresCoutsCtrl.recyclerCommande);

//VHF Canaux Attachements
router.post('/commandes/uploadCommandesAttached',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_etreEnCharge',]), loggerMiddleware.modificationLogger(), commandesCtrl.uploadCommandesAttachedMulter, commandesCtrl.uploadCommandesAttached);
router.post('/commandes/updateMetaDataCommandes',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_etreEnCharge',]), loggerMiddleware.modificationLogger(), commandesCtrl.updateMetaDataCommandes);
router.post('/commandes/dropCommandesDocument',       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_etreEnCharge',]), loggerMiddleware.suppressionLogger(),  commandesCtrl.dropCommandesDocument);

//Fournisseurs - Informations de base
router.get('/fournisseurs/getFournisseurs',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_lecture']),      jwtFunctions.decryptAesToken(),        fournisseursCtrl.getFournisseurs);
router.post('/fournisseurs/getOneFournisseur',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_lecture']),      jwtFunctions.decryptAesToken(),        fournisseursCtrl.getOneFournisseur);
router.post('/fournisseurs/addFournisseur',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_ajout']),        loggerMiddleware.modificationLogger(), fournisseursCtrl.addFournisseur);
router.post('/fournisseurs/updateFournisseur',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_modification']), loggerMiddleware.modificationLogger(), fournisseursCtrl.updateFournisseur);
router.post('/fournisseurs/deleteFournisseur',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_suppression']),  loggerMiddleware.suppressionLogger(),  fournisseursCtrl.deleteFournisseur);
//Fournisseurs - Gestion des informations chiffrées
router.post('/fournisseurs/authenticateForAES',       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_lecture']),                                                                             fournisseursAesCtrl.authenticateForAES);
router.post('/fournisseurs/initKey',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                                loggerMiddleware.modificationLogger(), fournisseursAesCtrl.initKey, fournisseursAesCtrl.authenticateForAES);
router.post('/fournisseurs/changeKey',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                jwtFunctions.decryptAesToken(), loggerMiddleware.modificationLogger(), fournisseursAesCtrl.updateAesKey, fournisseursAesCtrl.authenticateForAES);
router.post('/fournisseurs/disableAesAndDelete',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                jwtFunctions.decryptAesToken(), loggerMiddleware.suppressionLogger(),  fournisseursAesCtrl.disableAesAndDelete);
router.post('/fournisseurs/updateFournisseurAesData', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_modification']), jwtFunctions.decryptAesToken(), loggerMiddleware.modificationLogger(), fournisseursAesCtrl.updateFournisseurAesData);

//Véhicules
router.get('/vehicules/getAllVehicules',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_lecture']),                                             vehiculesCtrl.getAllVehicules);
router.post('/vehicules/getOneVehicule',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_lecture']),                                             vehiculesCtrl.getOneVehicule);
router.post('/vehicules/addVehicule',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_ajout']),        loggerMiddleware.modificationLogger(), vehiculesCtrl.addVehicule);
router.post('/vehicules/updateVehicule',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification']), loggerMiddleware.modificationLogger(), vehiculesCtrl.updateVehicule);
router.post('/vehicules/deleteVehicule',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_suppression']),  loggerMiddleware.suppressionLogger(),  vehiculesCtrl.deleteVehicule);
//Vehicules Attachements
router.post('/vehicules/uploadVehiculeAttached',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification',]), loggerMiddleware.modificationLogger(), vehiculesCtrl.uploadVehiculeAttachedMulter, vehiculesCtrl.uploadVehiculeAttached);
router.post('/vehicules/updateMetaDataVehicule',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification',]), loggerMiddleware.modificationLogger(), vehiculesCtrl.updateMetaDataVehicule);
router.post('/vehicules/dropVehiculeDocument',       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_suppression',]) , loggerMiddleware.suppressionLogger(),  vehiculesCtrl.dropVehiculeDocument);
//Releves Kilométriques
router.post('/vehicules/addReleveKM',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification']), loggerMiddleware.modificationLogger(), vehiculesCtrl.addReleveKM);
router.post('/vehicules/updateReleveKM',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification']), loggerMiddleware.modificationLogger(), vehiculesCtrl.updateReleveKM);
router.post('/vehicules/deleteReleveKM',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification']), loggerMiddleware.suppressionLogger(),  vehiculesCtrl.deleteReleveKM);
//Maintenances ponctuelles
router.post('/vehicules/addMaintenancePonctuelle',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification']), loggerMiddleware.modificationLogger(), vehiculesCtrl.addMaintenancePonctuelle);
router.post('/vehicules/updateMaintenancePonctuelle',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification']), loggerMiddleware.modificationLogger(), vehiculesCtrl.updateMaintenancePonctuelle);
router.post('/vehicules/deleteMaintenancePonctuelle',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_modification']), loggerMiddleware.suppressionLogger(),  vehiculesCtrl.deleteMaintenancePonctuelle);
//Maintenances régulières
router.get('/vehicules/getMaintenancesRegulieresDashoard',   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealth_lecture']),                                             vehiculesCtrl.getMaintenancesRegulieresDashoard);
router.post('/vehicules/addMaintenanceReguliere',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealth_ajout']),        loggerMiddleware.modificationLogger(), vehiculesCtrl.addMaintenanceReguliere);
router.post('/vehicules/updateMaintenanceReguliere',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealth_modification']), loggerMiddleware.modificationLogger(), vehiculesCtrl.updateMaintenanceReguliere);
router.post('/vehicules/deleteMaintenanceReguliere',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealth_modification']), loggerMiddleware.suppressionLogger(),  vehiculesCtrl.deleteMaintenanceReguliere);
router.post('/vehicules/updateMaintenanceReguliereAlertes',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealth_suppression']),  loggerMiddleware.modificationLogger(), vehiculesCtrl.updateMaintenanceReguliereAlertes);
//Désinfections
router.get('/vehicules/getDesinfectionsDashoard',    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['desinfections_lecture']),                                             vehiculesCtrl.getDesinfectionsDashoard);
router.post('/vehicules/addDesinfection',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['desinfections_ajout']),        loggerMiddleware.modificationLogger(), vehiculesCtrl.addDesinfection);
router.post('/vehicules/updateDesinfection',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['desinfections_modification']), loggerMiddleware.modificationLogger(), vehiculesCtrl.updateDesinfection);
router.post('/vehicules/deleteDesinfection',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['desinfections_modification']), loggerMiddleware.suppressionLogger(),  vehiculesCtrl.deleteDesinfection);
router.post('/vehicules/updateDesinfectionAlertes',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['desinfections_suppression']),  loggerMiddleware.modificationLogger(), vehiculesCtrl.updateDesinfectionAlertes);
//Alertes bénévoles
router.post('/vehicules/getVehiculesAlertes', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['alertesBenevolesVehicules_lecture']),                                                                                              vehiculesCtrl.getVehiculesAlertes);
router.post('/vehicules/autoAffect',          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['alertesBenevolesVehicules_affectation']),                                                 loggerMiddleware.modificationLogger(),   vehiculesCtrl.autoAffect);
router.post('/vehicules/affectationTier',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['alertesBenevolesVehicules_affectationTier']),                                             loggerMiddleware.modificationLogger(),   vehiculesCtrl.affectationTier);
router.post('/vehicules/udpateStatut',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),                       middlewaresFunctions.alerteVehiculeOwned(), loggerMiddleware.modificationLogger(),   vehiculesCtrl.udpateStatut);
//Alertes bénévoles - PUBLIC
router.post('/vehicules/createAlerte',     middlewaresFunctions.checkFunctionnalityBenevolesEnabled(), loggerMiddleware.httpLogger(),       vehiculesCtrl.createAlerte);

//Tenues - Catalogue
router.get('/tenues/getPersonnesSuggested',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_lecture']),                                             tenuesCtrl.getPersonnesSuggested);
router.get('/tenues/getCatalogue',              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_lecture']),                                             tenuesCtrl.getCatalogue);
router.post('/tenues/addCatalogue',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_ajout']),        loggerMiddleware.modificationLogger(), tenuesCtrl.addCatalogue);
router.post('/tenues/updateCatalogue',          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_modification']), loggerMiddleware.modificationLogger(), tenuesCtrl.updateCatalogue);
router.post('/tenues/deleteCatalogue',          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_suppression']),  loggerMiddleware.suppressionLogger(),  tenuesCtrl.deleteCatalogue);
//Tenues - Affectations
router.get('/tenues/getAffectations',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_lecture']),                                             tenuesCtrl.getAffectations);
router.get('/tenues/getAffectationsRow',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_lecture']),                                             tenuesCtrl.getAffectationsRow);
router.post('/tenues/addAffectations',    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_ajout']),        loggerMiddleware.modificationLogger(), tenuesCtrl.addAffectations);
router.post('/tenues/updateAffectations', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_modification']), loggerMiddleware.modificationLogger(), tenuesCtrl.updateAffectations);
router.post('/tenues/deleteAffectations', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_suppression']),  loggerMiddleware.suppressionLogger(),  tenuesCtrl.deleteAffectations);
//Tenues - Cautions
router.get('/tenues/getCautions',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_lecture']),                                             tenuesCtrl.getCautions);
router.get('/tenues/getCautionsRow',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_lecture']),                                             tenuesCtrl.getCautionsRow);
router.post('/tenues/addCautions',    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_ajout']),        loggerMiddleware.modificationLogger(), tenuesCtrl.addCautions);
router.post('/tenues/updateCautions', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_modification']), loggerMiddleware.modificationLogger(), tenuesCtrl.updateCautions);
router.post('/tenues/deleteCautions', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_suppression']),  loggerMiddleware.suppressionLogger(),  tenuesCtrl.deleteCautions);

//VHF Canaux
router.get('/vhf/getFrequences',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_lecture']),                                             vhfCtrl.getFrequences);
router.post('/vhf/addCanal',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_ajout']),        loggerMiddleware.modificationLogger(), vhfCtrl.addCanal);
router.post('/vhf/updateCanal',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_modification']), loggerMiddleware.modificationLogger(), vhfCtrl.updateCanal);
router.post('/vhf/deleteCanal',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_suppression']),  loggerMiddleware.suppressionLogger(),  vhfCtrl.deleteCanal);
//VHF Canaux Attachements
router.post('/vhf/getAllDocumentsOneCanal', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_lecture',]),                                             vhfCtrl.getAllDocumentsOneCanal);
router.post('/vhf/uploadCanalAttached',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_modification',]), loggerMiddleware.modificationLogger(), vhfCtrl.uploadCanalAttachedMulter, vhfCtrl.uploadCanalAttached);
router.post('/vhf/updateMetaDataCanal',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_modification',]), loggerMiddleware.modificationLogger(), vhfCtrl.updateMetaDataCanal);
router.post('/vhf/dropCanalDocument',       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_suppression',]) , loggerMiddleware.suppressionLogger(),  vhfCtrl.dropCanalDocument);
//VHF Plans
router.get('/vhf/getPlans',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture']),                                             vhfCtrl.getPlans);
router.post('/vhf/addPlan',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_ajout']),        loggerMiddleware.modificationLogger(), vhfCtrl.addPlan);
router.post('/vhf/updatePlan',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_modification']), loggerMiddleware.modificationLogger(), vhfCtrl.updatePlan);
router.post('/vhf/deletePlan',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_suppression']),  loggerMiddleware.suppressionLogger(),  vhfCtrl.deletePlan);
//VHF Plans Attachements
router.post('/vhf/getAllDocumentsOnePlan', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture',]),                                              vhfCtrl.getAllDocumentsOnePlan);
router.post('/vhf/uploadPlanAttached',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_modification',]), loggerMiddleware.modificationLogger(), vhfCtrl.uploadPlanAttachedMulter, vhfCtrl.uploadPlanAttached);
router.post('/vhf/updateMetaDataPlan',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_modification',]), loggerMiddleware.modificationLogger(), vhfCtrl.updateMetaDataPlan);
router.post('/vhf/dropPlanDocument',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_suppression',]) , loggerMiddleware.suppressionLogger(),  vhfCtrl.dropPlanDocument);
//VHF Plans canaux affecation
router.post('/vhf/getCanauxOnePlan',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture']),                                             vhfCtrl.getCanauxOnePlan);
router.post('/vhf/updateCanauxOnePlan',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_modification']), loggerMiddleware.modificationLogger(), vhfCtrl.updateCanauxOnePlan);
//VHF Equipements
router.get('/vhf/getEquipementsVhf',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_lecture']),                                              vhfCtrl.getEquipementsVhf);
router.post('/vhf/getOneEquipement',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_lecture']),                                              vhfCtrl.getOneEquipement);
router.post('/vhf/addEquipement',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_ajout']),         loggerMiddleware.modificationLogger(), vhfCtrl.addEquipement);
router.post('/vhf/updateEquipement',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_modification']),  loggerMiddleware.modificationLogger(), vhfCtrl.updateEquipement);
router.post('/vhf/deleteEquipement',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_suppression',]) , loggerMiddleware.suppressionLogger(),  vhfCtrl.deleteEquipement);
//VHF Equipements Accessoires
router.post('/vhf/addAccessoire',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_ajout']),         loggerMiddleware.modificationLogger(), vhfCtrl.addAccessoire);
router.post('/vhf/updateAccessoire',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_modification']),  loggerMiddleware.modificationLogger(), vhfCtrl.updateAccessoire);
router.post('/vhf/vhfEquipementsAccessoiresDelete', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_suppression',]) , loggerMiddleware.suppressionLogger(),  vhfCtrl.vhfEquipementsAccessoiresDelete);
//VHF Equipements Attachements
router.post('/vhf/uploadEquipementsAttached',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_modification',]), loggerMiddleware.modificationLogger(), vhfCtrl.uploadEquipementsAttachedMulter, vhfCtrl.uploadEquipementsAttached);
router.post('/vhf/updateMetaDataEquipements',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_modification',]), loggerMiddleware.modificationLogger(), vhfCtrl.updateMetaDataEquipements);
router.post('/vhf/dropEquipementsDocument',       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_suppression',]) , loggerMiddleware.suppressionLogger(),  vhfCtrl.dropEquipementsDocument);

//referentiels
router.get('/referentiels/getReferentiels',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_lecture']),                                                                  referentielsCtrl.getReferentiels);
router.post('/referentiels/getOneReferentiel',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_lecture']),                                                                  referentielsCtrl.getOneReferentiel);
router.post('/referentiels/addReferentiel',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_ajout']),                             loggerMiddleware.modificationLogger(), referentielsCtrl.addReferentiel);
router.post('/referentiels/getCatalogueForReferentielForm',   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_modification', 'catalogue_lecture']),                                        referentielsCtrl.getCatalogueForReferentielForm);
router.post('/referentiels/updateReferentiel',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_modification']),                      loggerMiddleware.modificationLogger(), referentielsCtrl.updateReferentiel);
router.post('/referentiels/deleteReferentiel',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_suppression']),                       loggerMiddleware.suppressionLogger(),  referentielsCtrl.deleteReferentiel);

//Messages généraux
router.get('/messagesGeneraux/getMessagesPublics',            loggerMiddleware.httpLogger(),                                                                                                    messagesGenerauxCtrl.getMessagesPublics);
router.get('/messagesGeneraux/getAllMessages',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),                                         messagesGenerauxCtrl.getAllMessages);
router.get('/messagesGeneraux/getMessagesTypes',              loggerMiddleware.httpLogger(),                                                                                                    messagesGenerauxCtrl.getMessagesTypes);
router.post('/messagesGeneraux/addMessage',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['messages_ajout']),       loggerMiddleware.modificationLogger(), messagesGenerauxCtrl.addMessage);
router.post('/messagesGeneraux/updateMessage',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['messages_ajout']),       loggerMiddleware.modificationLogger(), messagesGenerauxCtrl.updateMessage);
router.post('/messagesGeneraux/deleteMessage',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['messages_suppression']), loggerMiddleware.suppressionLogger(),  messagesGenerauxCtrl.deleteMessage);
router.post('/messagesGeneraux/messageMail',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['contactMailGroupe']),                                           messagesGenerauxCtrl.messageMail);

//ToDoList
router.get('/todolist/getPersonsForTDL',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),                                                                                    toDoListCtrl.getPersonsForTDL);
router.get('/todolist/getPioritesForTDL',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),                                                                                 toDoListCtrl.getPioritesForTDL);
router.get('/todolist/getAllTDL',             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),                                                                                    toDoListCtrl.getAllTDL);
router.post('/todolist/getOneTDL',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   middlewaresFunctions.hisOwnTdl(),                                             toDoListCtrl.getOneTDL);
router.get('/todolist/getUnaffectedTDL',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),                                                                                    toDoListCtrl.getUnaffectedTDL);
router.get('/todolist/getClosedTDL',          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),                                                                                    toDoListCtrl.getClosedTDL);
router.post('/todolist/getTDLonePerson',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   middlewaresFunctions.hisOwnTdlArray(),                                        toDoListCtrl.getTDLonePerson);
router.post('/todolist/addTDL',               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   middlewaresFunctions.addToHimself(),   loggerMiddleware.modificationLogger(), toDoListCtrl.addTDL);
router.post('/todolist/updateTDLAffectation', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_modification']),                                        loggerMiddleware.modificationLogger(), toDoListCtrl.updateTDLAffectation);
router.post('/todolist/updateTDL',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   middlewaresFunctions.editHisOwnTdl(),  loggerMiddleware.modificationLogger(), toDoListCtrl.updateTDL);
router.post('/todolist/completedTDL',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   middlewaresFunctions.editHisOwnTdl(),  loggerMiddleware.modificationLogger(), toDoListCtrl.completedTDL);
router.post('/todolist/unCompletedTDL',       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   middlewaresFunctions.editHisOwnTdl(),  loggerMiddleware.modificationLogger(), toDoListCtrl.unCompletedTDL);
router.post('/todolist/duplicateTDL',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_modification']),                                        loggerMiddleware.modificationLogger(), toDoListCtrl.duplicateTDL);
router.post('/todolist/deleteTDL',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_modification']),                                        loggerMiddleware.suppressionLogger(),  toDoListCtrl.deleteTDL);

//settings Métiers
router.get('/settingsMetiers/getCategoriesMateriels',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['categories_lecture']),                                                     settingsMetiersCtrl.getCategoriesMateriels);
router.post('/settingsMetiers/addCategoriesMateriels',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['categories_ajout']),                loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addCategoriesMateriels);
router.post('/settingsMetiers/updateCategoriesMateriels',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['categories_modification']),         loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateCategoriesMateriels);
router.post('/settingsMetiers/deleteCategoriesMateriels',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['categories_suppression']),          loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteCategoriesMateriels);
router.get('/settingsMetiers/getLieux',                                     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lieux_lecture']),                                                          settingsMetiersCtrl.getLieux);
router.post('/settingsMetiers/addLieux',                                    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lieux_ajout']),                     loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addLieux);
router.post('/settingsMetiers/updateLieux',                                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lieux_modification']),              loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateLieux);
router.post('/settingsMetiers/deleteLieux',                                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['lieux_suppression']),               loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteLieux);
router.get('/settingsMetiers/getTypesVehicules',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_types_lecture']),                                                settingsMetiersCtrl.getTypesVehicules);
router.post('/settingsMetiers/addTypesVehicules',                           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_types_ajout']),           loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addTypesVehicules);
router.post('/settingsMetiers/updateTypesVehicules',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_types_modification']),    loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateTypesVehicules);
router.post('/settingsMetiers/deleteTypesVehicules',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_types_suppression']),     loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteTypesVehicules);
router.get('/settingsMetiers/getTypesDesinfections',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesDesinfections_lecture']),                                             settingsMetiersCtrl.getTypesDesinfections);
router.post('/settingsMetiers/addTypesDesinfections',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesDesinfections_ajout']),        loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addTypesDesinfections);
router.post('/settingsMetiers/updateTypesDesinfections',                    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesDesinfections_modification']), loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateTypesDesinfections);
router.post('/settingsMetiers/deleteTypesDesinfections',                    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['typesDesinfections_suppression']),  loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteTypesDesinfections);
router.get('/settingsMetiers/getTypesMaintenancesRegulieresVehicules',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_lecture']),                                             settingsMetiersCtrl.getTypesMaintenancesRegulieresVehicules);
router.post('/settingsMetiers/addTypesMaintenancesRegulieresVehicules',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_ajout']),        loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addTypesMaintenancesRegulieresVehicules);
router.post('/settingsMetiers/updateTypesMaintenancesRegulieresVehicules',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_modification']), loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateTypesMaintenancesRegulieresVehicules);
router.post('/settingsMetiers/deleteTypesMaintenancesRegulieresVehicules',  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_suppression']),  loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteTypesMaintenancesRegulieresVehicules);
router.get('/settingsMetiers/getTypesMaintenancesPonctuellesVehicules',     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_lecture']),                                             settingsMetiersCtrl.getTypesMaintenancesPonctuellesVehicules);
router.post('/settingsMetiers/addTypesMaintenancesPonctuellesVehicules',    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_ajout']),        loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addTypesMaintenancesPonctuellesVehicules);
router.post('/settingsMetiers/updateTypesMaintenancesPonctuellesVehicules', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_modification']), loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateTypesMaintenancesPonctuellesVehicules);
router.post('/settingsMetiers/deleteTypesMaintenancesPonctuellesVehicules', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehiculeHealthType_suppression']),  loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteTypesMaintenancesPonctuellesVehicules);
router.get('/settingsMetiers/getCarburants',                                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['carburants_lecture']),                                                     settingsMetiersCtrl.getCarburants);
router.post('/settingsMetiers/addCarburants',                               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['carburants_ajout']),                loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addCarburants);
router.post('/settingsMetiers/updateCarburants',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['carburants_modification']),         loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateCarburants);
router.post('/settingsMetiers/deleteCarburants',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['carburants_suppression']),          loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteCarburants);
router.get('/settingsMetiers/getEtatsLots',                                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_lecture']),                                                          settingsMetiersCtrl.getEtatsLots);
router.post('/settingsMetiers/addEtatsLots',                                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_ajout']),                     loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addEtatsLots);
router.post('/settingsMetiers/updateEtatsLots',                             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_modification']),              loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateEtatsLots);
router.post('/settingsMetiers/deleteEtatsLots',                             loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_suppression']),               loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteEtatsLots);
router.get('/settingsMetiers/getEtatsMateriels',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_lecture']),                                                          settingsMetiersCtrl.getEtatsMateriels);
router.post('/settingsMetiers/addEtatsMateriels',                           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_ajout']),                     loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addEtatsMateriels);
router.post('/settingsMetiers/updateEtatsMateriels',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_modification']),              loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateEtatsMateriels);
router.post('/settingsMetiers/deleteEtatsMateriels',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_suppression']),               loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteEtatsMateriels);
router.get('/settingsMetiers/getEtatsVehicules',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_lecture']),                                                          settingsMetiersCtrl.getEtatsVehicules);
router.post('/settingsMetiers/addEtatsVehicules',                           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_ajout']),                     loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addEtatsVehicules);
router.post('/settingsMetiers/updateEtatsVehicules',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_modification']),              loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateEtatsVehicules);
router.post('/settingsMetiers/deleteEtatsVehicules',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['etats_suppression']),               loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteEtatsVehicules);
router.get('/settingsMetiers/getTypesDocuments',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                                             settingsMetiersCtrl.getTypesDocuments);
router.post('/settingsMetiers/addTypesDocuments',                           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addTypesDocuments);
router.post('/settingsMetiers/updateTypesDocuments',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateTypesDocuments);
router.post('/settingsMetiers/deleteTypesDocuments',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteTypesDocuments);
router.get('/settingsMetiers/getCatalogueMateriel',                         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['catalogue_lecture']),                                                      settingsMetiersCtrl.getCatalogueMateriel);
router.post('/settingsMetiers/addCatalogueMateriel',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['catalogue_ajout']),                 loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addCatalogueMateriel);
router.post('/settingsMetiers/updateCatalogueMateriel',                     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['catalogue_modification']),          loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateCatalogueMateriel);
router.post('/settingsMetiers/deleteCatalogueMateriel',                     loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['catalogue_suppression']),           loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteCatalogueMateriel);
router.get('/settingsMetiers/getCodesBarres',                               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['codeBarre_lecture']),                                                      settingsMetiersCtrl.getCodesBarres);
router.post('/settingsMetiers/addCodeBarres',                               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['codeBarre_ajout']),                 loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addCodeBarres);
router.post('/settingsMetiers/updateCodeBarres',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['codeBarre_modification']),          loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateCodeBarres);
router.post('/settingsMetiers/codesBarreDelete',                            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['codeBarre_suppression']),           loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.codesBarreDelete);
router.get('/settingsMetiers/getVHFTypesAccessoires',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                                             settingsMetiersCtrl.getVHFTypesAccessoires);
router.post('/settingsMetiers/addVHFTypesAccessoires',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addVHFTypesAccessoires);
router.post('/settingsMetiers/updateVHFTypesAccessoires',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateVHFTypesAccessoires);
router.post('/settingsMetiers/deleteVHFTypesAccessoires',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteVHFTypesAccessoires);
router.get('/settingsMetiers/getEtatsVHF',                                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                                             settingsMetiersCtrl.getEtatsVHF);
router.post('/settingsMetiers/addEtatsVHF',                                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addEtatsVHF);
router.post('/settingsMetiers/updateEtatsVHF',                              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateEtatsVHF);
router.post('/settingsMetiers/deleteEtatsVHF',                              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteEtatsVHF);
router.get('/settingsMetiers/getTechnologiesVHF',                           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                                             settingsMetiersCtrl.getTechnologiesVHF);
router.post('/settingsMetiers/addTechnologiesVHF',                          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addTechnologiesVHF);
router.post('/settingsMetiers/updateTechnologiesVHF',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateTechnologiesVHF);
router.post('/settingsMetiers/deleteTechnologiesVHF',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteTechnologiesVHF);
router.get('/settingsMetiers/getVHFTypesEquipements',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                                             settingsMetiersCtrl.getVHFTypesEquipements);
router.post('/settingsMetiers/addVHFTypesEquipements',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.addVHFTypesEquipements);
router.post('/settingsMetiers/updateVHFTypesEquipements',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.modificationLogger(), settingsMetiersCtrl.updateVHFTypesEquipements);
router.post('/settingsMetiers/deleteVHFTypesEquipements',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                      loggerMiddleware.suppressionLogger(),  settingsMetiersCtrl.deleteVHFTypesEquipements);

//settings techniques
router.get('/profils/getProfils',    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_lecture',]) ,                                             profilsCtrl.getProfils);
router.post('/profils/getOneProfil', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_lecture',]) ,                                             profilsCtrl.getOneProfil);
router.post('/profils/addProfil',    loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_ajout',]) ,        loggerMiddleware.modificationLogger(), profilsCtrl.addProfil);
router.post('/profils/updateProfil', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_modification',]) , loggerMiddleware.modificationLogger(), profilsCtrl.updateProfil);
router.post('/profils/deleteProfil', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_modification',]) , loggerMiddleware.suppressionLogger(),  profilsCtrl.deleteProfil);

//settings utilisateurs
router.get('/settingsUtilisateurs/getAllUsers',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_lecture',]) ,                                                                                settingsUtilisateursCtrl.getAllUsers);
router.post('/settingsUtilisateurs/getOneUser',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) , middlewaresFunctions.himselfRead(),                                         settingsUtilisateursCtrl.getOneUser);
router.post('/settingsUtilisateurs/getProfilsOneUser',               loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                                                                             settingsUtilisateursCtrl.getProfilsOneUser);
router.post('/settingsUtilisateurs/getMfaUrl',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                                                                             settingsUtilisateursCtrl.getMfaUrl);
router.post('/settingsUtilisateurs/enableMfa',                       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) , middlewaresFunctions.himselfWrite(), loggerMiddleware.modificationLogger(), settingsUtilisateursCtrl.enableMfa);
router.post('/settingsUtilisateurs/disableMfa',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) , middlewaresFunctions.himselfWrite(), loggerMiddleware.modificationLogger(), settingsUtilisateursCtrl.disableMfa);
router.post('/settingsUtilisateurs/updateMonCompte',                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) , middlewaresFunctions.himselfWrite(), loggerMiddleware.modificationLogger(), settingsUtilisateursCtrl.updateMonCompte);
router.post('/settingsUtilisateurs/addUser',                         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_ajout',]) ,                                           loggerMiddleware.modificationLogger(), settingsUtilisateursCtrl.addUser);
router.post('/settingsUtilisateurs/unlinkAD',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_modification',]) ,                                    loggerMiddleware.modificationLogger(), settingsUtilisateursCtrl.unlinkAD);
router.post('/settingsUtilisateurs/linkAD',                          loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_modification',]) ,                                    loggerMiddleware.modificationLogger(), settingsUtilisateursCtrl.linkAD);
router.post('/settingsUtilisateurs/updateProfils',                   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_modification',]) ,                                    loggerMiddleware.modificationLogger(), settingsUtilisateursCtrl.updateProfils);
router.post('/settingsUtilisateurs/pwdReinitRequest',                loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_mdp',]) ,                                             loggerMiddleware.modificationLogger(), connexionCtrl.pwdReinitRequest);
router.post('/settingsUtilisateurs/anonymiserUser',                  loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_suppression',]) ,                                     loggerMiddleware.suppressionLogger(),  settingsUtilisateursCtrl.anonymiserUser);
router.post('/settingsUtilisateurs/deleteUser',                      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_suppression',]) ,                                     loggerMiddleware.suppressionLogger(),  settingsUtilisateursCtrl.deleteUser);
router.post('/settingsUtilisateurs/delegate',                        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['delegation',]) ,                                                                                      connexionCtrl.delegate);

//settings techniques
router.get('/settingsTechniques/getConfigForAdmin',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) ,                                        settingsTechniquesCtrl.getConfigForAdmin);
router.post('/settingsTechniques/saveGlobalConfig',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , loggerMiddleware.modificationLogger(), settingsTechniquesCtrl.saveGlobalConfig);
router.post('/settingsTechniques/saveCnilConfig',              loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , loggerMiddleware.modificationLogger(), settingsTechniquesCtrl.saveCnilConfig);
router.post('/settingsTechniques/saveAlertesConfig',           loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , loggerMiddleware.modificationLogger(), settingsTechniquesCtrl.saveAlertesConfig);
router.post('/settingsTechniques/saveNotifsCommandesConfig',   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , loggerMiddleware.modificationLogger(), settingsTechniquesCtrl.saveNotifsCommandesConfig);
router.get('/settingsTechniques/getMailQueue',                 loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) ,                                        settingsTechniquesCtrl.getMailQueue);
router.post('/settingsTechniques/addOneCmdContrainte',         loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , loggerMiddleware.modificationLogger(), settingsTechniquesCtrl.addOneCmdContrainte);
router.post('/settingsTechniques/dropOneCmdContrainte',        loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) ,                                        settingsTechniquesCtrl.dropOneCmdContrainte);

//Actions massives
router.post('/actionsmassives/authenticateForAM',   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]),                                actionsMassivesCtrl.authenticateForAM);
router.post('/actionsmassives/getAvailableActions', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.getAvailableActions);
router.post('/actionsmassives/action11',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action11);
router.post('/actionsmassives/action12',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action12);
router.post('/actionsmassives/action13',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action13);
router.post('/actionsmassives/action21',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action21);
router.post('/actionsmassives/action22',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action22);
router.post('/actionsmassives/action31',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action31);
router.post('/actionsmassives/action32',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action32);
router.post('/actionsmassives/action33',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action33);
router.post('/actionsmassives/action41',            loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action41);


//get images and documents from secured backend
router.post('/getSecureFile/centresCouts',   loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_lecture',]),           serveDocumentsCtrl.centresCouts);
router.post('/getSecureFile/commandes',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_lecture',]),       serveDocumentsCtrl.commandes);
router.post('/getSecureFile/vehicules',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_lecture',]),      serveDocumentsCtrl.vehicules);
router.post('/getSecureFile/vhfCanaux',      loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_lecture',]),      serveDocumentsCtrl.vhfCanaux);
router.post('/getSecureFile/vhfEquipements', loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_lecture',]), serveDocumentsCtrl.vhfEquipements);
router.post('/getSecureFile/vhfPlans',       loggerMiddleware.httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture',]),       serveDocumentsCtrl.vhfPlans);


router.get('/', connexionCtrl.alive );

module.exports = router;