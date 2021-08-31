<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

	$_POST['conf_indicateur1Accueil'] = ($_POST['conf_indicateur1Accueil'] ==1) ? 1 : 0;
	$_POST['conf_indicateur2Accueil'] = ($_POST['conf_indicateur2Accueil'] ==1) ? 1 : 0;
	$_POST['conf_indicateur3Accueil'] = ($_POST['conf_indicateur3Accueil'] ==1) ? 1 : 0;
	$_POST['conf_indicateur4Accueil'] = ($_POST['conf_indicateur4Accueil'] ==1) ? 1 : 0;
	$_POST['conf_indicateur5Accueil'] = ($_POST['conf_indicateur5Accueil'] ==1) ? 1 : 0;
	$_POST['conf_indicateur6Accueil'] = ($_POST['conf_indicateur6Accueil'] ==1) ? 1 : 0;
	$_POST['conf_indicateur7Accueil'] = ($_POST['conf_indicateur7Accueil'] ==1) ? 1 : 0;
	$_POST['conf_indicateur8Accueil'] = ($_POST['conf_indicateur8Accueil'] ==1) ? 1 : 0;
    
    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET conf_joursCalendAccueil = :conf_joursCalendAccueil, conf_indicateur1Accueil = :conf_indicateur1Accueil, conf_indicateur2Accueil = :conf_indicateur2Accueil, conf_indicateur3Accueil = :conf_indicateur3Accueil, conf_indicateur4Accueil = :conf_indicateur4Accueil, conf_indicateur5Accueil = :conf_indicateur5Accueil, conf_indicateur6Accueil = :conf_indicateur6Accueil, conf_indicateur7Accueil = :conf_indicateur7Accueil, conf_indicateur8Accueil = :conf_indicateur8Accueil, conf_accueilRefresh = :conf_accueilRefresh WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_SESSION['idPersonne'],
        'conf_joursCalendAccueil' => $_POST['conf_joursCalendAccueil'],
        'conf_indicateur1Accueil' => $_POST['conf_indicateur1Accueil'],
        'conf_indicateur2Accueil' => $_POST['conf_indicateur2Accueil'],
        'conf_indicateur3Accueil' => $_POST['conf_indicateur3Accueil'],
        'conf_indicateur4Accueil' => $_POST['conf_indicateur4Accueil'],
        'conf_indicateur5Accueil' => $_POST['conf_indicateur5Accueil'],
        'conf_indicateur6Accueil' => $_POST['conf_indicateur6Accueil'],
        'conf_indicateur7Accueil' => $_POST['conf_indicateur7Accueil'],
        'conf_indicateur8Accueil' => $_POST['conf_indicateur8Accueil'],
        'conf_accueilRefresh' => $_POST['conf_accueilRefresh']
    ));

    $_SESSION['conf_joursCalendAccueil'] = $_POST['conf_joursCalendAccueil'];
    $_SESSION['conf_indicateur1Accueil'] = $_POST['conf_indicateur1Accueil'];
    $_SESSION['conf_indicateur2Accueil'] = $_POST['conf_indicateur2Accueil'];
    $_SESSION['conf_indicateur3Accueil'] = $_POST['conf_indicateur3Accueil'];
    $_SESSION['conf_indicateur4Accueil'] = $_POST['conf_indicateur4Accueil'];
	$_SESSION['conf_indicateur5Accueil'] = $_POST['conf_indicateur5Accueil'];
    $_SESSION['conf_indicateur6Accueil'] = $_POST['conf_indicateur6Accueil'];
    $_SESSION['conf_indicateur7Accueil'] = $_POST['conf_indicateur7Accueil'];
    $_SESSION['conf_indicateur8Accueil'] = $_POST['conf_indicateur8Accueil'];
    $_SESSION['conf_accueilRefresh'] = $_POST['conf_accueilRefresh'];


switch($query->errorCode())
{
    case '00000':
        writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a modifié son profil.", '3');
        $_SESSION['returnMessage'] = 'Profil mis à jour avec succès.';
        $_SESSION['returnType'] = '1';
        break;

    default:
        writeInLogs("Erreur inconnue lors de la modification de son profil par l'utilisateur " . $_SESSION['identifiant'], '5');
        $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du profil.";
        $_SESSION['returnType'] = '2';
}


    echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
?>