<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (isset($_GET['id']))
{
    $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE u LEFT OUTER JOIN PROFILS p ON u.idProfil = p.idProfil WHERE idPersonne=:idPersonne;');
    $query->execute(array('idPersonne' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [10];
require_once 'modal.php';