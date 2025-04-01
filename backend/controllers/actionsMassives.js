const db = require('../db');
const jwt = require('jsonwebtoken');
const logger = require('../winstonLogger');
const moment = require('moment');
const fonctionsAuthentification = require('../helpers/fonctionsAuthentification');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const fonctionsDelete = require('../helpers/fonctionsDelete');

exports.authenticateForAM = async (req, res)=>{
    try {
        let selectedUser = await db.query(`
            SELECT
                pr.motDePasse,
                v.actionsmassives
            FROM
                PERSONNE_REFERENTE pr
                LEFT OUTER JOIN VIEW_HABILITATIONS v ON pr.idPersonne = v.idPersonne
            WHERE
                pr.identifiant = :identifiant
            ;`,{
                identifiant : req.verifyJWTandProfile.identifiant,
            }
        );
        selectedUser = selectedUser[0];

        if(req.verifyJWTandProfile.isActiveDirectory && process.env.LDAP_ENABLED == "1")
        {
            checkAuthentication = await fonctionsAuthentification.ldapUserLogin(req.verifyJWTandProfile.identifiant, req.body.motDePasse);
        }
        else
        {
            checkAuthentication = await fonctionsAuthentification.localUserLogin(req.body.motDePasse, selectedUser);
        }

        checkAuthentication = checkAuthentication && selectedUser.actionsmassives;
        
        if(checkAuthentication)
        {
            logger.debug("L'authentification utilisateur est réussie, on génère donc un token chiffré à stocker dans le FrontEnd, à envoyer pour double-authentifier les actions massives.");
            logger.info('Authentification ActionsMassives réussie pour idPersonne ' + req.verifyJWTandProfile.idPersonne);

            const jwtExpirySeconds = parseInt(process.env.JWT_ACTIONSMASSIVES_EXPIRATION);
            const amTokenValidUntil = moment(new Date()).add(jwtExpirySeconds, 'seconds');
            const amToken = jwt.sign({idPersonne: req.verifyJWTandProfile.idPersonne}, process.env.JWT_ACTIONSMASSIVES_TOKEN, {
                expiresIn: jwtExpirySeconds,
            });

            return res.json({
                auth: true,
                amToken: amToken,
                amTokenValidUntil: amTokenValidUntil,
            });
        }
        else
        {
            logger.debug("checkIfKeyIsValid a retourné "+ checkAuthentication);
            return res.json({auth: false, message:"Mauvaise authentification"});
        }
        
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }

}

exports.getAvailableActions = async (req, res)=>{
    try {
        let availableAction = [];
        let impact;

        /* id: 11, description: "Supprimer tous les matériels de la section opérationnelle qui ne sont pas rattachés à aucun emplacement"*/
        impact = await db.query(`
            SELECT COUNT(*) AS nb FROM MATERIEL_ELEMENT WHERE idEmplacement IS NULL;
        `);
        availableAction.push({
            id: 11,
            categorie: 'Matériels',
            description: "Supprimer tous les matériels de la section opérationnelle qui ne sont pas rattachés à aucun emplacement",
            impact: impact[0].nb+" éléments matériels supprimés",
            endPointBo: '/actionsmassives/action11'
        });

        /* id: 12, description: "Modifier tous les matériels de la section opérationnelle pour supprimer toutes les dates de péremption"*/
        impact = await db.query(`
            SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT WHERE peremption Is Not Null OR peremptionNotification Is Not Null;
        `);
        availableAction.push({
            id: 12,
            categorie: 'Matériels',
            description: "Modifier tous les matériels de la section opérationnelle pour supprimer toutes les dates de péremption",
            impact: impact[0].nb+" éléments matériels",
            endPointBo: '/actionsmassives/action12'
        });

        /* id: 13, description: "Modifier tous les matériels de la section opérationnelle pour changer la quantité d'alerte avec la valeur: quantité actuelle - 1"*/
        impact = await db.query(`
            SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT WHERE quantiteAlerte != quantite - 1;
        `);
        availableAction.push({
            id: 13,
            categorie: 'Matériels',
            description: "Modifier tous les matériels de la section opérationnelle pour changer la quantité d'alerte avec la valeur: quantité actuelle - 1",
            impact: impact[0].nb+" quantites d'alerte mises à jour",
            endPointBo: '/actionsmassives/action13'
        });

        /* id: 21, description: "Supprimer tous les emplacements qui n'ont pas de sac de rattachement"*/
        impact = await db.query(`
            SELECT COUNT(*) as nb FROM MATERIEL_EMPLACEMENT WHERE idSac Is Null;
        `);
        availableAction.push({
            id: 21,
            categorie: 'Emplacements',
            description: "Supprimer tous les emplacements qui n'ont pas de sac de rattachement",
            impact: impact[0].nb+" emplacements supprimés",
            endPointBo: '/actionsmassives/action21'
        });

        /* id: 22, description: "Supprimer tous les emplacements qui ne contiennent pas de matériel"*/
        impact = await db.query(`
            SELECT
                COUNT(c.idEmplacement) as nb
            FROM
                (
                SELECT
                    em.idEmplacement,
                    COUNT(el.idElement) as nbMateriel
                FROM
                    MATERIEL_EMPLACEMENT em
                    LEFT OUTER JOIN MATERIEL_ELEMENT el ON em.idEmplacement = el.idEmplacement
                GROUP BY em.idEmplacement
                ) c
            WHERE
                c.nbMateriel = 0
        `);
        availableAction.push({
            id: 22,
            categorie: 'Emplacements',
            description: "Supprimer tous les emplacements qui ne contiennent pas de matériel",
            impact: impact[0].nb+" emplacements supprimés",
            endPointBo: '/actionsmassives/action22'
        });

        /* id: 31, description: "Supprimer tous les sacs qui n'ont pas de lot de rattachement"*/
        impact = await db.query(`
            SELECT COUNT(*) as nb FROM MATERIEL_SAC WHERE idLot Is Null;
        `);
        availableAction.push({
            id: 31,
            categorie: 'Sacs',
            description: "Supprimer tous les sacs qui n'ont pas de lot de rattachement",
            impact: impact[0].nb+" sacs supprimés",
            endPointBo: '/actionsmassives/action31'
        });

        /* id: 32, description: "Supprimer tous les sacs qui ne contiennent pas d'emplacement"*/
        impact = await db.query(`
            SELECT
                COUNT(c.idSac) as nb
            FROM
                (
                SELECT
                    em.idSac,
                    COUNT(el.idEmplacement) as nbEmplacement
                FROM
                    MATERIEL_SAC em
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT el ON em.idSac = el.idSac
                GROUP BY em.idSac
                ) c
            WHERE
                c.nbEmplacement = 0
        `);
        availableAction.push({
            id: 32,
            categorie: 'Sacs',
            description: "Supprimer tous les sacs qui ne contiennent pas d'emplacement",
            impact: impact[0].nb+" sacs supprimés",
            endPointBo: '/actionsmassives/action32'
        });

        /* id: 33, description: "Supprimer tous les sacs et leurs emplacements et leur matériel dès lors que les sacs ne sont pas rattachés à un lot"*/
        impact = await db.query(`
            SELECT
                COUNT(e.idElement) as nb
            FROM
                MATERIEL_SAC ms
                LEFT OUTER JOIN MATERIEL_EMPLACEMENT me ON ms.idSac=me.idSac
                LEFT OUTER JOIN MATERIEL_ELEMENT e ON me.idEmplacement = e.idEmplacement
            WHERE
                ms.idLot IS NULL
        `);
        availableAction.push({
            id: 33,
            categorie: 'Sacs',
            description: "Supprimer tous les sacs et leurs emplacements et leur matériel dès lors que les sacs ne sont pas rattachés à un lot",
            impact: impact[0].nb+" elements de matériel supprimés avec tous leurs emplacements/sacs",
            endPointBo: '/actionsmassives/action33'
        });

        /* id: 41, description: "Supprimer tous les lots qui ne contiennent pas de sac"*/
        impact = await db.query(`
            SELECT
                COUNT(c.idLot) as nb
            FROM
                (
                SELECT
                    em.idLot,
                    COUNT(el.idSac) as nbSacs
                FROM
                    LOTS_LOTS em
                    LEFT OUTER JOIN MATERIEL_SAC el ON em.idLot = el.idLot
                GROUP BY em.idLot
                ) c
            WHERE
                c.nbSacs = 0
        `);
        availableAction.push({
            id: 41,
            categorie: 'Lots',
            description: "Supprimer tous les lots qui ne contiennent pas de sac",
            impact: impact[0].nb+" lots supprimés",
            endPointBo: '/actionsmassives/action41'
        });

        /* id: 51, description: "Supprimer toutes les personnes externes orphelines"*/
        availableAction.push({
            id: 51,
            categorie: 'Tenues',
            description: "Supprimer toutes les personnes externes orphelines",
            impact: "N/A",
            endPointBo: '/actionsmassives/action51'
        });

        res.send(availableAction);
    } catch (error) {
        logger.error(error)
    }
}

exports.action11 = async (req, res) => {
    /*Supprimer tous les matériels de la section opérationnelle qui ne sont pas rattachés à aucun emplacement*/
    try {
        logger.info("Lancement de l'action massive action11");
        
        let dbQuery = await db.query(`
            SELECT * FROM MATERIEL_ELEMENT WHERE idEmplacement IS NULL;
        `);
        for(const item of dbQuery)
        {
            await fonctionsDelete.materielsDelete(req.verifyJWTandProfile.idPersonne, item.idElement);
        }

        await fonctionsMetiers.checkAllConf();

        logger.info("Fin de l'action massive action11");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action12 = async (req, res) => {
    /*Modifier tous les matériels de la section opérationnelle pour supprimer toutes les dates de péremption*/
    try {
        logger.info("Lancement de l'action massive action12");
        
        let dbQuery = await db.query(`
            UPDATE MATERIEL_ELEMENT SET peremption = Null;
        `);

        await fonctionsMetiers.checkAllConf();

        logger.info("Fin de l'action massive action12");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action13 = async (req, res) => {
    /*Modifier tous les matériels de la section opérationnelle pour changer la quantité d'alerte avec la valeur: quantité actuelle - 1*/
    try {
        logger.info("Lancement de l'action massive action13");
        
        let dbQuery = await db.query(`
            UPDATE MATERIEL_ELEMENT SET quantiteAlerte = quantite - 1;
        `);

        await fonctionsMetiers.checkAllConf();

        logger.info("Fin de l'action massive action13");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action21 = async (req, res) => {
    /*Supprimer tous les emplacements qui n'ont pas de sac de rattachement*/
    try {
        logger.info("Lancement de l'action massive action21");
        
        let dbQuery = await db.query(`
            SELECT * FROM MATERIEL_EMPLACEMENT WHERE idSac IS NULL;
        `);
        for(const item of dbQuery)
        {
            await fonctionsDelete.emplacementsDelete(req.verifyJWTandProfile.idPersonne, item.idEmplacement);
        }

        await fonctionsMetiers.checkAllConf();

        logger.info("Fin de l'action massive action21");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action22 = async (req, res) => {
    /*Supprimer tous les emplacements qui ne contiennent pas de matériel*/
    try {
        logger.info("Lancement de l'action massive action22");
        
        let dbQuery = await db.query(`
            SELECT
                c.idEmplacement
            FROM
                (
                SELECT
                    em.idEmplacement,
                    COUNT(el.idElement) as nbMateriel
                FROM
                    MATERIEL_EMPLACEMENT em
                    LEFT OUTER JOIN MATERIEL_ELEMENT el ON em.idEmplacement = el.idEmplacement
                GROUP BY em.idEmplacement
                ) c
            WHERE
                c.nbMateriel = 0
        `);
        for(const item of dbQuery)
        {
            await fonctionsDelete.emplacementsDelete(req.verifyJWTandProfile.idPersonne, item.idEmplacement);
        }

        await fonctionsMetiers.checkAllConf();

        logger.info("Fin de l'action massive action22");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action31 = async (req, res) => {
    /*Supprimer tous les sacs qui n'ont pas de lot de rattachement*/
    try {
        logger.info("Lancement de l'action massive action31");
        
        let dbQuery = await db.query(`
            SELECT * FROM MATERIEL_SAC WHERE idLot IS NULL;
        `);
        for(const item of dbQuery)
        {
            await fonctionsDelete.sacsDelete(req.verifyJWTandProfile.idPersonne, item.idSac);
        }

        await fonctionsMetiers.checkAllConf();

        logger.info("Fin de l'action massive action31");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action32 = async (req, res) => {
    /*Supprimer tous les sacs qui ne contiennent pas d'emplacement*/
    try {
        logger.info("Lancement de l'action massive action32");
        
        let dbQuery = await db.query(`
            SELECT
                c.idSac
            FROM
                (
                SELECT
                    em.idSac,
                    COUNT(el.idEmplacement) as nbEmplacement
                FROM
                    MATERIEL_SAC em
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT el ON em.idSac = el.idSac
                GROUP BY em.idSac
                ) c
            WHERE
                c.nbEmplacement = 0
        `);
        for(const item of dbQuery)
        {
            await fonctionsDelete.sacsDelete(req.verifyJWTandProfile.idPersonne, item.idSac);
        }

        await fonctionsMetiers.checkAllConf();

        logger.info("Fin de l'action massive action32");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action33 = async (req, res) => {
    /*Supprimer tous les sacs et leurs emplacements et leur matériel dès lors que les sacs ne sont pas rattachés à un lot*/
    try {
        logger.info("Lancement de l'action massive action33");
        
        let sacs = await db.query(`
            SELECT idSac FROM MATERIEL_SAC WHERE idLot IS NULL;
        `);
        for(const sac of sacs)
        {
            let emplacements = await db.query(`
                SELECT idEmplacement FROM MATERIEL_EMPLACEMENT WHERE idSac = :idSac;
            `,{
                idSac: sac.idSac,
            });
            for(const emplacement of emplacements)
            {
                let materiels = await db.query(`
                    SELECT idElement FROM MATERIEL_ELEMENT WHERE idEmplacement = :idEmplacement;
                `,{
                    idEmplacement: emplacement.idEmplacement,
                });
                for(const materiel of materiels)
                {
                    await fonctionsDelete.materielsDelete(req.verifyJWTandProfile.idPersonne, materiel.idElement);
                }

                await fonctionsDelete.emplacementsDelete(req.verifyJWTandProfile.idPersonne, emplacement.idEmplacement);
            }

            await fonctionsDelete.sacsDelete(req.verifyJWTandProfile.idPersonne, sac.idSac);
        }

        logger.info("Fin de l'action massive action33");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action41 = async (req, res) => {
    /*Supprimer tous les lots qui ne contiennent pas de sac*/
    try {
        logger.info("Lancement de l'action massive action41");
        
        let dbQuery = await db.query(`
            SELECT
                c.idLot
            FROM
                (
                SELECT
                    em.idLot,
                    COUNT(el.idSac) as nbSacs
                FROM
                    LOTS_LOTS em
                    LEFT OUTER JOIN MATERIEL_SAC el ON em.idLot = el.idLot
                GROUP BY em.idLot
                ) c
            WHERE
                c.nbSacs = 0
        `);
        for(const item of dbQuery)
        {
            await fonctionsDelete.lotsDelete(req.verifyJWTandProfile.idPersonne, item.idLot);
        }

        logger.info("Fin de l'action massive action41");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}

exports.action51 = async (req, res) => {
    /*Supprimer tous les lots qui ne contiennent pas de sac*/
    try {
        logger.info("Lancement de l'action massive action51");
        
        await fonctionsMetiers.cleanPersonnesExternes();

        logger.info("Fin de l'action massive action51");
        res.sendStatus(201);
    } catch (error) {
        logger.error(error)
        res.sendStatus(500);
    }
}
