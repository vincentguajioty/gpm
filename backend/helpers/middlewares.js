const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsMetiers = require('./fonctionsMetiers');

const himselfRead = () => {
    return function(req, res, next) {
        if(req.verifyJWTandProfile.idPersonne != req.body.idPersonne && !req.verifyJWTandProfile.annuaire_lecture)
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée');
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
            logger.info('Accès refusé par ACL et référence idPersonne croisée');
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
            logger.info('Accès refusé par ACL et référence idPersonne croisée');
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
            logger.info('Accès refusé par ACL et référence idPersonne croisée');
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
            logger.info('Accès refusé par ACL et référence idPersonne croisée');
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
            logger.info('Accès refusé par ACL et référence idPersonne croisée');
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        
    }
}

const alerteVehiculeOwned = () => {
    return async function(req, res, next) {
        const alerte = await db.query(`
            SELECT
                idTraitant
            FROM
                VEHICULES_ALERTES
            WHERE
                idAlerte = :idAlerte
        `,{
            idAlerte : req.body.idAlerte,
        });
        if(alerte.length == 1 && (alerte[0].idTraitant == null || alerte[0].idTraitant == req.verifyJWTandProfile.idPersonne))
        {
            next();
        }
        else
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée');
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        
    }
}

const alerteLotOwned = () => {
    return async function(req, res, next) {
        const alerte = await db.query(`
            SELECT
                idTraitant
            FROM
                LOTS_ALERTES
            WHERE
                idAlerte = :idAlerte
        `,{
            idAlerte : req.body.idAlerte,
        });
        if(alerte.length == 1 && (alerte[0].idTraitant == null || alerte[0].idTraitant == req.verifyJWTandProfile.idPersonne))
        {
            next();
        }
        else
        {
            logger.info('Accès refusé par ACL et référence idPersonne croisée');
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
        }
        
    }
}

const checkFunctionnalityBenevolesEnabled = () => {
    return async function(req, res, next) {
        let verifFonctionnalite = await fonctionsMetiers.checkFunctionnalityBenevolesEnabled();
        if(verifFonctionnalite == true)
        {
            next();
        }
        else
        {
            logger.info('Accès refusé par checkFunctionnalityBenevolesEnabled car fonctionnalité désactivée');
            res.status(403);
            res.send('Fonctionnalité désactivée');
        }
        
    }
}

const checkCmdStage = (acceptedStages) => {
    return async function(req, res, next) {
        const cmd = await db.query(`
            SELECT
                idEtat
            FROM
                COMMANDES
            WHERE
                idCommande = :idCommande
        `,{
            idCommande : req.body.idCommande || null,
        });

        if(cmd.length != 1 || !acceptedStages.includes(cmd[0].idEtat))
        {
            logger.info('Action non-authorisée pour ce statut de commande');
            res.status(403);
            res.send('Action non-authorisée pour ce statut de commande');
        }else{
            next();
        }
    }
}

const checkCmdRole = (acceptedRoles) => {
    return async function(req, res, next) {
        let okToPass = false;

        if(!req.body.idCommande || req.body.idCommande == null)
        {
            logger.info('idCommande nécessaire au controle de rôle sur ladite commande');
            res.status(403);
            res.send('idCommande nécessaire au controle de rôle sur ladite commande');
        }else{
            if(acceptedRoles.includes('noRoleNeeded'))
            {
                okToPass = true;
            }

            if(acceptedRoles.includes('valideur'))
            {
                let personnes = await fonctionsMetiers.getValideurs(req.body.idCommande);
                for(const personne of personnes)
                {
                    if(personne.value == req.verifyJWTandProfile.idPersonne){okToPass = true}
                }
            }

            if(acceptedRoles.includes('affectee'))
            {
                let personnes = await db.query(`
                    SELECT
                        p.idPersonne as value,
                        p.identifiant as label
                    FROM
                        COMMANDES_AFFECTEES c
                        LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idAffectee
                    WHERE
                        c.idCommande = :idCommande
                ;`,{
                    idCommande: req.body.idCommande,
                });
                for(const personne of personnes)
                {
                    if(personne.value == req.verifyJWTandProfile.idPersonne){okToPass = true}
                }
            }

            if(acceptedRoles.includes('demandeur'))
            {
                let personnes = await db.query(`
                    SELECT
                        p.idPersonne as value,
                        p.identifiant as label
                    FROM
                        COMMANDES_DEMANDEURS c
                        LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idDemandeur
                    WHERE
                        c.idCommande = :idCommande
                ;`,{
                    idCommande: req.body.idCommande,
                });
                for(const personne of personnes)
                {
                    if(personne.value == req.verifyJWTandProfile.idPersonne){okToPass = true}
                }
            }

            if(acceptedRoles.includes('observateur'))
            {
                let personnes = await db.query(`
                    SELECT
                        p.idPersonne as value,
                        p.identifiant as label
                    FROM
                        COMMANDES_OBSERVATEURS c
                        LEFT OUTER JOIN PERSONNE_REFERENTE p ON p.idPersonne = c.idObservateur
                    WHERE
                        c.idCommande = :idCommande
                ;`,{
                    idCommande: req.body.idCommande,
                });
                for(const personne of personnes)
                {
                    if(personne.value == req.verifyJWTandProfile.idPersonne){okToPass = true}
                }
            }
        }

        if(okToPass == true)
        {
            next();
        }else{
            logger.info('L\'utilisateur n\'a pas le rôle adéquat pour cette action pour ladite commande');
            res.status(403);
            res.send('L\'utilisateur n\'a pas le rôle adéquat pour cette action pour ladite commande');
        }
    }
}

module.exports = {
    himselfRead,
    himselfWrite,
    hisOwnTdlArray,
    hisOwnTdl,
    editHisOwnTdl,
    addToHimself,
    alerteVehiculeOwned,
    alerteLotOwned,
    checkFunctionnalityBenevolesEnabled,
    checkCmdStage,
    checkCmdRole,
};