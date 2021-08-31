<?php
session_start();

//---------- SECTION A PARAMETRER EN FONCTION DE LA DB MISE EN PLACE ---------- 

$SERVEUR = '0.0.0.0'; //adresse IP du serveur entre simple cote, ex: $SERVEUR = '192.169.1.5';
$DB = 'APOLLON'; //nom de la base de données, ex: $DB = 'APOLLON';
$CHARSET = 'utf8'; //type d'interclassement, utf8 étant recommandé, ex: $CHARSET = 'utf8';
$USER = 'user'; //nom d'utilisateur d'accès à la base de données, ex: $USER = 'utilisateur';
$PASSWORD = 'password'; //mot de passe d'accès à la base de données, ex: $PASSWORD = 'motDePasse';

//------------------------------------------------------------------------------

$db = new PDO('mysql:host=' . $SERVEUR . ';dbname=' . $DB . ';charset=' . $CHARSET, $USER , $PASSWORD);

function writeInLogs($contentEVT, $levelEVT)
{
    global $db;
    $query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurApollonEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurApollonEvt, :idLogLevel, :detailEvt);');
    $query->execute(array(
        'dateEvt' => date('Y-m-d H:i:s'),
        'adresseIP' => $_SERVER['REMOTE_ADDR'],
        'utilisateurApollonEvt' => $_SESSION['identifiant'],
        'idLogLevel' => $levelEVT,
        'detailEvt' => $contentEVT
    ));
}

?>