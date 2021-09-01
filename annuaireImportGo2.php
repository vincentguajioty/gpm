<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['annuaire_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
	$_SESSION['importStade'] = 2;
	
    echo "<script>window.location = document.referrer;</script>";


}
?>