<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';

if($RECAPTCHA_ENABLE)
{
	$reCaptchaReturn = getCaptcha($_POST['g-recaptcha-response']);
	
	if($reCaptchaReturn->success == true AND $reCaptchaReturn->score > $RECAPTCHA_SCOREMIN)
	{
		writeInLogs("Google reCaptcha V3 - Soumission du formulaire autorisée", '1', NULL);
	}
	else
	{
		writeInLogs("Google reCaptcha V3 - Soumission du formulaire bloquée avec un score de ".$reCaptchaReturn->score, '2', NULL);
		echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
		exit;
	}
}

$_POST['identifiant'] = str_replace($XSS_SECURITY, "", $_POST['identifiant']);
$_POST['motDePasse'] = str_replace($XSS_SECURITY, "", $_POST['motDePasse']);

$query = $db->prepare('SELECT * FROM VERROUILLAGE_IP WHERE adresseIPverr= :adresseIPverr;');
$query->execute(array(
    'adresseIPverr' => $_SERVER['REMOTE_ADDR']
));
$data = $query->fetch();

if ($data['idIP'] == "")
{
	
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS v ON p.idPersonne = v.idPersonne WHERE p.identifiant = :identifiant;');
	$query->execute(array(
	    'identifiant' => $_POST['identifiant']
	));
	$data = $query->fetch();
	
	if (empty($data['idPersonne']) OR $data['idPersonne'] == "" OR  !(password_verify($SELPRE.$_POST['motDePasse'].$SELPOST, $data['motDePasse'])))
	{
	    //pas bon
	    writeInLogs("Echec d'identification", '2', NULL);

        $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
        $query->execute(array(
            'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - '.$VERROUILLAGE_IP_TEMPS.' days'))
        ));
	    
	    $query = $db->prepare('SELECT COUNT(*) as nb FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
		$query->execute(array(
		    'adresseIP' => $_SERVER['REMOTE_ADDR']
		));
		$data = $query->fetch();
		
		if ($data['nb'] > $VERROUILLAGE_IP_OCCURANCES-2)
		{
			$query = $db->prepare('INSERT INTO VERROUILLAGE_IP(adresseIPverr, dateVerr, commentaire)VALUES(:adresseIPverr, :dateVerr, :commentaire);');
		    $query->execute(array(
		        'dateVerr' => date('Y-m-d H:i:s'),
		        'adresseIPverr' => $_SERVER['REMOTE_ADDR'],
		        'commentaire' => 'Erreur d\'authentification pour ' . $_POST['identifiant'],
		    ));
			
			writeInLogs("Verouillage définitif de l\'adresse IP suite à la tentative d'authentification avec ".$_POST['identifiant'], '2', NULL);

            $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
            $query->execute(array(
                'adresseIP' => $_SERVER['REMOTE_ADDR']
            ));
		}
		else
        {
            $query = $db->prepare('INSERT INTO VERROUILLAGE_IP_TEMP(adresseIP, dateEchec, commentaire)VALUES(:adresseIP, :dateEchec, :commentaire);');
            $query->execute(array(
                'dateEchec' => date('Y-m-d H:i:s'),
                'adresseIP' => $_SERVER['REMOTE_ADDR'],
                'commentaire' => 'Erreur d\'authentification pour ' . $_POST['identifiant'],
            ));

            writeInLogs("Verouillage temporaire de l\'adresse IP suite à la tentative d'authentification avec ".$_POST['identifiant'], '2', NULL);
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
            'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - '.$VERROUILLAGE_IP_TEMPS.' days'))
        ));

        $query = $db->prepare('DELETE FROM RESETPASSWORD WHERE idPersonne = :idPersonne;');
        $query->execute(array(
            'idPersonne' => $data['idPersonne']
        ));

        if($MAINTENANCE==1 AND $data['maintenance']==0)
        {
        	writeInLogs("Connexion refusée par le mode maintenance", '2', $_POST['identifiant']);
		    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
        	exit;
        }

		$_SESSION['idPersonne']                                = $data['idPersonne'];
		$_SESSION['idProfil']                                  = $data['idProfil'];
		$_SESSION['identifiant']                               = $data['identifiant'];
		$_SESSION['nomPersonne']                               = $data['nomPersonne'];
		$_SESSION['prenomPersonne']                            = $data['prenomPersonne'];
		$_SESSION['mailPersonne']                              = $data['mailPersonne'];
		$_SESSION['telPersonne']                               = $data['telPersonne'];
		$_SESSION['fonction']                                  = $data['fonction'];
		$_SESSION['annuaire_lecture']                          = $data['annuaire_lecture'];
		$_SESSION['annuaire_ajout']                            = $data['annuaire_ajout'];
		$_SESSION['annuaire_modification']                     = $data['annuaire_modification'];
		$_SESSION['annuaire_mdp']                              = $data['annuaire_mdp'];
		$_SESSION['annuaire_suppression']                      = $data['annuaire_suppression'];
		$_SESSION['profils_lecture']                           = $data['profils_lecture'];
		$_SESSION['profils_ajout']                             = $data['profils_ajout'];
		$_SESSION['profils_modification']                      = $data['profils_modification'];
		$_SESSION['profils_suppression']                       = $data['profils_suppression'];
		$_SESSION['categories_lecture']                        = $data['categories_lecture'];
		$_SESSION['categories_ajout']                          = $data['categories_ajout'];
		$_SESSION['categories_modification']                   = $data['categories_modification'];
		$_SESSION['categories_suppression']                    = $data['categories_suppression'];
		$_SESSION['fournisseurs_lecture']                      = $data['fournisseurs_lecture'];
		$_SESSION['fournisseurs_ajout']                        = $data['fournisseurs_ajout'];
		$_SESSION['fournisseurs_modification']                 = $data['fournisseurs_modification'];
		$_SESSION['fournisseurs_suppression']                  = $data['fournisseurs_suppression'];
		$_SESSION['typesLots_lecture']                         = $data['typesLots_lecture'];
		$_SESSION['typesLots_ajout']                           = $data['typesLots_ajout'];
		$_SESSION['typesLots_modification']                    = $data['typesLots_modification'];
		$_SESSION['typesLots_suppression']                     = $data['typesLots_suppression'];
		$_SESSION['lieux_lecture']                             = $data['lieux_lecture'];
		$_SESSION['lieux_ajout']                               = $data['lieux_ajout'];
		$_SESSION['lieux_modification']                        = $data['lieux_modification'];
		$_SESSION['lieux_suppression']                         = $data['lieux_suppression'];
		$_SESSION['lots_lecture']                              = $data['lots_lecture'];
		$_SESSION['lots_ajout']                                = $data['lots_ajout'];
		$_SESSION['lots_modification']                         = $data['lots_modification'];
		$_SESSION['lots_suppression']                          = $data['lots_suppression'];
		$_SESSION['sac_lecture']                               = $data['sac_lecture'];
		$_SESSION['sac_ajout']                                 = $data['sac_ajout'];
		$_SESSION['sac_modification']                          = $data['sac_modification'];
		$_SESSION['sac_suppression']                           = $data['sac_suppression'];
		$_SESSION['sac2_lecture']                              = $data['sac2_lecture'];
		$_SESSION['sac2_ajout']                                = $data['sac2_ajout'];
		$_SESSION['sac2_modification']                         = $data['sac2_modification'];
		$_SESSION['sac2_suppression']                          = $data['sac2_suppression'];
		$_SESSION['catalogue_lecture']                         = $data['catalogue_lecture'];
		$_SESSION['catalogue_ajout']                           = $data['catalogue_ajout'];
		$_SESSION['catalogue_modification']                    = $data['catalogue_modification'];
		$_SESSION['catalogue_suppression']                     = $data['catalogue_suppression'];
		$_SESSION['materiel_lecture']                          = $data['materiel_lecture'];
		$_SESSION['materiel_ajout']                            = $data['materiel_ajout'];
		$_SESSION['materiel_modification']                     = $data['materiel_modification'];
		$_SESSION['materiel_suppression']                      = $data['materiel_suppression'];
		$_SESSION['messages_ajout']                            = $data['messages_ajout'];
		$_SESSION['messages_suppression']                      = $data['messages_suppression'];
		$_SESSION['verrouIP']                                  = $data['verrouIP'];
		$_SESSION['commande_lecture']                          = $data['commande_lecture'];
		$_SESSION['commande_ajout']                            = $data['commande_ajout'];
		$_SESSION['commande_valider_delegate']                 = $data['commande_valider_delegate'];
		$_SESSION['commande_etreEnCharge']                     = $data['commande_etreEnCharge'];
		$_SESSION['commande_abandonner']                       = $data['commande_abandonner'];
		$_SESSION['cout_lecture']                              = $data['cout_lecture'];
		$_SESSION['cout_ajout']                                = $data['cout_ajout'];
		$_SESSION['cout_etreEnCharge']                         = $data['cout_etreEnCharge'];
		$_SESSION['cout_supprimer']                            = $data['cout_supprimer'];
		$_SESSION['appli_conf']                                = $data['appli_conf'];
		$_SESSION['reserve_lecture']                           = $data['reserve_lecture'];
		$_SESSION['reserve_ajout']                             = $data['reserve_ajout'];
		$_SESSION['reserve_modification']                      = $data['reserve_modification'];
		$_SESSION['reserve_suppression']                       = $data['reserve_suppression'];
		$_SESSION['reserve_cmdVersReserve']                    = $data['reserve_cmdVersReserve'];
		$_SESSION['reserve_ReserveVersLot']                    = $data['reserve_ReserveVersLot'];
		$_SESSION['vhf_canal_lecture']                         = $data['vhf_canal_lecture'];
		$_SESSION['vhf_canal_ajout']                           = $data['vhf_canal_ajout'];
		$_SESSION['vhf_canal_modification']                    = $data['vhf_canal_modification'];
		$_SESSION['vhf_canal_suppression']                     = $data['vhf_canal_suppression'];
		$_SESSION['vhf_plan_lecture']                          = $data['vhf_plan_lecture'];
		$_SESSION['vhf_plan_ajout']                            = $data['vhf_plan_ajout'];
		$_SESSION['vhf_plan_modification']                     = $data['vhf_plan_modification'];
		$_SESSION['vhf_plan_suppression']                      = $data['vhf_plan_suppression'];
		$_SESSION['vhf_equipement_lecture']                    = $data['vhf_equipement_lecture'];
		$_SESSION['vhf_equipement_ajout']                      = $data['vhf_equipement_ajout'];
		$_SESSION['vhf_equipement_modification']               = $data['vhf_equipement_modification'];
		$_SESSION['vhf_equipement_suppression']                = $data['vhf_equipement_suppression'];
		$_SESSION['vehicules_lecture']                         = $data['vehicules_lecture'];
		$_SESSION['vehicules_ajout']                           = $data['vehicules_ajout'];
		$_SESSION['vehicules_modification']                    = $data['vehicules_modification'];
		$_SESSION['vehicules_suppression']                     = $data['vehicules_suppression'];
		$_SESSION['vehicules_types_lecture']                   = $data['vehicules_types_lecture'];
		$_SESSION['vehicules_types_ajout']                     = $data['vehicules_types_ajout'];
		$_SESSION['vehicules_types_modification']              = $data['vehicules_types_modification'];
		$_SESSION['vehicules_types_suppression']               = $data['vehicules_types_suppression'];
		$_SESSION['tenues_lecture']                            = $data['tenues_lecture'];
		$_SESSION['tenues_ajout']                              = $data['tenues_ajout'];
		$_SESSION['tenues_modification']                       = $data['tenues_modification'];
		$_SESSION['tenues_suppression']                        = $data['tenues_suppression'];
		$_SESSION['tenuesCatalogue_lecture']                   = $data['tenuesCatalogue_lecture'];
		$_SESSION['tenuesCatalogue_ajout']                     = $data['tenuesCatalogue_ajout'];
		$_SESSION['tenuesCatalogue_modification']              = $data['tenuesCatalogue_modification'];
		$_SESSION['tenuesCatalogue_suppression']               = $data['tenuesCatalogue_suppression'];
		$_SESSION['cautions_lecture']                          = $data['cautions_lecture'];
		$_SESSION['cautions_ajout']                            = $data['cautions_ajout'];
		$_SESSION['cautions_modification']                     = $data['cautions_modification'];
		$_SESSION['cautions_suppression']                      = $data['cautions_suppression'];
		$_SESSION['maintenance']                               = $data['maintenance'];
		$_SESSION['todolist_perso']                            = $data['todolist_perso'];
		$_SESSION['todolist_lecture']                          = $data['todolist_lecture'];
		$_SESSION['todolist_modification']                     = $data['todolist_modification'];
		$_SESSION['contactMailGroupe']                         = $data['contactMailGroupe'];
		$_SESSION['etats_lecture']                             = $data['etats_lecture'];
		$_SESSION['etats_ajout']                               = $data['etats_ajout'];
		$_SESSION['etats_modification']                        = $data['etats_modification'];
		$_SESSION['etats_suppression']                         = $data['etats_suppression'];
		$_SESSION['notifications']                             = $data['notifications'];
		$_SESSION['actionsMassives']                           = $data['actionsMassives'];
		$_SESSION['delegation']                                = $data['delegation'];
		$_SESSION['desinfections_lecture']                     = $data['desinfections_lecture'];
		$_SESSION['desinfections_ajout']                       = $data['desinfections_ajout'];
		$_SESSION['desinfections_modification']                = $data['desinfections_modification'];
		$_SESSION['desinfections_suppression']                 = $data['desinfections_suppression'];
		$_SESSION['typesDesinfections_lecture']                = $data['typesDesinfections_lecture'];
		$_SESSION['typesDesinfections_ajout']                  = $data['typesDesinfections_ajout'];
		$_SESSION['typesDesinfections_modification']           = $data['typesDesinfections_modification'];
		$_SESSION['typesDesinfections_suppression']            = $data['typesDesinfections_suppression'];
		$_SESSION['carburants_lecture']                        = $data['carburants_lecture'];
		$_SESSION['carburants_ajout']                          = $data['carburants_ajout'];
		$_SESSION['carburants_modification']                   = $data['carburants_modification'];
		$_SESSION['carburants_suppression']                    = $data['carburants_suppression'];
		$_SESSION['vehiculeHealthType_lecture']                = $data['vehiculeHealthType_lecture'];
		$_SESSION['vehiculeHealthType_ajout']                  = $data['vehiculeHealthType_ajout'];
		$_SESSION['vehiculeHealthType_modification']           = $data['vehiculeHealthType_modification'];
		$_SESSION['vehiculeHealthType_suppression']            = $data['vehiculeHealthType_suppression'];
		$_SESSION['vehiculeHealth_lecture']                    = $data['vehiculeHealth_lecture'];
		$_SESSION['vehiculeHealth_ajout']                      = $data['vehiculeHealth_ajout'];
		$_SESSION['vehiculeHealth_modification']               = $data['vehiculeHealth_modification'];
		$_SESSION['vehiculeHealth_suppression']                = $data['vehiculeHealth_suppression'];
		$_SESSION['alertesBenevolesLots_lecture']              = $data['alertesBenevolesLots_lecture'];
		$_SESSION['alertesBenevolesLots_affectation']          = $data['alertesBenevolesLots_affectation'];
		$_SESSION['alertesBenevolesLots_affectationTier']      = $data['alertesBenevolesLots_affectationTier'];
		$_SESSION['alertesBenevolesVehicules_lecture']         = $data['alertesBenevolesVehicules_lecture'];
		$_SESSION['alertesBenevolesVehicules_affectation']     = $data['alertesBenevolesVehicules_affectation'];
		$_SESSION['alertesBenevolesVehicules_affectationTier'] = $data['alertesBenevolesVehicules_affectationTier'];
		
		$_SESSION['tableRowPerso']                             = $data['tableRowPerso'];
        
		$_SESSION['conf_accueilRefresh']                       = $data['conf_accueilRefresh'];
		$_SESSION['conf_indicateur1Accueil']                   = $data['conf_indicateur1Accueil'];
		$_SESSION['conf_indicateur2Accueil']                   = $data['conf_indicateur2Accueil'];
		$_SESSION['conf_indicateur3Accueil']                   = $data['conf_indicateur3Accueil'];
		$_SESSION['conf_indicateur4Accueil']                   = $data['conf_indicateur4Accueil'];
		$_SESSION['conf_indicateur5Accueil']                   = $data['conf_indicateur5Accueil'];
		$_SESSION['conf_indicateur6Accueil']                   = $data['conf_indicateur6Accueil'];
		$_SESSION['conf_indicateur7Accueil']                   = $data['conf_indicateur7Accueil'];
		$_SESSION['conf_indicateur8Accueil']                   = $data['conf_indicateur8Accueil'];
		$_SESSION['conf_indicateur9Accueil']                   = $data['conf_indicateur9Accueil'];
		$_SESSION['conf_indicateur10Accueil']                  = $data['conf_indicateur10Accueil'];
		$_SESSION['conf_indicateur11Accueil']                  = $data['conf_indicateur11Accueil'];
		$_SESSION['conf_indicateur12Accueil']                  = $data['conf_indicateur12Accueil'];
		
		$_SESSION['agenda_lots_peremption']                    = $data['agenda_lots_peremption'];
		$_SESSION['agenda_reserves_peremption']                = $data['agenda_reserves_peremption'];
		$_SESSION['agenda_lots_inventaireAF']                  = $data['agenda_lots_inventaireAF'];
		$_SESSION['agenda_lots_inventaireF']                   = $data['agenda_lots_inventaireF'];
		$_SESSION['agenda_commandes_livraison']                = $data['agenda_commandes_livraison'];
		$_SESSION['agenda_vehicules_revision']                 = $data['agenda_vehicules_revision'];
		$_SESSION['agenda_vehicules_ct']                       = $data['agenda_vehicules_ct'];
		$_SESSION['agenda_vehicules_assurance']                = $data['agenda_vehicules_assurance'];
		$_SESSION['agenda_vehicules_maintenance']              = $data['agenda_vehicules_maintenance'];
		$_SESSION['agenda_desinfections_desinfectionF']        = $data['agenda_desinfections_desinfectionF'];
		$_SESSION['agenda_desinfections_desinfectionAF']       = $data['agenda_desinfections_desinfectionAF'];
		$_SESSION['agenda_reserves_inventaireAF']              = $data['agenda_reserves_inventaireAF'];
		$_SESSION['agenda_reserves_inventaireF']               = $data['agenda_reserves_inventaireF'];
		$_SESSION['agenda_tenues_tenues']                      = $data['agenda_tenues_tenues'];
		$_SESSION['agenda_tenues_toDoList']                    = $data['agenda_tenues_toDoList'];
		$_SESSION['agenda_healthF']                            = $data['agenda_healthF'];
		$_SESSION['agenda_healthAF']                           = $data['agenda_healthAF'];
	    
		$_SESSION['layout']                                    = $data['layout'];
	    
		$_SESSION['derniereConnexion']                         = $data['derniereConnexion'];
	    
		$_SESSION['LAST_ACTIVITY']                             = time();
	    
		$_SESSION['DELEGATION_ACTIVE']                         = 0;
		$_SESSION['LOGS_DELEGATION_PREFIXE']                   = '';
	    
	    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET derniereConnexion = CURRENT_TIMESTAMP WHERE idPersonne = :idPersonne;');
		$query->execute(array(
		    'idPersonne' => $_SESSION['idPersonne']
		));
		
		sleep(1);
		
	    if ((password_verify($SELPRE.$_POST['identifiant'].$SELPOST, $data['motDePasse'])))
	    {
	        $_SESSION['connexion_connexion'] = "0";
	
	        writeInLogs("Connexion réussie et redirection automatique sur la modification de mot de passe.", '1', NULL);
	
	        echo "<script type='text/javascript'>document.location.replace('loginChangePWDstart.php');</script>";
	    }
	    else
	    {
	        //bon
	        $_SESSION['connexion_connexion'] = $data['connexion_connexion'];
	
	        writeInLogs("Connexion réussie.", '1', NULL);
	
	        echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
	    }
	
	}
}
else
{
	 //pas bon
    writeInLogs("Connexion refusée par le filtrage IP pour l'authentification avec ".$_POST['identifiant'], '2', NULL);
    echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
}

?>