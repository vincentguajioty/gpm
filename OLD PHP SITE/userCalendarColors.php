<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
    
    $query = $db->prepare('
    	UPDATE
    		PERSONNE_REFERENTE
    	SET
			agenda_lots_peremption              = :agenda_lots_peremption,
			agenda_reserves_peremption          = :agenda_reserves_peremption,
			agenda_lots_inventaireAF            = :agenda_lots_inventaireAF,
			agenda_lots_inventaireF             = :agenda_lots_inventaireF,
			agenda_commandes_livraison          = :agenda_commandes_livraison,
			agenda_vehicules_revision           = :agenda_vehicules_revision,
			agenda_vehicules_ct                 = :agenda_vehicules_ct,
			agenda_vehicules_assurance          = :agenda_vehicules_assurance,
			agenda_vehicules_maintenance        = :agenda_vehicules_maintenance,
			agenda_desinfections_desinfectionF  = :agenda_desinfections_desinfectionF,
			agenda_desinfections_desinfectionAF = :agenda_desinfections_desinfectionAF,
			agenda_reserves_inventaireAF        = :agenda_reserves_inventaireAF,
			agenda_reserves_inventaireF         = :agenda_reserves_inventaireF,
			agenda_tenues_tenues                = :agenda_tenues_tenues,
			agenda_tenues_toDoList              = :agenda_tenues_toDoList,
			agenda_healthF                      = :agenda_healthF,
			agenda_healthAF                     = :agenda_healthAF
    	WHERE
    		idPersonne = :idPersonne ;');
    $query->execute(array(
		'idPersonne'                          => $_SESSION['idPersonne'],
		'agenda_lots_peremption'              => $_POST['agenda_lots_peremption'],
		'agenda_reserves_peremption'          => $_POST['agenda_reserves_peremption'],
		'agenda_lots_inventaireAF'            => $_POST['agenda_lots_inventaireAF'],
		'agenda_lots_inventaireF'             => $_POST['agenda_lots_inventaireF'],
		'agenda_commandes_livraison'          => $_POST['agenda_commandes_livraison'],
		'agenda_vehicules_revision'           => $_POST['agenda_vehicules_revision'],
		'agenda_vehicules_ct'                 => $_POST['agenda_vehicules_ct'],
		'agenda_vehicules_assurance'          => $_POST['agenda_vehicules_assurance'],
		'agenda_vehicules_maintenance'        => $_POST['agenda_vehicules_maintenance'],
		'agenda_desinfections_desinfectionF'  => $_POST['agenda_desinfections_desinfectionF'],
		'agenda_desinfections_desinfectionAF' => $_POST['agenda_desinfections_desinfectionAF'],
		'agenda_reserves_inventaireAF'        => $_POST['agenda_reserves_inventaireAF'],
		'agenda_reserves_inventaireF'         => $_POST['agenda_reserves_inventaireF'],
		'agenda_tenues_tenues'                => $_POST['agenda_tenues_tenues'],
		'agenda_tenues_toDoList'              => $_POST['agenda_tenues_toDoList'],
		'agenda_healthF'                      => $_POST['agenda_healthF'],
        'agenda_healthAF'                     => $_POST['agenda_healthAF'],
		
    ));


switch($query->errorCode())
{
    case '00000':
        writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a modifié ses couleurs de calendrier dans son profil.", '1', NULL);
        $_SESSION['returnMessage'] = 'Couleurs mises à jour avec succès.';
        $_SESSION['returnType'] = '1';
    	
    	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne');
    	$query->execute(array(
        	'idPersonne' => $_SESSION['idPersonne']
        ));
        $data = $query->fetch();
    	
		$_SESSION['agenda_lots_peremption']              = $data['agenda_lots_peremption'];
		$_SESSION['agenda_reserves_peremption']          = $data['agenda_reserves_peremption'];
		$_SESSION['agenda_lots_inventaireAF']            = $data['agenda_lots_inventaireAF'];
		$_SESSION['agenda_lots_inventaireF']             = $data['agenda_lots_inventaireF'];
		$_SESSION['agenda_commandes_livraison']          = $data['agenda_commandes_livraison'];
		$_SESSION['agenda_vehicules_revision']           = $data['agenda_vehicules_revision'];
		$_SESSION['agenda_vehicules_ct']                 = $data['agenda_vehicules_ct'];
		$_SESSION['agenda_vehicules_assurance']          = $data['agenda_vehicules_assurance'];
		$_SESSION['agenda_vehicules_maintenance']        = $data['agenda_vehicules_maintenance'];
		$_SESSION['agenda_desinfections_desinfectionF']  = $data['agenda_desinfections_desinfectionF'];
		$_SESSION['agenda_desinfections_desinfectionAF'] = $data['agenda_desinfections_desinfectionAF'];
		$_SESSION['agenda_reserves_inventaireAF']        = $data['agenda_reserves_inventaireAF'];
		$_SESSION['agenda_reserves_inventaireF']         = $data['agenda_reserves_inventaireF'];
		$_SESSION['agenda_tenues_tenues']                = $data['agenda_tenues_tenues'];
		$_SESSION['agenda_tenues_toDoList']              = $data['agenda_tenues_toDoList'];
		$_SESSION['agenda_healthF']                      = $data['agenda_healthF'];
		$_SESSION['agenda_healthAF']                     = $data['agenda_healthAF'];
        
        break;

    default:
        writeInLogs("Erreur inconnue lors de la modification des couleurs de calendrier dans son profil par l'utilisateur " . $_SESSION['identifiant'], '3', NULL);
        $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification des couleurs.";
        $_SESSION['returnType'] = '2';
}

	
    
    echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
?>