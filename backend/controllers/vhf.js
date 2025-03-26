const db = require('../db');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsMail = require('../helpers/fonctionsMail');
const logger = require('../winstonLogger');
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
            ORDER BY
                c.chName
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
            ORDER BY
                p.libellePlan
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
            ORDER BY
                c.chName
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

//Equipements VHF
exports.getEquipementsVhf = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                e.*,
                ve.libelleVhfEtat,
                p.libellePlan,
                pr.nomPersonne,
                pr.prenomPersonne,
                pr.identifiant,
                t.libelleTechno,
                te.libelleType,
                COUNT(a.idVhfAccessoire) as nbAccessoires
            FROM
                VHF_EQUIPEMENTS e
                LEFT OUTER JOIN VHF_ETATS ve ON e.idVhfEtat = ve.idVhfEtat
                LEFT OUTER JOIN VHF_PLAN p ON e.idVhfPlan = p.idVhfPlan
                LEFT OUTER JOIN PERSONNE_REFERENTE pr ON e.idResponsable = pr.idPersonne
                LEFT OUTER JOIN VHF_TECHNOLOGIES t ON e.idVhfTechno = t.idVhfTechno
                LEFT OUTER JOIN VHF_TYPES_EQUIPEMENTS te ON e.idVhfType = te.idVhfType
                LEFT OUTER JOIN VHF_ACCESSOIRES a ON e.idVhfEquipement = a.idVhfEquipement
            GROUP BY
                e.idVhfEquipement
            ORDER BY
                e.vhfIndicatif
        ;`);
        for(const equipement of results)
        {
            let alertesBenevoles = await db.query(`
                SELECT
                    COUNT(a.idAlerte) as nbAlertesEnCours
                FROM
                    VHF_ALERTES a
                    LEFT OUTER JOIN VHF_ALERTES_ETATS e ON a.idVHFAlertesEtat = e.idVHFAlertesEtat
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
                WHERE
                    idVhfEquipement = :idVhfEquipement
                    AND dateResolutionAlerte IS NULL
            ;`,{
                idVhfEquipement: equipement.idVhfEquipement
            });

            equipement.nbAlertesEnCours = alertesBenevoles[0].nbAlertesEnCours;
        }
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneEquipement = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                e.*,
                ve.libelleVhfEtat,
                p.libellePlan,
                pr.nomPersonne,
                pr.prenomPersonne,
                pr.identifiant,
                t.libelleTechno,
                te.libelleType,
                COUNT(a.idVhfAccessoire) as nbAccessoires
            FROM
                VHF_EQUIPEMENTS e
                LEFT OUTER JOIN VHF_ETATS ve ON e.idVhfEtat = ve.idVhfEtat
                LEFT OUTER JOIN VHF_PLAN p ON e.idVhfPlan = p.idVhfPlan
                LEFT OUTER JOIN PERSONNE_REFERENTE pr ON e.idResponsable = pr.idPersonne
                LEFT OUTER JOIN VHF_TECHNOLOGIES t ON e.idVhfTechno = t.idVhfTechno
                LEFT OUTER JOIN VHF_TYPES_EQUIPEMENTS te ON e.idVhfType = te.idVhfType
                LEFT OUTER JOIN VHF_ACCESSOIRES a ON e.idVhfEquipement = a.idVhfEquipement
            WHERE
                e.idVhfEquipement = :idVhfEquipement
            GROUP BY
                e.idVhfEquipement
        ;`,{
            idVhfEquipement: req.body.idVhfEquipement,
        });

        for(const device of results)
        {
            let accessoires = await db.query(`
                SELECT
                    a.*,
                    t.libelleVhfAccessoireType
                FROM
                    VHF_ACCESSOIRES a
                    LEFT OUTER JOIN VHF_ACCESSOIRES_TYPES t ON a.idVhfAccessoireType = t.idVhfAccessoireType
                WHERE
                    a.idVhfEquipement = :idVhfEquipement
                ORDER BY
                    a.libelleVhfAccessoire
            ;`,{
                idVhfEquipement: device.idVhfEquipement,
            });
            device.accessoires = accessoires;

            let documents = await db.query(`
                SELECT
                    *
                FROM
                    VIEW_DOCUMENTS_VHF
                WHERE
                    idVhfEquipement = :idVhfEquipement
                ORDER BY
                    nomDocVHF ASC
            ;`,{
                idVhfEquipement: device.idVhfEquipement,
            });
            device.documents = documents;

            let alertesBenevoles = await db.query(`
                SELECT
                    a.*,
                    p.identifiant,
                    p.nomPersonne,
                    p.prenomPersonne,
                    e.libelleVHFAlertesEtat,
                    e.couleurVHFAlertesEtat
                FROM
                    VHF_ALERTES a
                    LEFT OUTER JOIN VHF_ALERTES_ETATS e ON a.idVHFAlertesEtat = e.idVHFAlertesEtat
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
                WHERE
                    idVhfEquipement = :idVhfEquipement
                ORDER BY
                    a.dateCreationAlerte DESC
            ;`,{
                idVhfEquipement: device.idVhfEquipement
            });

            device.alertesBenevoles = alertesBenevoles;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addEquipement = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VHF_EQUIPEMENTS
            SET
                vhfIndicatif = :vhfIndicatif,
                dispoBenevoles = false
        `,{
            vhfIndicatif: req.body.vhfIndicatif || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idVhfEquipement) as idVhfEquipement FROM VHF_EQUIPEMENTS;'
        );

        res.status(201);
        res.json({idVhfEquipement: selectLast[0].idVhfEquipement});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateEquipement = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VHF_EQUIPEMENTS
            SET
                vhfMarqueModele = :vhfMarqueModele,
                vhfSN = :vhfSN,
                vhfIndicatif = :vhfIndicatif,
                idVhfEtat = :idVhfEtat,
                idVhfType = :idVhfType,
                idVhfTechno = :idVhfTechno,
                idVhfPlan = :idVhfPlan,
                dateDerniereProg = :dateDerniereProg,
                idResponsable = :idResponsable,
                dispoBenevoles = :dispoBenevoles,
                remarquesVhfEquipement = :remarquesVhfEquipement
            WHERE
                idVhfEquipement = :idVhfEquipement
        `,{
            vhfMarqueModele: req.body.vhfMarqueModele || null,
            vhfSN: req.body.vhfSN || null,
            vhfIndicatif: req.body.vhfIndicatif || null,
            idVhfEtat: req.body.idVhfEtat || null,
            idVhfType: req.body.idVhfType || null,
            idVhfTechno: req.body.idVhfTechno || null,
            idVhfPlan: req.body.idVhfPlan || null,
            dateDerniereProg: req.body.dateDerniereProg || null,
            idResponsable: req.body.idResponsable || null,
            dispoBenevoles: req.body.dispoBenevoles || false,
            remarquesVhfEquipement: req.body.remarquesVhfEquipement || null,
            idVhfEquipement: req.body.idVhfEquipement,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteEquipement = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfEquipementsDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfEquipement);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Accessoires Equipements VHF
exports.addAccessoire = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VHF_ACCESSOIRES
            SET
                libelleVhfAccessoire = :libelleVhfAccessoire,
                marqueModeleVhfAccessoire = :marqueModeleVhfAccessoire,
                idVhfAccessoireType = :idVhfAccessoireType,
                SnVhfAccessoire = :SnVhfAccessoire,
                dateAchat = :dateAchat,
                dateMiseService = :dateMiseService,
                dureeVieEstimeeJours = :dureeVieEstimeeJours,
                remarquesVhfAccessoire = :remarquesVhfAccessoire,
                idVhfEquipement = :idVhfEquipement
        `,{
            libelleVhfAccessoire: req.body.libelleVhfAccessoire || null,
            marqueModeleVhfAccessoire: req.body.marqueModeleVhfAccessoire || null,
            idVhfAccessoireType: req.body.idVhfAccessoireType || null,
            SnVhfAccessoire: req.body.SnVhfAccessoire || null,
            dateAchat: req.body.dateAchat || null,
            dateMiseService: req.body.dateMiseService || null,
            dureeVieEstimeeJours: req.body.dureeVieEstimeeJours || null,
            remarquesVhfAccessoire: req.body.remarquesVhfAccessoire || null,
            idVhfEquipement: req.body.idVhfEquipement || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateAccessoire = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VHF_ACCESSOIRES
            SET
                libelleVhfAccessoire = :libelleVhfAccessoire,
                marqueModeleVhfAccessoire = :marqueModeleVhfAccessoire,
                idVhfAccessoireType = :idVhfAccessoireType,
                SnVhfAccessoire = :SnVhfAccessoire,
                dateAchat = :dateAchat,
                dateMiseService = :dateMiseService,
                dureeVieEstimeeJours = :dureeVieEstimeeJours,
                remarquesVhfAccessoire = :remarquesVhfAccessoire
            WHERE
                idVhfAccessoire = :idVhfAccessoire
        `,{
            libelleVhfAccessoire: req.body.libelleVhfAccessoire || null,
            marqueModeleVhfAccessoire: req.body.marqueModeleVhfAccessoire || null,
            idVhfAccessoireType: req.body.idVhfAccessoireType || null,
            SnVhfAccessoire: req.body.SnVhfAccessoire || null,
            dateAchat: req.body.dateAchat || null,
            dateMiseService: req.body.dateMiseService || null,
            dureeVieEstimeeJours: req.body.dureeVieEstimeeJours || null,
            remarquesVhfAccessoire: req.body.remarquesVhfAccessoire || null,
            idVhfAccessoire: req.body.idVhfAccessoire || null,
        });
        
        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.vhfEquipementsAccessoiresDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfEquipementsAccessoiresDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfAccessoire);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Equipements VHF PJ
const multerConfigVHF = multer.diskStorage({
    destination: (req, file, callback) => {
        callback(null, 'uploads/vhfEquipements');
    },
    filename: (req, file, callback) => {
        const ext = file.mimetype.split('/')[1];
        callback(null, `vhfEquipements-${Date.now()}.${ext}`);
    }
});

const uploadVHF = multer({
    storage: multerConfigVHF,
});

exports.uploadEquipementsAttachedMulter = uploadVHF.single('file');

exports.uploadEquipementsAttached = async (req, res, next)=>{
    try {
        const newFileToDB = await db.query(
            `INSERT INTO
                DOCUMENTS_VHF
            SET
                urlFichierDocVHF = :filename,
                idVhfEquipement  = :idVhfEquipement
        `,{
            filename : req.file.filename,
            idVhfEquipement : req.query.idVhfEquipement,
        });

        const lastSelect = await db.query(`SELECT MAX(idDocVHF) as idDocVHF FROM DOCUMENTS_VHF`);

        res.status(200);
        res.json({idDocVHF: lastSelect[0].idDocVHF})
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateMetaDataEquipements = async (req, res, next)=>{
    try {
        const document = await db.query(
            `SELECT
                *
            FROM
                DOCUMENTS_VHF
            WHERE
                idDocVHF = :idDocVHF
        `,{
            idDocVHF : req.body.idDocVHF,
        });

        const update = await db.query(
            `UPDATE
                DOCUMENTS_VHF
            SET
                nomDocVHF   = :nomDocVHF,
                formatDocVHF = :formatDocVHF,
                dateDocVHF   = :dateDocVHF,
                idTypeDocument = :idTypeDocument
            WHERE
                idDocVHF        = :idDocVHF
        `,{
            nomDocVHF    : req.body.nomDocVHF || null,
            formatDocVHF : document[0].urlFichierDocVHF.split('.')[1],
            dateDocVHF   : req.body.dateDocVHF || new Date(),
            idDocVHF     : req.body.idDocVHF,
            idTypeDocument    : req.body.idTypeDocument || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.dropEquipementsDocument = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfEquipementsDocDelete(req.verifyJWTandProfile.idPersonne , req.body.idDocVHF);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//VHF - stocks de consommables
exports.getAllVhfStock = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                s.*,
                c.libelleMateriel,
                c.idCategorie,
                c.sterilite,
                c.peremptionAnticipationVHF,
                f.nomFournisseur
            FROM
                VHF_STOCK s
                LEFT OUTER JOIN VIEW_MATERIEL_CATALOGUE_VHF c ON s.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN FOURNISSEURS f ON s.idFournisseur = f.idFournisseur
        ;`);
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneVhfStock = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                s.*,
                c.libelleMateriel,
                c.idCategorie,
                c.sterilite,
                c.peremptionAnticipationVHF,
                f.nomFournisseur
            FROM
                VHF_STOCK s
                LEFT OUTER JOIN VIEW_MATERIEL_CATALOGUE_VHF c ON s.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN FOURNISSEURS f ON s.idFournisseur = f.idFournisseur
            WHERE
                idVhfStock = :idVhfStock
        ;`,{
            idVhfStock : req.body.idVhfStock,
        });
        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addVhfStock = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VHF_STOCK
            SET
                idMaterielCatalogue            = :idMaterielCatalogue,
                idFournisseur                  = :idFournisseur,
                quantiteVhfStock               = :quantiteVhfStock,
                quantiteAlerteVhfStock         = :quantiteAlerteVhfStock,
                peremptionVhfStock             = :peremptionVhfStock,
                peremptionAnticipationVhfStock = :peremptionAnticipationVhfStock,
                commentairesVhfStock           = :commentairesVhfStock
        `,{
            idMaterielCatalogue            : req.body.idMaterielCatalogue || null,
            idFournisseur                  : req.body.idFournisseur || null,
            quantiteVhfStock               : req.body.quantiteVhfStock || 0,
            quantiteAlerteVhfStock         : req.body.quantiteAlerteVhfStock || 0,
            peremptionVhfStock             : req.body.peremptionVhfStock || null,
            peremptionAnticipationVhfStock : req.body.peremptionAnticipationVhfStock || null,
            commentairesVhfStock           : req.body.commentairesVhfStock || null,
        });

        let selectLast = await db.query(
            'SELECT MAX(idVhfStock) as idVhfStock FROM VHF_STOCK;'
        );
        await fonctionsMetiers.updateConformiteMaterielVhf(selectLast[0].idVhfStock);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateVhfStock = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VHF_STOCK
            SET
                idMaterielCatalogue            = :idMaterielCatalogue,
                idFournisseur                  = :idFournisseur,
                quantiteVhfStock               = :quantiteVhfStock,
                quantiteAlerteVhfStock         = :quantiteAlerteVhfStock,
                peremptionVhfStock             = :peremptionVhfStock,
                peremptionAnticipationVhfStock = :peremptionAnticipationVhfStock,
                commentairesVhfStock           = :commentairesVhfStock
            WHERE
                idVhfStock                     = :idVhfStock
        `,{
            idMaterielCatalogue            : req.body.idMaterielCatalogue || null,
            idFournisseur                  : req.body.idFournisseur || null,
            quantiteVhfStock               : req.body.quantiteVhfStock || 0,
            quantiteAlerteVhfStock         : req.body.quantiteAlerteVhfStock || 0,
            peremptionVhfStock             : req.body.peremptionVhfStock || null,
            peremptionAnticipationVhfStock : req.body.peremptionAnticipationVhfStock || null,
            commentairesVhfStock           : req.body.commentairesVhfStock || null,
            idVhfStock                     : req.body.idVhfStock,
        });

        await fonctionsMetiers.updateConformiteMaterielVhf(req.body.idVhfStock);

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.deleteVhfStock = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.vhfStockDelete(req.verifyJWTandProfile.idPersonne , req.body.idVhfStock);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//VHF - Alertes bénévoles
exports.getVHFAlertes = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                a.*,
                e.libelleVHFAlertesEtat,
                e.couleurVHFAlertesEtat,
                v.vhfIndicatif,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                VHF_ALERTES a
                LEFT OUTER JOIN VHF_ALERTES_ETATS e ON a.idVHFAlertesEtat = e.idVHFAlertesEtat
                LEFT OUTER JOIN VHF_EQUIPEMENTS v ON a.idVhfEquipement = v.idVhfEquipement
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
            ORDER BY
                a.dateCreationAlerte DESC
        ;`);

        if(req.body.idVhfEquipement && req.body.idVhfEquipement != null && req.body.idVhfEquipement > 0)
        {
            results = results.filter(alerte => alerte.idVhfEquipement == req.body.idVhfEquipement)
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.autoAffect = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VHF_ALERTES
            SET
                idTraitant = :idTraitant,
                idVHFAlertesEtat = 2,
                datePriseEnCompteAlerte = CURRENT_TIMESTAMP
            WHERE
                idAlerte = :idAlerte
        `,{
            idTraitant : req.verifyJWTandProfile.idPersonne,
            idAlerte: req.body.idAlerte,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.affectationTier = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VHF_ALERTES
            SET
                idTraitant = :idTraitant,
                idVHFAlertesEtat = 2,
                datePriseEnCompteAlerte = CURRENT_TIMESTAMP
            WHERE
                idAlerte = :idAlerte
        `,{
            idTraitant : req.body.idTraitant,
            idAlerte: req.body.idAlerte,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.udpateStatut = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                VHF_ALERTES
            SET
                idVHFAlertesEtat = :idVHFAlertesEtat
            WHERE
                idAlerte = :idAlerte
        `,{
            idAlerte: req.body.idAlerte,
            idVHFAlertesEtat: req.body.idVHFAlertesEtat,
        });

        if(req.body.idVHFAlertesEtat == 4 || req.body.idVHFAlertesEtat == 5)
        {
            const result = await db.query(`
                UPDATE
                    VHF_ALERTES
                SET
                    dateResolutionAlerte = CURRENT_TIMESTAMP
                WHERE
                    idAlerte = :idAlerte
            `,{
                idAlerte: req.body.idAlerte,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//VHF - Alertes bénévoles Création publique
exports.createAlerte = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                VHF_ALERTES
            SET
                idVHFAlertesEtat = 1,
                dateCreationAlerte = CURRENT_TIMESTAMP,
                nomDeclarant = :nomDeclarant,
                mailDeclarant = :mailDeclarant,
                idVhfEquipement = :idVhfEquipement,
                messageAlerteVHF = :messageAlerteVHF
        `,{
            nomDeclarant: req.body.nomDeclarant || null,
            mailDeclarant: req.body.mailDeclarant || null,
            idVhfEquipement: req.body.idVhfEquipement || null,
            messageAlerteVHF: req.body.messageAlerteVHF || null,
        });

        let selectLast = await db.query(
            'SELECT MAX(idAlerte) as idAlerte FROM VHF_ALERTES;'
        );

        if(req.body.mailDeclarant && req.body.mailDeclarant != null && req.body.mailDeclarant != "")
        {
            await fonctionsMail.registerToMailQueue({
                typeMail: 'confirmationAlerteVHF',
                idObject: selectLast[0].idAlerte,
                otherMail: req.body.mailDeclarant,
            });
        }
        
        const usersToNotify = await db.query(`
            SELECT
                idPersonne
            FROM
                VIEW_HABILITATIONS
            WHERE
                notif_benevoles_vhf = true
                AND
                notifications = true
                AND
                mailPersonne IS NOT NULL
                AND
                mailPersonne <> ""
        `);
        for(const personne of usersToNotify)
        {
            await fonctionsMail.registerToMailQueue({
                typeMail: 'alerteBenevolesVHF',
                idPersonne: personne.idPersonne,
                idObject: selectLast[0].idAlerte,
            });
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}