<?php
session_start();

//---------------------- BASE DE DONNEES ---------------------- 
$SERVEURDB = 'x.x.x.x'; //adresse IP du serveur avec le port du service, le tout entre simple cote, ex: $SERVEUR = '192.169.1.5:3306';
$DB = 'GPM'; //nom de la base de données, ex: $DB = 'GPM';
$CHARSET = 'utf8'; //type d'interclassement, utf8 étant recommandé, ex: $CHARSET = 'utf8';
$USER = 'xxxx'; //nom d'utilisateur d'accès à la base de données, ex: $USER = 'utilisateur';
$PASSWORD = 'xxxx'; //mot de passe d'accès à la base de données, ex: $PASSWORD = 'motDePasse';
//-------------------- FIN BASE DE DONNEES ---------------------

$db = new PDO('mysql:host=' . $SERVEURDB . ';dbname=' . $DB . ';charset=' . $CHARSET, $USER , $PASSWORD);

function writeInLogs($contentEVT, $levelEVT)
{
    global $db;
    $query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
    $query->execute(array(
        'dateEvt' => date('Y-m-d H:i:s'),
        'adresseIP' => $_SERVER['REMOTE_ADDR'],
        'utilisateurEvt' => $_SESSION['identifiant'],
        'idLogLevel' => $levelEVT,
        'detailEvt' => $contentEVT
    ));
}

?>