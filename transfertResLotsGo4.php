<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['reserve_cmdVersReserve']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
	
	$_SESSION['transfertqttTrans'] = $_POST['qttTrans'];
	!isset($_SESSION['transfertIdMaterielLot']) ? $_SESSION['transfertIdMaterielLot'] = $_POST['idElement'] : '';
	$_SESSION['transfertStade'] = 4;
	
    echo "<script>window.location = document.referrer;</script>";


}
?>