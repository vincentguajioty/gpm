<?php

session_start();
if ($_SESSION['identifiant'] == Null)
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
require_once 'config/bdd.php';
require_once 'config/config.php';

$update = $db->prepare('UPDATE PERSONNE_REFERENTE SET disclaimerAccept = CURRENT_TIMESTAMP() WHERE idPersonne = :idPersonne');
$update->execute(array('idPersonne'=>$_SESSION['idPersonne']));

$_SESSION['disclaimerAccept'] = date("Y-m-d H:i:s");

echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
?>