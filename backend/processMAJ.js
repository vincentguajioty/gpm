const db = require('./db');
const fs = require('fs');
const logger = require('./winstonLogger');
const fonctionsMetiers = require('./fonctionsMetiers');

const BACKEND_VERSION = '15.0';

const runDBScript = async (fileURL) => {
    try {
        logger.debug(fileURL);
        const query = fs.readFileSync(fileURL, 'utf8').toString();
        logger.debug(query);
        let install = await db.query(query);
        logger.debug(install);
    } catch (error) {
        logger.error(error)
    }
}

const installDB = async () => {
    try {
        logger.info('DB détectée vide, installation de la V14.2 en cours', {idUtilisateur: 'SYSTEM'});
        let install;
        install = await runDBScript('./dbScripts/install1.0.sql');
        install = await runDBScript('./dbScripts/update2.3.sql');
        install = await runDBScript('./dbScripts/update2.4.sql');
        install = await runDBScript('./dbScripts/update2.5.sql');
        install = await runDBScript('./dbScripts/update2.6.sql');
        install = await runDBScript('./dbScripts/update3.0.sql');
        install = await runDBScript('./dbScripts/update3.1.sql');
        install = await runDBScript('./dbScripts/update4.0.sql');
        install = await runDBScript('./dbScripts/update4.1.sql');
        install = await runDBScript('./dbScripts/update4.2.sql');
        install = await runDBScript('./dbScripts/update4.3.sql');
        install = await runDBScript('./dbScripts/update5.0.sql');
        install = await runDBScript('./dbScripts/update5.1.sql');
        install = await runDBScript('./dbScripts/update5.2.sql');
        install = await runDBScript('./dbScripts/update5.3.sql');
        install = await runDBScript('./dbScripts/update5.4.sql');
        install = await runDBScript('./dbScripts/update5.5.sql');
        install = await runDBScript('./dbScripts/update5.6.sql');
        install = await runDBScript('./dbScripts/update5.7.sql');
        install = await runDBScript('./dbScripts/update6.0.sql');
        install = await runDBScript('./dbScripts/update6.1.sql');
        install = await runDBScript('./dbScripts/update6.2.sql');
        install = await runDBScript('./dbScripts/update7.0.sql');
        install = await runDBScript('./dbScripts/update7.1.sql');
        install = await runDBScript('./dbScripts/update7.2.sql');
        install = await runDBScript('./dbScripts/update7.3.sql');
        install = await runDBScript('./dbScripts/update7.4.1.sql');
        install = await runDBScript('./dbScripts/update7.4.2.sql');
        install = await runDBScript('./dbScripts/update7.5.sql');
        install = await runDBScript('./dbScripts/update7.6.sql');
        install = await runDBScript('./dbScripts/update8.0.sql');
        install = await runDBScript('./dbScripts/update8.1.1.sql');
        install = await runDBScript('./dbScripts/update8.1.2.sql');
        install = await runDBScript('./dbScripts/update8.2.sql');
        install = await runDBScript('./dbScripts/update8.3.sql');
        install = await runDBScript('./dbScripts/update8.4.sql');
        install = await runDBScript('./dbScripts/update8.5.sql');
        install = await runDBScript('./dbScripts/update8.6.sql');
        install = await runDBScript('./dbScripts/update9.0.sql');
        install = await runDBScript('./dbScripts/update9.1.sql');
        install = await runDBScript('./dbScripts/update9.2.sql');
        install = await runDBScript('./dbScripts/update9.3.sql');
        install = await runDBScript('./dbScripts/update9.4.sql');
        install = await runDBScript('./dbScripts/update9.5.sql');
        install = await runDBScript('./dbScripts/update9.6.sql');
        install = await runDBScript('./dbScripts/update9.7.sql');
        install = await runDBScript('./dbScripts/update10.0.sql');
        install = await runDBScript('./dbScripts/update10.1.sql');
        install = await runDBScript('./dbScripts/update10.2.sql');
        install = await runDBScript('./dbScripts/update10.3.sql');
        install = await runDBScript('./dbScripts/update11.0.sql');
        install = await runDBScript('./dbScripts/update11.1.sql');
        install = await runDBScript('./dbScripts/update12.0.sql');
        install = await runDBScript('./dbScripts/update12.1.sql');
        install = await runDBScript('./dbScripts/update12.2.sql');
        install = await runDBScript('./dbScripts/update12.3.sql');
        install = await runDBScript('./dbScripts/update13.0.sql');
        install = await runDBScript('./dbScripts/update13.1.sql');
        install = await runDBScript('./dbScripts/update13.2.sql');
        install = await runDBScript('./dbScripts/update13.3.1.sql');
        install = await runDBScript('./dbScripts/update13.3.2.sql');
        install = await runDBScript('./dbScripts/update13.4.sql');
        install = await runDBScript('./dbScripts/update13.5.sql');
        install = await runDBScript('./dbScripts/update13.6.sql');
        install = await runDBScript('./dbScripts/update14.0.sql');
        install = await runDBScript('./dbScripts/update14.1.sql');
        install = await runDBScript('./dbScripts/update14.2.sql');
        install = await runDBScript('./dbScripts/update15.0.sql');
        logger.debug(install);
    } catch (error) {
        logger.error(error);
    }
}

const majDB = async () => {
    try {
        logger.info('Lancement des mises à jour de version', {idUtilisateur: 'SYSTEM'});

        let finalResult = true;
        logger.debug(finalResult);

        let configDB = await db.query(
            `SELECT version FROM CONFIG;`
        );
        logger.debug(configDB);
        if(configDB[0].version == BACKEND_VERSION)
        {
            logger.info('DB à jour, pas d\'action à mener', {idUtilisateur: 'SYSTEM'});
            return true;
        }

        let update;

        switch(configDB[0].version)
        {
            case '14.0':
                logger.info('Version 14.0 détectée - Upgrade à la version suivante', {idUtilisateur: 'SYSTEM'})
                update = await runDBScript('./dbScripts/update1.1.sql');
                logger.debug(update);
                finalResult = finalResult && await majDB();
                logger.debug(finalResult);
            break;

            case '14.1':
                logger.info('Version 14.1 détectée - Upgrade à la version suivante', {idUtilisateur: 'SYSTEM'})
                update = await runDBScript('./dbScripts/update14.2.sql');
                logger.debug(update);
                finalResult = finalResult && await majDB();
                logger.debug(finalResult);
            break;

            case '14.2':
                logger.info('Version 14.2 détectée - Upgrade à la version suivante', {idUtilisateur: 'SYSTEM'})
                update = await runDBScript('./dbScripts/update15.0.sql');
                logger.debug(update);
                finalResult = finalResult && await majDB();
                logger.debug(finalResult);
            break;

            default:
                logger.error('Erreur sur l\'analyse de la version');
                return false;
            break;
        }

        logger.info('Fin des mises à jour de version', {idUtilisateur: 'SYSTEM'});
        logger.debug(finalResult);
        return finalResult;
        
    } catch (error) {
        logger.debug(error.sqlState);
        if(error.sqlState == '42S02')
        {
            //DB non-initialisée
            await installDB();
            logger.warn('Suite au process d\'installation de la DB, relancer le backend pour dépiler les MAJ', {idUtilisateur: 'SYSTEM'});
            return false;
        }
        else
        {
            //inconnue
            logger.error(error);
        }
    }
}

module.exports = {
    majDB,
};