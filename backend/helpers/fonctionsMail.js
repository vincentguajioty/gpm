const db = require('../db');
const dotenv = require('dotenv').config();
const logger = require('../winstonLogger');
const fonctionsMetiers = require('./fonctionsMetiers');
const fs = require('fs');

const hbs = require('nodemailer-express-handlebars');
const nodemailer = require('nodemailer');
const path = require('path');
const hbsHelpers = require('./handlebarsHelpers');

const handlebarOptions = {
    viewEngine: {
        partialsDir: path.resolve('./templates/'),
        defaultLayout: false,
        helpers: hbsHelpers,
    },
    viewPath: path.resolve('./templates/'),
};

const getTransporter = () => {
    let transporterOptions = {
        host: process.env.SMTP_SERVER,
        port: process.env.SMTP_PORT,
        secure: process.env.SMTP_SECURE == 1 ? true : false,
        auth: {
            user: process.env.SMTP_USER,
            pass: process.env.SMTP_PASSWORD,
        },
    };

    if(process.env.DKIM_ENABLED && process.env.DKIM_ENABLED == true)
    {
        transporterOptions.dkim = {
            domainName: process.env.DKIM_DOMAIN,
            keySelector: process.env.DKIM_KEYSELECTOR,
            privateKey: fs.readFileSync('./privateDKIM.pem', "utf8"),
        }
    }

    return nodemailer.createTransport(transporterOptions);
}

/* --------- FONCTIONS LOCALES CAS PAR CAS --------- */

const autoResetPwd = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get data for the email
        const users = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_REFERENTE
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: requestInfo.idPersonne,
        });

        const resetQuery = await db.query(`
            SELECT
                *
            FROM
                RESETPASSWORD
            WHERE
                idReset = :idReset
        `,{
            idReset: requestInfo.idObject,
        });
        
        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        for (const personne of users) {
            mailOptions = {
                from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                cc: configDB.mailcopy ? configDB.mailserver : null,
                subject: '['+process.env.APP_NAME+'] Terminer la réinitialisation de votre mot de passe',
                template: 'autoResetPwd', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    resetQuery: resetQuery[0],
                    appname: process.env.APP_NAME,
                    urlsite: configDB.urlsite,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:'+personne.idUtilisateur,
                        comment: 'Ne plus recevoir de mails',
                    },
                },
            };
            logger.debug(mailOptions);
    
            // trigger the sending of the E-mail
            const sendMailResult = await transporter.sendMail(mailOptions);
            if(sendMailResult.rejected.length == 0)
            {
                logger.debug(sendMailResult);
            }
            else
            {
                logger.error(error);
                emailErrors += 1;
            }
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const resetPassword = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get data for the email
        const users = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_REFERENTE
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: requestInfo.idPersonne,
        });

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        for (const personne of users) {
            mailOptions = {
                from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                cc: configDB.mailcopy ? configDB.mailserver : null,
                subject: '['+process.env.APP_NAME+'] Réinitialisation de votre mot de passe',
                template: 'resetPassword', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    appname: process.env.APP_NAME,
                    urlsite: configDB.urlsite,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:'+personne.idUtilisateur,
                        comment: 'Ne plus recevoir de mails',
                    },
                },
            };
            logger.debug(mailOptions);
    
            // trigger the sending of the E-mail
            const sendMailResult = await transporter.sendMail(mailOptions);
            if(sendMailResult.rejected.length == 0)
            {
                logger.debug(sendMailResult);
            }
            else
            {
                logger.error(error);
                emailErrors += 1;
            }
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const confirmationAlerteLot = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        mailOptions = {
            from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
            to: requestInfo.otherMail, // list of receivers
            cc: configDB.mailcopy ? configDB.mailserver : null,
            subject: '['+process.env.APP_NAME+'] Alerte sur un lot opérationnel',
            template: 'confirmationAlerteLot', // the name of the template file i.e email.handlebars
            context:{
                appname: process.env.APP_NAME,
                urlsite: configDB.urlsite,
            },
            list: {
                unsubscribe: {
                    url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:0',
                    comment: 'Ne plus recevoir de mails',
                },
            },
        };
        logger.debug(mailOptions);

        // trigger the sending of the E-mail
        const sendMailResult = await transporter.sendMail(mailOptions);
        if(sendMailResult.rejected.length == 0)
        {
            logger.debug(sendMailResult);
        }
        else
        {
            logger.error(error);
            emailErrors += 1;
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const alerteBenevolesLot = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get data for the email
        const users = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_REFERENTE
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: requestInfo.idPersonne,
        });

        let detailsAlerte = await db.query(`
            SELECT
                a.*,
                e.libelleLotsAlertesEtat,
                e.couleurLotsAlertesEtat,
                v.libelleLot,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                LOTS_ALERTES a
                LEFT OUTER JOIN LOTS_ALERTES_ETATS e ON a.idLotsAlertesEtat = e.idLotsAlertesEtat
                LEFT OUTER JOIN LOTS_LOTS v ON a.idLot = v.idLot
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
            WHERE
                a.idAlerte = :idAlerte
        `,{
            idAlerte: requestInfo.idObject,
        });
        detailsAlerte = detailsAlerte[0];
        
        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        for (const personne of users) {
            mailOptions = {
                from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                cc: configDB.mailcopy ? configDB.mailserver : null,
                subject: '['+process.env.APP_NAME+'] Alerte sur '+detailsAlerte.libelleLot,
                template: 'alerteBenevolesLot', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    detailsAlerte: detailsAlerte,
                    appname: process.env.APP_NAME,
                    urlsite: configDB.urlsite,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:'+personne.idUtilisateur,
                        comment: 'Ne plus recevoir de mails',
                    },
                },
            };
            logger.debug(mailOptions);
    
            // trigger the sending of the E-mail
            const sendMailResult = await transporter.sendMail(mailOptions);
            if(sendMailResult.rejected.length == 0)
            {
                logger.debug(sendMailResult);
            }
            else
            {
                logger.error(error);
                emailErrors += 1;
            }
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const confirmationAlerteVehicule = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        mailOptions = {
            from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
            to: requestInfo.otherMail, // list of receivers
            cc: configDB.mailcopy ? configDB.mailserver : null,
            subject: '['+process.env.APP_NAME+'] Alerte sur un véhicule',
            template: 'confirmationAlerteVehicule', // the name of the template file i.e email.handlebars
            context:{
                appname: process.env.APP_NAME,
                urlsite: configDB.urlsite,
            },
            list: {
                unsubscribe: {
                    url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:0',
                    comment: 'Ne plus recevoir de mails',
                },
            },
        };
        logger.debug(mailOptions);

        // trigger the sending of the E-mail
        const sendMailResult = await transporter.sendMail(mailOptions);
        if(sendMailResult.rejected.length == 0)
        {
            logger.debug(sendMailResult);
        }
        else
        {
            logger.error(error);
            emailErrors += 1;
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const alerteBenevolesVehicule = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get data for the email
        const users = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_REFERENTE
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: requestInfo.idPersonne,
        });

        let detailsAlerte = await db.query(`
            SELECT
                a.*,
                e.libelleVehiculesAlertesEtat,
                e.couleurVehiuclesAlertesEtat,
                v.libelleVehicule,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                VEHICULES_ALERTES a
                LEFT OUTER JOIN VEHICULES_ALERTES_ETATS e ON a.idVehiculesAlertesEtat = e.idVehiculesAlertesEtat
                LEFT OUTER JOIN VEHICULES v ON a.idVehicule = v.idVehicule
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
            WHERE
                a.idAlerte = :idAlerte
        `,{
            idAlerte: requestInfo.idObject,
        });
        detailsAlerte = detailsAlerte[0];
        
        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        for (const personne of users) {
            mailOptions = {
                from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                cc: configDB.mailcopy ? configDB.mailserver : null,
                subject: '['+process.env.APP_NAME+'] Alerte sur '+detailsAlerte.libelleVehicule,
                template: 'alerteBenevolesVehicule', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    detailsAlerte: detailsAlerte,
                    appname: process.env.APP_NAME,
                    urlsite: configDB.urlsite,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:'+personne.idUtilisateur,
                        comment: 'Ne plus recevoir de mails',
                    },
                },
            };
            logger.debug(mailOptions);
    
            // trigger the sending of the E-mail
            const sendMailResult = await transporter.sendMail(mailOptions);
            if(sendMailResult.rejected.length == 0)
            {
                logger.debug(sendMailResult);
            }
            else
            {
                logger.error(error);
                emailErrors += 1;
            }
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const finDeclarationConso = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get data for the email
        const users = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_REFERENTE
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: requestInfo.idPersonne,
        });

        //infos générales sur la déclaration
        const consommation = await db.query(`
            SELECT
                *
            FROM
                LOTS_CONSOMMATION
            WHERE
                idConsommation = :idConsommation
        `,{
            idConsommation: requestInfo.idObject,
        });
        const qttMaterielsReconditionne = await db.query(`
            SELECT
                COUNT(idConsommationMateriel) as nb
            FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idConsommation = :idConsommation
                AND
                idConteneur IS NOT NULL
        `,{
            idConsommation: requestInfo.idObject
        });
        consommation[0].qttMaterielsReconditionne = qttMaterielsReconditionne[0].nb;
        const qttMaterielsNonReconditionne = await db.query(`
            SELECT
                COUNT(idConsommationMateriel) as nb
            FROM
                LOTS_CONSOMMATION_MATERIEL
            WHERE
                idConsommation = :idConsommation
                AND
                idConteneur IS NULL
        `,{
            idConsommation: requestInfo.idObject
        });
        consommation[0].qttMaterielsNonReconditionne = qttMaterielsNonReconditionne[0].nb;

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        for (const personne of users) {
            mailOptions = {
                from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                cc: configDB.mailcopy ? configDB.mailserver : null,
                subject: '['+process.env.APP_NAME+'] Consommation lors de '+consommation[0].evenementConsommation,
                template: 'finDeclarationConso', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    consommation: consommation[0],
                    appname: process.env.APP_NAME,
                    urlsite: configDB.urlsite,
                    consommation_benevoles_auto: configDB.consommation_benevoles_auto == true ? 1 : 0,
                    cc: configDB.mailcopy ? configDB.mailserver : null,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:'+personne.idUtilisateur,
                        comment: 'Ne plus recevoir de mails',
                    },
                },
            };
            logger.debug(mailOptions);
    
            // trigger the sending of the E-mail
            const sendMailResult = await transporter.sendMail(mailOptions);
            if(sendMailResult.rejected.length == 0)
            {
                logger.debug(sendMailResult);
            }
            else
            {
                logger.error(error);
                emailErrors += 1;
            }
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const notifJournaliere = async (requestInfo) => {
try {
    // use a template file with nodemailer
    let configDB = await db.query(
        `SELECT * FROM CONFIG;`
    );
    configDB = configDB[0]

    const transporter = getTransporter();
    transporter.use('compile', hbs(handlebarOptions))

    //get data for the email
    const users = await db.query(`
        SELECT
            *
        FROM
            PERSONNE_REFERENTE
        WHERE
            idPersonne = :idPersonne
    `,{
        idPersonne: requestInfo.idPersonne,
    });

    //get users and send the mail to each one      
    let mailOptions={};
    let emailErrors = 0;
    for (const personne of users) {
        // notif_lots_manquants
        let lots_manquants = [];
        if(personne.notif_lots_manquants)
        {
            lots_manquants = await db.query(`
                SELECT
                    l.libelleLot,
                    c.libelleMateriel
                FROM
                    MATERIEL_ELEMENT m
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                    LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                    LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                    LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON l.idNotificationEnabled = notif.idNotificationEnabled
                WHERE
                    (quantite < quantiteAlerte OR quantite = quantiteAlerte)
                    AND
                    notifiationEnabled = true
            ;`);
        }

        // notif_lots_peremptions
        let lots_peremptions = [];
        if(personne.notif_lots_peremptions)
        {
            lots_peremptions = await db.query(`
                SELECT
                    l.libelleLot,
                    c.libelleMateriel
                FROM
                    MATERIEL_ELEMENT m
                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                    LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
                    LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
                    LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                    LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON l.idNotificationEnabled = notif.idNotificationEnabled
                WHERE
                    (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE)
                    AND
                    notifiationEnabled = true
            ;`);
        }

        // notif_lots_inventaires
        let lots_inventaires = [];
        if(personne.notif_lots_inventaires)
        {
            lots_inventaires = await db.query(`
                SELECT
                    libelleLot
                FROM
                    LOTS_LOTS l
                    LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON l.idNotificationEnabled = notif.idNotificationEnabled
                WHERE
                    notifiationEnabled = true
                    AND
                    (frequenceInventaire IS NOT NULL)
                    AND
                    (
                        (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE)
                        OR
                        (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE)
                    )
            ;`);
        }

        // notif_lots_conformites
        let lots_conformites = [];
        if(personne.notif_lots_conformites)
        {
            lots_conformites = await db.query(`
                SELECT
                    l.libelleLot
                FROM
                    LOTS_LOTS l
                    LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON l.idNotificationEnabled = notif.idNotificationEnabled
                WHERE
                    alerteConfRef = 1
                    AND
                    notifiationEnabled = true
            ;`);
        }

        // notif_reserves_manquants
        let reserves_manquants = [];
        if(personne.notif_reserves_manquants)
        {
            reserves_manquants = await db.query(`
                SELECT
                    c.libelleConteneur,
                    r.libelleMateriel
                FROM
                    RESERVES_MATERIEL m
                    LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur
                    LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue
                WHERE
                    quantiteReserve < quantiteAlerteReserve
                    OR
                    quantiteReserve = quantiteAlerteReserve
            ;`);
        }

        // notif_reserves_peremptions
        let reserves_peremptions = [];
        if(personne.notif_reserves_peremptions)
        {
            reserves_peremptions = await db.query(`
                SELECT
                    c.libelleConteneur,
                    r.libelleMateriel
                FROM
                    RESERVES_MATERIEL m
                    LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur
                    LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue
                WHERE
                    peremptionNotificationReserve < CURRENT_DATE
                    OR
                    peremptionNotificationReserve = CURRENT_DATE
            ;`);
        }

        // notif_reserves_inventaires
        let reserves_inventaires = [];
        if(personne.notif_reserves_inventaires)
        {
            reserves_inventaires = await db.query(`
                SELECT
                    libelleConteneur
                FROM
                    RESERVES_CONTENEUR
                WHERE
                    (frequenceInventaire IS NOT NULL)
                    AND
                    (
                        (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE)
                        OR
                        (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE)
                    )
            ;`);
        }

        // notif_vehicules_desinfections
        let vehicules_desinfections = [];
        if(personne.notif_vehicules_desinfections)
        {
            vehicules_desinfections = await db.query(`
                SELECT
                    v.libelleVehicule
                FROM
                    VEHICULES v
                    LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON v.idNotificationEnabled = notif.idNotificationEnabled
                WHERE
                    notifiationEnabled = true
                    AND 
                    alerteDesinfection = 1
            ;`);
        }

        // notif_vehicules_health
        let vehicules_health = [];
        if(personne.notif_vehicules_health)
        {
            vehicules_health = await db.query(`
                SELECT
                    v.libelleVehicule
                FROM
                    VEHICULES v
                    LEFT OUTER JOIN NOTIFICATIONS_ENABLED notif ON v.idNotificationEnabled = notif.idNotificationEnabled
                WHERE
                    notifiationEnabled = true
                    AND 
                    alerteMaintenance = 1
            `);
        }

        // notif_tenues_stock
        let tenues_stock = [];
        if(personne.notif_tenues_stock)
        {
            tenues_stock = await db.query(`
                SELECT
                    libelleCatalogueTenue
                FROM
                    TENUES_CATALOGUE
                WHERE
                    stockCatalogueTenue < stockAlerteCatalogueTenue
                    OR
                    stockCatalogueTenue = stockAlerteCatalogueTenue
            ;`);
        }

        // notif_tenues_retours
        let tenues_retours = [];
        if(personne.notif_tenues_retours)
        {
            tenues_retours = await db.query(`
                SELECT
                    nomPersonne,
                    prenomPersonne,
                    personneNonGPM,    
                    libelleCatalogueTenue
                FROM
                    TENUES_AFFECTATION ta
                    JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue
                    LEFT OUTER JOIN PERSONNE_REFERENTE p ON ta.idPersonne = p.idPersonne
                WHERE
                    dateRetour < CURRENT_DATE
                    OR
                    dateRetour = CURRENT_DATE
            ;`);
        }

        let qttAlerte = lots_manquants.length + lots_peremptions.length + lots_inventaires.length + lots_conformites.length + reserves_manquants.length + reserves_peremptions.length + reserves_inventaires.length + vehicules_desinfections.length + vehicules_health.length + tenues_stock.length + tenues_retours.length;

        if(qttAlerte > 0)
        {
            mailOptions = {
                from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                cc: configDB.mailcopy ? configDB.mailserver : null,
                subject: '['+process.env.APP_NAME+'] Bilan journalier ',
                template: 'notifJournaliere', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    appname: process.env.APP_NAME,
                    urlsite: configDB.urlsite,
                    qttAlerte : qttAlerte,
                    lots_manquants: lots_manquants,
                    lots_peremptions: lots_peremptions,
                    lots_inventaires: lots_inventaires,
                    lots_conformites: lots_conformites,
                    reserves_manquants: reserves_manquants,
                    reserves_peremptions: reserves_peremptions,
                    reserves_inventaires: reserves_inventaires,
                    vehicules_desinfections: vehicules_desinfections,
                    vehicules_health: vehicules_health,
                    tenues_stock: tenues_stock,
                    tenues_retours: tenues_retours,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:'+personne.idUtilisateur,
                        comment: 'Ne plus recevoir de mails',
                    },
                },
            };
            logger.debug(mailOptions);
    
            // trigger the sending of the E-mail
            const sendMailResult = await transporter.sendMail(mailOptions);
            if(sendMailResult.rejected.length == 0)
            {
                logger.debug(sendMailResult);
            }
            else
            {
                logger.error(error);
                emailErrors += 1;
            }
        }
    }

    if(emailErrors == 0)
    {
        return true;
    }
    else
    {
        return false;
    }
} catch (error) {
    logger.error(error);
    return false;
}
}

const contactDev = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        mailOptions = {
            from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
            to: requestInfo.otherMail, // list of receivers
            cc: configDB.mailcopy ? configDB.mailserver : null,
            subject: '[GPM]['+process.env.APP_NAME+'] Contact Developpeur depuis une app cliente',
            template: 'contactDev', // the name of the template file i.e email.handlebars
            context:{
                appname: process.env.APP_NAME,
                urlsite: configDB.urlsite,
                content: requestInfo.otherContent
            },
            list: {
                unsubscribe: {
                    url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:0',
                    comment: 'Ne plus recevoir de mails',
                },
            },
        };
        logger.debug(mailOptions);

        // trigger the sending of the E-mail
        const sendMailResult = await transporter.sendMail(mailOptions);
        if(sendMailResult.rejected.length == 0)
        {
            logger.debug(sendMailResult);
        }
        else
        {
            logger.error(error);
            emailErrors += 1;
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const mailDeGroupe = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get data for the email
        const users = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_REFERENTE
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: requestInfo.idPersonne,
        });

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        for (const personne of users) {
            mailOptions = {
                from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                cc: configDB.mailcopy ? configDB.mailserver : null,
                subject: '['+process.env.APP_NAME+'] Mail de groupe: ' + requestInfo.otherSubject,
                template: 'mailDeGroupe', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    appname: process.env.APP_NAME,
                    urlsite: configDB.urlsite,
                    otherContent: requestInfo.otherContent,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:'+personne.idUtilisateur,
                        comment: 'Ne plus recevoir de mails',
                    },
                },
            };
            logger.debug(mailOptions);
    
            // trigger the sending of the E-mail
            const sendMailResult = await transporter.sendMail(mailOptions);
            if(sendMailResult.rejected.length == 0)
            {
                logger.debug(sendMailResult);
            }
            else
            {
                logger.error(error);
                emailErrors += 1;
            }
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const commandeNotif = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get data for the email
        const users = await db.query(`
            SELECT
                *
            FROM
                PERSONNE_REFERENTE
            WHERE
                idPersonne = :idPersonne
        `,{
            idPersonne: requestInfo.idPersonne,
        });
        
        let commande = await db.query(`
            SELECT
                c.*,
                f.nomFournisseur,
                couts.libelleCentreDecout
            FROM
                COMMANDES c
                LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = c.idFournisseur
                LEFT OUTER JOIN CENTRE_COUTS couts ON couts.idCentreDeCout = c.idCentreDeCout
            WHERE
                idCommande = :idCommande
        `,{
            idCommande: requestInfo.idObject,
        });
        commande = commande[0];

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        for (const personne of users) {
            mailOptions = {
                from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                cc: configDB.mailcopy ? configDB.mailserver : null,
                subject: '['+process.env.APP_NAME+'][COMMANDE] ' + requestInfo.otherSubject,
                template: 'commandeNotif', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    appname: process.env.APP_NAME,
                    urlsite: configDB.urlsite,
                    message: requestInfo.otherContent,
                    commande: commande,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:'+personne.idUtilisateur,
                        comment: 'Ne plus recevoir de mails',
                    },
                },
            };
            logger.debug(mailOptions);
    
            // trigger the sending of the E-mail
            const sendMailResult = await transporter.sendMail(mailOptions);
            if(sendMailResult.rejected.length == 0)
            {
                logger.debug(sendMailResult);
            }
            else
            {
                logger.error(error);
                emailErrors += 1;
            }
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const tenuesAffectation = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        mailOptions = {
            from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
            to: requestInfo.otherMail, // list of receivers
            cc: configDB.mailcopy ? configDB.mailserver : null,
            subject: '['+process.env.APP_NAME+'] Affectation d\'une tenue',
            template: 'tenuesAffectation', // the name of the template file i.e email.handlebars
            context:{
                appname: process.env.APP_NAME,
                urlsite: configDB.urlsite,
                tableauTenues: requestInfo.otherContent,
            },
            list: {
                unsubscribe: {
                    url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:0',
                    comment: 'Ne plus recevoir de mails',
                },
            },
        };
        logger.debug(mailOptions);

        // trigger the sending of the E-mail
        const sendMailResult = await transporter.sendMail(mailOptions);
        if(sendMailResult.rejected.length == 0)
        {
            logger.debug(sendMailResult);
        }
        else
        {
            logger.error(error);
            emailErrors += 1;
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const tenuesRetourAnticipation = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        mailOptions = {
            from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
            to: requestInfo.otherMail, // list of receivers
            cc: configDB.mailcopy ? configDB.mailserver : null,
            subject: '['+process.env.APP_NAME+'] Pensez au retour de votre tenue',
            template: 'tenuesRetourAnticipation', // the name of the template file i.e email.handlebars
            context:{
                appname: process.env.APP_NAME,
                urlsite: configDB.urlsite,
                tableauTenues: requestInfo.otherContent,
            },
            list: {
                unsubscribe: {
                    url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:0',
                    comment: 'Ne plus recevoir de mails',
                },
            },
        };
        logger.debug(mailOptions);

        // trigger the sending of the E-mail
        const sendMailResult = await transporter.sendMail(mailOptions);
        if(sendMailResult.rejected.length == 0)
        {
            logger.debug(sendMailResult);
        }
        else
        {
            logger.error(error);
            emailErrors += 1;
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}

const tenuesRetourRetard = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = getTransporter();
        transporter.use('compile', hbs(handlebarOptions))

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        mailOptions = {
            from: process.env.APP_NAME+' <'+process.env.SMTP_USER+'>', // sender address
            to: requestInfo.otherMail, // list of receivers
            cc: configDB.mailcopy ? configDB.mailserver : null,
            subject: '['+process.env.APP_NAME+'] RAPPEL - Retour d\'une tenue',
            template: 'tenuesRetourRetard', // the name of the template file i.e email.handlebars
            context:{
                appname: process.env.APP_NAME,
                urlsite: configDB.urlsite,
                tableauTenues: requestInfo.otherContent,
            },
            list: {
                unsubscribe: {
                    url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+process.env.APP_NAME+'-forUser:0',
                    comment: 'Ne plus recevoir de mails',
                },
            },
        };
        logger.debug(mailOptions);

        // trigger the sending of the E-mail
        const sendMailResult = await transporter.sendMail(mailOptions);
        if(sendMailResult.rejected.length == 0)
        {
            logger.debug(sendMailResult);
        }
        else
        {
            logger.error(error);
            emailErrors += 1;
        }

        if(emailErrors == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } catch (error) {
        logger.error(error);
        return false;
    }
}


/* --------- FONCTIONS EXPORTEES --------- */

const sendMailQueue = async () => {
    try {
        const updateAccordingRetries = await db.query(`
            UPDATE
                MAIL_QUEUE
            SET
                abordSend = CURRENT_TIMESTAMP
            WHERE
                abordSend IS NULL
                AND
                successSend IS NULL
                AND
                retryCounter >= :MAX_RETRY
        ;`,{
            MAX_RETRY: process.env.MAX_RETRY,
        });
        
        const mailQueue = await db.query(`
            SELECT
                *
            FROM
                MAIL_QUEUE
            WHERE
                abordSend IS NULL
                AND
                successSend IS NULL
        ;`);
        for(const mailNeeded of mailQueue)
        {
            let successCheck;
            switch (mailNeeded.typeMail) {
                case 'autoResetPwd':
                    successCheck = await autoResetPwd(mailNeeded);
                break;

                case 'resetPassword':
                    successCheck = await resetPassword(mailNeeded);
                break;

                case 'confirmationAlerteLot':
                    successCheck = await confirmationAlerteLot(mailNeeded);
                break;

                case 'alerteBenevolesLot':
                    successCheck = await alerteBenevolesLot(mailNeeded);
                break;

                case 'confirmationAlerteVehicule':
                    successCheck = await confirmationAlerteVehicule(mailNeeded);
                break;

                case 'alerteBenevolesVehicule':
                    successCheck = await alerteBenevolesVehicule(mailNeeded);
                break;

                case 'finDeclarationConso':
                    successCheck = await finDeclarationConso(mailNeeded);
                break;

                case 'notifJournaliere':
                    successCheck = await notifJournaliere(mailNeeded);
                break;

                case 'contactDev':
                    successCheck = await contactDev(mailNeeded);
                break;

                case 'mailDeGroupe':
                    successCheck = await mailDeGroupe(mailNeeded);
                break;

                case 'commandeNotif':
                    successCheck = await commandeNotif(mailNeeded);
                break;

                case 'tenuesAffectation':
                    successCheck = await tenuesAffectation(mailNeeded);
                break;

                case 'tenuesRetourAnticipation':
                    successCheck = await tenuesRetourAnticipation(mailNeeded);
                break;

                case 'tenuesRetourRetard':
                    successCheck = await tenuesRetourRetard(mailNeeded);
                break;

            
                default:
                    logger.warn('Mail '+mailNeeded.idMailQueue+' ne peut pas être envoyé pour cause d\'erreur dans le type de mail')
                    successCheck = false;
                    break;
            }

            if(successCheck == true)
            {
                const clean = await db.query(`UPDATE MAIL_QUEUE SET successSend=CURRENT_TIMESTAMP WHERE idMailQueue = :idMailQueue;`,{idMailQueue: mailNeeded.idMailQueue});
            }
            else
            {
                const clean = await db.query(`UPDATE MAIL_QUEUE SET lastTry=CURRENT_TIMESTAMP, retryCounter = retryCounter + 1 WHERE idMailQueue = :idMailQueue;`,{idMailQueue: mailNeeded.idMailQueue});
            }
        }
    } catch (error) {
        logger.error(error);
    }
}

const registerToMailQueue = async ({
    typeMail,
    idObject,
    idPersonne,
    otherMail,
    otherSubject,
    otherContent,
}) => {
    try {
        const updateAccordingRetries = await db.query(`
            INSERT INTO
                MAIL_QUEUE
            SET
                typeMail = :typeMail,
                idObject = :idObject,
                idPersonne = :idPersonne,
                otherMail = :otherMail,
                otherSubject = :otherSubject,
                otherContent = :otherContent,
                lastTry = null,
                sendRequest = CURRENT_TIMESTAMP,
                successSend = null,
                abordSend = null,
                retryCounter = 0
        ;`,{
            typeMail: typeMail || 'ERROR',
            idObject: idObject || null,
            idPersonne: idPersonne || null,
            otherMail: otherMail || null,
            otherSubject: otherSubject || null,
            otherContent: otherContent || null,
        });
    } catch (error) {
        logger.error(error)
    }
}

const clearMailQueue = async () => {
    try {
        const updateAccordingRetries = await db.query(`
            UPDATE
                MAIL_QUEUE
            SET
                abordSend = CURRENT_TIMESTAMP
            WHERE
                abordSend IS NULL
                AND
                successSend IS NULL
        ;`);
    } catch (error) {
        logger.error(error)
    }
}

module.exports = {
    registerToMailQueue,
    sendMailQueue,
    clearMailQueue,
};
