<?php
session_start();
$db = new PDO('mysql:host=x.x.x.x;dbname=APOLLON-DEV;charset=utf8', 'USER', 'PASSWORD');

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