const db = require('./db');
const fs = require('fs');
const logger = require('./winstonLogger');
const fonctionsMetiers = require('./helpers/fonctionsMetiers');

const BACKEND_VERSION = '15.2';

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
        logger.info('DB détectée vide, installation de la V14.2 en cours');
        let install;

        logger.info('Installation initiale etape 01/66, lancement de install1.0.sql');
        install = await runDBScript('./dbScripts/install1.0.sql');
        logger.info('Installation initiale etape 01/66, terminée');

        logger.info('Installation initiale etape 02/66, lancement de update2.3.sql');
        install = await runDBScript('./dbScripts/update2.3.sql');
        logger.info('Installation initiale etape 02/66, terminée');

        logger.info('Installation initiale etape 03/66, lancement de update2.4.sql');
        install = await runDBScript('./dbScripts/update2.4.sql');
        logger.info('Installation initiale etape 03/66, terminée');

        logger.info('Installation initiale etape 04/66, lancement de update2.5.sql');
        install = await runDBScript('./dbScripts/update2.5.sql');
        logger.info('Installation initiale etape 04/66, terminée');

        logger.info('Installation initiale etape 05/66, lancement de update2.6.sql');
        install = await runDBScript('./dbScripts/update2.6.sql');
        logger.info('Installation initiale etape 05/66, terminée');

        logger.info('Installation initiale etape 06/66, lancement de update3.0.sql');
        install = await runDBScript('./dbScripts/update3.0.sql');
        logger.info('Installation initiale etape 06/66, terminée');

        logger.info('Installation initiale etape 07/66, lancement de update3.1.sql');
        install = await runDBScript('./dbScripts/update3.1.sql');
        logger.info('Installation initiale etape 07/66, terminée');

        logger.info('Installation initiale etape 08/66, lancement de update4.0.sql');
        install = await runDBScript('./dbScripts/update4.0.sql');
        logger.info('Installation initiale etape 08/66, terminée');

        logger.info('Installation initiale etape 09/66, lancement de update4.1.sql');
        install = await runDBScript('./dbScripts/update4.1.sql');
        logger.info('Installation initiale etape 09/66, terminée');

        logger.info('Installation initiale etape 10/66, lancement de update4.2.sql');
        install = await runDBScript('./dbScripts/update4.2.sql');
        logger.info('Installation initiale etape 10/66, terminée');

        logger.info('Installation initiale etape 11/66, lancement de update4.3.sql');
        install = await runDBScript('./dbScripts/update4.3.sql');
        logger.info('Installation initiale etape 11/66, terminée');

        logger.info('Installation initiale etape 12/66, lancement de update5.0.sql');
        install = await runDBScript('./dbScripts/update5.0.sql');
        logger.info('Installation initiale etape 12/66, terminée');

        logger.info('Installation initiale etape 13/66, lancement de update5.1.sql');
        install = await runDBScript('./dbScripts/update5.1.sql');
        logger.info('Installation initiale etape 13/66, terminée');

        logger.info('Installation initiale etape 14/66, lancement de update5.2.sql');
        install = await runDBScript('./dbScripts/update5.2.sql');
        logger.info('Installation initiale etape 14/66, terminée');

        logger.info('Installation initiale etape 15/66, lancement de update5.3.sql');
        install = await runDBScript('./dbScripts/update5.3.sql');
        logger.info('Installation initiale etape 15/66, terminée');

        logger.info('Installation initiale etape 16/66, lancement de update5.4.sql');
        install = await runDBScript('./dbScripts/update5.4.sql');
        logger.info('Installation initiale etape 16/66, terminée');

        logger.info('Installation initiale etape 17/66, lancement de update5.5.sql');
        install = await runDBScript('./dbScripts/update5.5.sql');
        logger.info('Installation initiale etape 17/66, terminée');

        logger.info('Installation initiale etape 18/66, lancement de update5.6.sql');
        install = await runDBScript('./dbScripts/update5.6.sql');
        logger.info('Installation initiale etape 18/66, terminée');

        logger.info('Installation initiale etape 19/66, lancement de update5.7.sql');
        install = await runDBScript('./dbScripts/update5.7.sql');
        logger.info('Installation initiale etape 19/66, terminée');

        logger.info('Installation initiale etape 20/66, lancement de update6.0.sql');
        install = await runDBScript('./dbScripts/update6.0.sql');
        logger.info('Installation initiale etape 20/66, terminée');

        logger.info('Installation initiale etape 21/66, lancement de update6.1.sql');
        install = await runDBScript('./dbScripts/update6.1.sql');
        logger.info('Installation initiale etape 21/66, terminée');

        logger.info('Installation initiale etape 22/66, lancement de update6.2.sql');
        install = await runDBScript('./dbScripts/update6.2.sql');
        logger.info('Installation initiale etape 22/66, terminée');

        logger.info('Installation initiale etape 23/66, lancement de update7.0.sql');
        install = await runDBScript('./dbScripts/update7.0.sql');
        logger.info('Installation initiale etape 23/66, terminée');

        logger.info('Installation initiale etape 24/66, lancement de update7.1.sql');
        install = await runDBScript('./dbScripts/update7.1.sql');
        logger.info('Installation initiale etape 24/66, terminée');

        logger.info('Installation initiale etape 25/66, lancement de update7.2.sql');
        install = await runDBScript('./dbScripts/update7.2.sql');
        logger.info('Installation initiale etape 25/66, terminée');

        logger.info('Installation initiale etape 26/66, lancement de update7.3.sql');
        install = await runDBScript('./dbScripts/update7.3.sql');
        logger.info('Installation initiale etape 26/66, terminée');

        logger.info('Installation initiale etape 27/66, lancement de update7.4.sql');
        install = await runDBScript('./dbScripts/update7.4.sql');
        logger.info('Installation initiale etape 27/66, terminée');

        logger.info('Installation initiale etape 28/66, lancement de update7.5.sql');
        install = await runDBScript('./dbScripts/update7.5.sql');
        logger.info('Installation initiale etape 28/66, terminée');

        logger.info('Installation initiale etape 29/66, lancement de update7.6.sql');
        install = await runDBScript('./dbScripts/update7.6.sql');
        logger.info('Installation initiale etape 29/66, terminée');

        logger.info('Installation initiale etape 30/66, lancement de update8.0.sql');
        install = await runDBScript('./dbScripts/update8.0.sql');
        logger.info('Installation initiale etape 30/66, terminée');

        logger.info('Installation initiale etape 31/66, lancement de update8.1.sql');
        install = await runDBScript('./dbScripts/update8.1.sql');
        logger.info('Installation initiale etape 31/66, terminée');

        logger.info('Installation initiale etape 32/66, lancement de update8.2.sql');
        install = await runDBScript('./dbScripts/update8.2.sql');
        logger.info('Installation initiale etape 32/66, terminée');

        logger.info('Installation initiale etape 33/66, lancement de update8.3.sql');
        install = await runDBScript('./dbScripts/update8.3.sql');
        logger.info('Installation initiale etape 33/66, terminée');

        logger.info('Installation initiale etape 34/66, lancement de update8.4.sql');
        install = await runDBScript('./dbScripts/update8.4.sql');
        logger.info('Installation initiale etape 34/66, terminée');

        logger.info('Installation initiale etape 35/66, lancement de update8.5.sql');
        install = await runDBScript('./dbScripts/update8.5.sql');
        logger.info('Installation initiale etape 35/66, terminée');

        logger.info('Installation initiale etape 36/66, lancement de update8.6.sql');
        install = await runDBScript('./dbScripts/update8.6.sql');
        logger.info('Installation initiale etape 36/66, terminée');

        logger.info('Installation initiale etape 37/66, lancement de update9.0.sql');
        install = await runDBScript('./dbScripts/update9.0.sql');
        logger.info('Installation initiale etape 37/66, terminée');

        logger.info('Installation initiale etape 38/66, lancement de update9.1.sql');
        install = await runDBScript('./dbScripts/update9.1.sql');
        logger.info('Installation initiale etape 38/66, terminée');

        logger.info('Installation initiale etape 39/66, lancement de update9.2.sql');
        install = await runDBScript('./dbScripts/update9.2.sql');
        logger.info('Installation initiale etape 39/66, terminée');

        logger.info('Installation initiale etape 40/66, lancement de update9.3.sql');
        install = await runDBScript('./dbScripts/update9.3.sql');
        logger.info('Installation initiale etape 40/66, terminée');

        logger.info('Installation initiale etape 41/66, lancement de update9.4.sql');
        install = await runDBScript('./dbScripts/update9.4.sql');
        logger.info('Installation initiale etape 41/66, terminée');

        logger.info('Installation initiale etape 42/66, lancement de update9.5.sql');
        install = await runDBScript('./dbScripts/update9.5.sql');
        logger.info('Installation initiale etape 42/66, terminée');

        logger.info('Installation initiale etape 43/66, lancement de update9.6.sql');
        install = await runDBScript('./dbScripts/update9.6.sql');
        logger.info('Installation initiale etape 43/66, terminée');

        logger.info('Installation initiale etape 44/66, lancement de update9.7.sql');
        install = await runDBScript('./dbScripts/update9.7.sql');
        logger.info('Installation initiale etape 44/66, terminée');

        logger.info('Installation initiale etape 45/66, lancement de update9.8.sql');
        install = await runDBScript('./dbScripts/update9.8.sql');
        logger.info('Installation initiale etape 45/66, terminée');

        logger.info('Installation initiale etape 46/66, lancement de update10.0.sql');
        install = await runDBScript('./dbScripts/update10.0.sql');
        logger.info('Installation initiale etape 46/66, terminée');

        logger.info('Installation initiale etape 47/66, lancement de update10.1.sql');
        install = await runDBScript('./dbScripts/update10.1.sql');
        logger.info('Installation initiale etape 47/66, terminée');

        logger.info('Installation initiale etape 48/66, lancement de update10.2.sql');
        install = await runDBScript('./dbScripts/update10.2.sql');
        logger.info('Installation initiale etape 48/66, terminée');

        logger.info('Installation initiale etape 49/66, lancement de update10.3.sql');
        install = await runDBScript('./dbScripts/update10.3.sql');
        logger.info('Installation initiale etape 49/66, terminée');

        logger.info('Installation initiale etape 50/66, lancement de update11.0.sql');
        install = await runDBScript('./dbScripts/update11.0.sql');
        logger.info('Installation initiale etape 50/66, terminée');

        logger.info('Installation initiale etape 51/66, lancement de update11.1.sql');
        install = await runDBScript('./dbScripts/update11.1.sql');
        logger.info('Installation initiale etape 51/66, terminée');

        logger.info('Installation initiale etape 52/66, lancement de update12.0.sql');
        install = await runDBScript('./dbScripts/update12.0.sql');
        logger.info('Installation initiale etape 52/66, terminée');

        logger.info('Installation initiale etape 53/66, lancement de update12.1.sql');
        install = await runDBScript('./dbScripts/update12.1.sql');
        logger.info('Installation initiale etape 53/66, terminée');

        logger.info('Installation initiale etape 54/66, lancement de update12.2.sql');
        install = await runDBScript('./dbScripts/update12.2.sql');
        logger.info('Installation initiale etape 54/66, terminée');

        logger.info('Installation initiale etape 55/66, lancement de update12.3.sql');
        install = await runDBScript('./dbScripts/update12.3.sql');
        logger.info('Installation initiale etape 55/66, terminée');

        logger.info('Installation initiale etape 56/66, lancement de update13.0.sql');
        install = await runDBScript('./dbScripts/update13.0.sql');
        logger.info('Installation initiale etape 56/66, terminée');

        logger.info('Installation initiale etape 57/66, lancement de update13.1.sql');
        install = await runDBScript('./dbScripts/update13.1.sql');
        logger.info('Installation initiale etape 57/66, terminée');

        logger.info('Installation initiale etape 58/66, lancement de update13.2.sql');
        install = await runDBScript('./dbScripts/update13.2.sql');
        logger.info('Installation initiale etape 58/66, terminée');

        logger.info('Installation initiale etape 59/66, lancement de update13.3.sql');
        install = await runDBScript('./dbScripts/update13.3.sql');
        logger.info('Installation initiale etape 59/66, terminée');

        logger.info('Installation initiale etape 60/66, lancement de update13.4.sql');
        install = await runDBScript('./dbScripts/update13.4.sql');
        logger.info('Installation initiale etape 60/66, terminée');

        logger.info('Installation initiale etape 61/66, lancement de update13.5.sql');
        install = await runDBScript('./dbScripts/update13.5.sql');
        logger.info('Installation initiale etape 61/66, terminée');

        logger.info('Installation initiale etape 62/66, lancement de update13.6.sql');
        install = await runDBScript('./dbScripts/update13.6.sql');
        logger.info('Installation initiale etape 62/66, terminée');

        logger.info('Installation initiale etape 63/66, lancement de update14.0.sql');
        install = await runDBScript('./dbScripts/update14.0.sql');
        logger.info('Installation initiale etape 63/66, terminée');

        logger.info('Installation initiale etape 64/66, lancement de update14.1.sql');
        install = await runDBScript('./dbScripts/update14.1.sql');
        logger.info('Installation initiale etape 64/66, terminée');

        logger.info('Installation initiale etape 65/66, lancement de update14.2.sql');
        install = await runDBScript('./dbScripts/update14.2.sql');
        logger.info('Installation initiale etape 65/66, terminée');

        logger.info('Installation initiale etape 66/66, lancement de update15.0.sql');
        install = await runDBScript('./dbScripts/update15.0.sql');
        logger.info('Installation initiale etape 66/66, terminée');

        logger.debug(install);
    } catch (error) {
        logger.error(error);
    }
}

const majDB = async () => {
    try {
        logger.info('Lancement des mises à jour de version');

        let finalResult = true;
        logger.debug(finalResult);

        let configDB = await db.query(
            `SELECT version FROM CONFIG;`
        );
        logger.debug(configDB);
        if(configDB[0].version == BACKEND_VERSION)
        {
            logger.info('DB à jour, pas d\'action à mener');
            return true;
        }

        let update;

        switch(configDB[0].version)
        {
            case '14.0':
                logger.info('Version 14.0 détectée - Upgrade à la version suivante')
                update = await runDBScript('./dbScripts/update14.1.sql');
                logger.debug(update);
                finalResult = finalResult && await majDB();
                logger.debug(finalResult);
            break;

            case '14.1':
                logger.info('Version 14.1 détectée - Upgrade à la version suivante')
                update = await runDBScript('./dbScripts/update14.2.sql');
                logger.debug(update);
                finalResult = finalResult && await majDB();
                logger.debug(finalResult);
            break;

            case '14.2':
                logger.info('Version 14.2 détectée - Upgrade à la version suivante')
                update = await runDBScript('./dbScripts/update15.0.sql');
                logger.debug(update);
                finalResult = finalResult && await majDB();
                logger.debug(finalResult);

                await fonctionsMetiers.calculerTousTotauxCommandes();
                await fonctionsMetiers.calculerTousTotauxCentreDeCouts();
            break;

            case '15.0':
                logger.info('Version 15.0 détectée - Upgrade à la version suivante')
                update = await runDBScript('./dbScripts/update15.1.sql');
                logger.debug(update);
                finalResult = finalResult && await majDB();
                logger.debug(finalResult);
            break;

            case '15.1':
                logger.info('Version 15.1 détectée - Upgrade à la version suivante')
                update = await runDBScript('./dbScripts/update15.2.sql');
                logger.debug(update);
                finalResult = finalResult && await majDB();
                logger.debug(finalResult);
            break;

            default:
                logger.error('Erreur sur l\'analyse de la version');
                return false;
            break;
        }

        logger.info('Fin des mises à jour de version');
        logger.debug(finalResult);
        return finalResult;
        
    } catch (error) {
        logger.debug(error.sqlState);
        if(error.sqlState == '42S02')
        {
            //DB non-initialisée
            await installDB();
            logger.warn('Suite au process d\'installation de la DB, relancer le backend pour dépiler les MAJ');
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