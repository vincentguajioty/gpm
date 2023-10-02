<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

function addCommandeComment($idCommande, $detailsEvtCommande, $idComIcon)
{
    global $db;
    
    $query = $db->prepare('INSERT INTO COMMANDES_TIMELINE(idCommande, dateEvtCommande, detailsEvtCommande, idComIcon) VALUES (:idCommande, CURRENT_TIMESTAMP, :detailsEvtCommande, :idComIcon);');
    $query->execute(array(
        'idCommande' => $idCommande,
        'detailsEvtCommande' => $detailsEvtCommande,
        'idComIcon' => $idComIcon
    ));
    
}

?>