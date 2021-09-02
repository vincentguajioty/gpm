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

if ($ALERTES_BENEVOLES_LOTS==0 AND $ALERTES_BENEVOLES_VEHICULES==0)
{
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
}
else
{
	
	if($RECAPTCHA_ENABLE)
	{
		$reCaptchaReturn = getCaptcha($_POST['g-recaptcha-response']);
		
		if($reCaptchaReturn->success == true AND $reCaptchaReturn->score > $RECAPTCHA_SCOREMIN)
		{
			writeInLogs("Google reCaptcha V3 - Soumission du formulaire autorisée", '1', NULL);
		}
		else
		{
			writeInLogs("Google reCaptcha V3 - Soumission du formulaire bloquée avec un score de ".$reCaptchaReturn->score, '2', NULL);
			echo "<script type='text/javascript'>document.location.replace('alerteBenevoleFailure.php');</script>";
			exit;
		}
	}
	
	
	$_POST['nomDeclarant']          = str_replace($XSS_SECURITY, "", $_POST['nomDeclarant']);
	$_POST['mailDeclarant']         = str_replace($XSS_SECURITY, "", $_POST['mailDeclarant']);
	$_POST['ipDeclarant']           = str_replace($XSS_SECURITY, "", $_POST['ipDeclarant']);
	$_POST['idLot']                 = str_replace($XSS_SECURITY, "", $_POST['idLot']);
	$_POST['messageAlerteLot']      = str_replace($XSS_SECURITY, "", $_POST['messageAlerteLot']);
	$_POST['idVehicule']            = str_replace($XSS_SECURITY, "", $_POST['idVehicule']);
	$_POST['messageAlerteVehicule'] = str_replace($XSS_SECURITY, "", $_POST['messageAlerteVehicule']);

	$_POST['ipDeclarant']           = $_SERVER['REMOTE_ADDR'];

    if($ALERTES_BENEVOLES_LOTS)
    {
    	$_POST['idLot'] = ($_POST['idLot'] == Null) ? Null : $_POST['idLot'];
    	if($_POST['idLot'] > 0 OR $_POST['messageAlerteLot'] != "")
    	{
    		$query = $db->prepare('
		        INSERT INTO
		            LOTS_ALERTES
		        SET
					idLotsAlertesEtat       = 1,
					dateCreationAlerte      = CURRENT_TIMESTAMP(),
					nomDeclarant            = :nomDeclarant,
					mailDeclarant           = :mailDeclarant,
					ipDeclarant             = :ipDeclarant,
					idLot                   = :idLot,
					messageAlerteLot        = :messageAlerteLot
		        ;');
		    $query->execute(array(
				'nomDeclarant'      => $_POST['nomDeclarant'],
				'mailDeclarant'     => $_POST['mailDeclarant'],
				'ipDeclarant'       => $_POST['ipDeclarant'],
				'idLot'             => $_POST['idLot'],
				'messageAlerteLot'  => $_POST['messageAlerteLot'],
			));
			switch($query->errorCode())
			{
			    case '00000':
			        writeInLogs("Ajout d'une alerte bénévoles sur les lots au nom de ".$_POST['nomDeclarant']." depuis l'IP ".$_POST['ipDeclarant'], '1', NULL);
			        break;

			    default:
			        writeInLogs("Erreur inconnue lors de l'ajout d'une alerte bénévoles sur les lots au nom de ".$_POST['nomDeclarant']." depuis l'IP ".$_POST['ipDeclarant'], '3', NULL);
			}
    	}
    }

    if($ALERTES_BENEVOLES_VEHICULES)
    {
    	$_POST['idVehicule'] = ($_POST['idVehicule'] == Null) ? Null : $_POST['idVehicule'];
    	if($_POST['idVehicule'] > 0 OR $_POST['messageAlerteVehicule'] != "")
    	{
    		$query = $db->prepare('
		        INSERT INTO
		            VEHICULES_ALERTES
		        SET
					idVehiculesAlertesEtat = 1,
					dateCreationAlerte     = CURRENT_TIMESTAMP(),
					nomDeclarant           = :nomDeclarant,
					mailDeclarant          = :mailDeclarant,
					ipDeclarant            = :ipDeclarant,
					idVehicule             = :idVehicule,
					messageAlerteVehicule  = :messageAlerteVehicule
		        ;');
		    $query->execute(array(
				'nomDeclarant'          => $_POST['nomDeclarant'],
				'mailDeclarant'         => $_POST['mailDeclarant'],
				'ipDeclarant'           => $_POST['ipDeclarant'],
				'idVehicule'            => $_POST['idVehicule'],
				'messageAlerteVehicule' => $_POST['messageAlerteVehicule'],
			));
			switch($query->errorCode())
			{
			    case '00000':
			        writeInLogs("Ajout d'une alerte bénévoles sur les véhicules au nom de ".$_POST['nomDeclarant']." depuis l'IP ".$_POST['ipDeclarant'], '1', NULL);
			        break;

			    default:
			        writeInLogs("Erreur inconnue lors de l'ajout d'une alerte bénévoles sur les véhicules au nom de ".$_POST['nomDeclarant']." depuis l'IP ".$_POST['ipDeclarant'], '3', NULL);
			}
    	}
    }

    
    echo "<script type='text/javascript'>document.location.replace('alerteBenevoleSuccess.php');</script>";
    
}
?>