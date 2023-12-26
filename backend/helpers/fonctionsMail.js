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

const transporterWithDKIM = nodemailer.createTransport(
    {
        host: process.env.SMTP_SERVER,
        port: process.env.SMTP_PORT,
        secure: process.env.SMTP_SECURE == 1 ? true : false,
        auth: {
            user: process.env.SMTP_USER,
            pass: process.env.SMTP_PASSWORD,
        },
        dkim: {
            domainName: process.env.DKIM_DOMAIN,
            keySelector: process.env.DKIM_KEYSELECTOR,
            privateKey: fs.readFileSync('./privateDKIM.pem', "utf8"),
        }
    }
);

const transporterWithoutDKIM = nodemailer.createTransport(
    {
        host: process.env.SMTP_SERVER,
        port: process.env.SMTP_PORT,
        secure: process.env.SMTP_SECURE == 1 ? true : false,
        auth: {
            user: process.env.SMTP_USER,
            pass: process.env.SMTP_PASSWORD,
        },
    }
);

const autoResetPwd = async (requestInfo) => {
    try {
        // use a template file with nodemailer
        let configDB = await db.query(
            `SELECT * FROM CONFIG;`
        );
        configDB = configDB[0]

        const transporter = process.env.DKIM_ENABLED && process.env.DKIM_ENABLED == true ? transporterWithDKIM : transporterWithoutDKIM;
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
                from: configDB.appname+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                subject: '['+configDB.appname+'] Terminer la réinitialisation de votre mot de passe',
                template: 'autoResetPwd', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    resetQuery: resetQuery[0],
                    appname: configDB.appname,
                    urlsite: configDB.urlsite,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+configDB.appname+'-forUser:'+personne.idUtilisateur,
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

        const transporter = process.env.DKIM_ENABLED && process.env.DKIM_ENABLED == true ? transporterWithDKIM : transporterWithoutDKIM;
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
                from: configDB.appname+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                subject: '['+configDB.appname+'] Réinitialisation de votre mot de passe',
                template: 'resetPassword', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    appname: configDB.appname,
                    urlsite: configDB.urlsite,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+configDB.appname+'-forUser:'+personne.idUtilisateur,
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

        const transporter = process.env.DKIM_ENABLED && process.env.DKIM_ENABLED == true ? transporterWithDKIM : transporterWithoutDKIM;
        transporter.use('compile', hbs(handlebarOptions))

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        mailOptions = {
            from: configDB.appname+' <'+process.env.SMTP_USER+'>', // sender address
            to: requestInfo.otherMail, // list of receivers
            subject: '['+configDB.appname+'] Alerte sur un lot opérationnel',
            template: 'confirmationAlerteLot', // the name of the template file i.e email.handlebars
            context:{
                appname: configDB.appname,
                urlsite: configDB.urlsite,
            },
            list: {
                unsubscribe: {
                    url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+configDB.appname+'-forUser:0',
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

        const transporter = process.env.DKIM_ENABLED && process.env.DKIM_ENABLED == true ? transporterWithDKIM : transporterWithoutDKIM;
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
                from: configDB.appname+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                subject: '['+configDB.appname+'] Alerte sur '+detailsAlerte.libelleLot,
                template: 'alerteBenevolesLot', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    detailsAlerte: detailsAlerte,
                    appname: configDB.appname,
                    urlsite: configDB.urlsite,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+configDB.appname+'-forUser:'+personne.idUtilisateur,
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

        const transporter = process.env.DKIM_ENABLED && process.env.DKIM_ENABLED == true ? transporterWithDKIM : transporterWithoutDKIM;
        transporter.use('compile', hbs(handlebarOptions))

        //get users and send the mail to each one      
        let mailOptions={};
        let emailErrors = 0;
        mailOptions = {
            from: configDB.appname+' <'+process.env.SMTP_USER+'>', // sender address
            to: requestInfo.otherMail, // list of receivers
            subject: '['+configDB.appname+'] Alerte sur un véhicule',
            template: 'confirmationAlerteVehicule', // the name of the template file i.e email.handlebars
            context:{
                appname: configDB.appname,
                urlsite: configDB.urlsite,
            },
            list: {
                unsubscribe: {
                    url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+configDB.appname+'-forUser:0',
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

        const transporter = process.env.DKIM_ENABLED && process.env.DKIM_ENABLED == true ? transporterWithDKIM : transporterWithoutDKIM;
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
                from: configDB.appname+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                subject: '['+configDB.appname+'] Alerte sur '+detailsAlerte.libelleVehicule,
                template: 'alerteBenevolesVehicule', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    detailsAlerte: detailsAlerte,
                    appname: configDB.appname,
                    urlsite: configDB.urlsite,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+configDB.appname+'-forUser:'+personne.idUtilisateur,
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

        const transporter = process.env.DKIM_ENABLED && process.env.DKIM_ENABLED == true ? transporterWithDKIM : transporterWithoutDKIM;
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
                from: configDB.appname+' <'+process.env.SMTP_USER+'>', // sender address
                to: personne.mailPersonne, // list of receivers
                subject: '['+configDB.appname+'] Consommation lors de '+consommation[0].evenementConsommation,
                template: 'finDeclarationConso', // the name of the template file i.e email.handlebars
                context:{
                    personne: personne,
                    consommation: consommation[0],
                    appname: configDB.appname,
                    urlsite: configDB.urlsite,
                    consommation_benevoles_auto: configDB.consommation_benevoles_auto == true ? 1 : 0,
                },
                list: {
                    unsubscribe: {
                        url: 'mailto:'+process.env.SMTP_USER+'?subject=unsubscribe:'+configDB.appname+'-forUser:'+personne.idUtilisateur,
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
        //TODO
        
        return true;
    } catch (error) {
        logger.error(error);
        return false;
    }
}


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

const registerToMailQueue = async (
    {typeMail,
    idObject,
    idPersonne,
    otherMail,
    otherSubject,
    otherContent,}
) => {
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
