const express = require('express');
const app = express();
const http = require('http').Server(app);
const processMAJ = require('./processMAJ');
const dotenv = require('dotenv').config();
const fonctionsMetiers = require('./fonctionsMetiers')
const fonctionsMail = require('./fonctionsMail')

const logger = require('./winstonLogger');

const routes = require('./routes.js');

const cors = require('cors');

const bodyParser = require('body-parser');
const cookieParser = require('cookie-parser');
const session = require('express-session');

const schedule = require('node-schedule');

app.use(express.json());
app.use(cors({
    origin: [process.env.CORS_ORIGINS],
    methods: ["GET", "POST"],
    credentials: true,
}));
app.use(cookieParser());
app.use(bodyParser.urlencoded({extended:true}));

app.use(session({
    key: "idPersonne",
    secret: process.env.JWT_TOKEN,
    resave: false,
    saveUninitialized: false,
    cookie: {
        expires: parseInt(process.env.JWT_EXPIRATION) //en secondes
    },
}));

app.use('/', routes);

const startServer = () => {
    http.listen(3001, () => {
        logger.info(`Server listening on `+3001, {idPersonne: 'SYSTEM'});
    });
}

const startApp = async () => {
    try {
        if(process.env.LOCK_DB_UPGRADE == 0)
        {
            const readyToStart = await processMAJ.majDB();
            if(readyToStart)
            {
                startServer();
            }
            else
            {
                logger.error('Serveur non-fonctionnel car incident de contrôle/MAJ de la base de données');
                setTimeout(startApp, 10000);
            }
        }
        else
        {
            startServer();
        }
    } catch (error) {
        logger.error(error)
        setTimeout(startApp, 10000);
    }
}

startApp();

//Envoi régulier des emails à récurrence définie dans la config
schedule.scheduleJob(process.env.CRON_MAIL_QUEUE, async function() {
    logger.debug('Lancement cron de dépilement des emails en queue');
    if(process.env.LOCK_ALL_MAIL == 0)
    {
        logger.debug('Autorisation accordée dans la conf pour enoyer les mails');
        await fonctionsMail.sendMailQueue();
    }
    else
    {
        logger.debug('Autorisation refusée dans la conf pour enoyer les mails, aucun traitement lancé');
    }
    logger.debug('Fin cron de dépilement des emails en queue');
});
