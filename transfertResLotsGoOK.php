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
	
	//GESION DES ERREURS
	if (!isset($_SESSION['transfertStade']) OR !isset($_SESSION['transfertIdMaterielLot']) OR !isset($_SESSION['transfertIdMaterielCatalogue']) OR !isset($_SESSION['transfertQttMax']) OR !isset($_SESSION['transfertIdReserveElement']) OR !isset($_SESSION['transfertqttTrans']))
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
    $reserve = $query->fetch();    
	$query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT WHERE idElement = :idElement;');
	$query->execute(array(
        'idElement' => $_SESSION['transfertIdMaterielLot']
    ));
    $lot = $query->fetch();

    $query = $db->prepare('UPDATE MATERIEL_ELEMENT SET quantite = quantite + :transfertqttTrans WHERE idElement = :idElement;');
	$query->execute(array(
        'idElement' => $_SESSION['transfertIdMaterielLot'],
        'transfertqttTrans' => $_SESSION['transfertqttTrans']
    ));
    
    $query = $db->prepare('UPDATE RESERVES_MATERIEL SET quantiteReserve = quantiteReserve - :transfertqttTrans WHERE idReserveElement = :idReserveElement;');
	$query->execute(array(
        'idReserveElement' => $_SESSION['transfertIdReserveElement'],
        'transfertqttTrans' => $_SESSION['transfertqttTrans']
    ));
    
    
    //GESTION DES DATES
	if (isset($reserve['peremptionReserve']))
	{
		if ($lot['peremption'] != Null)
		{
			if ($lot['peremption'] > $reserve['peremptionReserve'])
			{
				$query = $db->prepare('UPDATE MATERIEL_ELEMENT SET peremption = :peremption WHERE idElement = :idElement;');
				$query->execute(array(
			        'idElement' => $_SESSION['transfertIdMaterielLot'],
			        'peremption' => $reserve['peremptionReserve']
			    ));
			    writeInLogs("Transfert de matériel de l'élément de réserve " . $_SESSION['transfertIdMaterielCatalogue'] . " vers l'élément de lots ".$_SESSION['transfertIdMaterielLot']." avec modification de date.", '3');
			}
		}
		else
		{
			$query = $db->prepare('UPDATE MATERIEL_ELEMENT SET peremption = :peremption WHERE idElement = :idElement;');
			$query->execute(array(
		        'idElement' => $_SESSION['transfertIdMaterielLot'],
		        'peremption' => $reserve['peremptionReserve']
		    ));
		    writeInLogs("Transfert de matériel de l'élément de réserve " . $_SESSION['transfertIdMaterielCatalogue'] . " vers l'élément de lots ".$_SESSION['transfertIdMaterielLot']." avec modification de date.", '3');
		}
	}
	else
	{
		writeInLogs("Transfert de matériel de l'élément de réserve " . $_SESSION['transfertIdMaterielCatalogue'] . " vers l'élément de lots ".$_SESSION['transfertIdMaterielLot']." sans modification de date.", '3');
	}
	
	
	//SORTIE
	$_SESSION['returnMessage'] = 'Transfert effectué.';
    $_SESSION['returnType'] = '1';
    echo "<script type='text/javascript'>document.location.replace('transfertReset.php');</script>";
}
?>