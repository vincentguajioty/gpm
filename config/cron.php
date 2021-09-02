<?php

require_once 'bdd.php';

//Analyse complète des lots
writeInLogs("CRON - Début de la vérification de conformité de tous les lots.", '1', NULL);
checkAllConf();
writeInLogs("CRON - Fin de la vérification de conformité de tous les lots.", '1', NULL);

//Analyse complète des désinfections de véhicules
writeInLogs("CRON - Début de la vérification des désinfections de tous les véhicules.", '1', NULL);
checkAllDesinfection();
writeInLogs("CRON - Fin de la vérification des désinfections de tous les véhicules", '1', NULL);

//Suppression de toutes les demandes de réinitialisation de mot de passe non-effectuées
writeInLogs("CRON - Vidage de la table de tocken de reset de mots de passe.", '1', NULL);
$query = $db->query('TRUNCATE TABLE RESETPASSWORD;');

?>