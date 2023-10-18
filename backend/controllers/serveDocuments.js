const path = require('path');
const logger = require('../winstonLogger');

exports.centresCouts = async (req, res, next)=>{
    try {
        res.sendFile(path.join(__dirname, '..', `uploads/centresCouts/${req.body.urlFichierDocCouts}`));
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.commandes = async (req, res, next)=>{
    try {
        res.sendFile(path.join(__dirname, '..', `uploads/commandes/${req.body.urlFichierDocCommande}`));
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.vehicules = async (req, res, next)=>{
    try {
        res.sendFile(path.join(__dirname, '..', `uploads/vehicules/${req.body.urlFichierDocVehicule}`));
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.vhfCanaux = async (req, res, next)=>{
    try {
        res.sendFile(path.join(__dirname, '..', `uploads/vhfCanaux/${req.body.urlFichierDocCanalVHF}`));
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.vhfEquipements = async (req, res, next)=>{
    try {
        res.sendFile(path.join(__dirname, '..', `uploads/vhfEquipements/${req.body.urlFichierDocVHF}`));
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.vhfPlans = async (req, res, next)=>{
    try {
        res.sendFile(path.join(__dirname, '..', `uploads/vhfPlans/${req.body.urlFichierDocPlanVHF}`));
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}