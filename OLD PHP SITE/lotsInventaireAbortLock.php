<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['lots_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
	unlockLotsInventaires();
    
    echo "<script type='text/javascript'>document.location.replace('lots.php');</script>";
}
?>