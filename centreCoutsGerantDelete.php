<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['cout_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('DELETE FROM CENTRE_COUTS_PERSONNES WHERE idGerant = :idGerant;');
    $query->execute(array(
        'idGerant' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression d'un gérant de entre de cout.", '1', NULL);
            $_SESSION['returnMessage'] = 'Gérant supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression d'un gérant de centre de couts", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression du gérant.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>