<?php

require_once 'bdd.php';

$VERSIONCHECK = '14.2';

$query = $db->query('SELECT * FROM CONFIG;');
$data = $query->fetch();

$SITECOLOR       = $data['sitecolor'];
$APPNAME         = $data['appname'];
$URLSITE         = $data['urlsite'];
$VERSION         = $data['version'];
$MAILSERVER      = $data['mailserver'];
$MAILISSMTP      = $data['mailIsSMTP'];
$MAILCNIL        = $data['mailcnil'];
$MAILCOPY        = $data['mailcopy'];
$LOGOUTTEMP      = $data['logouttemp'];
$MAINTENANCE     = $data['maintenance'];
$CONFSUPPRESSION = $data['confirmationSuppression'];
$RESETPASSWORD   = $data['resetPassword'];
$SELPRE          = $data['selPre'];
$SELPOST         = $data['selPost'];

$ALERTES_BENEVOLES_LOTS      = $data['alertes_benevoles_lots'];
$ALERTES_BENEVOLES_VEHICULES = $data['alertes_benevoles_vehicules'];
$CONSOMMATION_BENEVOLES      = $data['consommation_benevoles'];

if($data['aesFournisseurTemoin'] != Null){$AESFOUR = true;}else{$AESFOUR = false;}

$VERROUILLAGE_IP_OCCURANCES = $data['verrouillage_ip_occurances'];
$VERROUILLAGE_IP_TEMPS      = $data['verrouillage_ip_temps'];

$RECAPTCHA_ENABLE    = $data['reCaptcha_enable'];
$RECAPTCHA_SITEKEY   = $data['reCaptcha_siteKey'];
$RECAPTCHA_SECRETKEY = $data['reCaptcha_secretKey'];
$RECAPTCHA_SCOREMIN  = $data['reCaptcha_scoreMin'];

$XSS_SECURITY    = array("script", "SCRIPT", "<", ">", "/");

$LDAP_DOMAIN   = $data['LDAP_DOMAIN'];
$LDAP_BASEDN   = $data['LDAP_BASEDN'];
$LDAP_ISWINAD  = $data['LDAP_ISWINAD'];
$LDAP_SSL      = $data['LDAP_SSL'];
$LDAP_USER     = $data['LDAP_USER'];
$LDAP_PASSWORD = $data['LDAP_PASSWORD'];

if($VERSION >= 12)
{
	$lotsLocks = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE inventaireEnCours = 1;');
	$lotsLocks = $lotsLocks->fetch();
	if($lotsLocks['nb']>0)
	{
		$LOTSLOCK = 1;
	}
	else
	{
		$LOTSLOCK = 0;
	}

	$reservesLock = $db->query('SELECT COUNT(*) as nb FROM RESERVES_CONTENEUR WHERE inventaireEnCours = 1;');
	$reservesLock = $reservesLock->fetch();
	if($reservesLock['nb']>0)
	{
		$RESERVESLOCK = 1;
	}
	else
	{
		$RESERVESLOCK = 0;
	}
}

?>