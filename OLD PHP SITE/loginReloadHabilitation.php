<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';

require_once('logCheck.php');

loadSession($_SESSION['idPersonne']);

majIndicateursPersonne($_SESSION['idPersonne'],0);
majNotificationsPersonne($_SESSION['idPersonne'],0);

?>