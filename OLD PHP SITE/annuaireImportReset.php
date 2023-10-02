<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['annuaire_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
	unset($_SESSION['importStade']);

	$query = $db->query('TRUNCATE PERSONNE_REFERENTE_TEMP;');
	
	echo "<script type='text/javascript'>document.location.replace('annuaire.php');</script>";
}
?>