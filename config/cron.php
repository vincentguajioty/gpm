<?php

require_once 'bdd.php';

//Nettoyage des logs
$query = $db->prepare('DELETE FROM LOGS WHERE dateEvt < :dateEvt ;');
$query->execute(array(
    'dateEvt' => date('Y-m-d', strtotime(date('Y-m-d') . ' -' . 90 . ' days'))
));

//Analyse complète des lots
checkAllConf();

//Suppression de toutes les demandes de réinitialisation de mot de passe non-effectuées
$query = $db->query('TRUNCATE TABLE RESETPASSWORD;');

?>