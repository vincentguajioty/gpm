<?php

require_once 'bdd.php';

$VERSIONCHECK = '9.5';

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

$VEHICULES_CT_DELAIS_NOTIF = $data['vehicules_ct_delais_notif'];
$VEHICULES_REVISION_DELAIS_NOTIF = $data['vehicules_revision_delais_notif'];
$VEHICULES_ASSURANCE_DELAIS_NOTIF = $data['vehicules_assurance_delais_notif'];

$DEBUG           = 0;

$XSS_SECURITY    = array("script", "<", ">", "/");

?>