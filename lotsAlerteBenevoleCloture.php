<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['alertesBenevolesLots_affectation']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('
        UPDATE
            LOTS_ALERTES
        SET
            dateResolutionAlerte = CURRENT_TIMESTAMP(),
            idLotsAlertesEtat    = 4
        WHERE
            idAlerte             = :idAlerte
        ;');
    $query->execute(array(
        'idAlerte'                => $_GET['id'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Résolution de l'alerte de lots ".$_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'L\'alerte a été résolue.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur dans la résolution de l'alerte de lots ".$_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la résolution de l'alerte.";
            $_SESSION['returnType'] = '2';
    }
    
    echo "<script type='text/javascript'>document.location.replace('lotsAlerteBenevoleSynthese.php');</script>";
}
?>