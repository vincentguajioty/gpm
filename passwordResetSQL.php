<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($RESETPASSWORD == 0)
{ 
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    exit;
}

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

if(isIpLock()==true)
{
    writeInLogs("Connexion refusée par le filtrage IP pour l'authentification avec ".$_POST['identifiant'], '2', NULL);
    echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
    exit;
}

$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS v ON p.idPersonne = v.idPersonne WHERE p.identifiant = :identifiant AND p.mailPersonne = :mailPersonne;');
$query->execute(array(
    'identifiant' => $_POST['identifiant'],
    'mailPersonne' => $_POST['mailPersonne']
));
$data = $query->fetch();

if (empty($data['idPersonne']) OR $data['idPersonne'] == "")
{
    //pas bon
    writeInLogs("Echec de la tentative de reset du mot de passe oublié.", '2', $_POST['identifiant']);
    lockIpOnce($_POST['identifiant']);
    echo "<script type='text/javascript'>document.location.replace('passwordResetKO.php');</script>";
}
else
{
    if($data['isActiveDirectory'])
    {
    	echo "<script type='text/javascript'>document.location.replace('passwordResetADKO.php');</script>";
    }
    else
    {
    	$query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
	    $query->execute(array(
	        'adresseIP' => $_SERVER['REMOTE_ADDR']
	    ));
	    $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
	    $query->execute(array(
	        'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - '.$VERROUILLAGE_IP_TEMPS.' days'))
	    ));
	    $query = $db->prepare('DELETE FROM RESETPASSWORD WHERE idPersonne = :idPersonne;');
	    $query->execute(array(
	        'idPersonne' => $data['idPersonne']
	    ));
		
		writeInLogs("Génération d'un token de réinitialisation du mot de passe pour l'identifiant ".$_POST['identifiant'], '1', NULL);

		$token = bin2hex(random_bytes(30));
		$tokenHash = password_hash($token, PASSWORD_DEFAULT);

		$query = $db->prepare('
			INSERT INTO
				RESETPASSWORD
			SET
				idPersonne   = :idPersonne,
				dateDemande  = :dateDemande,
				tokenReset   = :tokenReset,
				dateValidite = :dateValidite
			;');
	    $query->execute(array(
			'idPersonne'   => $data['idPersonne'],
			'dateDemande'  => date('Y-m-d H:i:s'),
			'tokenReset'   => $tokenHash,
			'dateValidite' => date('Y-m-d H:i:s', strtotime('+1 hour'))
	    ));
	    $resetSession = $db->query('SELECT MAX(idReset) as idReset FROM RESETPASSWORD;');
	    $resetSession=$resetSession->fetch();

	    $lienReset = $URLSITE."/passwordResetResult.php?idReset=".$resetSession['idReset']."&token=".$token;
	    $lienKill = $URLSITE."/passwordResetKill.php?idReset=".$resetSession['idReset']."&token=".$token;

	    $sujet = "[" . $APPNAME . "] Réinitialisation de votre mot de passe.";
	    $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Vous venez de faire une demande de réinitialisation de mot de passe oublié.<br/><br/>Si cette demande provient bien de vous, cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe:<br/>".$lienReset."<br/><br/>Si cette demande vous semble frauduleuse, cliquez sur le lien ci-dessous pour la neutraliser:<br/>".$lienKill;
	    $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
	    $message = $RETOURLIGNE.$message.$RETOURLIGNE;

	    if(sendmail($_POST['mailPersonne'], $sujet, 2, $message))
	    {
		    writeInLogs("Mail de réinitialisation de mot de passe envoyé à " . $_POST['mailPersonne'] . " pour le compte référence ".$data['idPersonne'], '1', NULL);
	    }
	    else
	    {
	         writeInLogs("Erreur d'envoi du mail de réinitialisation de mot de passe à " . $_POST['mailPersonne'] . " pour le compte référence ".$data['idPersonne'], '3', NULL);
	    }

		echo "<script type='text/javascript'>document.location.replace('passwordResetOK.php');</script>";
    }
}


?>