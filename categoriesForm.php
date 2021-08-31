<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (isset($_GET['id']))
{
    $query = $db->prepare('SELECT * FROM MATERIEL_CATEGORIES WHERE idCategorie=:idCategorie;');
    $query->execute(array('idCategorie' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [7];
require_once 'modal.php';