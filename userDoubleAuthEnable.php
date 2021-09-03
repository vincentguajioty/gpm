<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once('plugins/authenticator/authenticator.php');

if ($_SESSION['DELEGATION_ACTIVE'] == 1)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $Authenticator = new Authenticator();
    $checkResult = $Authenticator->verifyCode($_SESSION['doubleAuthSecret_config'], $_POST['code'], 1);

    if (!$checkResult)
    {
        $_SESSION['returnMessage'] = 'Erreur lors de la vérification du code.';
        $_SESSION['returnType'] = '2';
    }
    else
    {
        $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET doubleAuthSecret = :doubleAuthSecret WHERE idPersonne = :idPersonne ;');
        $query->execute(array(
            'doubleAuthSecret' => $_SESSION['doubleAuthSecret_config'],
            'idPersonne' => $_SESSION['idPersonne']
        ));

        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a activé le 2FA.", '1', NULL);
                $_SESSION['returnMessage'] = 'Double authentification activée avec succès';
                $_SESSION['returnType'] = '1';
                unset($_SESSION['doubleAuthSecret_config']);
                break;

            default:
                writeInLogs("Erreur inconnue lors de la tentative d'activation du 2FA par " . $_SESSION['identifiant'], '3', NULL);
                $_SESSION['returnMessage'] = "Erreur inconnue lors de l'activation de la double authentification.";
                $_SESSION['returnType'] = '2';
        }
    }

    echo "<script>window.location = document.referrer;</script>";
}

?>
