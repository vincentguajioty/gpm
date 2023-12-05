const db = require('../db');
const logger = require('../winstonLogger');

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
            logger.info('Accès refusé par ACL et référence idPersonne croisée', {idPersonne: 'SYSTEM'});
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
            logger.info('Accès refusé par ACL et référence idPersonne croisée', {idPersonne: 'SYSTEM'});
            res.status(403);
            res.send('Accès refusé par le contrôle de profile');
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
};