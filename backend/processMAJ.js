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

        logger.info('Installation initiale etape 1/68, lancement de install1.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/install1.0.sql');
        logger.info('Installation initiale etape 1/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 2/68, lancement de update2.3.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update2.3.sql');
        logger.info('Installation initiale etape 2/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 3/68, lancement de update2.4.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update2.4.sql');
        logger.info('Installation initiale etape 3/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 4/68, lancement de update2.5.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update2.5.sql');
        logger.info('Installation initiale etape 4/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 5/68, lancement de update2.6.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update2.6.sql');
        logger.info('Installation initiale etape 5/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 6/68, lancement de update3.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update3.0.sql');
        logger.info('Installation initiale etape 6/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 7/68, lancement de update3.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update3.1.sql');
        logger.info('Installation initiale etape 7/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 8/68, lancement de update4.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update4.0.sql');
        logger.info('Installation initiale etape 8/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 9/68, lancement de update4.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update4.1.sql');
        logger.info('Installation initiale etape 9/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 10/68, lancement de update4.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update4.2.sql');
        logger.info('Installation initiale etape 10/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 11/68, lancement de update4.3.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update4.3.sql');
        logger.info('Installation initiale etape 11/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 12/68, lancement de update5.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update5.0.sql');
        logger.info('Installation initiale etape 12/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 13/68, lancement de update5.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update5.1.sql');
        logger.info('Installation initiale etape 13/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 14/68, lancement de update5.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update5.2.sql');
        logger.info('Installation initiale etape 14/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 15/68, lancement de update5.3.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update5.3.sql');
        logger.info('Installation initiale etape 15/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 16/68, lancement de update5.4.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update5.4.sql');
        logger.info('Installation initiale etape 16/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 17/68, lancement de update5.5.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update5.5.sql');
        logger.info('Installation initiale etape 17/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 18/68, lancement de update5.6.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update5.6.sql');
        logger.info('Installation initiale etape 18/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 19/68, lancement de update5.7.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update5.7.sql');
        logger.info('Installation initiale etape 19/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 20/68, lancement de update6.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update6.0.sql');
        logger.info('Installation initiale etape 20/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 21/68, lancement de update6.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update6.1.sql');
        logger.info('Installation initiale etape 21/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 22/68, lancement de update6.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update6.2.sql');
        logger.info('Installation initiale etape 22/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 23/68, lancement de update7.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update7.0.sql');
        logger.info('Installation initiale etape 23/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 24/68, lancement de update7.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update7.1.sql');
        logger.info('Installation initiale etape 24/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 25/68, lancement de update7.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update7.2.sql');
        logger.info('Installation initiale etape 25/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 26/68, lancement de update7.3.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update7.3.sql');
        logger.info('Installation initiale etape 26/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 27/68, lancement de update7.4.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update7.4.1.sql');
        logger.info('Installation initiale etape 27/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 28/68, lancement de update7.4.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update7.4.2.sql');
        logger.info('Installation initiale etape 28/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 29/68, lancement de update7.5.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update7.5.sql');
        logger.info('Installation initiale etape 29/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 30/68, lancement de update7.6.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update7.6.sql');
        logger.info('Installation initiale etape 30/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 31/68, lancement de update8.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update8.0.sql');
        logger.info('Installation initiale etape 31/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 32/68, lancement de update8.1.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update8.1.1.sql');
        logger.info('Installation initiale etape 32/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 33/68, lancement de update8.1.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update8.1.2.sql');
        logger.info('Installation initiale etape 33/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 34/68, lancement de update8.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update8.2.sql');
        logger.info('Installation initiale etape 34/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 35/68, lancement de update8.3.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update8.3.sql');
        logger.info('Installation initiale etape 35/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 36/68, lancement de update8.4.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update8.4.sql');
        logger.info('Installation initiale etape 36/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 37/68, lancement de update8.5.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update8.5.sql');
        logger.info('Installation initiale etape 37/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 38/68, lancement de update8.6.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update8.6.sql');
        logger.info('Installation initiale etape 38/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 39/68, lancement de update9.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update9.0.sql');
        logger.info('Installation initiale etape 39/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 40/68, lancement de update9.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update9.1.sql');
        logger.info('Installation initiale etape 40/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 41/68, lancement de update9.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update9.2.sql');
        logger.info('Installation initiale etape 41/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 42/68, lancement de update9.3.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update9.3.sql');
        logger.info('Installation initiale etape 42/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 43/68, lancement de update9.4.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update9.4.sql');
        logger.info('Installation initiale etape 43/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 44/68, lancement de update9.5.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update9.5.sql');
        logger.info('Installation initiale etape 44/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 45/68, lancement de update9.6.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update9.6.sql');
        logger.info('Installation initiale etape 45/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 46/68, lancement de update9.7.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update9.7.sql');
        logger.info('Installation initiale etape 46/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 47/68, lancement de update10.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update10.0.sql');
        logger.info('Installation initiale etape 47/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 48/68, lancement de update10.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update10.1.sql');
        logger.info('Installation initiale etape 48/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 49/68, lancement de update10.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update10.2.sql');
        logger.info('Installation initiale etape 49/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 50/68, lancement de update10.3.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update10.3.sql');
        logger.info('Installation initiale etape 50/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 51/68, lancement de update11.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update11.0.sql');
        logger.info('Installation initiale etape 51/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 52/68, lancement de update11.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update11.1.sql');
        logger.info('Installation initiale etape 52/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 53/68, lancement de update12.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update12.0.sql');
        logger.info('Installation initiale etape 53/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 54/68, lancement de update12.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update12.1.sql');
        logger.info('Installation initiale etape 54/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 55/68, lancement de update12.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update12.2.sql');
        logger.info('Installation initiale etape 55/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 56/68, lancement de update12.3.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update12.3.sql');
        logger.info('Installation initiale etape 56/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 57/68, lancement de update13.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update13.0.sql');
        logger.info('Installation initiale etape 57/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 58/68, lancement de update13.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update13.1.sql');
        logger.info('Installation initiale etape 58/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 59/68, lancement de update13.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update13.2.sql');
        logger.info('Installation initiale etape 59/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 60/68, lancement de update13.3.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update13.3.1.sql');
        logger.info('Installation initiale etape 60/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 61/68, lancement de update13.3.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update13.3.2.sql');
        logger.info('Installation initiale etape 61/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 62/68, lancement de update13.4.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update13.4.sql');
        logger.info('Installation initiale etape 62/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 63/68, lancement de update13.5.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update13.5.sql');
        logger.info('Installation initiale etape 63/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 64/68, lancement de update13.6.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update13.6.sql');
        logger.info('Installation initiale etape 64/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 65/68, lancement de update14.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update14.0.sql');
        logger.info('Installation initiale etape 65/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 66/68, lancement de update14.1.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update14.1.sql');
        logger.info('Installation initiale etape 66/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 67/68, lancement de update14.2.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update14.2.sql');
        logger.info('Installation initiale etape 67/68, terminée', {idUtilisateur: 'SYSTEM'});

        logger.info('Installation initiale etape 68/68, lancement de update15.0.sql', {idUtilisateur: 'SYSTEM'});
        install = await runDBScript('./dbScripts/update15.0.sql');
        logger.info('Installation initiale etape 68/68, terminée', {idUtilisateur: 'SYSTEM'});

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