<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if (isset($_GET['id'])) {
    $query = $db->prepare('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN FOURNISSEURS f ON s.idFournisseur = f.idFournisseur WHERE idSac = :idSac;');
    $query->execute(array('idSac' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [3];
require_once 'modal.php';

