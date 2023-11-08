const express = require('express');
const db = require('./db');
const logger = require('./winstonLogger');
const jwtFunctions = require('./jwt');
const router = express.Router();
const fonctionsMetiers = require('./helpers/fonctionsMetiers');

const connexionCtrl = require('./controllers/connexion');
const settingsMetiersCtrl = require('./controllers/settingsMetiers');
const settingsUtilisateursCtrl = require('./controllers/settingsUtilisateurs');
const settingsTechniquesCtrl = require('./controllers/settingsTechniques');
const profilsCtrl = require('./controllers/profils');

const materielsCtrl = require('./controllers/materiels');

const commandesCtrl = require('./controllers/commandes');
const fournisseursCtrl = require('./controllers/fournisseurs');
const fournisseursAesCtrl = require('./controllers/fournisseursAes');

const tenuesCtrl = require('./controllers/tenues');
const vhfCtrl = require('./controllers/vhf');

const referentielsCtrl = require('./controllers/referentiels');
const messagesGenerauxCtrl = require('./controllers/messagesGeneraux');
const toDoListCtrl = require('./controllers/toDoList');

const actionsMassivesCtrl = require('./controllers/actionsMassives');

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

const himselfRead = () => {
    return function(req, res, next) {
        if(req.verifyJWTandProfile.idPersonne != req.body.idPersonne && !req.verifyJWTandProfile.annuaire_lecture)
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée', {idPersonne: 'SYSTEM'});
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        else
        {
            next();
        }
        
    }
}
const himselfWrite = () => {
    return function(req, res, next) {
        if(req.verifyJWTandProfile.idPersonne != req.body.idPersonne && !req.verifyJWTandProfile.annuaire_modification)
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée', {idPersonne: 'SYSTEM'});
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        else
        {
            next();
        }
        
    }
}

const hisOwnTdlArray = () => {
    return async function(req, res, next) {
        if(req.body.idPersonne == req.verifyJWTandProfile.idPersonne || req.verifyJWTandProfile.todolist_lecture)
        {
            next();
        }
        else
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée', {idPersonne: 'SYSTEM'});
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        
    }
}

const hisOwnTdl = () => {
    return async function(req, res, next) {
        let result = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                TODOLIST_PERSONNES
            WHERE
                idExecutant = :idPersonne
                AND
                idTache = :idTache
        `,{
            idPersonne: req.verifyJWTandProfile.idPersonne,
            idTache: req.body.idTache,
        });

        if(result[0].nb > 0 || req.verifyJWTandProfile.todolist_lecture)
        {
            next();
        }
        else
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée', {idPersonne: 'SYSTEM'});
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        
    }
}

const editHisOwnTdl = () => {
    return async function(req, res, next) {
        let result = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                TODOLIST_PERSONNES
            WHERE
                idExecutant = :idPersonne
                AND
                idTache = :idTache
        `,{
            idPersonne: req.verifyJWTandProfile.idPersonne,
            idTache: req.body.idTache,
        });

        if((result[0].nb > 0 && req.verifyJWTandProfile.todolist_perso) || req.verifyJWTandProfile.todolist_modification)
        {
            next();
        }
        else
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée', {idPersonne: 'SYSTEM'});
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        
    }
}

const addToHimself = () => {
    return function(req, res, next) {
        if((req.verifyJWTandProfile.idPersonne == req.body.idExecutant && req.verifyJWTandProfile.todolist_perso) || req.verifyJWTandProfile.todolist_modification)
        {
            next();
        }
        else
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée', {idPersonne: 'SYSTEM'});
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        
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

//OPERATIONNEL - Matériels
router.get('/materiels/getMateriels',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['materiel_lecture']),  materielsCtrl.getMateriels);

//Fournisseurs - Informations de base
router.get('/fournisseurs/getFournisseurs',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_lecture']),      jwtFunctions.decryptAesToken(),  fournisseursCtrl.getFournisseurs);
router.post('/fournisseurs/getOneFournisseur',                httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_lecture']),      jwtFunctions.decryptAesToken(),  fournisseursCtrl.getOneFournisseur);
router.post('/fournisseurs/addFournisseur',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_ajout']),        modificationLogger(),            fournisseursCtrl.addFournisseur);
router.post('/fournisseurs/updateFournisseur',                httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_modification']), modificationLogger(),            fournisseursCtrl.updateFournisseur);
router.post('/fournisseurs/deleteFournisseur',                httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_suppression']),  suppressionLogger(),             fournisseursCtrl.deleteFournisseur);
//Fournisseurs - Gestion des informations chiffrées
router.post('/fournisseurs/authenticateForAES',       httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_lecture']),                                                            fournisseursAesCtrl.authenticateForAES);
router.post('/fournisseurs/initKey',                  httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                                                modificationLogger(), fournisseursAesCtrl.initKey, fournisseursAesCtrl.authenticateForAES);
router.post('/fournisseurs/changeKey',                httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                jwtFunctions.decryptAesToken(), modificationLogger(), fournisseursAesCtrl.updateAesKey, fournisseursAesCtrl.authenticateForAES);
router.post('/fournisseurs/disableAesAndDelete',      httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf']),                jwtFunctions.decryptAesToken(), suppressionLogger(),  fournisseursAesCtrl.disableAesAndDelete);
router.post('/fournisseurs/updateFournisseurAesData', httpLogger(), jwtFunctions.verifyJWTandProfile(['fournisseurs_modification']), jwtFunctions.decryptAesToken(), modificationLogger(), fournisseursAesCtrl.updateFournisseurAesData);

//Tenues - Catalogue
router.get('/tenues/getPersonnesSuggested',     httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_lecture']),                            tenuesCtrl.getPersonnesSuggested);
router.get('/tenues/getCatalogue',              httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_lecture']),                            tenuesCtrl.getCatalogue);
router.post('/tenues/addCatalogue',             httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_ajout']),        modificationLogger(), tenuesCtrl.addCatalogue);
router.post('/tenues/updateCatalogue',          httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_modification']), modificationLogger(), tenuesCtrl.updateCatalogue);
router.post('/tenues/deleteCatalogue',          httpLogger(), jwtFunctions.verifyJWTandProfile(['tenuesCatalogue_suppression']),  suppressionLogger(),  tenuesCtrl.deleteCatalogue);
//Tenues - Affectations
router.get('/tenues/getAffectations',     httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_lecture']),                            tenuesCtrl.getAffectations);
router.get('/tenues/getAffectationsRow',  httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_lecture']),                            tenuesCtrl.getAffectationsRow);
router.post('/tenues/addAffectations',    httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_ajout']),        modificationLogger(), tenuesCtrl.addAffectations);
router.post('/tenues/updateAffectations', httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_modification']), modificationLogger(), tenuesCtrl.updateAffectations);
router.post('/tenues/deleteAffectations', httpLogger(), jwtFunctions.verifyJWTandProfile(['tenues_suppression']),  suppressionLogger(),  tenuesCtrl.deleteAffectations);
//Tenues - Cautions
router.get('/tenues/getCautions',     httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_lecture']),                            tenuesCtrl.getCautions);
router.get('/tenues/getCautionsRow',  httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_lecture']),                            tenuesCtrl.getCautionsRow);
router.post('/tenues/addCautions',    httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_ajout']),        modificationLogger(), tenuesCtrl.addCautions);
router.post('/tenues/updateCautions', httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_modification']), modificationLogger(), tenuesCtrl.updateCautions);
router.post('/tenues/deleteCautions', httpLogger(), jwtFunctions.verifyJWTandProfile(['cautions_suppression']),  suppressionLogger(),  tenuesCtrl.deleteCautions);

//VHF Canaux
router.get('/vhf/getFrequences',     httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_lecture']),                            vhfCtrl.getFrequences);
router.post('/vhf/addCanal',         httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_ajout']),        modificationLogger(), vhfCtrl.addCanal);
router.post('/vhf/updateCanal',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_modification']), modificationLogger(), vhfCtrl.updateCanal);
router.post('/vhf/deleteCanal',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_suppression']),  suppressionLogger(),  vhfCtrl.deleteCanal);
//VHF Canaux Attachements
router.post('/vhf/getAllDocumentsOneCanal', httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_lecture',]),                            vhfCtrl.getAllDocumentsOneCanal);
router.post('/vhf/uploadCanalAttached',     httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_modification',]), modificationLogger(), vhfCtrl.uploadCanalAttachedMulter, vhfCtrl.uploadCanalAttached);
router.post('/vhf/updateMetaDataCanal',     httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_modification',]), modificationLogger(), vhfCtrl.updateMetaDataCanal);
router.post('/vhf/dropCanalDocument',       httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_suppression',]) , suppressionLogger(),  vhfCtrl.dropCanalDocument);
//VHF Plans
router.get('/vhf/getPlans',         httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture']),                            vhfCtrl.getPlans);
router.post('/vhf/addPlan',         httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_ajout']),        modificationLogger(), vhfCtrl.addPlan);
router.post('/vhf/updatePlan',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_modification']), modificationLogger(), vhfCtrl.updatePlan);
router.post('/vhf/deletePlan',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_suppression']),  suppressionLogger(),  vhfCtrl.deletePlan);
//VHF Plans Attachements
router.post('/vhf/getAllDocumentsOnePlan', httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture',]),                             vhfCtrl.getAllDocumentsOnePlan);
router.post('/vhf/uploadPlanAttached',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_modification',]), modificationLogger(), vhfCtrl.uploadPlanAttachedMulter, vhfCtrl.uploadPlanAttached);
router.post('/vhf/updateMetaDataPlan',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_modification',]), modificationLogger(), vhfCtrl.updateMetaDataPlan);
router.post('/vhf/dropPlanDocument',        httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_suppression',]) , suppressionLogger(),  vhfCtrl.dropPlanDocument);
//VHF Plans canaux affecation
router.post('/vhf/getCanauxOnePlan',         httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture']),                            vhfCtrl.getCanauxOnePlan);
router.post('/vhf/updateCanauxOnePlan',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_modification']), modificationLogger(), vhfCtrl.updateCanauxOnePlan);


//referentiels
router.get('/referentiels/getReferentiels',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_lecture']),                                                 referentielsCtrl.getReferentiels);
router.post('/referentiels/getOneReferentiel',                httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_lecture']),                                                 referentielsCtrl.getOneReferentiel);
router.post('/referentiels/addReferentiel',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_ajout']),                             modificationLogger(), referentielsCtrl.addReferentiel);
router.post('/referentiels/getCatalogueForReferentielForm',   httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_modification', 'catalogue_lecture']),                       referentielsCtrl.getCatalogueForReferentielForm);
router.post('/referentiels/updateReferentiel',                httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_modification']),                      modificationLogger(), referentielsCtrl.updateReferentiel);
router.post('/referentiels/deleteReferentiel',                httpLogger(), jwtFunctions.verifyJWTandProfile(['typesLots_suppression']),                       suppressionLogger(),  referentielsCtrl.deleteReferentiel);

//Messages généraux
router.get('/messagesGeneraux/getMessagesPublics',            httpLogger(),                                                                                    messagesGenerauxCtrl.getMessagesPublics);
router.get('/messagesGeneraux/getAllMessages',                httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),                         messagesGenerauxCtrl.getAllMessages);
router.get('/messagesGeneraux/getMessagesTypes',              httpLogger(),                                                                                    messagesGenerauxCtrl.getMessagesTypes);
router.post('/messagesGeneraux/addMessage',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['messages_ajout']),       modificationLogger(),  messagesGenerauxCtrl.addMessage);
router.post('/messagesGeneraux/updateMessage',                httpLogger(), jwtFunctions.verifyJWTandProfile(['messages_ajout']),       modificationLogger(),  messagesGenerauxCtrl.updateMessage);
router.post('/messagesGeneraux/deleteMessage',                httpLogger(), jwtFunctions.verifyJWTandProfile(['messages_suppression']), suppressionLogger(),   messagesGenerauxCtrl.deleteMessage);

//ToDoList
router.get('/todolist/getPersonsForTDL',      httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),                                                    toDoListCtrl.getPersonsForTDL);
router.get('/todolist/getPioritesForTDL',     httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),                                                 toDoListCtrl.getPioritesForTDL);
router.get('/todolist/getAllTDL',             httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),                                                    toDoListCtrl.getAllTDL);
router.post('/todolist/getOneTDL',            httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   hisOwnTdl(),                                  toDoListCtrl.getOneTDL);
router.get('/todolist/getUnaffectedTDL',      httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),                                                    toDoListCtrl.getUnaffectedTDL);
router.get('/todolist/getClosedTDL',          httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_lecture']),                                                    toDoListCtrl.getClosedTDL);
router.post('/todolist/getTDLonePerson',      httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   hisOwnTdlArray(),                             toDoListCtrl.getTDLonePerson);
router.post('/todolist/addTDL',               httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   addToHimself(),        modificationLogger(),  toDoListCtrl.addTDL);
router.post('/todolist/updateTDLAffectation', httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_modification']),                        modificationLogger(),  toDoListCtrl.updateTDLAffectation);
router.post('/todolist/updateTDL',            httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   editHisOwnTdl(),       modificationLogger(),  toDoListCtrl.updateTDL);
router.post('/todolist/completedTDL',         httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   editHisOwnTdl(),       modificationLogger(),  toDoListCtrl.completedTDL);
router.post('/todolist/unCompletedTDL',       httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion']),   editHisOwnTdl(),       modificationLogger(),  toDoListCtrl.unCompletedTDL);
router.post('/todolist/duplicateTDL',         httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_modification']),                        modificationLogger(),  toDoListCtrl.duplicateTDL);
router.post('/todolist/deleteTDL',            httpLogger(), jwtFunctions.verifyJWTandProfile(['todolist_modification']),                        suppressionLogger(),   toDoListCtrl.deleteTDL);

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

//settings techniques
router.get('/profils/getProfils',    httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_lecture',]) ,                            profilsCtrl.getProfils);
router.post('/profils/getOneProfil', httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_lecture',]) ,                            profilsCtrl.getOneProfil);
router.post('/profils/addProfil',    httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_ajout',]) ,        modificationLogger(), profilsCtrl.addProfil);
router.post('/profils/updateProfil', httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_modification',]) , modificationLogger(), profilsCtrl.updateProfil);
router.post('/profils/deleteProfil', httpLogger(), jwtFunctions.verifyJWTandProfile(['profils_modification',]) , suppressionLogger(),  profilsCtrl.deleteProfil);

//settings utilisateurs
router.get('/settingsUtilisateurs/getAllUsers',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_lecture',]) ,                                              settingsUtilisateursCtrl.getAllUsers);
router.post('/settingsUtilisateurs/getOneUser',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) , himselfRead(),                            settingsUtilisateursCtrl.getOneUser);
router.post('/settingsUtilisateurs/getProfilsOneUser',               httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                                           settingsUtilisateursCtrl.getProfilsOneUser);
router.post('/settingsUtilisateurs/getMfaUrl',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) ,                                           settingsUtilisateursCtrl.getMfaUrl);
router.post('/settingsUtilisateurs/enableMfa',                       httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) , himselfWrite(), modificationLogger(),     settingsUtilisateursCtrl.enableMfa);
router.post('/settingsUtilisateurs/disableMfa',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) , himselfWrite(), modificationLogger(),     settingsUtilisateursCtrl.disableMfa);
router.post('/settingsUtilisateurs/updateMonCompte',                 httpLogger(), jwtFunctions.verifyJWTandProfile(['connexion_connexion',]) , himselfWrite(), modificationLogger(),     settingsUtilisateursCtrl.updateMonCompte);
router.post('/settingsUtilisateurs/addUser',                         httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_ajout',]) ,                      modificationLogger(),     settingsUtilisateursCtrl.addUser);
router.post('/settingsUtilisateurs/unlinkAD',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_modification',]) ,               modificationLogger(),     settingsUtilisateursCtrl.unlinkAD);
router.post('/settingsUtilisateurs/linkAD',                          httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_modification',]) ,               modificationLogger(),     settingsUtilisateursCtrl.linkAD);
router.post('/settingsUtilisateurs/updateProfils',                   httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_modification',]) ,               modificationLogger(),     settingsUtilisateursCtrl.updateProfils);
router.post('/settingsUtilisateurs/pwdReinitRequest',                httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_mdp',]) ,                        modificationLogger(),     connexionCtrl.pwdReinitRequest);
router.post('/settingsUtilisateurs/anonymiserUser',                  httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_suppression',]) ,                suppressionLogger(),      settingsUtilisateursCtrl.anonymiserUser);
router.post('/settingsUtilisateurs/deleteUser',                      httpLogger(), jwtFunctions.verifyJWTandProfile(['annuaire_suppression',]) ,                suppressionLogger(),      settingsUtilisateursCtrl.deleteUser);
router.post('/settingsUtilisateurs/delegate',                        httpLogger(), jwtFunctions.verifyJWTandProfile(['delegation',]) ,                          suppressionLogger(),      connexionCtrl.delegate);



//settings techniques
router.get('/settingsTechniques/getConfigForAdmin',            httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) ,                       settingsTechniquesCtrl.getConfigForAdmin);
router.post('/settingsTechniques/saveGlobalConfig',            httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , modificationLogger(), settingsTechniquesCtrl.saveGlobalConfig);
router.post('/settingsTechniques/saveCnilConfig',              httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , modificationLogger(), settingsTechniquesCtrl.saveCnilConfig);
router.post('/settingsTechniques/saveAlertesConfig',           httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , modificationLogger(), settingsTechniquesCtrl.saveAlertesConfig);
router.post('/settingsTechniques/saveNotifsCommandesConfig',   httpLogger(), jwtFunctions.verifyJWTandProfile(['appli_conf',]) , modificationLogger(), settingsTechniquesCtrl.saveNotifsCommandesConfig);

//Actions massives
router.post('/actionsmassives/authenticateForAM',   httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]),                                actionsMassivesCtrl.authenticateForAM);
router.post('/actionsmassives/getAvailableActions', httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.getAvailableActions);
router.post('/actionsmassives/action11',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action11);
router.post('/actionsmassives/action12',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action12);
router.post('/actionsmassives/action13',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action13);
router.post('/actionsmassives/action21',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action21);
router.post('/actionsmassives/action22',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action22);
router.post('/actionsmassives/action31',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action31);
router.post('/actionsmassives/action32',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action32);
router.post('/actionsmassives/action33',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action33);
router.post('/actionsmassives/action41',            httpLogger(), jwtFunctions.verifyJWTandProfile(['actionsMassives',]), jwtFunctions.decryptAMToken(), actionsMassivesCtrl.action41);


//get images and documents from secured backend
router.post('/getSecureFile/centresCouts',   httpLogger(), jwtFunctions.verifyJWTandProfile(['cout_lecture',]),           serveDocumentsCtrl.centresCouts);
router.post('/getSecureFile/commandes',      httpLogger(), jwtFunctions.verifyJWTandProfile(['commande_lecture',]),       serveDocumentsCtrl.commandes);
router.post('/getSecureFile/vehicules',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vehicules_lecture',]),      serveDocumentsCtrl.vehicules);
router.post('/getSecureFile/vhfCanaux',      httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_canal_lecture',]),      serveDocumentsCtrl.vhfCanaux);
router.post('/getSecureFile/vhfEquipements', httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_equipement_lecture',]), serveDocumentsCtrl.vhfEquipements);
router.post('/getSecureFile/vhfPlans',       httpLogger(), jwtFunctions.verifyJWTandProfile(['vhf_plan_lecture',]),       serveDocumentsCtrl.vhfPlans);


router.get('/', connexionCtrl.alive );

module.exports = router;