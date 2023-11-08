const db = require('../db');
const jwt = require('jsonwebtoken');
const logger = require('../winstonLogger');
const moment = require('moment');

const checkIfFeatureIsSetup = async () => {
    try {
        const temoinEncrypted = await db.query(`
            SELECT
                aesFournisseurTemoin
            FROM CONFIG
            ;`
        );

        if(temoinEncrypted[0].aesFournisseurTemoin != null)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error)
    }
}

const checkIfKeyIsValid = async (key) => {
    try {
        const getDecryptedChain = await db.query(`
            SELECT
                CAST(AES_DECRYPT(aesFournisseurTemoin, :key) AS CHAR) as temoinDecrypte
            FROM CONFIG
            ;`,{
                key : key,
            }
        );
        
        if(getDecryptedChain[0].temoinDecrypte == 'temoin')
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error)
    }
}

exports.authenticateForAES = async (req, res)=>{
    try {
        let checkEnabled = await checkIfFeatureIsSetup();
        if(! checkEnabled)
        {
            logger.debug("checkIfFeatureIsSetup a retourné "+ checkEnabled);
            return res.json({featureEnabled: false, message:"La fonctionnalité n'est pas configurée"});
        }

        let checkTheKey = await checkIfKeyIsValid(req.body.aesKey);
        if(checkTheKey)
        {
            logger.debug("La clef saisie est valide, on génère donc un token chiffré à stocker dans le FrontEnd, à envoyer pour décrypter");
            logger.info('Authentification AES réussie pour idUtilisateur ' + req.verifyJWTandProfile.idPersonne);

            const jwtExpirySeconds = parseInt(process.env.JWT_AESFOURNISSEURS_EXPIRATION);
            const aesTokenValidUntil = moment(new Date()).add(jwtExpirySeconds, 'seconds');
            const aesToken = jwt.sign({aesKey: req.body.aesKey}, process.env.JWT_AESFOURNISSEURS_TOKEN, {
                expiresIn: jwtExpirySeconds,
            });

            return res.json({
                featureEnabled: true,
                auth: true,
                aesToken: aesToken,
                aesTokenValidUntil: aesTokenValidUntil,
            });
        }
        else
        {
            logger.debug("checkIfKeyIsValid a retourné "+ checkTheKey);
            return res.json({featureEnabled: true, auth: false, message:"Mauvaise clef"});
        }
        
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }

}

exports.initKey = async (req, res, next)=>{
    try {
        const newKey = req.body.aesKey;

        if(await checkIfFeatureIsSetup())
        {
            logger.debug("checkIfFeatureIsSetup a retourné "+ checkEnabled);
            return res.json({featureEnabled: true, message:"La fonctionnalité est déjà configurée"});
        }
        
        const changeEncryptionConfig = await db.query(`
            UPDATE
                CONFIG
            SET
                aesFournisseurTemoin = AES_ENCRYPT("temoin", :aesNew)
            ;`,{
                aesNew: newKey,
            }
        );

        next();
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateAesKey = async (req, res, next) => {
    try {
        const oldKey = req.aesKey;
        const newKey = req.body.aesKey;
        
        const changeEncryptionFournisseur = await db.query(`
            UPDATE
                FOURNISSEURS
            SET
                aesFournisseur = AES_ENCRYPT(CAST(AES_DECRYPT(aesFournisseur, :aesOld) AS CHAR), :aesNew)
            ;`,{
                aesOld : oldKey,
                aesNew: newKey,
            }
        );

        const changeEncryptionConfig = await db.query(`
            UPDATE
                CONFIG
            SET
                aesFournisseurTemoin = AES_ENCRYPT("temoin", :aesNew)
            ;`,{
                aesNew: newKey,
            }
        );

        next();
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateFournisseurAesData = async (req, res, next) => {
    try {
        const checkKey = await checkIfKeyIsValid(req.aesKey);

        if(checkKey)
        {
            const changeEncryptionFournisseur = await db.query(`
                UPDATE
                    FOURNISSEURS
                SET
                    aesFournisseur = AES_ENCRYPT(:aesFournisseur, :aesKey)
                WHERE
                    idFournisseur = :idFournisseur
                ;`,{
                    aesFournisseur : req.body.aesFournisseur || null,
                    idFournisseur : req.body.idFournisseur || null,
                    aesKey: req.aesKey,
                }
            );

            res.sendStatus(201);
        }
        else
        {
            res.sendStatus(500);
        }

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.disableAesAndDelete = async (req, res, next) => {
    try {
        const changeEncryptionFournisseur = await db.query(`
            UPDATE
                FOURNISSEURS
            SET
                aesFournisseur = null
            ;`
        );

        const changeEncryptionConfig = await db.query(`
            UPDATE
                CONFIG
            SET
                aesFournisseurTemoin = null
            ;`
        );

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}