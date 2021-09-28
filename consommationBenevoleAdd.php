<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'verrouIPcheck.php';

if (checkIP($_SERVER['REMOTE_ADDR'])==1)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

if ($MAINTENANCE)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

if ($CONSOMMATION_BENEVOLES==0)
{
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
}
else
{
	$line = [$_POST['idMaterielCatalogue'],$_POST['idLot'],$_POST['quantiteConsommation'],$_POST['idConteneur']];

	$_SESSION['consoArray'][] = $line;
    
    echo "<script type='text/javascript'>document.location.replace('consommationBenevole.php');</script>";
    
}
?>