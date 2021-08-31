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
	
	//GESION DES ERREURS
	if (!isset($_SESSION['transfertStade']) OR !isset($_SESSION['transfertCmd']) OR !isset($_SESSION['transfertIdMaterielCatalogue']) OR !isset($_SESSION['transfertQttMax']) OR !isset($_SESSION['transfertIdReserveElement']) OR !isset($_SESSION['transfertqttTrans']))
	{
		echo "<script type='text/javascript'>document.location.replace('transfertReset.php');</script>";
		writeInLogs("Erreur lors du transfert de matériel de la commande " . $_SESSION['transfertCmd'] . " vers l'élément de réserve ".$_SESSION['transfertIdMaterielCatalogue'], '5');
		$_SESSION['returnMessage'] = 'Erreur inconnue lors de l\'execution du transfert.';
        $_SESSION['returnType'] = '2';
		exit;
	}
	
	if (($_SESSION['transfertStade'])!=4)
	{
		echo "<script type='text/javascript'>document.location.replace('transfertReset.php');</script>";
		$_SESSION['returnMessage'] = 'Erreur inconnue lors de l\'execution du transfert.';
        $_SESSION['returnType'] = '2';
		exit;
	}
	
	//GESTION DES QTT
	$query = $db->prepare('SELECT * FROM RESERVES_MATERIEL WHERE idReserveElement = :idReserveElement;');
	$query->execute(array(
        'idReserveElement' => $_SESSION['transfertIdReserveElement']
    ));
    $data = $query->fetch();
    
    $query = $db->prepare('UPDATE RESERVES_MATERIEL SET quantiteReserve = quantiteReserve + :quantiteReserve WHERE idReserveElement = :idReserveElement;');
	$query->execute(array(
        'idReserveElement' => $_SESSION['transfertIdReserveElement'],
        'quantiteReserve' =>$_SESSION['transfertqttTrans']
    ));
    $query = $db->prepare('UPDATE COMMANDES_MATERIEL SET quantiteAtransferer = quantiteAtransferer - :quantiteReserve WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_SESSION['transfertCmd'],
        'quantiteReserve' =>$_SESSION['transfertqttTrans']
    ));
    
    
    //GESTION DES DATES
	if (isset($_SESSION['transfertPeremption']))
	{
		if ($data['peremptionReserve'] != Null)
		{
			if ($data['peremptionReserve'] > $_SESSION['transfertPeremption'])
			{
				$query = $db->prepare('UPDATE RESERVES_MATERIEL SET peremptionReserve = :peremptionReserve WHERE idReserveElement = :idReserveElement;');
				$query->execute(array(
			        'idReserveElement' => $_SESSION['transfertIdReserveElement'],
			        'peremptionReserve' => $_SESSION['transfertPeremption']
			    ));
			    writeInLogs("Transfert de matériel de la commande " . $_SESSION['transfertCmd'] . " vers l'élément de réserve ".$_SESSION['transfertIdMaterielCatalogue']." avec modification de date.", '3');
			}
		}
		else
		{
			$query = $db->prepare('UPDATE RESERVES_MATERIEL SET peremptionReserve = :peremptionReserve WHERE idReserveElement = :idReserveElement;');
			$query->execute(array(
		        'idReserveElement' => $_SESSION['transfertIdReserveElement'],
		        'peremptionReserve' => $_SESSION['transfertPeremption']
		    ));
		    writeInLogs("Transfert de matériel de la commande " . $_SESSION['transfertCmd'] . " vers l'élément de réserve ".$_SESSION['transfertIdMaterielCatalogue']." avec modification de date.", '3');
		}
	}
	else
	{
		writeInLogs("Transfert de matériel de la commande " . $_SESSION['transfertCmd'] . " vers l'élément de réserve ".$_SESSION['transfertIdMaterielCatalogue']." sans modification de date.", '3');
	}
	
	
	//SORTIE
	$_SESSION['returnMessage'] = 'Transfert effectué.';
    $_SESSION['returnType'] = '1';
    echo "<script type='text/javascript'>document.location.replace('transfertReset.php');</script>";
}
?>