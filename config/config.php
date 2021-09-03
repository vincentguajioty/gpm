<?php

require_once 'bdd.php';

$VERSIONCHECK = '11.1';

$query = $db->query('SELECT * FROM CONFIG;');
$data = $query->fetch();

$SITECOLOR       = $data['sitecolor'];
$APPNAME         = $data['appname'];
$URLSITE         = $data['urlsite'];
$VERSION         = $data['version'];
$MAILSERVER      = $data['mailserver'];
$MAILCOPY        = $data['mailcopy'];
$LOGOUTTEMP      = $data['logouttemp'];
$MAINTENANCE     = $data['maintenance'];
$CONFSUPPRESSION = $data['confirmationSuppression'];
$RESETPASSWORD   = $data['resetPassword'];
$SELPRE          = $data['selPre'];
$SELPOST         = $data['selPost'];

$ALERTES_BENEVOLES_LOTS      = $data['alertes_benevoles_lots'];
$ALERTES_BENEVOLES_VEHICULES = $data['alertes_benevoles_vehicules'];

if($data['aesFournisseurTemoin'] != Null){$AESFOUR = true;}else{$AESFOUR = false;}

$VERROUILLAGE_IP_OCCURANCES = $data['verrouillage_ip_occurances'];
$VERROUILLAGE_IP_TEMPS      = $data['verrouillage_ip_temps'];

$VEHICULES_CT_DELAIS_NOTIF        = $data['vehicules_ct_delais_notif'];
$VEHICULES_REVISION_DELAIS_NOTIF  = $data['vehicules_revision_delais_notif'];
$VEHICULES_ASSURANCE_DELAIS_NOTIF = $data['vehicules_assurance_delais_notif'];

$RECAPTCHA_ENABLE    = $data['reCaptcha_enable'];
$RECAPTCHA_SITEKEY   = $data['reCaptcha_siteKey'];
$RECAPTCHA_SECRETKEY = $data['reCaptcha_secretKey'];
$RECAPTCHA_SCOREMIN  = $data['reCaptcha_scoreMin'];

$XSS_SECURITY    = array("script", "SCRIPT", "<", ">", "/");

?>