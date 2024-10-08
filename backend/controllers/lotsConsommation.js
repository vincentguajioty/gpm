const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');

//----- PUBLIC ----
exports.getOneConso = async (req, res)=>{
    try {
        let verifFonctionnalite = await fonctionsMetiers.checkFunctionnalityBenevolesEnabled();
        if(verifFonctionnalite == false){return;}

        const consommation = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION
            WHERE
                idConsommation = :idConsommation
        `,{
            idConsommation: req.body.idConsommation || null
        });
        
        const qttMaterielsTraites = await db.query(`
            SELECT
                COUNT(idConsommationMateriel) as nb
            FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idConsommation = :idConsommation
                AND
                traiteOperateur = true
        `,{
            idConsommation: consommation[0].idConsommation
        });
        consommation[0].qttMaterielsTraites = qttMaterielsTraites[0].nb;
        const qttMaterielsNonTraites = await db.query(`
            SELECT
                COUNT(idConsommationMateriel) as nb
            FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idConsommation = :idConsommation
                AND
                traiteOperateur = false
        `,{
            idConsommation: consommation[0].idConsommation
        });
        consommation[0].qttMaterielsNonTraites = qttMaterielsNonTraites[0].nb;

        if(consommation[0].declarationEnCours && consommation[0].reapproEnCours){consommation[0].statut = {label:'Réappro', color:'info', adminReadOnly: true}}
        else if(consommation[0].declarationEnCours){consommation[0].statut = {label:'Saisie', color:'info', adminReadOnly: true}}
        else if(qttMaterielsNonTraites[0].nb > 0){consommation[0].statut = {label:'A TRAITER', color:'danger', adminReadOnly: false}}
        else {consommation[0].statut = {label:'Traité', color:'success', adminReadOnly: true}}

        const elements = await db.query(`
            SELECT
                m.*,
                l.libelleLot,
                c.libelleMateriel,
                res.libelleConteneur
            FROM
                LOTS_CONSOMMATION_MATERIEL m
                LEFT OUTER JOIN LOTS_LOTS l ON m.idLot = l.idLot
                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN RESERVES_CONTENEUR res ON m.idConteneur = res.idConteneur
            WHERE
                idConsommation = :idConsommation
            ORDER BY
                l.libelleLot,
                c.libelleMateriel
        `,{
            idConsommation: req.body.idConsommation || null
        });

        const lotsImpactes = await db.query(`
            SELECT DISTINCT
                l.idLot,
                l.libelleLot,
                l.idLot as value,
                l.libelleLot as label
            FROM
                LOTS_CONSOMMATION_MATERIEL m
                LEFT OUTER JOIN LOTS_LOTS l ON m.idLot = l.idLot
            WHERE
                idConsommation = :idConsommation
            ORDER BY
                l.libelleLot
        `,{
            idConsommation: req.body.idConsommation || null
        });
        
        res.json({consommation: consommation[0], elements: elements, lotsImpactes: lotsImpactes});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.createConso = async (req, res)=>{
    try {
        let verifFonctionnalite = await fonctionsMetiers.checkFunctionnalityBenevolesEnabled();
        if(verifFonctionnalite == false){return;}

        const result = await db.query(`
            INSERT INTO
                LOTS_CONSOMMATION
            SET
                nomDeclarantConsommation = :nomDeclarantConsommation,
                dateConsommation = :dateConsommation,
                evenementConsommation = :evenementConsommation,
                declarationEnCours = true,
                reapproEnCours = false
        `,{
            nomDeclarantConsommation: req.body.nomDeclarantConsommation || null,
            dateConsommation: req.body.dateConsommation || null,
            evenementConsommation: req.body.evenementConsommation || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idConsommation) as idConsommation FROM LOTS_CONSOMMATION;'
        );

        res.status(201);
        res.json({idConsommation: selectLast[0].idConsommation});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//----- AUTHENTICATED ----
exports.getAllConso = async (req, res)=>{
    try {
        const consommations = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION
            ORDER BY
                dateConsommation DESC
        `);
        res.send(consommations);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.decompterActionDefaut = async (req, res)=>{
    try {
        let traitement = await fonctionsMetiers.validerUnElementConsomme(req.body.idConsommationMateriel);

        if(traitement == true)
        {
            res.sendStatus(201);
        }else{
            res.sendStatus(501);
        }
    } catch (error) {
        logger.error(error)
        res.sendStatus(501);
    }
}

exports.annulerTouteAction = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                LOTS_CONSOMMATION_MATERIEL
            SET
                traiteOperateur = true
            WHERE
                idConsommationMateriel = :idConsommationMateriel
        `,{
            idConsommationMateriel: req.body.idConsommationMateriel || null,
        });
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
    }
}

exports.lotsConsommationsDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.lotsConsommationsDelete(req.verifyJWTandProfile.idPersonne , req.body.idConsommation);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.decompterToutesActionsDefaut = async (req, res)=>{
    try {
        let toutesLesConso = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idConsommation = :idConsommation
        `,{
            idConsommation: req.body.idConsommation || null,
        });

        for(const itemFromConso of toutesLesConso)
        {
            let traitement = await fonctionsMetiers.validerUnElementConsomme(itemFromConso.idConsommationMateriel);
        }

        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
    }
}