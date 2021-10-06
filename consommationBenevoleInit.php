<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'verrouIPcheck.php';

if (checkIP($_SERVER['REMOTE_ADDR'])==1)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

if ($MAINTENANCE)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

if ($CONSOMMATION_BENEVOLES==0)
{
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
}
else
{
	
	if($RECAPTCHA_ENABLE)
	{
		$reCaptchaReturn = getCaptcha($_POST['g-recaptcha-response']);
		
		if($reCaptchaReturn->success == true AND $reCaptchaReturn->score > $RECAPTCHA_SCOREMIN)
		{
			writeInLogs("Google reCaptcha V3 - Soumission du formulaire autorisée", '1', NULL);
		}
		else
		{
			writeInLogs("Google reCaptcha V3 - Soumission du formulaire bloquée avec un score de ".$reCaptchaReturn->score, '2', NULL);
			echo "<script type='text/javascript'>document.location.replace('alerteBenevoleFailure.php');</script>";
			exit;
		}
	}
	
	
    $_POST['nomDeclarantConsommation']    = str_replace($XSS_SECURITY, "", $_POST['nomDeclarantConsommation']);
    $_POST['evenementConsommation']       = str_replace($XSS_SECURITY, "", $_POST['evenementConsommation']);
    $_POST['dateConsommation']            = str_replace($XSS_SECURITY, "", $_POST['dateConsommation']);
	
    $_SESSION['nomDeclarantConsommation'] = $_POST['nomDeclarantConsommation'];
    $_SESSION['dateConsommation']         = $_POST['dateConsommation'];
    $_SESSION['evenementConsommation']    = $_POST['evenementConsommation'];
    $_SESSION['ipDeclarantConsommation']  = $_SERVER['REMOTE_ADDR'];
    
    echo "<script type='text/javascript'>document.location.replace('consommationBenevole.php');</script>";
    
}
?>