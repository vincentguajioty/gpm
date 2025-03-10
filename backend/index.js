const express = require('express');
const app = express();
const http = require('http').Server(app);
const processMAJ = require('./processMAJ');
const dotenv = require('dotenv').config();
const fonctionsMetiers = require('./helpers/fonctionsMetiers')
const fonctionsMail = require('./helpers/fonctionsMail')
const fonctionsLDAP = require('./helpers/fonctionsLDAP')
const fonctionsSocketIO = require('./socketIO.js');

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
        logger.info(`Server listening on `+3001);

        fonctionsSocketIO.socketInterface(http);
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
    if(http.listening)
    {
        logger.debug('Lancement cron de dépilement des emails en queue');

        if(process.env.LOCK_ALL_MAIL == 0)
        {
            logger.debug('Autorisation accordée dans la conf pour envoyer les mails');
            await fonctionsMail.sendMailQueue();
        }
        else
        {
            logger.debug('Autorisation refusée dans la conf pour envoyer les mails, aucun traitement lancé');
            await fonctionsMail.clearMailQueue();
        }
        logger.debug('Fin cron de dépilement des emails en queue');
    }
});

//Comptabilisation des rapports de consommation en auto
schedule.scheduleJob(process.env.CRON_CONSOMMATIONS_AUTO, async function() {
    if(http.listening)
    {
        logger.debug('Lancement cron de comptabilisation de consommations en auto');
        await fonctionsMetiers.comptabiliserToutesConsommations();
        logger.debug('Fin cron de comptabilisation de consommations en auto');
    }
});

//Cron journalier
schedule.scheduleJob(process.env.CRON_DAILY, async function() {
    if(http.listening)
    {
        logger.info('CRON - Début du CRON');

        //Nettoyage des fichiers temporaires
        fonctionsMetiers.cleanTempFolder();

        //Mise à jour des users AD
        logger.debug("CRON - Début de la mise à jour des users AD");
        await fonctionsLDAP.updateAllUsersFromAD();
        logger.debug("CRON - Fin de la mise à jour des users AD");

        //Kill de tous les tokens pour utilisateurs sans profils
        logger.debug("CRON - Début de suppression de token pour utilisateurs sans profils");
        await fonctionsLDAP.killTokensForNoProfils();
        logger.debug("CRON - Fin de suppression de token pour utilisateurs sans profils");

        //Mise à jour des anticipations de péremption
        logger.debug("CRON - Début de la mise à jour des anticipations de péremption et péremptions à quantités nulles");
        await fonctionsMetiers.updatePeremptionsAnticipations();
        logger.debug("CRON - Fin de la mise à jour des anticipations de péremption et péremptions à quantités nulles");

        //Analyse complète des lots
        logger.debug("CRON - Début de la vérification de conformité de tous les lots");
        await fonctionsMetiers.checkAllConf();
        logger.debug("CRON - Fin de la vérification de conformité de tous les lots");

        //Analyse complète des désinfections de véhicules
        logger.debug("CRON - Début de la vérification des désinfections de tous les véhicules");
        await fonctionsMetiers.checkAllDesinfection();
        logger.debug("CRON - Fin de la vérification des désinfections de tous les véhicule");

        //Analyse complète des maintenances de véhicules
        logger.debug("CRON - Début de la vérification des maintenances de tous les véhicules");
        await fonctionsMetiers.checkAllMaintenance();
        logger.debug("CRON - Fin de la vérification des maintenances de tous les véhicule");

        //Clean table de reset des tokens
        logger.debug("CRON - Vidage de la table de token de reset de mots de passe");
        await fonctionsMetiers.clearPwdReinitTable();

        //CNIL - Anonymisation des comptes qui doivent l'être
        logger.debug("CRON - Début de l'anonymisation des comptes CNIL");
        await fonctionsMetiers.cnilAnonymeCron();
        logger.debug("CRON - Fin de l'anonymisation des comptes CNIL");

        //Mise à jour des conditions de notifications et envoi de la notif journaliere
        logger.debug("CRON - Début de la vérification des conditions de notification et envoi de la notif");
        await fonctionsMetiers.notificationsConditionsMAJ();
        await fonctionsMetiers.notificationsMAJpersonne();
        await fonctionsMetiers.queueNotificationJournaliere();
        logger.debug("CRON - Fin de la vérification des conditions de notification et envoi de la notif");

        //Envoi des emails relatifs aux tenues
        logger.debug("CRON - Début d'envoi des notifications tenues");
        await fonctionsMetiers.envoyerNotificationsTenuesBenevoles();
        logger.debug("CRON - Fin d'envoi des notifications tenues");
        
        logger.info('CRON - Fin du CRON');
    }
});
