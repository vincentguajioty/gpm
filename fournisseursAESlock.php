<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

unset($_SESSION['aesFour']);
writeInLogs("Session de décryptage des données fournisseurs terminée", '1', NULL);
echo "<script>window.location = document.referrer;</script>";


?>