<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['DELEGATION_ACTIVE'] == 1)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET doubleAuthSecret = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_SESSION['idPersonne'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a désactivé le 2FA.", '1', NULL);
            $_SESSION['returnMessage'] = 'Double authentification désactivée avec succès';
            $_SESSION['returnType'] = '1';
            unset($_SESSION['doubleAuthSecret_config']);
            break;

        default:
            writeInLogs("Erreur inconnue lors de la tentative de désactivation du 2FA par " . $_SESSION['identifiant'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la désactivation de la double authentification.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}

?>
