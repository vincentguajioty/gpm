<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['tenues_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT idCatalogueTenue FROM TENUES_AFFECTATION WHERE idTenue = :idTenue');
    $query->execute(array('idTenue' => $_GET['id']));
    $data = $query->fetch();
    $query = $db->prepare('UPDATE TENUES_CATALOGUE SET stockCatalogueTenue = stockCatalogueTenue + 1 WHERE idCatalogueTenue = :idCatalogueTenue');
    $query->execute(array('idCatalogueTenue' => $data['idCatalogueTenue']));

    $_POST['idCatalogueTenue'] = ($_POST['idCatalogueTenue'] == Null) ? Null : $_POST['idCatalogueTenue'];
	$_POST['idPersonne'] = ($_POST['idPersonne'] == Null) ? Null : $_POST['idPersonne'];
    $_POST['personneNonGPM'] = ($_POST['idPersonne'] == Null) ? $_POST['personneNonGPM'] : Null;
    $_POST['dateAffectation'] = ($_POST['dateAffectation'] == Null) ? Null : $_POST['dateAffectation'];
    $_POST['dateRetour'] = ($_POST['dateRetour'] == Null) ? Null : $_POST['dateRetour'];

    $query = $db->prepare('
        UPDATE
            TENUES_AFFECTATION
        SET
            idCatalogueTenue = :idCatalogueTenue,
            idPersonne       = :idPersonne,
            personneNonGPM   = :personneNonGPM,
            dateAffectation  = :dateAffectation,
            dateRetour       = :dateRetour
        WHERE
            idTenue          = :idTenue
        ;');
    $query->execute(array(
        'idCatalogueTenue' => $_POST['idCatalogueTenue'],
        'idPersonne'       => $_POST['idPersonne'],
        'personneNonGPM'   => $_POST['personneNonGPM'],
        'dateAffectation'  => $_POST['dateAffectation'],
        'dateRetour'       => $_POST['dateRetour'],
        'idTenue'          => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification d'une affectation de tenue.", '1', NULL);
            $_SESSION['returnMessage'] = 'Element modifié avec succès.';
            $_SESSION['returnType'] = '1';

            $query = $db->prepare('UPDATE TENUES_CATALOGUE SET stockCatalogueTenue = stockCatalogueTenue - 1 WHERE idCatalogueTenue = :idCatalogueTenue');
            $query->execute(array('idCatalogueTenue' => $_POST['idCatalogueTenue']));
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de l'affectation.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'affectation.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>