<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';

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
		echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
		exit;
	}
}

$_POST['identifiant'] = str_replace($XSS_SECURITY, "", $_POST['identifiant']);
$_POST['motDePasse'] = str_replace($XSS_SECURITY, "", $_POST['motDePasse']);

if(isIpLock()==true)
{
    writeInLogs("Connexion refusée par le filtrage IP pour l'authentification avec ".$_POST['identifiant'], '2', NULL);
    echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
    exit;
}

if(checkUserPassword($_POST['identifiant'], $_POST['motDePasse'])==false)
{
    writeInLogs("Echec d'identification", '2', NULL);
    lockIpOnce($_POST['identifiant']);
    echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
    exit;
}

$query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
$query->execute(array(
    'adresseIP' => $_SERVER['REMOTE_ADDR']
));
$query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
$query->execute(array(
    'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - '.$VERROUILLAGE_IP_TEMPS.' days'))
));

$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS v ON p.idPersonne = v.idPersonne WHERE p.identifiant = :identifiant;');
$query->execute(array(
    'identifiant' => $_POST['identifiant']
));
$data = $query->fetch();

$query = $db->prepare('DELETE FROM RESETPASSWORD WHERE idPersonne = :idPersonne;');
$query->execute(array(
    'idPersonne' => $data['idPersonne']
));

if($MAINTENANCE==1 AND $data['maintenance']==0)
{
	writeInLogs("Connexion refusée par le mode maintenance", '2', $_POST['identifiant']);
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

loadSession($data['idPersonne']);

sleep(1);

if (isUserFirstLogin($data['idPersonne'])==true)
{
    writeInLogs("Connexion réussie et redirection automatique sur la modification de mot de passe.", '1', NULL);
    echo "<script type='text/javascript'>document.location.replace('loginChangePWDstart.php');</script>";
    exit;
}

if($data['doubleAuthSecret'] == Null)
{
	$_SESSION['connexion_connexion'] = $data['connexion_connexion'];
	writeInLogs("MFA non-paramétré", '1', NULL);
	writeInLogs("Connexion réussie.", '1', NULL);
	echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
}
else
{
	$_SESSION['connexion_connexion'] = "0";
	writeInLogs("MFA paramétré, redirection sur la page MFA", '1', NULL);
	echo "<script type='text/javascript'>document.location.replace('loginMFA.php');</script>";
	exit;
}


?>