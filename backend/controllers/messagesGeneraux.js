const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');

exports.getMessagesPublics = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                m.*,
                mt.libelleMessageType,
                mt.couleurMessageType,
                p.identifiant,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                MESSAGES m
                LEFT OUTER JOIN MESSAGES_TYPES mt ON m.idMessageType = mt.idMessageType
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idPersonne = p.idPersonne
            WHERE
                isPublic = true
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getAllMessages = async (req, res)=>{
    try {
        let results = await db.query(`
        SELECT
            m.*,
            mt.libelleMessageType,
            mt.couleurMessageType,
            p.identifiant,
            p.nomPersonne,
            p.prenomPersonne
        FROM
            MESSAGES m
            LEFT OUTER JOIN MESSAGES_TYPES mt ON m.idMessageType = mt.idMessageType
            LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idPersonne = p.idPersonne
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getMessagesTypes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                *
            FROM
                MESSAGES_TYPES
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addMessage = async (req, res)=>{
    try {
        let results = await db.query(
            `INSERT INTO
                MESSAGES
            SET
                idPersonne = :idPersonne,
                corpsMessage = :corpsMessage,
                idMessageType = :idMessageType,
                isPublic = :isPublic
            ;`,
        {
            idPersonne : req.verifyJWTandProfile.idPersonne,
            corpsMessage : req.body.corpsMessage || null,
            idMessageType : req.body.idMessageType || null,
            isPublic : req.body.isPublic || false,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMessage = async (req, res)=>{
    try {
        let results = await db.query(
            `UPDATE
                MESSAGES
            SET
                idPersonne = :idPersonne,
                corpsMessage = :corpsMessage,
                idMessageType = :idMessageType,
                isPublic = :isPublic
            WHERE
                idMessage = :idMessage
            ;`,
        {
            idPersonne : req.verifyJWTandProfile.idPersonne,
            corpsMessage : req.body.corpsMessage || null,
            idMessageType : req.body.idMessageType || null,
            isPublic : req.body.isPublic || false,
            idMessage      : req.body.idMessage || null
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteMessage = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.messagesDelete(req.verifyJWTandProfile.idPersonne , req.body.idMessage);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}