<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

	$query = $db->query('UPDATE CONFIG SET maintenance = 0;');

	writeInLogs("Désactivation de la page de maintenance.", '2', NULL);
	echo "<script type='text/javascript'>document.location.replace('index.php');</script>";

}

?>