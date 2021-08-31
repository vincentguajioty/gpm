<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if (isset($_GET['id']))
{
    $query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN ETATS s on l.idEtat = s.idEtat LEFT OUTER JOIN LIEUX e ON l.idLieu = e.idLieu LEFT OUTER JOIN PERSONNE_REFERENTE p on l.idPersonne = p.idPersonne WHERE idLot =:idLot;');
    $query->execute(array('idLot' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [2];
require_once 'modal.php';

