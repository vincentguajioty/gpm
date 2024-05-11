const db = require('../db');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const logger = require('../winstonLogger');

exports.getPersonsForTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                idPersonne,
                identifiant,
                nomPersonne,
                prenomPersonne,
                idPersonne as value,
                identifiant as label
            FROM
                VIEW_HABILITATIONS
            WHERE
                todolist_perso = true
                OR
                todolist_lecture = true
            ORDER BY
                identifiant
        `);

        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getPioritesForTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                *,
                idTDLpriorite as value,
                libellePriorite as label
            FROM
                TODOLIST_PRIORITES
        `);
        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getAllTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                tdl.*,
                prio.libellePriorite,
                prio.couleurPriorite
            FROM
                TODOLIST tdl
                LEFT OUTER JOIN TODOLIST_PRIORITES prio ON tdl.idTDLpriorite = prio.idTDLpriorite
            ORDER BY
                dateCreation
        `);
        for(const tache of result)
        {
            let idExecutant = await db.query(`
                SELECT
                    p.idPersonne,    
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne
                FROM
                    TODOLIST_PERSONNES tp
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON tp.idExecutant = p.idPersonne
                WHERE
                    tp.idTache = :idTache
                ORDER BY
                    p.identifiant
            `,{
                idTache: tache.idTache
            });
            tache.idExecutant = idExecutant;
        }

        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                tdl.*,
                prio.libellePriorite,
                prio.couleurPriorite
            FROM
                TODOLIST tdl
                LEFT OUTER JOIN TODOLIST_PRIORITES prio ON tdl.idTDLpriorite = prio.idTDLpriorite
            WHERE
                idTache = :idTache
        `,{
            idTache: req.body.idTache,
        });
        for(const tache of result)
        {
            let idExecutant = await db.query(`
                SELECT
                    p.idPersonne,    
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne,
                    p.idPersonne as value,
                    p.identifiant as label
                FROM
                    TODOLIST_PERSONNES tp
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON tp.idExecutant = p.idPersonne
                WHERE
                    tp.idTache = :idTache
                ORDER BY
                    p.identifiant
            `,{
                idTache: tache.idTache
            });
            tache.idExecutant = idExecutant;
        }

        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getUnaffectedTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                tdl.*,
                tp.idExecutant,
                pr.identifiant,
                prio.libellePriorite,
                prio.couleurPriorite
            FROM
                TODOLIST tdl
                LEFT OUTER JOIN TODOLIST_PERSONNES tp ON tdl.idTache = tp.idTache
                LEFT OUTER JOIN PERSONNE_REFERENTE pr ON tp.idExecutant = pr.idPersonne
                LEFT OUTER JOIN TODOLIST_PRIORITES prio ON tdl.idTDLpriorite = prio.idTDLpriorite
            WHERE
                tp.idExecutant IS NULL
            ORDER BY
                tdl.dateCreation
        `);

        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getClosedTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                tdl.*,
                prio.libellePriorite,
                prio.couleurPriorite
            FROM
                TODOLIST tdl
                LEFT OUTER JOIN TODOLIST_PRIORITES prio ON tdl.idTDLpriorite = prio.idTDLpriorite
            WHERE
                tdl.dateCloture IS NOT NULL
            ORDER BY
                tdl.dateCloture DESC
        `);
        for(const tache of result)
        {
            let idExecutant = await db.query(`
                SELECT
                    p.idPersonne,    
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne
                FROM
                    TODOLIST_PERSONNES tp
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON tp.idExecutant = p.idPersonne
                WHERE
                    tp.idTache = :idTache
            `,{
                idTache: tache.idTache
            });
            tache.idExecutant = idExecutant;
        }

        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getTDLonePerson = async (req, res, next)=>{
    try {
        let result = await db.query(`
            SELECT
                tdl.*,
                tp.idExecutant,
                pr.identifiant,
                prio.libellePriorite,
                prio.couleurPriorite
            FROM
                TODOLIST tdl
                LEFT OUTER JOIN TODOLIST_PERSONNES tp ON tdl.idTache = tp.idTache
                LEFT OUTER JOIN PERSONNE_REFERENTE pr ON tp.idExecutant = pr.idPersonne
                LEFT OUTER JOIN TODOLIST_PRIORITES prio ON tdl.idTDLpriorite = prio.idTDLpriorite
            WHERE
                tp.idExecutant = :idPersonne
                AND
                dateCloture IS NULL
        `,{
            idPersonne: req.body.idPersonne,
        });
        for(const tache of result)
        {
            let idExecutant = await db.query(`
                SELECT
                    p.idPersonne,    
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne
                FROM
                    TODOLIST_PERSONNES tp
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON tp.idExecutant = p.idPersonne
                WHERE
                    tp.idTache = :idTache
            `,{
                idTache: tache.idTache
            });
            tache.idExecutant = idExecutant;
        }

        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            INSERT INTO
                TODOLIST
            SET
                idCreateur    = :idCreateur,
                dateCreation  = CURRENT_TIMESTAMP,
                dateExecution = :dateExecution,
                dateCloture	  = null,
                titre         = :titre,
                details       = :details,
                idTDLpriorite = :idTDLpriorite
        `,{
            idCreateur: req.verifyJWTandProfile.idPersonne || null ,
            dateExecution: req.body.dateExecution || null ,
            titre: req.body.titre || null ,
            details: req.body.details || null ,
            idTDLpriorite: req.body.idTDLpriorite || null ,
        });

        if(req.body.idExecutant != null && req.body.idExecutant > 0)
        {
            let selectLast = await db.query(
                'SELECT MAX(idTache) as idTache FROM TODOLIST;'
            );

            const insertQuery = await db.query(`
                INSERT INTO
                    TODOLIST_PERSONNES
                SET
                    idTache     = :idTache,
                    idExecutant = :idExecutant
            `,{
                idTache     : selectLast[0].idTache || null,
                idExecutant : req.body.idExecutant || null,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateTDLAffectation = async (req, res, next)=>{
    try {
        const cleanQuery = await db.query(`
            DELETE FROM
                TODOLIST_PERSONNES
            WHERE
                idTache     = :idTache
        `,{
            idTache     : req.body.idTache || null,
        });

        for(const entry of req.body.idExecutant)
        {
            const insertQuery = await db.query(`
                INSERT INTO
                    TODOLIST_PERSONNES
                SET
                    idTache     = :idTache,
                    idExecutant = :idExecutant
            `,{
                idTache     : req.body.idTache || null,
                idExecutant : entry.value || null,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            UPDATE
                TODOLIST
            SET
                dateExecution = :dateExecution,
                dateCloture	  = :dateCloture,
                titre         = :titre,
                details       = :details,
                idTDLpriorite = :idTDLpriorite
            WHERE
                idTache       = :idTache
        `,{
            dateExecution: req.body.dateExecution || null ,
            dateCloture: req.body.dateCloture || null ,
            titre: req.body.titre || null ,
            details: req.body.details || null ,
            idTDLpriorite: req.body.idTDLpriorite || null ,
            idTache: req.body.idTache || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.completedTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            UPDATE
                TODOLIST
            SET
                dateCloture	  = CURRENT_TIMESTAMP
            WHERE
                idTache       = :idTache
        `,{
            idTache: req.body.idTache || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.unCompletedTDL = async (req, res, next)=>{
    try {
        let result = await db.query(`
            UPDATE
                TODOLIST
            SET
                dateCloture	  = null
            WHERE
                idTache       = :idTache
        `,{
            idTache: req.body.idTache || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.duplicateTDL = async (req, res, next)=>{
    try {
        let initialTDL = await db.query(`
            SELECT
                *
            FROM
                TODOLIST
            WHERE
                idTache = :idTache
        `,{
            idTache: req.body.idTache || null ,
        });
        let initialTDLAffectations = await db.query(`
            SELECT
                *
            FROM
                TODOLIST_PERSONNES
            WHERE
                idTache = :idTache
        `,{
            idTache: req.body.idTache || null ,
        });

        let result = await db.query(`
            INSERT INTO
                TODOLIST
            SET
                idCreateur    = :idCreateur,
                dateCreation  = CURRENT_TIMESTAMP,
                dateExecution = :dateExecution,
                dateCloture	  = null,
                titre         = :titre,
                details       = :details,
                idTDLpriorite = :idTDLpriorite
        `,{
            idCreateur: req.verifyJWTandProfile.idPersonne || null ,
            dateExecution: initialTDL[0].dateExecution || null ,
            titre: initialTDL[0].titre || null ,
            details: initialTDL[0].details || null ,
            idTDLpriorite: initialTDL[0].idTDLpriorite || null ,
        });
        let newTDL = await db.query(
            'SELECT MAX(idTache) as idTache FROM TODOLIST;'
        );
        for(const entry of initialTDLAffectations)
        {
            const insertQuery = await db.query(`
                INSERT INTO
                    TODOLIST_PERSONNES
                SET
                    idTache     = :idTache,
                    idExecutant = :idExecutant
            `,{
                idTache     : newTDL[0].idTache || null,
                idExecutant : entry.idExecutant || null,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteTDL = async (req, res, next)=>{
    try {
        const deleteResult = await fonctionsDelete.todolistDelete(req.verifyJWTandProfile.idPersonne , req.body.idTache);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}