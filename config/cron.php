<?php

require_once 'bdd.php';
require_once 'config.php';

writeInLogs("CRON - Début du CRON.", '1', NULL);

//Mise à jour des conditions de notifications
writeInLogs("CRON - Début de la vérification des conditions de notification.", '1', NULL);
notificationsConditionsMAJ();
notificationsMAJpersonne();
writeInLogs("CRON - Fin de la vérification des conditions de notification.", '1', NULL);

//Mise à jour des anticipations de péremption
writeInLogs("CRON - Début de la mise à jour des anticipations de péremption.", '1', NULL);
updatePeremptionsAnticipations();
writeInLogs("CRON - Fin de la mise à jour des anticipations de péremption.", '1', NULL);

//Déverrouillage des locks sur les lots et réserves
writeInLogs("CRON - Début du déveouillage des locks d'inventaire des lots et des reserves.", '1', NULL);
unlockLotsInventaires();
unlockReservesInventaires();
writeInLogs("CRON - Fin du déveouillage des locks d'inventaire des lots et des reserves.", '1', NULL);

//Analyse complète des lots
writeInLogs("CRON - Début de la vérification de conformité de tous les lots.", '1', NULL);
checkAllConf();
writeInLogs("CRON - Fin de la vérification de conformité de tous les lots.", '1', NULL);

//Analyse complète des désinfections de véhicules
writeInLogs("CRON - Début de la vérification des désinfections de tous les véhicules.", '1', NULL);
checkAllDesinfection();
writeInLogs("CRON - Fin de la vérification des désinfections de tous les véhicules", '1', NULL);

//Analyse complète des maintenances de véhicules
writeInLogs("CRON - Début de la vérification des maintenances de tous les véhicules.", '1', NULL);
checkAllMaintenance();
writeInLogs("CRON - Fin de la vérification des maintenances de tous les véhicules", '1', NULL);

//Suppression de toutes les demandes de réinitialisation de mot de passe non-effectuées
writeInLogs("CRON - Vidage de la table de tocken de reset de mots de passe.", '1', NULL);
$query = $db->query('TRUNCATE TABLE RESETPASSWORD;');

//Alerting de l'administrateur sur les comptes qui seront dans la passe CNIL_ANONYME d'ici 6 mois
cnilAnonymeAlerte6();

//Alerting de l'administrateur sur les comptes qui seront dans la passe CNIL_ANONYME d'ici 1 mois
cnilAnonymeAlerte1();

//Anonymisation des comptes
cnilAnonymeCron();

writeInLogs("CRON - Fin du CRON.", '1', NULL);

?>