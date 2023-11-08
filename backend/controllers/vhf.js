const db = require('../db');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsLDAP = require('../helpers/fonctionsLDAP');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const dotenv = require('dotenv').config();
const authenticator = require('authenticator');
const logger = require('../winstonLogger');
const brcypt = require('bcryptjs');
const multer = require('multer');

//Canaux
exports.getFrequences = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                c.*,
                t.libelleTechno,
                COUNT(doc.idDocCanalVHF) as nbDocuments
            FROM
                VHF_CANAL c
                LEFT OUTER JOIN VHF_TECHNOLOGIES t ON c.idVhfTechno = t.idVhfTechno
                LEFT OUTER JOIN VIEW_DOCUMENTS_CANAL_VHF doc ON c.idVhfCanal = doc.idVhfCanal
            GROUP BY
                c.idVhfCanal
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addCanal = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VHF_CANAL
            SET
                rxFreq = :rxFreq,
                txFreq = :txFreq,
                rxCtcss = :rxCtcss,
                txCtcss = :txCtcss,
                niveauCtcss = :niveauCtcss,
                txPower = :txPower,
                chName = :chName,
                appelSelectifCode = :appelSelectifCode,
                appelSelectifPorteuse = :appelSelectifPorteuse,
                let = :let,
                notone = :notone,
                idVhfTechno = :idVhfTechno,
                remarquesCanal = :remarquesCanal
        `,{
            rxFreq: req.body.rxFreq || null,
            txFreq: req.body.txFreq || null,
            rxCtcss: req.body.rxCtcss || null,
            txCtcss: req.body.txCtcss || null,
            niveauCtcss: req.body.niveauCtcss || null,
            txPower: req.body.txPower || null,
            chName: req.body.chName || null,
            appelSelectifCode: req.body.appelSelectifCode || null,
            appelSelectifPorteuse: req.body.appelSelectifPorteuse || null,
            let: req.body.let || null,
            notone: req.body.notone || null,
            idVhfTechno: req.body.idVhfTechno || null,
            remarquesCanal: req.body.remarquesCanal || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateCanal = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VHF_CANAL
            SET
                rxFreq = :rxFreq,
                txFreq = :txFreq,
                rxCtcss = :rxCtcss,
                txCtcss = :txCtcss,
                niveauCtcss = :niveauCtcss,
                txPower = :txPower,
                chName = :chName,
                appelSelectifCode = :appelSelectifCode,
                appelSelectifPorteuse = :appelSelectifPorteuse,
                let = :let,
                notone = :notone,
                idVhfTechno = :idVhfTechno,
                remarquesCanal = :remarquesCanal
            WHERE
                idVhfCanal = :idVhfCanal
        `,{
            rxFreq: req.body.rxFreq || null,
            txFreq: req.body.txFreq || null,
            rxCtcss: req.body.rxCtcss || null,
            txCtcss: req.body.txCtcss || null,
            niveauCtcss: req.body.niveauCtcss || null,
            txPower: req.body.txPower || null,
            chName: req.body.chName || null,
            appelSelectifCode: req.body.appelSelectifCode || null,
            appelSelectifPorteuse: req.body.appelSelectifPorteuse || null,
            let: req.body.let || null,
            notone: req.body.notone || null,
            idVhfTechno: req.body.idVhfTechno || null,
            remarquesCanal: req.body.remarquesCanal || null,
            idVhfCanal: req.body.idVhfCanal,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteCanal = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfCanauxDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfCanal);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Canaux PJ
const multerConfigCanaux = multer.diskStorage({
    destination: (req, file, callback) => {
        callback(null, 'uploads/vhfCanaux');
    },
    filename: (req, file, callback) => {
        const ext = file.mimetype.split('/')[1];
        callback(null, `vhfCanaux-${Date.now()}.${ext}`);
    }
});

const uploadCanaux = multer({
    storage: multerConfigCanaux,
});

exports.uploadCanalAttachedMulter = uploadCanaux.single('file');

exports.uploadCanalAttached = async (req, res, next)=>{
    try {
        const newFileToDB = await db.query(
            `INSERT INTO
                DOCUMENTS_CANAL_VHF
            SET
                urlFichierDocCanalVHF = :filename,
                idVhfCanal                 = :idVhfCanal
        `,{
            filename : req.file.filename,
            idVhfCanal : req.query.idVhfCanal,
        });

        const lastSelect = await db.query(`SELECT MAX(idDocCanalVHF) as idDocCanalVHF FROM DOCUMENTS_CANAL_VHF`);

        res.status(200);
        res.json({idDocCanalVHF: lastSelect[0].idDocCanalVHF})
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMetaDataCanal = async (req, res, next)=>{
    try {
        const document = await db.query(
            `SELECT
                *
            FROM
                DOCUMENTS_CANAL_VHF
            WHERE
                idDocCanalVHF = :idDocCanalVHF
        `,{
            idDocCanalVHF : req.body.idDocCanalVHF,
        });

        const update = await db.query(
            `UPDATE
                DOCUMENTS_CANAL_VHF
            SET
                nomDocCanalVHF   = :nomDocCanalVHF,
                formatDocCanalVHF = :formatDocCanalVHF,
                dateDocCanalVHF   = :dateDocCanalVHF,
                idTypeDocument = :idTypeDocument
            WHERE
                idDocCanalVHF        = :idDocCanalVHF
        `,{
            nomDocCanalVHF    : req.body.nomDocCanalVHF || null,
            formatDocCanalVHF : document[0].urlFichierDocCanalVHF.split('.')[1],
            dateDocCanalVHF   : req.body.dateDocCanalVHF || new Date(),
            idDocCanalVHF     : req.body.idDocCanalVHF,
            idTypeDocument    : req.body.idTypeDocument || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.dropCanalDocument = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfCanauxDocDelete(req.verifyJWTandProfile.idPersonne , req.body.idDocCanalVHF);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getAllDocumentsOneCanal = async (req, res)=>{
    try {
        const result = await db.query(
            `SELECT
                *
            FROM
                VIEW_DOCUMENTS_CANAL_VHF
            WHERE
                idVhfCanal = :idVhfCanal
            ORDER BY
                nomDocCanalVHF ASC
        `,
        {
            idVhfCanal : req.body.idVhfCanal,
        }
        );
        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Plans de fréquences
exports.getPlans = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                p.*,
                COUNT(pc.idVHfCanal) as nbCanaux,
                COUNT(doc.idDocPlanVHF) as nbDocuments
            FROM
                VHF_PLAN p
                LEFT OUTER JOIN VHF_PLAN_CANAL pc ON p.idVhfPlan = pc.idVhfPlan
                LEFT OUTER JOIN VIEW_DOCUMENTS_PLAN_VHF doc ON p.idVhfPlan = doc.idVhfPlan
            GROUP BY
                p.idVhfPlan
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addPlan = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VHF_PLAN
            SET
                libellePlan = :libellePlan,
                remarquesPlan = :remarquesPlan
        `,{
            libellePlan: req.body.libellePlan || null,
            remarquesPlan: req.body.remarquesPlan || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updatePlan = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VHF_PLAN
            SET
                libellePlan = :libellePlan,
                remarquesPlan = :remarquesPlan
            WHERE
                idVhfPlan = :idVhfPlan
        `,{
            libellePlan: req.body.libellePlan || null,
            remarquesPlan: req.body.remarquesPlan || null,
            idVhfPlan: req.body.idVhfPlan,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deletePlan = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfPlansDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfPlan);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Plans de fréquences PJ
const multerConfigPlans = multer.diskStorage({
    destination: (req, file, callback) => {
        callback(null, 'uploads/vhfPlans');
    },
    filename: (req, file, callback) => {
        const ext = file.mimetype.split('/')[1];
        callback(null, `vhfPlans-${Date.now()}.${ext}`);
    }
});

const uploadPlans = multer({
    storage: multerConfigPlans,
});

exports.uploadPlanAttachedMulter = uploadPlans.single('file');

exports.uploadPlanAttached = async (req, res, next)=>{
    try {
        const newFileToDB = await db.query(
            `INSERT INTO
                DOCUMENTS_PLAN_VHF
            SET
                urlFichierDocPlanVHF = :filename,
                idVhfPlan                 = :idVhfPlan
        `,{
            filename : req.file.filename,
            idVhfPlan : req.query.idVhfPlan,
        });

        const lastSelect = await db.query(`SELECT MAX(idDocPlanVHF) as idDocPlanVHF FROM DOCUMENTS_PLAN_VHF`);

        res.status(200);
        res.json({idDocPlanVHF: lastSelect[0].idDocPlanVHF})
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMetaDataPlan = async (req, res, next)=>{
    try {
        const document = await db.query(
            `SELECT
                *
            FROM
                DOCUMENTS_PLAN_VHF
            WHERE
                idDocPlanVHF = :idDocPlanVHF
        `,{
            idDocPlanVHF : req.body.idDocPlanVHF,
        });

        const update = await db.query(
            `UPDATE
                DOCUMENTS_PLAN_VHF
            SET
                nomDocPlanVHF   = :nomDocPlanVHF,
                formatDocPlanVHF = :formatDocPlanVHF,
                dateDocPlanVHF   = :dateDocPlanVHF,
                idTypeDocument = :idTypeDocument
            WHERE
                idDocPlanVHF        = :idDocPlanVHF
        `,{
            nomDocPlanVHF    : req.body.nomDocPlanVHF || null,
            formatDocPlanVHF : document[0].urlFichierDocPlanVHF.split('.')[1],
            dateDocPlanVHF   : req.body.dateDocPlanVHF || new Date(),
            idDocPlanVHF     : req.body.idDocPlanVHF,
            idTypeDocument    : req.body.idTypeDocument || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.dropPlanDocument = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfPlansDocDelete(req.verifyJWTandProfile.idPersonne , req.body.idDocPlanVHF);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getAllDocumentsOnePlan = async (req, res)=>{
    try {
        const result = await db.query(
            `SELECT
                *
            FROM
                VIEW_DOCUMENTS_PLAN_VHF
            WHERE
                idVhfPlan = :idVhfPlan
            ORDER BY
                nomDocPlanVHF ASC
        `,
        {
            idVhfPlan : req.body.idVhfPlan,
        }
        );
        res.send(result);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Plans de fréquences Canaux
exports.getCanauxOnePlan = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                p.*,
                c.chName
            FROM
                VHF_PLAN_CANAL p
                LEFT OUTER JOIN VHF_CANAL c ON p.idVhfCanal = c.idVhfCanal
            WHERE
                p.idVhfPlan = :idVhfPlan
        ;`,{
            idVhfPlan: req.body.idVhfPlan,
        });
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateCanauxOnePlan = async (req, res)=>{
    try {
        let clean = await db.query(`
            DELETE FROM
                VHF_PLAN_CANAL
            WHERE
                idVhfPlan = :idVhfPlan
        ;`,{
            idVhfPlan: req.body.idVhfPlan,
        });

        for(const frequence of req.body.canaux)
        {
            if(frequence.idVhfCanal > 0 && frequence.numeroCanal > 0)
            {
                let insert = await db.query(`
                    INSERT INTO
                        VHF_PLAN_CANAL
                    SET
                        idVhfPlan = :idVhfPlan,
                        idVhfCanal = :idVhfCanal,
                        numeroCanal = :numeroCanal
                ;`,{
                    idVhfPlan: req.body.idVhfPlan,
                    idVhfCanal: frequence.idVhfCanal,
                    numeroCanal: frequence.numeroCanal,
                });
            }
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}