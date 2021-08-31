<?php
session_start();
require_once 'config/bdd.php';
if ($_SESSION['connexion_connexion'] == 0)
        echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
        
$query = $db->prepare('SELECT * FROM VERROUILLAGE_IP WHERE adresseIPverr= :adresseIPverr;');
$query->execute(array(
    'adresseIPverr' => $_SERVER['REMOTE_ADDR']
));
$data = $query->fetch();

if ($data['idIP'] > 0)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
}
?>