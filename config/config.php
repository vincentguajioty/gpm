<?php

require_once 'bdd.php';

$query = $db->query('SELECT * FROM CONFIG;');
$data = $query->fetch();

$SITECOLOR = $data['sitecolor'];
$APPNAME = $data['appname'];
$URLSITE = $data['urlsite'];
$VERSION = $data['version'];
$MAILSERVER = $data['mailserver'];
$LOGOUTTEMP = $data['logouttemp'];

?>