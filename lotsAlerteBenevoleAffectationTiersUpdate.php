<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['alertesBenevolesLots_affectationTier']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('
        UPDATE
            LOTS_ALERTES
        SET
            datePriseEnCompteAlerte = CURRENT_TIMESTAMP(),
            idTraitant              = :idTraitant,
            idLotsAlertesEtat       = 2
        WHERE
            idAlerte                = :idAlerte
        ;');
    $query->execute(array(
        'idAlerte'                => $_GET['id'],
        'idTraitant'              => $_POST['idPersonne'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Affectation manuelle de l'alerte de lots ".$_GET['id']." à la personne ".$_POST['idPersonne'], '1', NULL);
            $_SESSION['returnMessage'] = 'Le traitement de l\'alerte a bien été affecté à une personne de l\'équipe.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur dans l'affectation manuellen de l'alerte de lots ".$_GET['id']." à la personne ".$_SESSION['idPersonne'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'affectation de l'alerte.";
            $_SESSION['returnType'] = '2';
    }
    
    echo "<script type='text/javascript'>document.location.replace('lotsAlerteBenevoleSynthese.php');</script>";
}
?>