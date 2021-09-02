<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

echo "<script type='text/javascript'>document.location.replace('vehiculesSyntheseKM.php?dateInf=".$_POST['dateInf']."&dateSup=".$_POST['dateSup']."');</script>";

?>