<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (isset($_GET['id'])) {
    $query = $db->prepare('SELECT * FROM FOURNISSEURS WHERE idFournisseur=:idFournisseur;');
    $query->execute(array('idFournisseur' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [9];
require_once 'modal.php';