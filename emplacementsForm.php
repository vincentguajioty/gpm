<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (isset($_GET['id'])) {
    $query = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE idEmplacement = :idEmplacement;');
    $query->execute(array('idEmplacement' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [4];
require_once 'modal.php';