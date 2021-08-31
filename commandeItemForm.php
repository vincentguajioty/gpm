<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (isset($_GET['idElement']))
{
    $query = $db->prepare('SELECT * FROM COMMANDES_MATERIEL WHERE idCommande=:idCommande AND idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array('idCommande' => $_GET['idCommande'], 'idMaterielCatalogue' => $_GET['idElement']));
    $data = $query->fetch();
    $query->closeCursor();
}


$_SESSION['modals'] = [1];
require_once 'modal.php';