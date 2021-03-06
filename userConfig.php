<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

	$_POST['conf_indicateur1Accueil']       = ($_POST['conf_indicateur1Accueil']       == 1) ? 1 : 0;
	$_POST['conf_indicateur2Accueil']       = ($_POST['conf_indicateur2Accueil']       == 1) ? 1 : 0;
	$_POST['conf_indicateur3Accueil']       = ($_POST['conf_indicateur3Accueil']       == 1) ? 1 : 0;
	$_POST['conf_indicateur4Accueil']       = ($_POST['conf_indicateur4Accueil']       == 1) ? 1 : 0;
	$_POST['conf_indicateur5Accueil']       = ($_POST['conf_indicateur5Accueil']       == 1) ? 1 : 0;
	$_POST['conf_indicateur6Accueil']       = ($_POST['conf_indicateur6Accueil']       == 1) ? 1 : 0;
	$_POST['conf_indicateur9Accueil']       = ($_POST['conf_indicateur9Accueil']       == 1) ? 1 : 0;
	$_POST['conf_indicateur10Accueil']      = ($_POST['conf_indicateur10Accueil']      == 1) ? 1 : 0;
	$_POST['conf_indicateur11Accueil']      = ($_POST['conf_indicateur11Accueil']      == 1) ? 1 : 0;
	$_POST['conf_indicateur12Accueil']      = ($_POST['conf_indicateur12Accueil']      == 1) ? 1 : 0;
	
	$_POST['notif_lots_manquants']          = ($_POST['notif_lots_manquants']          == 1) ? 1 : 0;
	$_POST['notif_lots_peremptions']        = ($_POST['notif_lots_peremptions']        == 1) ? 1 : 0;
	$_POST['notif_lots_inventaires']        = ($_POST['notif_lots_inventaires']        == 1) ? 1 : 0;
	$_POST['notif_lots_conformites']        = ($_POST['notif_lots_conformites']        == 1) ? 1 : 0;
	$_POST['notif_reserves_manquants']      = ($_POST['notif_reserves_manquants']      == 1) ? 1 : 0;
	$_POST['notif_reserves_peremptions']    = ($_POST['notif_reserves_peremptions']    == 1) ? 1 : 0;
	$_POST['notif_reserves_inventaires']    = ($_POST['notif_reserves_inventaires']    == 1) ? 1 : 0;
	$_POST['notif_vehicules_desinfections'] = ($_POST['notif_vehicules_desinfections'] == 1) ? 1 : 0;
	$_POST['notif_vehicules_health']        = ($_POST['notif_vehicules_health']        == 1) ? 1 : 0;
	$_POST['notif_tenues_stock']            = ($_POST['notif_tenues_stock']            == 1) ? 1 : 0;
	$_POST['notif_tenues_retours']          = ($_POST['notif_tenues_retours']          == 1) ? 1 : 0;
	$_POST['notif_benevoles_lots']          = ($_POST['notif_benevoles_lots']          == 1) ? 1 : 0;
	$_POST['notif_benevoles_vehicules']     = ($_POST['notif_benevoles_vehicules']     == 1) ? 1 : 0;
    
    $query = $db->prepare('
    	UPDATE
    		PERSONNE_REFERENTE
    	SET
    		conf_indicateur1Accueil       = :conf_indicateur1Accueil,
    		conf_indicateur2Accueil       = :conf_indicateur2Accueil,
    		conf_indicateur3Accueil       = :conf_indicateur3Accueil,
    		conf_indicateur4Accueil       = :conf_indicateur4Accueil,
    		conf_indicateur5Accueil       = :conf_indicateur5Accueil,
    		conf_indicateur6Accueil       = :conf_indicateur6Accueil,
    		conf_indicateur9Accueil       = :conf_indicateur9Accueil,
    		conf_indicateur10Accueil      = :conf_indicateur10Accueil,
    		conf_indicateur11Accueil      = :conf_indicateur11Accueil,
    		conf_indicateur12Accueil      = :conf_indicateur12Accueil,
    		conf_accueilRefresh           = :conf_accueilRefresh,
    		notif_lots_manquants          = :notif_lots_manquants,
    		notif_lots_peremptions        = :notif_lots_peremptions,
    		notif_lots_inventaires        = :notif_lots_inventaires,
    		notif_lots_conformites        = :notif_lots_conformites,
    		notif_reserves_manquants      = :notif_reserves_manquants,
    		notif_reserves_peremptions    = :notif_reserves_peremptions,
    		notif_reserves_inventaires    = :notif_reserves_inventaires,
    		notif_vehicules_desinfections = :notif_vehicules_desinfections,
    		notif_vehicules_health        = :notif_vehicules_health,
    		notif_tenues_stock            = :notif_tenues_stock,
    		notif_tenues_retours          = :notif_tenues_retours,
    		notif_benevoles_lots          = :notif_benevoles_lots,
			notif_benevoles_vehicules     = :notif_benevoles_vehicules,
    		tableRowPerso                 = :tableRowPerso,
    		layout                        = :layout
    	WHERE
    		idPersonne = :idPersonne ;');
    $query->execute(array(
		'idPersonne'                    => $_SESSION['idPersonne'],
		'conf_indicateur1Accueil'       => $_POST['conf_indicateur1Accueil'],
		'conf_indicateur2Accueil'       => $_POST['conf_indicateur2Accueil'],
		'conf_indicateur3Accueil'       => $_POST['conf_indicateur3Accueil'],
		'conf_indicateur4Accueil'       => $_POST['conf_indicateur4Accueil'],
		'conf_indicateur5Accueil'       => $_POST['conf_indicateur5Accueil'],
		'conf_indicateur6Accueil'       => $_POST['conf_indicateur6Accueil'],
		'conf_indicateur9Accueil'       => $_POST['conf_indicateur9Accueil'],
		'conf_indicateur10Accueil'      => $_POST['conf_indicateur10Accueil'],
		'conf_indicateur11Accueil'      => $_POST['conf_indicateur11Accueil'],
		'conf_indicateur12Accueil'      => $_POST['conf_indicateur12Accueil'],
		'conf_accueilRefresh'           => $_POST['conf_accueilRefresh'],
		'notif_lots_manquants'          => $_POST['notif_lots_manquants'],
		'notif_lots_peremptions'        => $_POST['notif_lots_peremptions'],
		'notif_lots_inventaires'        => $_POST['notif_lots_inventaires'],
		'notif_lots_conformites'        => $_POST['notif_lots_conformites'],
		'notif_reserves_manquants'      => $_POST['notif_reserves_manquants'],
		'notif_reserves_peremptions'    => $_POST['notif_reserves_peremptions'],
		'notif_reserves_inventaires'    => $_POST['notif_reserves_inventaires'],
		'notif_vehicules_desinfections' => $_POST['notif_vehicules_desinfections'],
		'notif_vehicules_health'        => $_POST['notif_vehicules_health'],
		'notif_tenues_stock'            => $_POST['notif_tenues_stock'],
		'notif_tenues_retours'          => $_POST['notif_tenues_retours'],
		'notif_benevoles_lots'          => $_POST['notif_benevoles_lots'],
		'notif_benevoles_vehicules'     => $_POST['notif_benevoles_vehicules'],
		'tableRowPerso'                 => $_POST['tableRowPerso'],
		'layout'                        => $_POST['layout'],
    ));

    $_SESSION['tableRowPerso'] = $_POST['tableRowPerso'];


switch($query->errorCode())
{
    case '00000':
        writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a modifi?? son profil.", '1', NULL);
        $_SESSION['returnMessage'] = 'Profil mis ?? jour avec succ??s.';
        $_SESSION['returnType'] = '1';
        
        majIndicateursPersonne($_SESSION['idPersonne'],1);
    	majNotificationsPersonne($_SESSION['idPersonne'],1);
    	
    	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne');
    	$query->execute(array(
        	'idPersonne' => $_SESSION['idPersonne']
        ));
        $data = $query->fetch();
    	
		$_SESSION['conf_indicateur1Accueil']  = $data['conf_indicateur1Accueil'];
		$_SESSION['conf_indicateur2Accueil']  = $data['conf_indicateur2Accueil'];
		$_SESSION['conf_indicateur3Accueil']  = $data['conf_indicateur3Accueil'];
		$_SESSION['conf_indicateur4Accueil']  = $data['conf_indicateur4Accueil'];
		$_SESSION['conf_indicateur5Accueil']  = $data['conf_indicateur5Accueil'];
		$_SESSION['conf_indicateur6Accueil']  = $data['conf_indicateur6Accueil'];
		$_SESSION['conf_indicateur7Accueil']  = $data['conf_indicateur7Accueil'];
		$_SESSION['conf_indicateur8Accueil']  = $data['conf_indicateur8Accueil'];
		$_SESSION['conf_indicateur9Accueil']  = $data['conf_indicateur9Accueil'];
		$_SESSION['conf_indicateur10Accueil'] = $data['conf_indicateur10Accueil'];
		$_SESSION['conf_indicateur11Accueil'] = $data['conf_indicateur11Accueil'];
		$_SESSION['conf_indicateur12Accueil'] = $data['conf_indicateur12Accueil'];
		$_SESSION['conf_accueilRefresh']      = $data['conf_accueilRefresh'];

		$_SESSION['layout']                   = $data['layout'];
		
		$queryDelete = $db->prepare('DELETE FROM NOTIFICATIONS_ABONNEMENTS WHERE idPersonne = :idPersonne');
	    $queryDelete->execute([
	        ':idPersonne' => $_SESSION['idPersonne']
	    ]);
	    if (!empty($_POST['idCondition'])) {
	        $insertSQL = 'INSERT INTO NOTIFICATIONS_ABONNEMENTS (idCondition, idPersonne) VALUES';
	        foreach ($_POST['idCondition'] as $idCondition) {
	            $insertSQL .= ' ('. (int)$idCondition.', '. (int)$_SESSION['idPersonne'] .'),';
	        }
	
	        $insertSQL = substr($insertSQL, 0, -1);
	
	        $db->query($insertSQL);
	    }
        
        break;

    default:
        writeInLogs("Erreur inconnue lors de la modification de son profil par l'utilisateur " . $_SESSION['identifiant'], '3', NULL);
        $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du profil.";
        $_SESSION['returnType'] = '2';
}

	
    
    echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
?>