<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if (isset($_GET['id'])) {
    $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN FOURNISSEURS f ON m.idFournisseur = f.idFournisseur WHERE idElement = :idElement;');
    $query->execute(array('idElement' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [5];
require_once 'modal.php';
