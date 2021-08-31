<?php
session_start();
require_once 'config/config.php';
require_once 'verrouIPcheck.php';

if (($VERSION != $VERSIONCHECK) AND (file_exists('distmaj/INSTALL.php')))
{
    echo "<script type='text/javascript'>document.location.replace('distmaj/INSTALL.php');</script>";
}

if (($_SESSION['connexion_connexion'] == 0) OR (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $LOGOUTTEMP * 60)))
        echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
        

if (checkIP($_SERVER['REMOTE_ADDR'])==1)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
}

$_SESSION['LAST_ACTIVITY'] = time();
?>