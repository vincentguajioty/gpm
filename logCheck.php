<?php
session_start();
require_once 'config/config.php';
require_once 'verrouIPcheck.php';

if (($VERSION != $VERSIONCHECK) AND (file_exists('distmaj/INSTALL.php')))
{
    echo "<script type='text/javascript'>document.location.replace('distmaj/INSTALL.php');</script>";
    exit;
}

if ($_SESSION['connexion_connexion'] == 0)
{
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    exit;
}

if(
	(strpos($_SERVER['PHP_SELF'], "loginCNIL.php") == false)
	AND $_SESSION['disclaimerAccept'] == Null
)
{
	echo "<script type='text/javascript'>document.location.replace('loginCNIL.php');</script>";
    exit;
}

if ($MAINTENANCE == 1 AND $_SESSION['maintenance'] == 0)
{
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    exit;
}

if (
    (strpos($_SERVER['HTTP_REFERER'], "lotsInventaireNew.php") == false)
    AND (strpos($_SERVER['HTTP_REFERER'], "reserveInventaireNew.php") == false)
    AND (strpos($_SERVER['HTTP_REFERER'], "lotsInventaireCBNew.php") == false)
    AND (strpos($_SERVER['HTTP_REFERER'], "lotsInventaireCBNewCheckScan.php") == false)
    AND (strpos($_SERVER['HTTP_REFERER'], "reserveInventaireCBNew.php") == false)
    AND (strpos($_SERVER['HTTP_REFERER'], "reserveInventaireCBNewCheckScan.php") == false)
    AND (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $LOGOUTTEMP * 60)))
{
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    exit;
}

if (checkIP($_SERVER['REMOTE_ADDR'])==1)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

$_SESSION['LAST_ACTIVITY'] = time();
?>