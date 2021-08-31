<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (isset($_GET['id'])) {
    $query = $db->prepare('SELECT * FROM LIEUX WHERE idLieu=:idLieu;');
    $query->execute(array('idLieu' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}

$_SESSION['modals'] = [8];
require_once 'modal.php';
