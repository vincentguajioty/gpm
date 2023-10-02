<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['annuaire_mdp'] == 0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET doubleAuthSecret = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Désactivation pour l'utilisateur " . $_GET['id'] . " du 2FA.", '1', NULL);
            $_SESSION['returnMessage'] = 'Double authentification désactivée avec succès';
            $_SESSION['returnType'] = '1';
            unset($_SESSION['doubleAuthSecret_config']);
            break;

        default:
            writeInLogs("Erreur inconnue lors de la désactivation du 2FA pour l'utilisateur " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la désactivation de la double authentification.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}

?>
