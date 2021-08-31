<?php

require_once 'bdd.php';


$VERSIONCHECK = '2.4';




$query = $db->query('SELECT * FROM CONFIG;');
$data = $query->fetch();

$SITECOLOR = $data['sitecolor'];
$APPNAME = $data['appname'];
$URLSITE = $data['urlsite'];
$VERSION = $data['version'];
$MAILSERVER = $data['mailserver'];
$LOGOUTTEMP = $data['logouttemp'];

?>