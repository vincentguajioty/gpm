const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMail = require('../helpers/fonctionsMail');

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

exports.messageMail = async (req, res)=>{
    try {
        let listePersonnes;
        if(req.body.data.idPersonne && req.body.data.idPersonne.length > 0)
        {
            listePersonnes = "SELECT DISTINCT idPersonne FROM PERSONNE_REFERENTE WHERE ";
            for(const personne of req.body.data.idPersonne)
            {
                listePersonnes += 'idPersonne=' + parseInt(personne.value) + ' OR ';
            }
            listePersonnes = listePersonnes.slice(0, -3);
        }else{
            listePersonnes = "SELECT idPersonne FROM PERSONNE_REFERENTE WHERE idPersonne IS NULL";
        }

        let listeProfils;
        if(req.body.data.idProfil && req.body.data.idProfil.length > 0)
        {
            listeProfils = "SELECT DISTINCT pe.idPersonne FROM PROFILS_PERSONNES po LEFT OUTER JOIN PERSONNE_REFERENTE pe ON po.idPersonne = pe.idPersonne WHERE ";
            for(const profil of req.body.data.idProfil)
            {
                listeProfils += 'po.idProfil=' + parseInt(profil.value) + ' OR ';
            }
            listeProfils = listeProfils.slice(0, -3);
        }else{
            listeProfils = "SELECT idPersonne FROM PERSONNE_REFERENTE WHERE idPersonne IS NULL";
        }

        let listeFinale = '('+listePersonnes+')UNION('+listeProfils+')';

        const finalListRecievers = await db.query(listeFinale);

        for(const mailToSend of finalListRecievers)
        {
            await fonctionsMail.registerToMailQueue({
                typeMail: 'mailDeGroupe',
                idPersonne: mailToSend.idPersonne,
                otherSubject: req.body.data.sujet,
                otherContent: req.body.data.message,
            })
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}