<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';

$query = $db->prepare('SELECT * FROM VERROUILLAGE_IP WHERE adresseIPverr= :adresseIPverr;');
$query->execute(array(
    'adresseIPverr' => $_SERVER['REMOTE_ADDR']
));
$data = $query->fetch();

if ($data['idIP'] == "")
{
	$query = $db->prepare('
		SELECT 
			pe.idPersonne,
			pe.identifiant,
			pe.nomPersonne,
			pe.prenomPersonne,
			pe.mailPersonne,
			pe.telPersonne,
			pe.fonction,
			pe.motDePasse,
			pe.conf_accueilRefresh,
			pe.conf_indicateur1Accueil,
			pe.conf_indicateur2Accueil,
			pe.conf_indicateur3Accueil,
			pe.conf_indicateur4Accueil,
			pe.conf_indicateur5Accueil,
			pe.conf_indicateur6Accueil,
			pe.conf_indicateur7Accueil,
			pe.conf_indicateur8Accueil,
			pe.derniereConnexion,
			MAX(po.connexion_connexion) as connexion_connexion,
			MAX(po.logs_lecture) as logs_lecture,
			MAX(po.annuaire_lecture) as annuaire_lecture,
			MAX(po.annuaire_ajout) as annuaire_ajout,
			MAX(po.annuaire_modification) as annuaire_modification,
			MAX(po.annuaire_mdp) as annuaire_mdp,
			MAX(po.annuaire_suppression) as annuaire_suppression,
			MAX(po.profils_lecture) as profils_lecture,
			MAX(po.profils_ajout) as profils_ajout,
			MAX(po.profils_modification) as profils_modification,
			MAX(po.profils_suppression) as profils_suppression,
			MAX(po.categories_lecture) as categories_lecture,
			MAX(po.categories_ajout) as categories_ajout,
			MAX(po.categories_modification) as categories_modification,
			MAX(po.categories_suppression) as categories_suppression,
			MAX(po.fournisseurs_lecture) as fournisseurs_lecture,
			MAX(po.fournisseurs_ajout) as fournisseurs_ajout,
			MAX(po.fournisseurs_modification) as fournisseurs_modification,
			MAX(po.fournisseurs_suppression) as fournisseurs_suppression,
			MAX(po.typesLots_lecture) as typesLots_lecture,
			MAX(po.typesLots_ajout) as typesLots_ajout,
			MAX(po.typesLots_modification) as typesLots_modification,
			MAX(po.typesLots_suppression) as typesLots_suppression,
			MAX(po.lieux_lecture) as lieux_lecture,
			MAX(po.lieux_ajout) as lieux_ajout,
			MAX(po.lieux_modification) as lieux_modification,
			MAX(po.lieux_suppression) as lieux_suppression,
			MAX(po.lots_lecture) as lots_lecture,
			MAX(po.lots_ajout) as lots_ajout,
			MAX(po.lots_modification) as lots_modification,
			MAX(po.lots_suppression) as lots_suppression,
			MAX(po.sac_lecture) as sac_lecture,
			MAX(po.sac_ajout) as sac_ajout,
			MAX(po.sac_modification) as sac_modification,
			MAX(po.sac_suppression) as sac_suppression,
			MAX(po.sac2_lecture) as sac2_lecture,
			MAX(po.sac2_ajout) as sac2_ajout,
			MAX(po.sac2_modification) as sac2_modification,
			MAX(po.sac2_suppression) as sac2_suppression,
			MAX(po.catalogue_lecture) as catalogue_lecture,
			MAX(po.catalogue_ajout) as catalogue_ajout,
			MAX(po.catalogue_modification) as catalogue_modification,
			MAX(po.catalogue_suppression) as catalogue_suppression,
			MAX(po.materiel_lecture) as materiel_lecture,
			MAX(po.materiel_ajout) as materiel_ajout,
			MAX(po.materiel_modification) as materiel_modification,
			MAX(po.materiel_suppression) as materiel_suppression,
			MAX(po.messages_ajout) as messages_ajout,
			MAX(po.messages_suppression) as messages_suppression,
			MAX(po.verrouIP) as verrouIP,
			MAX(po.commande_lecture) as commande_lecture,
			MAX(po.commande_ajout) as commande_ajout,
			MAX(po.commande_valider) as commande_valider,
			MAX(po.commande_etreEnCharge) as commande_etreEnCharge,
			MAX(po.commande_abandonner) as commande_abandonner,
			MAX(po.cout_lecture) as cout_lecture,
			MAX(po.cout_ajout) as cout_ajout,
			MAX(po.cout_etreEnCharge) as cout_etreEnCharge,
			MAX(po.cout_supprimer) as cout_supprimer,
			MAX(po.appli_conf) as appli_conf,
			MAX(po.reserve_lecture) as reserve_lecture,
			MAX(po.reserve_ajout) as reserve_ajout,
			MAX(po.reserve_modification) as reserve_modification,
			MAX(po.reserve_suppression) as reserve_suppression,
			MAX(po.reserve_cmdVersReserve) as reserve_cmdVersReserve,
			MAX(po.reserve_ReserveVersLot) as reserve_ReserveVersLot,
			MAX(po.vhf_canal_lecture) as vhf_canal_lecture,
			MAX(po.vhf_canal_ajout) as vhf_canal_ajout,
			MAX(po.vhf_canal_modification) as vhf_canal_modification,
			MAX(po.vhf_canal_suppression) as vhf_canal_suppression,
			MAX(po.vhf_plan_lecture) as vhf_plan_lecture,
			MAX(po.vhf_plan_ajout) as vhf_plan_ajout,
			MAX(po.vhf_plan_modification) as vhf_plan_modification,
			MAX(po.vhf_plan_suppression) as vhf_plan_suppression,
			MAX(po.vhf_equipement_lecture) as vhf_equipement_lecture,
			MAX(po.vhf_equipement_ajout) as vhf_equipement_ajout,
			MAX(po.vhf_equipement_modification) as vhf_equipement_modification,
			MAX(po.vhf_equipement_suppression) as vhf_equipement_suppression,
			MAX(po.vehicules_lecture) as vehicules_lecture,
			MAX(po.vehicules_ajout) as vehicules_ajout,
			MAX(po.vehicules_modification) as vehicules_modification,
			MAX(po.vehicules_suppression) as vehicules_suppression,
			MAX(po.vehicules_types_lecture) as vehicules_types_lecture,
			MAX(po.vehicules_types_ajout) as vehicules_types_ajout,
			MAX(po.vehicules_types_modification) as vehicules_types_modification,
			MAX(po.vehicules_types_suppression) as vehicules_types_suppression,
			MAX(po.todolist_perso) as todolist_perso,
			MAX(po.todolist_lecture) as todolist_lecture,
			MAX(po.todolist_modification) as todolist_modification,
			MAX(po.contactMailGroupe) as contactMailGroupe,
			MAX(po.maintenance) as maintenance
		FROM
			PROFILS_PERSONNES j
				LEFT OUTER JOIN PERSONNE_REFERENTE pe ON j.idPersonne = pe.idPersonne
				LEFT OUTER JOIN PROFILS po ON j.idProfil = po.idProfil
		WHERE
			pe.identifiant = :identifiant;');
	$query->execute(array(
	    'identifiant' => $_POST['identifiant']
	));
	$data = $query->fetch();
	
	if (empty($data['idPersonne']) OR $data['idPersonne'] == "" OR  !(password_verify($_POST['motDePasse'], $data['motDePasse'])))
	{
	    //pas bon
	    $query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
	    $query->execute(array(
	        'dateEvt' => date('Y-m-d H:i:s'),
	        'adresseIP' => $_SERVER['REMOTE_ADDR'],
	        'utilisateurEvt' => $_POST['identifiant'],
	        'idLogLevel' => '5',
	        'detailEvt' => 'Echec d\'identification sur ' . $APPNAME
	    ));

        $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
        $query->execute(array(
            'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - 1 days'))
        ));
	    
	    $query = $db->prepare('SELECT COUNT(*) as nb FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
		$query->execute(array(
		    'adresseIP' => $_SERVER['REMOTE_ADDR']
		));
		$data = $query->fetch();
		
		if ($data['nb'] > 1)
		{
			$query = $db->prepare('INSERT INTO VERROUILLAGE_IP(adresseIPverr, dateVerr)VALUES(:adresseIPverr, :dateVerr);');
		    $query->execute(array(
		        'dateVerr' => date('Y-m-d H:i:s'),
		        'adresseIPverr' => $_SERVER['REMOTE_ADDR']
		    ));
			
			$query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
		    $query->execute(array(
		        'dateEvt' => date('Y-m-d H:i:s'),
		        'adresseIP' => $_SERVER['REMOTE_ADDR'],
		        'utilisateurEvt' => $_POST['identifiant'],
		        'idLogLevel' => '5',
		        'detailEvt' => 'Verouillage de l\'adresse IP.'
		    ));

            $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
            $query->execute(array(
                'adresseIP' => $_SERVER['REMOTE_ADDR']
            ));
		}
		else
        {
            $query = $db->prepare('INSERT INTO VERROUILLAGE_IP_TEMP(adresseIP, dateEchec)VALUES(:adresseIP, :dateEchec);');
            $query->execute(array(
                'dateEchec' => date('Y-m-d H:i:s'),
                'adresseIP' => $_SERVER['REMOTE_ADDR']
            ));
        }
	    
	    echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
	}
	else
	{
        $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
        $query->execute(array(
            'adresseIP' => $_SERVER['REMOTE_ADDR']
        ));
        $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
        $query->execute(array(
            'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - 1 days'))
        ));

        if($MAINTENANCE==1 AND $data['maintenance']==0)
        {
        	$query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
		    $query->execute(array(
		        'dateEvt' => date('Y-m-d H:i:s'),
		        'adresseIP' => $_SERVER['REMOTE_ADDR'],
		        'utilisateurEvt' => $_POST['identifiant'],
		        'idLogLevel' => '5',
		        'detailEvt' => 'Connexion refusée par le mode maintenance.'
		    ));
		    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
        	exit;
        }

	    $_SESSION['idPersonne'] = $data['idPersonne'];
	    $_SESSION['idProfil'] = $data['idProfil'];
	    $_SESSION['identifiant'] = $data['identifiant'];
	    $_SESSION['nomPersonne'] = $data['nomPersonne'];
	    $_SESSION['prenomPersonne'] = $data['prenomPersonne'];
	    $_SESSION['mailPersonne'] = $data['mailPersonne'];
	    $_SESSION['telPersonne'] = $data['telPersonne'];
	    $_SESSION['fonction'] = $data['fonction'];
	    $_SESSION['logs_lecture'] = $data['logs_lecture'];
	    $_SESSION['annuaire_lecture'] = $data['annuaire_lecture'];
	    $_SESSION['annuaire_ajout'] = $data['annuaire_ajout'];
	    $_SESSION['annuaire_modification'] = $data['annuaire_modification'];
	    $_SESSION['annuaire_mdp'] = $data['annuaire_mdp'];
	    $_SESSION['annuaire_suppression'] = $data['annuaire_suppression'];
	    $_SESSION['profils_lecture'] = $data['profils_lecture'];
	    $_SESSION['profils_ajout'] = $data['profils_ajout'];
	    $_SESSION['profils_modification'] = $data['profils_modification'];
	    $_SESSION['profils_suppression'] = $data['profils_suppression'];
	    $_SESSION['categories_lecture'] = $data['categories_lecture'];
	    $_SESSION['categories_ajout'] = $data['categories_ajout'];
	    $_SESSION['categories_modification'] = $data['categories_modification'];
	    $_SESSION['categories_suppression'] = $data['categories_suppression'];
	    $_SESSION['fournisseurs_lecture'] = $data['fournisseurs_lecture'];
	    $_SESSION['fournisseurs_ajout'] = $data['fournisseurs_ajout'];
	    $_SESSION['fournisseurs_modification'] = $data['fournisseurs_modification'];
	    $_SESSION['fournisseurs_suppression'] = $data['fournisseurs_suppression'];
	    $_SESSION['typesLots_lecture'] = $data['typesLots_lecture'];
	    $_SESSION['typesLots_ajout'] = $data['typesLots_ajout'];
	    $_SESSION['typesLots_modification'] = $data['typesLots_modification'];
	    $_SESSION['typesLots_suppression'] = $data['typesLots_suppression'];
	    $_SESSION['lieux_lecture'] = $data['lieux_lecture'];
	    $_SESSION['lieux_ajout'] = $data['lieux_ajout'];
	    $_SESSION['lieux_modification'] = $data['lieux_modification'];
	    $_SESSION['lieux_suppression'] = $data['lieux_suppression'];
	    $_SESSION['lots_lecture'] = $data['lots_lecture'];
	    $_SESSION['lots_ajout'] = $data['lots_ajout'];
	    $_SESSION['lots_modification'] = $data['lots_modification'];
	    $_SESSION['lots_suppression'] = $data['lots_suppression'];
	    $_SESSION['sac_lecture'] = $data['sac_lecture'];
	    $_SESSION['sac_ajout'] = $data['sac_ajout'];
	    $_SESSION['sac_modification'] = $data['sac_modification'];
	    $_SESSION['sac_suppression'] = $data['sac_suppression'];
	    $_SESSION['sac2_lecture'] = $data['sac2_lecture'];
	    $_SESSION['sac2_ajout'] = $data['sac2_ajout'];
	    $_SESSION['sac2_modification'] = $data['sac2_modification'];
	    $_SESSION['sac2_suppression'] = $data['sac2_suppression'];
	    $_SESSION['catalogue_lecture'] = $data['catalogue_lecture'];
	    $_SESSION['catalogue_ajout'] = $data['catalogue_ajout'];
	    $_SESSION['catalogue_modification'] = $data['catalogue_modification'];
	    $_SESSION['catalogue_suppression'] = $data['catalogue_suppression'];
	    $_SESSION['materiel_lecture'] = $data['materiel_lecture'];
	    $_SESSION['materiel_ajout'] = $data['materiel_ajout'];
	    $_SESSION['materiel_modification'] = $data['materiel_modification'];
	    $_SESSION['materiel_suppression'] = $data['materiel_suppression'];
	    $_SESSION['messages_ajout'] = $data['messages_ajout'];
	    $_SESSION['messages_suppression'] = $data['messages_suppression'];
	    $_SESSION['verrouIP'] = $data['verrouIP'];
	    $_SESSION['commande_lecture'] = $data['commande_lecture'];
	    $_SESSION['commande_ajout'] = $data['commande_ajout'];
	    $_SESSION['commande_valider'] = $data['commande_valider'];
	    $_SESSION['commande_etreEnCharge'] = $data['commande_etreEnCharge'];
	    $_SESSION['commande_abandonner'] = $data['commande_abandonner'];
	    $_SESSION['cout_lecture'] = $data['cout_lecture'];
	    $_SESSION['cout_ajout'] = $data['cout_ajout'];
	    $_SESSION['cout_etreEnCharge'] = $data['cout_etreEnCharge'];
	    $_SESSION['cout_supprimer'] = $data['cout_supprimer'];
	    $_SESSION['appli_conf'] = $data['appli_conf'];
	    $_SESSION['reserve_lecture'] = $data['reserve_lecture'];
	    $_SESSION['reserve_ajout'] = $data['reserve_ajout'];
	    $_SESSION['reserve_modification'] = $data['reserve_modification'];
	    $_SESSION['reserve_suppression'] = $data['reserve_suppression'];
	    $_SESSION['reserve_cmdVersReserve'] = $data['reserve_cmdVersReserve'];
	    $_SESSION['reserve_ReserveVersLot'] = $data['reserve_ReserveVersLot'];
        $_SESSION['vhf_canal_lecture'] = $data['vhf_canal_lecture'];
        $_SESSION['vhf_canal_ajout'] = $data['vhf_canal_ajout'];
        $_SESSION['vhf_canal_modification'] = $data['vhf_canal_modification'];
        $_SESSION['vhf_canal_suppression'] = $data['vhf_canal_suppression'];
        $_SESSION['vhf_plan_lecture'] = $data['vhf_plan_lecture'];
        $_SESSION['vhf_plan_ajout'] = $data['vhf_plan_ajout'];
        $_SESSION['vhf_plan_modification'] = $data['vhf_plan_modification'];
        $_SESSION['vhf_plan_suppression'] = $data['vhf_plan_suppression'];
        $_SESSION['vhf_equipement_lecture'] = $data['vhf_equipement_lecture'];
        $_SESSION['vhf_equipement_ajout'] = $data['vhf_equipement_ajout'];
        $_SESSION['vhf_equipement_modification'] = $data['vhf_equipement_modification'];
        $_SESSION['vhf_equipement_suppression'] = $data['vhf_equipement_suppression'];
        $_SESSION['vehicules_lecture'] = $data['vehicules_lecture'];
        $_SESSION['vehicules_ajout'] = $data['vehicules_ajout'];
        $_SESSION['vehicules_modification'] = $data['vehicules_modification'];
        $_SESSION['vehicules_suppression'] = $data['vehicules_suppression'];
        $_SESSION['vehicules_types_lecture'] = $data['vehicules_types_lecture'];
        $_SESSION['vehicules_types_ajout'] = $data['vehicules_types_ajout'];
        $_SESSION['vehicules_types_modification'] = $data['vehicules_types_modification'];
        $_SESSION['vehicules_types_suppression'] = $data['vehicules_types_suppression'];
        $_SESSION['maintenance'] = $data['maintenance'];
        $_SESSION['todolist_perso'] = $data['todolist_perso'];
		$_SESSION['todolist_lecture'] = $data['todolist_lecture'];
		$_SESSION['todolist_modification'] = $data['todolist_modification'];
		$_SESSION['contactMailGroupe'] = $data['contactMailGroupe'];
        
        $_SESSION['conf_accueilRefresh'] = $data['conf_accueilRefresh'];
        $_SESSION['conf_indicateur1Accueil'] = $data['conf_indicateur1Accueil'];
        $_SESSION['conf_indicateur2Accueil'] = $data['conf_indicateur2Accueil'];
        $_SESSION['conf_indicateur3Accueil'] = $data['conf_indicateur3Accueil'];
        $_SESSION['conf_indicateur4Accueil'] = $data['conf_indicateur4Accueil'];
        $_SESSION['conf_indicateur5Accueil'] = $data['conf_indicateur5Accueil'];
        $_SESSION['conf_indicateur6Accueil'] = $data['conf_indicateur6Accueil'];
        $_SESSION['conf_indicateur7Accueil'] = $data['conf_indicateur7Accueil'];
        $_SESSION['conf_indicateur8Accueil'] = $data['conf_indicateur8Accueil'];
	    
	    $_SESSION['derniereConnexion'] = $data['derniereConnexion'];
	    
	    $_SESSION['LAST_ACTIVITY'] = time();
	    
	    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET derniereConnexion = CURRENT_TIMESTAMP WHERE idPersonne = :idPersonne;');
		$query->execute(array(
		    'idPersonne' => $_SESSION['idPersonne']
		));
	
	    if ((password_verify($_POST['identifiant'], $data['motDePasse'])))
	    {
	        $_SESSION['connexion_connexion'] = "0";
	
	        writeInLogs("Connexion réussie et redirection automatique sur la modification de mot de passe.", '1');
	
	        echo "<script type='text/javascript'>document.location.replace('loginChangePWDstart.php');</script>";
	    }
	    else
	    {
	        //bon
	        $_SESSION['connexion_connexion'] = $data['connexion_connexion'];
	
	        writeInLogs("Connexion réussie.", '1');
	
	        echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
	    }
	
	}
}
else
{
	 //pas bon
	    $query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
	    $query->execute(array(
	        'dateEvt' => date('Y-m-d H:i:s'),
	        'adresseIP' => $_SERVER['REMOTE_ADDR'],
	        'utilisateurEvt' => $_POST['identifiant'],
	        'idLogLevel' => '5',
	        'detailEvt' => 'Connexion refusée par le filtrage IP.'
	    ));
	    echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
}

?>