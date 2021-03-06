<?php

session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($RESETPASSWORD == 0)
{ 
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    exit;
}           

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

$reset = $db->prepare('SELECT * FROM RESETPASSWORD WHERE idReset = :idReset;');
$reset->execute(array('idReset'=>$_GET['idReset']));
$reset = $reset->fetch();

if ($_POST['new1'] != $_POST['new2'])
{
    echo "<script type='text/javascript'>document.location.replace('passwordResetResult.php?pwd=ko');</script>";
}
else
{
    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET motDePasse = :motDePasse WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'motDePasse' => password_hash($SELPRE.$_POST['new1'].$SELPOST, PASSWORD_DEFAULT),
        'idPersonne' => $reset['idPersonne']
    ));

    switch($query->errorCode())
    {
        case '00000':
            $delete = $db->prepare('DELETE FROM RESETPASSWORD WHERE idReset = :idReset;');
            $delete->execute(array('idReset'=>$_GET['idReset']));
            writeInLogs("L'utilisateur " . $reset['idPersonne'] . " a réinitialisé son mot de passe par mail.", '1', NULL);
            $_SESSION['returnMessage'] = 'Mot de passe modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la tentative de la réinitialisation du mot de passe par mail de l'utilisateur " . $reset['idPersonne'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du mot de passe.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";

}
?>