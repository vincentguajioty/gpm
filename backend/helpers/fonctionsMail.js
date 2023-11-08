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


const sendMailQueue = async () => {
    try {
        const mailQueue = await db.query(`SELECT * FROM MAIL_QUEUE;`);
        for(const mailNeeded of mailQueue)
        {
            let successCheck;
            switch (mailNeeded.typeMail) {
                
                // case 'autoResetPwd':
                //     successCheck = await autoResetPwd(mailNeeded.idObject, mailNeeded.idSecondaire);
                // break;
            
                default:
                    logger.warn('Mail '+mailNeeded.idMailQueue+' ne peut pas être envoyé pour cause d\'erreur dans le type de mail', {idUtilisateur: 'SYSTEM'})
                    successCheck = false;
                    break;
            }

            if(successCheck == true)
            {
                const clean = await db.query(`DELETE FROM MAIL_QUEUE WHERE idMailQueue = :idMailQueue;`,{idMailQueue: mailNeeded.idMailQueue});
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

const clearMailQueue = async () => {
    try {
        const mailQueue = await db.query(`TRUNCATE MAIL_QUEUE;`);
    } catch (error) {
        logger.error(error)
    }
}

module.exports = {
    sendMailQueue,
    clearMailQueue,
};
