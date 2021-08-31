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
	
	$query = $db->prepare('SELECT * FROM RESERVES_MATERIEL WHERE idReserveElement = :idReserveElement');
	$query->execute(array(
		'idReserveElement' =>  $_POST['idReserveElement']
	));
	$data = $query->fetch();
	
	$_SESSION['transfertQttMax'] = $data['quantiteReserve'];
	
	$_SESSION['transfertStade'] = 3;
	
    echo "<script>window.location = document.referrer;</script>";


}
?>