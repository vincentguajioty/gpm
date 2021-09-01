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
	$_POST['conf_indicateur9Accueil'] = ($_POST['conf_indicateur9Accueil'] ==1) ? 1 : 0;
	$_POST['conf_indicateur10Accueil'] = ($_POST['conf_indicateur10Accueil'] ==1) ? 1 : 0;
	
	$_POST['notif_lots_manquants'] = ($_POST['notif_lots_manquants'] ==1) ? 1 : 0;
	$_POST['notif_lots_peremptions'] = ($_POST['notif_lots_peremptions'] ==1) ? 1 : 0;
	$_POST['notif_lots_inventaires'] = ($_POST['notif_lots_inventaires'] ==1) ? 1 : 0;
	$_POST['notif_lots_conformites'] = ($_POST['notif_lots_conformites'] ==1) ? 1 : 0;
	$_POST['notif_reserves_manquants'] = ($_POST['notif_reserves_manquants'] ==1) ? 1 : 0;
	$_POST['notif_reserves_peremptions'] = ($_POST['notif_reserves_peremptions'] ==1) ? 1 : 0;
	$_POST['notif_reserves_inventaires'] = ($_POST['notif_reserves_inventaires'] ==1) ? 1 : 0;
	$_POST['notif_vehicules_assurances'] = ($_POST['notif_vehicules_assurances'] ==1) ? 1 : 0;
	$_POST['notif_vehicules_revisions'] = ($_POST['notif_vehicules_revisions'] ==1) ? 1 : 0;
	$_POST['notif_vehicules_ct'] = ($_POST['notif_vehicules_ct'] ==1) ? 1 : 0;
	$_POST['notif_tenues_stock'] = ($_POST['notif_tenues_stock'] ==1) ? 1 : 0;
	$_POST['notif_tenues_retours'] = ($_POST['notif_tenues_retours'] ==1) ? 1 : 0;
    
    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET conf_indicateur1Accueil = :conf_indicateur1Accueil, conf_indicateur2Accueil = :conf_indicateur2Accueil, conf_indicateur3Accueil = :conf_indicateur3Accueil, conf_indicateur4Accueil = :conf_indicateur4Accueil, conf_indicateur5Accueil = :conf_indicateur5Accueil, conf_indicateur6Accueil = :conf_indicateur6Accueil, conf_indicateur7Accueil = :conf_indicateur7Accueil, conf_indicateur8Accueil = :conf_indicateur8Accueil, conf_indicateur9Accueil = :conf_indicateur9Accueil, conf_indicateur10Accueil = :conf_indicateur10Accueil, conf_accueilRefresh = :conf_accueilRefresh, notif_lots_manquants = :notif_lots_manquants, notif_lots_peremptions = :notif_lots_peremptions, notif_lots_inventaires = :notif_lots_inventaires, notif_lots_conformites = :notif_lots_conformites, notif_reserves_manquants = :notif_reserves_manquants, notif_reserves_peremptions = :notif_reserves_peremptions, notif_reserves_inventaires = :notif_reserves_inventaires, notif_vehicules_assurances = :notif_vehicules_assurances, notif_vehicules_revisions = :notif_vehicules_revisions, notif_vehicules_ct = :notif_vehicules_ct, notif_tenues_stock = :notif_tenues_stock, notif_tenues_retours = :notif_tenues_retours, tableRowPerso = :tableRowPerso WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_SESSION['idPersonne'],
        'conf_indicateur1Accueil' => $_POST['conf_indicateur1Accueil'],
        'conf_indicateur2Accueil' => $_POST['conf_indicateur2Accueil'],
        'conf_indicateur3Accueil' => $_POST['conf_indicateur3Accueil'],
        'conf_indicateur4Accueil' => $_POST['conf_indicateur4Accueil'],
        'conf_indicateur5Accueil' => $_POST['conf_indicateur5Accueil'],
        'conf_indicateur6Accueil' => $_POST['conf_indicateur6Accueil'],
        'conf_indicateur7Accueil' => $_POST['conf_indicateur7Accueil'],
        'conf_indicateur8Accueil' => $_POST['conf_indicateur8Accueil'],
        'conf_indicateur9Accueil' => $_POST['conf_indicateur9Accueil'],
        'conf_indicateur10Accueil' => $_POST['conf_indicateur10Accueil'],
        'conf_accueilRefresh' => $_POST['conf_accueilRefresh'],
        'notif_lots_manquants' => $_POST['notif_lots_manquants'],
		'notif_lots_peremptions' => $_POST['notif_lots_peremptions'],
		'notif_lots_inventaires' => $_POST['notif_lots_inventaires'],
		'notif_lots_conformites' => $_POST['notif_lots_conformites'],
		'notif_reserves_manquants' => $_POST['notif_reserves_manquants'],
		'notif_reserves_peremptions' => $_POST['notif_reserves_peremptions'],
		'notif_reserves_inventaires' => $_POST['notif_reserves_inventaires'],
		'notif_vehicules_assurances' => $_POST['notif_vehicules_assurances'],
		'notif_vehicules_revisions' => $_POST['notif_vehicules_revisions'],
		'notif_vehicules_ct' => $_POST['notif_vehicules_ct'],
		'notif_tenues_stock' => $_POST['notif_tenues_stock'],
		'notif_tenues_retours' => $_POST['notif_tenues_retours'],
		'tableRowPerso' => $_POST['tableRowPerso']
    ));

    $_SESSION['tableRowPerso'] = $_POST['tableRowPerso'];


switch($query->errorCode())
{
    case '00000':
        writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a modifié son profil.", '3');
        $_SESSION['returnMessage'] = 'Profil mis à jour avec succès.';
        $_SESSION['returnType'] = '1';
        
        majIndicateursPersonne($_SESSION['idPersonne'],1);
    	majNotificationsPersonne($_SESSION['idPersonne'],1);
    	
    	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne');
    	$query->execute(array(
        	'idPersonne' => $_SESSION['idPersonne']
        ));
        $data = $query->fetch();
    	
    	$_SESSION['conf_indicateur1Accueil'] = $data['conf_indicateur1Accueil'];
	    $_SESSION['conf_indicateur2Accueil'] = $data['conf_indicateur2Accueil'];
	    $_SESSION['conf_indicateur3Accueil'] = $data['conf_indicateur3Accueil'];
	    $_SESSION['conf_indicateur4Accueil'] = $data['conf_indicateur4Accueil'];
		$_SESSION['conf_indicateur5Accueil'] = $data['conf_indicateur5Accueil'];
	    $_SESSION['conf_indicateur6Accueil'] = $data['conf_indicateur6Accueil'];
	    $_SESSION['conf_indicateur7Accueil'] = $data['conf_indicateur7Accueil'];
	    $_SESSION['conf_indicateur8Accueil'] = $data['conf_indicateur8Accueil'];
	    $_SESSION['conf_indicateur9Accueil'] = $data['conf_indicateur9Accueil'];
	    $_SESSION['conf_indicateur10Accueil'] = $data['conf_indicateur10Accueil'];
	    $_SESSION['conf_accueilRefresh'] = $data['conf_accueilRefresh'];
        
        break;

    default:
        writeInLogs("Erreur inconnue lors de la modification de son profil par l'utilisateur " . $_SESSION['identifiant'], '5');
        $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du profil.";
        $_SESSION['returnType'] = '2';
}

	
    
    echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
?>