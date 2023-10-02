<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['reserve_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
	unlockReservesInventaires();
    
    echo "<script type='text/javascript'>document.location.replace('reserveConteneurs.php');</script>";
}
?>