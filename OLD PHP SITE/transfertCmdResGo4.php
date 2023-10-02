<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['reserve_ReserveVersLot']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
	
	$_SESSION['transfertIdReserveElement'] = $_POST['idReserveElement'];
	$_SESSION['transfertqttTrans']         = $_POST['qttTrans'];
	$_SESSION['transfertStade']            = 4;
	
    echo "<script>window.location = document.referrer;</script>";


}
?>