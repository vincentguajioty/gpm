<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['sac_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
	unset($_SESSION['transfertStade']);
	unset($_SESSION['transfertCmd']);
	unset($_SESSION['transfertIdMaterielCatalogue']);
	unset($_SESSION['transfertPeremption']);
	unset($_SESSION['transfertQttMax']);
	unset($_SESSION['transfertIdReserveElement']);
	unset($_SESSION['transfertqttTrans']);
	unset($_SESSION['transfertIdMaterielLot']);
	
	echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
}
?>