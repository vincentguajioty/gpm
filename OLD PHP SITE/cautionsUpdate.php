<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['cautions_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idPersonne'] = ($_POST['idPersonne'] == Null) ? Null : $_POST['idPersonne'];
    $_POST['personneNonGPM'] = ($_POST['personneNonGPM'] == Null) ? Null : $_POST['personneNonGPM'];
    $_POST['montantCaution'] = ($_POST['montantCaution'] == Null) ? Null : $_POST['montantCaution'];
    $_POST['dateEmissionCaution'] = ($_POST['dateEmissionCaution'] == Null) ? Null : $_POST['dateEmissionCaution'];
    $_POST['dateExpirationCaution'] = ($_POST['dateExpirationCaution'] == Null) ? Null : $_POST['dateExpirationCaution'];
    $_POST['detailsMoyenPaiement'] = ($_POST['detailsMoyenPaiement'] == Null) ? Null : $_POST['detailsMoyenPaiement'];

    $query = $db->prepare('
        UPDATE
            CAUTIONS
        SET
            idPersonne            = :idPersonne,
            personneNonGPM        = :personneNonGPM,
            montantCaution        = :montantCaution,
            dateEmissionCaution   = :dateEmissionCaution,
            dateExpirationCaution = :dateExpirationCaution,
            detailsMoyenPaiement  = :detailsMoyenPaiement
        WHERE
            idCaution = :idCaution;');
    $query->execute(array(
        'idPersonne'            => $_POST['idPersonne'],
        'personneNonGPM'        => $_POST['personneNonGPM'],
        'montantCaution'        => $_POST['montantCaution'],
        'dateEmissionCaution'   => $_POST['dateEmissionCaution'],
        'dateExpirationCaution' => $_POST['dateExpirationCaution'],
        'detailsMoyenPaiement'  => $_POST['detailsMoyenPaiement'],
        'idCaution'             => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification d'une caution avec idCaution ".$_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Caution modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification d'une caution avec idCaution ".$_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>