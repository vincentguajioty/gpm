<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (isset($_GET['id'])) {
    $query = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN MATERIEL_CATEGORIES b ON c.idCategorie = b.idCategorie WHERE idMaterielCatalogue=:idMaterielCatalogue;');
    $query->execute(array('idMaterielCatalogue' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [6];
require_once 'modal.php';
