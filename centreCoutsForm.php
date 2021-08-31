<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (isset($_GET['id'])) {
    $query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout=:idCentreDeCout;');
    $query->execute(array('idCentreDeCout' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [11];
require_once 'modal.php';
