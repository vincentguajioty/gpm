<?php
session_start();

require_once('bdd.php');

function writeInLogs($contentEVT, $levelEVT, $userSpecifique)
{
    switch($levelEVT)
    {
        case '1':
            $logsLevel = 'INFO';
        break;

        case '2':
            $logsLevel = 'WARN';
        break;

        case '3':
            $logsLevel = 'ERROR';
        break;

        case '4':
            $logsLevel = 'DEBUG';
        break;

        default:
            $logsLevel = 'UNDEFINED';
    }

    if(!isset($userSpecifique) OR $userSpecifique == Null OR $userSpecifique == '')
    {
        if(isset($_SESSION['identifiant']) AND $_SESSION['identifiant'] != '')
        {
            $userSpecifique = $_SESSION['identifiant'];
        }
        else
        {
            $userSpecifique = 'SYSTEM';
        }
    }
    $userSpecifique .= $_SESSION['LOGS_DELEGATION_PREFIXE'];

    if(isset($_SERVER['REMOTE_ADDR']) AND $_SERVER['REMOTE_ADDR'] != NULL AND $_SERVER['REMOTE_ADDR'] != '')
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    else
    {
        $ip = 'local';
    }

    $log = '['.date("Y-m-d H:i:s").']'."\t".'['.$ip.']'."\t".'['.$userSpecifique.']'."\t".'['.$logsLevel.'] '.$contentEVT.PHP_EOL;
    if(is_dir('logs'))
    {
        file_put_contents('logs/'.date("Ymd").'.log', $log, FILE_APPEND);
    }
    else
    {
        if(is_dir('../logs'))
        {
            file_put_contents('../logs/'.date("Ymd").'.log', $log, FILE_APPEND);
        }
        else
        {
            chdir(dirname(__FILE__));
            if(is_dir('logs'))
            {
                file_put_contents('logs/'.date("Ymd").'.log', $log, FILE_APPEND);
            }
            else
            {
                if(is_dir('../logs'))
                {
                    file_put_contents('../logs/'.date("Ymd").'.log', $log, FILE_APPEND);
                }
            }
        }
    }
}

function getCaptcha($secretKey)
{
	global $RECAPTCHA_SECRETKEY;
	
	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$RECAPTCHA_SECRETKEY."&response={$secretKey}");
	$return = json_decode($response);
	return $return;
}

function updateUserFromAD($idPersonne)
{
	global $db;
	global $LDAP_DOMAIN;
	global $LDAP_BASEDN;
	global $LDAP_ISWINAD;
	global $LDAP_SSL;

	global $LDAP_USER;
	global $LDAP_PASSWORD;

	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
	$query->execute(array(
	    'idPersonne' => $idPersonne
	));
	$data = $query->fetch();

	$connect = false;
	$ldap_username = $LDAP_USER;
	$ldap_password = $LDAP_PASSWORD;
	$ldap_domain = $LDAP_DOMAIN;
	$ldap_base_dn = $LDAP_BASEDN;
	if($LDAP_ISWINAD)
	{ $ldap_login = $ldap_username.'@'.$ldap_domain; }
	else
	{ $ldap_login = "uid=".$ldap_username.",cn=users,".$ldap_base_dn; }
	$ldap_connection = ldap_connect($ldap_domain);
	ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
	ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.
	if($LDAP_SSL){ ldap_start_tls($ldap_connection); }

	if (FALSE === $ldap_connection)
	{
		writeInLogs("Erreur LDAP", '2', NULL);
		return false;
		exit;
	}
	
	if(TRUE === ldap_bind($ldap_connection, $ldap_login, $ldap_password))
	{
		if($LDAP_ISWINAD)
		{ $results = ldap_search($ldap_connection,$ldap_base_dn,"(samaccountname=".$data['identifiant'].")",array("memberof","primarygroupid")); }
		else
		{ $results = ldap_search($ldap_connection,$ldap_base_dn,"(uid=".$data['identifiant'].")",array("memberof","primarygroupid")); }
		$resultsUsers = ldap_get_entries($ldap_connection, $results);

		$query = $db->prepare('DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne');
		$query->execute(array('idPersonne'=>$idPersonne));

		$query = $db->query('SELECT * FROM PROFILS;');
		$LDAP_GROUP = $query->fetchAll();

		foreach($resultsUsers as $resultsUser)
		{
			foreach($resultsUser['memberof'] as $group)
			{
				foreach($LDAP_GROUP as $authGroup)
				{
					if(strpos($group, $authGroup['LDAP_BINDDN']))
					{
						$query = $db->prepare('INSERT INTO PROFILS_PERSONNES SET idPersonne = :idPersonne, idProfil = :idProfil;');
						$query->execute(array(
							'idPersonne' => $idPersonne,
							'idProfil' => $authGroup['idProfil']
						));
					}
				}
			}
		}
		
		majIndicateursPersonne($idPersonne,1);
    	majNotificationsPersonne($idPersonne,1);
    	majValideursPersonne(1);

        return true;
	}
	
	return false;

}

function updateAllUsersFromAD()
{
	global $db;

	$personnes = $db->query('SELECT * FROM PERSONNE_REFERENTE WHERE isActiveDirectory = 1 AND cnil_anonyme = 0;');
	while($personne = $personnes->fetch())
	{
		$update = updateUserFromAD($personne['idPersonne']);

		if($update)
		{
			writeInLogs("Mise ?? jour LDAP avec succ??s de l'utilisateur ".$personne['idPersonne'], '1', NULL);
		}
		else
		{
			writeInLogs("Mise ?? jour LDAP en echec de l'utilisateur ".$personne['idPersonne'], '3', NULL);
		}
	}
}

function checkUserPasswordLocal($identifiant, $password)
{
	global $db;
	global $SELPRE;
	global $SELPOST;
	
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE identifiant = :identifiant;');
	$query->execute(array(
	    'identifiant' => $identifiant
	));
	$data = $query->fetch();
	
	$result = password_verify($SELPRE.$password.$SELPOST, $data['motDePasse']);
	
	if($result == true)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function checkUserPasswordAD($identifiant, $password, $idPersonne)
{
	global $db;
	global $LDAP_DOMAIN;
	global $LDAP_BASEDN;
	global $LDAP_ISWINAD;
	global $LDAP_SSL;
	
	$connect = false;
	$ldap_username = $identifiant;
	$ldap_password = $password;
	$ldap_domain = $LDAP_DOMAIN;
	$ldap_base_dn = $LDAP_BASEDN;
	if($LDAP_ISWINAD)
	{ $ldap_login = $ldap_username.'@'.$ldap_domain; }
	else
	{ $ldap_login = "uid=".$ldap_username.",cn=users,".$ldap_base_dn; }
	$ldap_connection = ldap_connect($ldap_domain);
	ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
	ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.
	if($LDAP_SSL){ ldap_start_tls($ldap_connection); }

	if (FALSE === $ldap_connection)
	{
		writeInLogs("Erreur LDAP", '2', NULL);
		return false;
		exit;
	}
	
	if(TRUE === ldap_bind($ldap_connection, $ldap_login, $ldap_password))
	{
		if($LDAP_ISWINAD)
		{ $results = ldap_search($ldap_connection,$ldap_base_dn,"(samaccountname=".$ldap_username.")",array("memberof","primarygroupid")); }
		else
		{ $results = ldap_search($ldap_connection,$ldap_base_dn,"(uid=".$ldap_username.")",array("memberof","primarygroupid")); }
		$resultsUsers = ldap_get_entries($ldap_connection, $results);

		$query = $db->prepare('DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne');
		$query->execute(array('idPersonne'=>$idPersonne));

		$query = $db->query('SELECT * FROM PROFILS;');
		$LDAP_GROUP = $query->fetchAll();

		foreach($resultsUsers as $resultsUser)
		{
			foreach($resultsUser['memberof'] as $group)
			{
				foreach($LDAP_GROUP as $authGroup)
				{
					if(strpos($group, $authGroup['LDAP_BINDDN']))
					{
						$query = $db->prepare('INSERT INTO PROFILS_PERSONNES SET idPersonne = :idPersonne, idProfil = :idProfil;');
						$query->execute(array(
							'idPersonne' => $idPersonne,
							'idProfil' => $authGroup['idProfil']
						));
					}
				}
			}
		}
		
		majIndicateursPersonne($idPersonne,1);
    	majNotificationsPersonne($idPersonne,1);
    	majValideursPersonne(1);

        return true;
	}
	
	return false;
}

function checkUserExistingAD($identifiant, $password)
{
	global $db;
	global $LDAP_DOMAIN;
	global $LDAP_BASEDN;
	global $LDAP_ISWINAD;
	global $LDAP_SSL;
	
	$connect = false;
	$ldap_username = $identifiant;
	$ldap_password = $password;
	$ldap_domain = $LDAP_DOMAIN;
	$ldap_base_dn = $LDAP_BASEDN;
	if($LDAP_ISWINAD)
	{ $ldap_login = $ldap_username.'@'.$ldap_domain; }
	else
	{ $ldap_login = "uid=".$ldap_username.",cn=users,".$ldap_base_dn; }
	$ldap_connection = ldap_connect($ldap_domain);
	ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
	ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.
	if($LDAP_SSL){ ldap_start_tls($ldap_connection); }

	if (FALSE === $ldap_connection)
	{
		writeInLogs("Erreur LDAP", '2', NULL);
		return false;
		exit;
	}
	
	if(TRUE === ldap_bind($ldap_connection, $ldap_login, $ldap_password))
	{
		if($LDAP_ISWINAD)
		{ $results = ldap_search($ldap_connection,$ldap_base_dn,"(samaccountname=".$ldap_username.")",array("memberof","primarygroupid")); }
		else
		{ $results = ldap_search($ldap_connection,$ldap_base_dn,"(uid=".$ldap_username.")",array("memberof","primarygroupid")); }
		$resultsUsers = ldap_get_entries($ldap_connection, $results);

		$query = $db->prepare('
	        INSERT INTO
	            PERSONNE_REFERENTE
	        SET
	            identifiant                         = :identifiant,
				notif_lots_manquants                = 1,
				notif_lots_peremptions              = 1,
				notif_lots_inventaires              = 1,
				notif_lots_conformites              = 1,
				notif_reserves_manquants            = 1,
				notif_reserves_peremptions          = 1,
				notif_reserves_inventaires          = 1,
				notif_vehicules_desinfections       = 1,
				notif_vehicules_health              = 1,
				notif_tenues_stock                  = 1,
				notif_tenues_retours                = 1,
				notif_benevoles_lots                = 1,
				notif_benevoles_vehicules           = 1,
				conf_indicateur1Accueil             = 1,
				conf_indicateur2Accueil             = 1,
				conf_indicateur3Accueil             = 1,
				conf_indicateur4Accueil             = 1,
				conf_indicateur5Accueil             = 1,
				conf_indicateur6Accueil             = 1,
				conf_indicateur9Accueil             = 1,
				conf_indicateur10Accueil            = 1,
				conf_indicateur11Accueil            = 1,
				conf_indicateur12Accueil            = 1,
				conf_accueilRefresh                 = 120,
				tableRowPerso                       = 25,
				agenda_lots_peremption              = "#dd4b39",
				agenda_reserves_peremption          = "#dd4b39",
				agenda_lots_inventaireAF            = "#00c0ef",
				agenda_lots_inventaireF             = "#00c0ef",
				agenda_commandes_livraison          = "#00a65a",
				agenda_vehicules_revision           = "#f39c12",
				agenda_vehicules_ct                 = "#f39c12",
				agenda_vehicules_assurance          = "#f39c12",
				agenda_vehicules_maintenance        = "#f39c12",
				agenda_desinfections_desinfectionF  = "#f39c12",
				agenda_desinfections_desinfectionAF = "#f39c12",
				agenda_reserves_inventaireAF        = "#3c8dbc",
				agenda_reserves_inventaireF         = "#3c8dbc",
				agenda_tenues_tenues                = "#00a65a",
				agenda_tenues_toDoList              = "#3c8db",
				agenda_healthF                      = "#f39c12",
				agenda_healthAF                     = "#f39c12",
				layout                              = "fixed",
				cnil_anonyme                        = false,
				isActiveDirectory                   = true
	        ;');
	    $query->execute(array(
	        'identifiant'    => $identifiant,
	    ));
		
	    $query = $db->query('SELECT MAX(idPersonne) as idPersonne FROM PERSONNE_REFERENTE;');
        $userCreate = $query->fetch();
        $idPersonne = $userCreate['idPersonne'];

        $notifications_abonnements = $db->prepare('INSERT INTO NOTIFICATIONS_ABONNEMENTS SET idPersonne = :idPersonne, idCondition = 1;');
        $notifications_abonnements->execute(array(
        	'idPersonne' => $idPersonne,
        ));

        writeInLogs("Utilisateur ".$idPersonne." (".$identifiant.") cr???? automatiquement par lien AD lors de sa premi??re connexion", '1', NULL);

		$query = $db->query('SELECT * FROM PROFILS;');
		$LDAP_GROUP = $query->fetchAll();

		foreach($resultsUsers as $resultsUser)
		{
			foreach($resultsUser['memberof'] as $group)
			{
				foreach($LDAP_GROUP as $authGroup)
				{
					if(strpos($group, $authGroup['LDAP_BINDDN']))
					{
						$query = $db->prepare('INSERT INTO PROFILS_PERSONNES SET idPersonne = :idPersonne, idProfil = :idProfil;');
						$query->execute(array(
							'idPersonne' => $idPersonne,
							'idProfil' => $authGroup['idProfil']
						));
					}
				}
			}
		}
		
		majIndicateursPersonne($idPersonne,1);
    	majNotificationsPersonne($idPersonne,1);
    	majValideursPersonne(1);

        return true;
	}
	
	return false;
}

function checkUserPassword($identifiant, $password)
{
	global $db;
	
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE identifiant = :identifiant;');
	$query->execute(array(
	    'identifiant' => $identifiant
	));
	$data = $query->fetch();

	if($data['idPersonne'] > 0)
	{
		if($data['isActiveDirectory'])
		{
			return checkUserPasswordAD($identifiant, $password, $data['idPersonne']);
		}
		else
		{
			return checkUserPasswordLocal($identifiant, $password);
		}
	}
	else
	{
		return checkUserExistingAD($identifiant, $password);
	}
}

function isUserFirstLogin($idPersonne)
{
	global $db;
	global $SELPRE;
	global $SELPOST;
	
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
	$query->execute(array(
	    'idPersonne' => $idPersonne
	));
	$data = $query->fetch();
	
	$result = password_verify($SELPRE.$data['identifiant'].$SELPOST, $data['motDePasse']);
	
	if($result == true)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function isIpLock()
{
	global $db;
	
	$query = $db->prepare('SELECT COUNT(*) as nb FROM VERROUILLAGE_IP WHERE adresseIPverr= :adresseIPverr;');
	$query->execute(array(
	    'adresseIPverr' => $_SERVER['REMOTE_ADDR']
	));
	$data = $query->fetch();
	
	if($data['nb']>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function lockIpOnce($identifiant)
{
	global $db;
	global $VERROUILLAGE_IP_TEMPS;
	global $VERROUILLAGE_IP_OCCURANCES;
	
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
	        'commentaire' => 'Erreur d\'authentification pour ' . $identifiant,
	    ));
		
		writeInLogs("Verouillage d??finitif de l\'adresse IP suite ?? la tentative d'authentification avec ".$identifiant, '2', NULL);

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
            'commentaire' => 'Erreur d\'authentification pour ' . $identifiant,
        ));

        writeInLogs("Verouillage temporaire de l\'adresse IP suite ?? la tentative d'authentification avec ".$identifiant, '2', NULL);
    }
}

function loadSession($idPersonne)
{
	global $db;
	
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS v ON p.idPersonne = v.idPersonne WHERE p.idPersonne = :idPersonne;');
	$query->execute(array(
	    'idPersonne' => $idPersonne
	));
	$data = $query->fetch();
	
	$_SESSION['idPersonne']                                = $data['idPersonne'];
	$_SESSION['idProfil']                                  = $data['idProfil'];
	$_SESSION['identifiant']                               = $data['identifiant'];
	$_SESSION['nomPersonne']                               = $data['nomPersonne'];
	$_SESSION['prenomPersonne']                            = $data['prenomPersonne'];
	$_SESSION['mailPersonne']                              = $data['mailPersonne'];
	$_SESSION['telPersonne']                               = $data['telPersonne'];
	$_SESSION['fonction']                                  = $data['fonction'];
	$_SESSION['disclaimerAccept']                          = $data['disclaimerAccept'];
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
	$_SESSION['consommationLots_lecture']                  = $data['consommationLots_lecture'];
	$_SESSION['consommationLots_affectation']              = $data['consommationLots_affectation'];
	$_SESSION['consommationLots_supression']               = $data['consommationLots_supression'];
	$_SESSION['codeBarre_lecture']                         = $data['codeBarre_lecture'];
	$_SESSION['codeBarre_ajout']                           = $data['codeBarre_ajout'];
	$_SESSION['codeBarre_modification']                    = $data['codeBarre_modification'];
	$_SESSION['codeBarre_suppression']                     = $data['codeBarre_suppression'];
	
	$_SESSION['tableRowPerso']                             = $data['tableRowPerso'];
	
	$_SESSION['conf_accueilRefresh']                       = $data['conf_accueilRefresh'];
	$_SESSION['conf_indicateur1Accueil']                   = $data['conf_indicateur1Accueil'];
	$_SESSION['conf_indicateur2Accueil']                   = $data['conf_indicateur2Accueil'];
	$_SESSION['conf_indicateur3Accueil']                   = $data['conf_indicateur3Accueil'];
	$_SESSION['conf_indicateur4Accueil']                   = $data['conf_indicateur4Accueil'];
	$_SESSION['conf_indicateur5Accueil']                   = $data['conf_indicateur5Accueil'];
	$_SESSION['conf_indicateur6Accueil']                   = $data['conf_indicateur6Accueil'];
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
	
	$_SESSION['isActiveDirectory']                         = $data['isActiveDirectory'];
	
	$_SESSION['LAST_ACTIVITY']                             = time();
	
	if(!(isset($_SESSION['DELEGATION_ACTIVE'])))
	{
		$_SESSION['DELEGATION_ACTIVE']                         = 0;
		$_SESSION['LOGS_DELEGATION_PREFIXE']                   = '';
	}
	
	$query = $db->prepare('UPDATE PERSONNE_REFERENTE SET derniereConnexion = CURRENT_TIMESTAMP WHERE idPersonne = :idPersonne;');
	$query->execute(array(
	    'idPersonne' => $_SESSION['idPersonne']
	));
}

function randomColor()
{
	$rand = array('#111111','#dd4b39','#f39c12','#00c0ef','#0073b7','#3c8dbc','#00a65a','#001f3f','#39cccc','#3d9970','#01ff70','#ff851b','#f012be','#605ca8','#d81b60','#000000','#d33724','#db8b0b','#00a7d0','#005384','#357ca5','#008d4c','#001a35','#30bbbb','#368763','#00e765','#ff7701','#db0ead','#555299','#ca195a','#606c84');
	$color = $rand[rand(0,34)];
	return $color;
}

function unlockLotsInventaires()
{
    global $db;

    $unlock = $db->query('UPDATE LOTS_LOTS SET inventaireEnCours = Null;');
}
function unlockReservesInventaires()
{
    global $db;

    $unlock = $db->query('UPDATE RESERVES_CONTENEUR SET inventaireEnCours = Null;');
}

function updatePeremptionsAnticipations()
{
    global $db;

    writeInLogs("D??but de la mise ?? jour de la table de mat??riel op??rationnel pour mise ?? jour du champ d'anticipation des p??remption conform??ment au catalogue.", '1', NULL);
    $query = $db->query('UPDATE MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue SET e.peremptionAnticipation = c.peremptionAnticipationOpe WHERE c.peremptionAnticipationOpe IS NOT NULL');
    writeInLogs("Fin de la mise ?? jour de la table de mat??riel op??rationnel pour mise ?? jour du champ d'anticipation des p??remption conform??ment au catalogue.", '1', NULL);

    writeInLogs("D??but de la mise ?? jour de la table de mat??riel en reserve pour mise ?? jour du champ d'anticipation des p??remption conform??ment au catalogue.", '1', NULL);
    $query = $db->query('UPDATE RESERVES_MATERIEL e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue SET e.peremptionReserveAnticipation = c.peremptionAnticipationRes WHERE c.peremptionAnticipationRes IS NOT NULL');
    writeInLogs("Fin de la mise ?? jour de la table de mat??riel en reserve pour mise ?? jour du champ d'anticipation des p??remption conform??ment au catalogue.", '1', NULL);
}

function notificationsMAJpersonne()
{
	global $db;
	
	writeInLogs("D??but de la mise ?? jour des conditions de notification dans la table des personnes.", '1', NULL);
	
	$personnes = $db->query('
		SELECT
				p.idPersonne,
				MAX(actifCeJour) as actifCeJour
			FROM
				PERSONNE_REFERENTE p
				LEFT OUTER JOIN NOTIFICATIONS_ABONNEMENTS n ON p.idPersonne = n.idPersonne
				LEFT OUTER JOIN NOTIFICATIONS_CONDITIONS c ON n.idCondition = c.idCondition
			GROUP BY
				p.idPersonne
	;');
	while($personne = $personnes->fetch())
	{
		$update = $db->prepare('
			UPDATE
				PERSONNE_REFERENTE
			SET
				notifications_abo_cejour = :actifCeJour
			WHERE
				idPersonne = :idPersonne
		');
		$update->execute(array(
			'actifCeJour' => $personne['actifCeJour'],
			'idPersonne' => $personne['idPersonne'],
		));
	}
	
	writeInLogs("Fin de la mise ?? jour des conditions de notification dans la table des personnes.", '1', NULL);
}

function notificationsConditionsMAJ()
{
	global $db;
	
	writeInLogs("D??but de la mise ?? jour de la table des conditions de notification.", '1', NULL);
	
	$query = $db->query('UPDATE NOTIFICATIONS_CONDITIONS SET actifCeJour = 0;');
	$query = $db->query('UPDATE NOTIFICATIONS_CONDITIONS SET actifCeJour = 1 WHERE idCondition = 1;');
	
	$query = $db->prepare('
		UPDATE
			NOTIFICATIONS_CONDITIONS
		SET
			actifCeJour = 1
		WHERE
			codeCondition = :numJour
			OR
			UPPER(codeCondition) = UPPER(:nomJour)
	;');
	$query->execute(array(
		'numJour' => date('l'),
		'nomJour' => date('d'),
	));
	
	writeInLogs("Fin de la mise ?? jour de la table des conditions de notification.", '1', NULL);
}

function checkVehiculeMaintenance($idVehicule)
{
    global $db;
    $erreurs = 0;

    $alertes = $db->prepare('
        SELECT
            *
        FROM
            VEHICULES_HEALTH_ALERTES
        WHERE
            idVehicule = :idVehicule
        ;');
    $alertes->execute(array('idVehicule'=>$idVehicule));

    while($alerte = $alertes->fetch())
    {
        $last = $db->prepare('
            SELECT
                MAX(dateHealth) as dateHealth
            FROM
                VEHICULES_HEALTH_CHECKS c
                LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth
            WHERE
                idVehicule = :idVehicule
                AND
                idHealthType = :idHealthType
        ;');
        $last->execute(array(
            'idVehicule' => $idVehicule,
            'idHealthType' => $alerte['idHealthType'],
        ));
        $last = $last->fetch();

        if($last['dateHealth'] == Null)
        {
            $erreurs += 1;
        }
        else
        {
            if(date('Y-m-d', strtotime($last['dateHealth']. ' + '.$alerte['frequenceHealth'].' days')) <= date('Y-m-d'))
            {
                $erreurs += 1;
            }
        }
    }

    if($erreurs == 0)
    {
        return 0;
    }
    else
    {
        return 1;
    }
}

function checkAllMaintenance()
{
    global $db;
    writeInLogs("Lancement de la v??rification des maintenance de tous les v??hicules.", '1', NULL);
    $vehicules = $db->query('SELECT * FROM VEHICULES;');

    while ($vehicule = $vehicules->fetch())
    {
        checkOneMaintenance($vehicule['idVehicule']);
    }
    writeInLogs("Fin de la v??rification des maintenance de tous les v??hicules.", '1', NULL);
}

function checkOneMaintenance($idVehicule)
{
    global $db;

    writeInLogs("Lancement de la v??rification des maintenance du v??hicule ".$idVehicule, '1', NULL);
    
    $alertes = $db->prepare('SELECT COUNT(*) as nb FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule;');
    $alertes->execute(array('idVehicule'=>$idVehicule));
    $alertes = $alertes->fetch();
    $alertes = $alertes['nb'];
    
    if ($alertes > 0)
    {
        if (checkVehiculeMaintenance($idVehicule) == 1)
        {
            writeInLogs("V??hicule ".$idVehicule.' contr??l?? en erreur de maintenance.', '1', NULL);
            $query2 = $db->prepare('UPDATE VEHICULES SET alerteMaintenance = True WHERE idVehicule = :idVehicule;');
            $query2->execute(array(
                'idVehicule' => $idVehicule
            ));
        }
        else
        {
            writeInLogs("V??hicule ".$idVehicule.' contr??l?? en maintenance ok.', '1', NULL);
            $query2 = $db->prepare('UPDATE VEHICULES SET alerteMaintenance = False WHERE idVehicule = :idVehicule;');
            $query2->execute(array(
                'idVehicule' => $idVehicule
            ));
        }
    }
    else
    {
        writeInLogs("V??rification des maintenance du vehicule ".$idVehicule." impossible car aucune alerte param??tr??e.", '2', NULL);
        $query2 = $db->prepare('UPDATE VEHICULES SET alerteMaintenance = Null WHERE idVehicule = :idVehicule;');
        $query2->execute(array(
            'idVehicule' => $idVehicule
        ));
    }

    writeInLogs("Fin de la v??rification des maintenance du v??hicule ".$idVehicule, '1', NULL);
}

function checkVehiculeDesinfection($idVehicule)
{
    global $db;
    $erreurs = 0;

    $alertes = $db->prepare('
        SELECT
            *
        FROM
            VEHICULES_DESINFECTIONS_ALERTES
        WHERE
            idVehicule = :idVehicule
        ;');
    $alertes->execute(array('idVehicule'=>$idVehicule));

    while($alerte = $alertes->fetch())
    {
        $last = $db->prepare('
            SELECT
                MAX(dateDesinfection) as dateDesinfection
            FROM
                VEHICULES_DESINFECTIONS
            WHERE
                idVehicule = :idVehicule
                AND
                idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
        ;');
        $last->execute(array(
            'idVehicule' => $idVehicule,
            'idVehiculesDesinfectionsType' => $alerte['idVehiculesDesinfectionsType'],
        ));
        $last = $last->fetch();

        if($last['dateDesinfection'] == Null)
        {
            $erreurs += 1;
        }
        else
        {
            if(date('Y-m-d', strtotime($last['dateDesinfection']. ' + '.$alerte['frequenceDesinfection'].' days')) <= date('Y-m-d'))
            {
                $erreurs += 1;
            }
        }
    }

    if($erreurs == 0)
    {
        return 0;
    }
    else
    {
        return 1;
    }
}

function checkAllDesinfection()
{
    global $db;
    writeInLogs("Lancement de la v??rification des d??sinfections de tous les v??hicules.", '1', NULL);
    $vehicules = $db->query('SELECT * FROM VEHICULES;');

    while ($vehicule = $vehicules->fetch())
    {
        checkOneDesinfection($vehicule['idVehicule']);
    }
    writeInLogs("Fin de la v??rification des d??sinfections de tous les v??hicules.", '1', NULL);
}

function checkOneDesinfection($idVehicule)
{
    global $db;

    writeInLogs("Lancement de la v??rification des d??sinfections du v??hicule ".$idVehicule, '1', NULL);
    
    $alertes = $db->prepare('SELECT COUNT(*) as nb FROM VEHICULES_DESINFECTIONS_ALERTES WHERE idVehicule = :idVehicule;');
    $alertes->execute(array('idVehicule'=>$idVehicule));
    $alertes = $alertes->fetch();
    $alertes = $alertes['nb'];
    
    if ($alertes > 0)
    {
        if (checkVehiculeDesinfection($idVehicule) == 1)
        {
            writeInLogs("V??hicule ".$idVehicule.' contr??l?? en erreur de d??sinfection.', '1', NULL);
            $query2 = $db->prepare('UPDATE VEHICULES SET alerteDesinfection = True WHERE idVehicule = :idVehicule;');
            $query2->execute(array(
                'idVehicule' => $idVehicule
            ));
        }
        else
        {
            writeInLogs("V??hicule ".$idVehicule.' contr??l?? en d??sinfection ok.', '1', NULL);
            $query2 = $db->prepare('UPDATE VEHICULES SET alerteDesinfection = False WHERE idVehicule = :idVehicule;');
            $query2->execute(array(
                'idVehicule' => $idVehicule
            ));
        }
    }
    else
    {
        writeInLogs("V??rification des d??sinfections du vehicule ".$idVehicule." impossible car aucune alerte param??tr??e.", '2', NULL);
        $query2 = $db->prepare('UPDATE VEHICULES SET alerteDesinfection = Null WHERE idVehicule = :idVehicule;');
        $query2->execute(array(
            'idVehicule' => $idVehicule
        ));
    }

    writeInLogs("Fin de la v??rification des d??sinfections du v??hicule ".$idVehicule, '1', NULL);
}

function checkLotsConf($idLot)
{
    global $db;
    writeInLogs("V??rification de conformit?? du lot " . $idLot, '1', NULL);
    $errorsConf = 0;
    $query = $db->prepare('SELECT idTypeLot FROM LOTS_LOTS WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $idLot
    ));
    $data = $query->fetch();

    $query = $db->prepare('SELECT * FROM REFERENTIELS r LEFT OUTER JOIN LOTS_TYPES t on r.idTypeLot=t.idTypeLot LEFT OUTER JOIN MATERIEL_CATALOGUE m on r.idMaterielCatalogue = m.idMaterielCatalogue WHERE r.idTypeLot = :idTypeLot;');
    $query->execute(array(
        'idTypeLot' => $data['idTypeLot']
    ));

    while ($data = $query->fetch())
    {
        $query2 = $db->prepare('SELECT MIN(peremption) as peremption FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE idMaterielCatalogue = :idMaterielCatalogue AND s.idLot = :idLot;');
        $query2->execute(array(
            'idMaterielCatalogue' => $data['idMaterielCatalogue'],
            'idLot' => $idLot
        ));
        $data2 = $query2->fetch();

        if (($data['obligatoire'] == 1) AND ($data['sterilite'] == 1) AND ($data2['peremption'] < date('Y-m-d')))
        {
            $errorsConf = $errorsConf +1;
        }

        if (($data['obligatoire'] == 1) AND ($data['sterilite'] == 1) AND ($data2['peremption'] == 0))
        {
            $errorsConf = $errorsConf +1;
        }

        if (($data['obligatoire'] == 1) AND ($data['sterilite'] == 1) AND ($data2['peremption'] == '0000-00-00'))
        {
            $errorsConf = $errorsConf +1;
        }

        if (($data['obligatoire'] == 1) AND ($data['sterilite'] == 1) AND ($data2['peremption'] == Null))
        {
            $errorsConf = $errorsConf +1;
        }

        $query3 = $db->prepare('SELECT SUM(quantite) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE idMaterielCatalogue = :idMaterielCatalogue AND s.idLot = :idLot;');
        $query3->execute(array(
            'idMaterielCatalogue' => $data['idMaterielCatalogue'],
            'idLot' => $idLot
        ));
        $data3 = $query3->fetch();

        if (($data['obligatoire'] == 1) AND ($data3['nb']<$data['quantiteReferentiel']))
        {
            $errorsConf = $errorsConf +1;
        }
    }

    if ($errorsConf>0)
    {
        writeInLogs("Lot " . $idLot." v??rifi?? non-conforme", '1', NULL);
        return 1;
    }
    else
    {
        writeInLogs("Lot " . $idLot." v??rifi?? conforme", '1', NULL);
        return 0;
    }
}

function checkAllConf()
{
    global $db;
    writeInLogs("Lancement de la v??rification de conformit?? de tous les lots.", '1', NULL);
    $query = $db->query('SELECT * FROM LOTS_LOTS;');
    
    while ($data = $query->fetch())
    {
        if ($data['idTypeLot'] > 0)
        {
            if (checkLotsConf($data['idLot']) == 1)
            {
                $query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = True WHERE idLot = :idLot;');
                $query2->execute(array(
                    'idLot' => $data['idLot']
                ));
            }
            else
            {
                $query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = False WHERE idLot = :idLot;');
                $query2->execute(array(
                    'idLot' => $data['idLot']
                ));
            }
        }
        else
        {
            $query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = Null WHERE idLot = :idLot;');
            $query2->execute(array(
                'idLot' => $data['idLot']
            ));
        }
    }
    writeInLogs("Fin de la v??rification de conformit?? de tous les lots.", '1', NULL);
}


function checkOneConf($idLot)
{
    global $db;
    $query = $db->prepare('SELECT * FROM LOTS_LOTS WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $idLot
    ));
    
    $data = $query->fetch();
    
    if ($data['idTypeLot'] > 0)
    {
        if (checkLotsConf($data['idLot']) == 1)
        {
            $query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = True WHERE idLot = :idLot;');
            $query2->execute(array(
                'idLot' => $data['idLot']
            ));
        }
        else
        {
            $query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = False WHERE idLot = :idLot;');
            $query2->execute(array(
                'idLot' => $data['idLot']
            ));
        }
    }
    else
    {
        writeInLogs("V??rification de conformit?? du lot ".$idLot." impossible car non-rattach?? ?? un r??f??rentiel", '2', NULL);
        $query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = Null WHERE idLot = :idLot;');
        $query2->execute(array(
            'idLot' => $data['idLot']
        ));
    }
}

function majIndicateursPersonne($idPersonne, $enableLog)
{
    global $db;

    $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;');
    $query -> execute(array('idPersonne' => $idPersonne));
    $data = $query->fetch();

    $conf_indicateur1Accueil = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['conf_indicateur1Accueil']);

    $conf_indicateur2Accueil = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['conf_indicateur2Accueil']);

    $conf_indicateur3Accueil = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['conf_indicateur3Accueil']);

    $conf_indicateur4Accueil = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['conf_indicateur4Accueil']);

    $conf_indicateur5Accueil = ($data['reserve_lecture']) && ($data['conf_indicateur5Accueil']);

    $conf_indicateur6Accueil = ($data['reserve_lecture']) && ($data['conf_indicateur6Accueil']);
    
    $conf_indicateur9Accueil = ($data['tenues_lecture'] OR $data['tenuesCatalogue_lecture']) && ($data['conf_indicateur9Accueil']);
    
    $conf_indicateur10Accueil = ($data['tenues_lecture'] OR $data['tenuesCatalogue_lecture']) && ($data['conf_indicateur10Accueil']);

    $conf_indicateur11Accueil = ($data['desinfections_lecture']) && ($data['conf_indicateur11Accueil']);

    $conf_indicateur12Accueil = ($data['vehiculeHealth_lecture']) && ($data['conf_indicateur12Accueil']);

    $query = $db->prepare('
        UPDATE
            PERSONNE_REFERENTE
        SET
            conf_indicateur1Accueil  = :conf_indicateur1Accueil,
            conf_indicateur2Accueil  = :conf_indicateur2Accueil,
            conf_indicateur3Accueil  = :conf_indicateur3Accueil,
            conf_indicateur4Accueil  = :conf_indicateur4Accueil,
            conf_indicateur5Accueil  = :conf_indicateur5Accueil,
            conf_indicateur6Accueil  = :conf_indicateur6Accueil,
            conf_indicateur9Accueil  = :conf_indicateur9Accueil,
            conf_indicateur10Accueil = :conf_indicateur10Accueil,
            conf_indicateur11Accueil = :conf_indicateur11Accueil,
            conf_indicateur12Accueil = :conf_indicateur12Accueil
        WHERE
            idPersonne = :idPersonne
    ;');
    $query->execute(array(
        'idPersonne'               => $idPersonne,
        'conf_indicateur1Accueil'  => (int)$conf_indicateur1Accueil,
        'conf_indicateur2Accueil'  => (int)$conf_indicateur2Accueil,
        'conf_indicateur3Accueil'  => (int)$conf_indicateur3Accueil,
        'conf_indicateur4Accueil'  => (int)$conf_indicateur4Accueil,
        'conf_indicateur5Accueil'  => (int)$conf_indicateur5Accueil,
        'conf_indicateur6Accueil'  => (int)$conf_indicateur6Accueil,
        'conf_indicateur9Accueil'  => (int)$conf_indicateur9Accueil,
        'conf_indicateur10Accueil' => (int)$conf_indicateur10Accueil,
        'conf_indicateur11Accueil' => (int)$conf_indicateur11Accueil,
        'conf_indicateur12Accueil' => (int)$conf_indicateur12Accueil,
    ));

    if ($enableLog == 1)
    {
        writeInLogs("Revue des indicateurs d'accueil pour la personne " . $idPersonne, '1', NULL);
    }
}

function majIndicateursProfil($idProfil)
{
    global $db;
    $query = $db->prepare('SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;');
    $query -> execute(array('idProfil' => $idProfil));

    writeInLogs("Revue des indicateurs d'accueil pour le profil " . $idProfil, '1', NULL);

    while ($data = $query->fetch())
    {
        majIndicateursPersonne($data['idPersonne'], 0);
    }
}

function majNotificationsPersonne($idPersonne, $enableLog)
{
    global $db;

    $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;');
    $query -> execute(array('idPersonne' => $idPersonne));
    $data = $query->fetch();
    
    $notif_lots_manquants = $data['notifications'] && ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_manquants']);
    
    $notif_lots_peremptions = $data['notifications'] && ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_peremptions']);
    
    $notif_lots_inventaires = $data['notifications'] && ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_inventaires']);
    
    $notif_lots_conformites = $data['notifications'] && ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_conformites']);
    
    $notif_reserves_manquants = $data['notifications'] && ($data['reserve_lecture']) && ($data['notif_reserves_manquants']);
    
    $notif_reserves_peremptions = $data['notifications'] && ($data['reserve_lecture']) && ($data['notif_reserves_peremptions']);
    
    $notif_reserves_inventaires = $data['notifications'] && ($data['reserve_lecture']) && ($data['notif_reserves_inventaires']);

    $notif_vehicules_desinfections = $data['notifications'] && ($data['desinfections_lecture']) && ($data['notif_vehicules_desinfections']);
    
    $notif_vehicules_health = $data['notifications'] && ($data['vehiculeHealth_lecture']) && ($data['notif_vehicules_health']);
    
    $notif_tenues_stock = $data['notifications'] && ($data['tenuesCatalogue_lecture']) && ($data['notif_tenues_stock']);
    
    $notif_tenues_retours = $data['notifications'] && ($data['tenues_lecture']) && ($data['notif_tenues_retours']);

    $notif_benevoles_lots = $data['notifications'] && ($data['alertesBenevolesLots_lecture']) && ($data['notif_benevoles_lots']);

    $notif_benevoles_vehicules = $data['notifications'] && ($data['alertesBenevolesVehicules_lecture']) && ($data['notif_benevoles_vehicules']);
    
    $query = $db->prepare('
        UPDATE
            PERSONNE_REFERENTE
        SET
            notif_lots_manquants          = :notif_lots_manquants,
            notif_lots_peremptions        = :notif_lots_peremptions,
            notif_lots_inventaires        = :notif_lots_inventaires,
            notif_lots_conformites        = :notif_lots_conformites,
            notif_reserves_manquants      = :notif_reserves_manquants,
            notif_reserves_peremptions    = :notif_reserves_peremptions,
            notif_reserves_inventaires    = :notif_reserves_inventaires,
            notif_vehicules_health        = :notif_vehicules_health,
            notif_vehicules_desinfections = :notif_vehicules_desinfections,
            notif_tenues_stock            = :notif_tenues_stock,
            notif_tenues_retours          = :notif_tenues_retours,
            notif_benevoles_lots          = :notif_benevoles_lots,
            notif_benevoles_vehicules     = :notif_benevoles_vehicules
        WHERE
            idPersonne                    = :idPersonne
    ;');
    $query->execute(array(
        'idPersonne'                    => $idPersonne,
        'notif_lots_manquants'          => (int)$notif_lots_manquants,
        'notif_lots_peremptions'        => (int)$notif_lots_peremptions,
        'notif_lots_inventaires'        => (int)$notif_lots_inventaires,
        'notif_lots_conformites'        => (int)$notif_lots_conformites,
        'notif_reserves_manquants'      => (int)$notif_reserves_manquants,
        'notif_reserves_peremptions'    => (int)$notif_reserves_peremptions,
        'notif_reserves_inventaires'    => (int)$notif_reserves_inventaires,
        'notif_vehicules_desinfections' => (int)$notif_vehicules_desinfections,
        'notif_vehicules_health'        => (int)$notif_vehicules_health,
        'notif_tenues_stock'            => (int)$notif_tenues_stock,
        'notif_tenues_retours'          => (int)$notif_tenues_retours,
        'notif_benevoles_lots'          => (int)$notif_benevoles_lots,
        'notif_benevoles_vehicules'     => (int)$notif_benevoles_vehicules
    ));
    
    if ($enableLog == 1)
    {
        writeInLogs("Revue des notifications pour la personne " . $idPersonne, '1', NULL);
    }
}

function majNotificationsProfil($idProfil)
{
    global $db;
    $query = $db->prepare('SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;');
    $query -> execute(array('idProfil' => $idProfil));

    writeInLogs("Revue des notifications pour le profil " . $idProfil, '1', NULL);

    while ($data = $query->fetch())
    {
        majNotificationsPersonne($data['idPersonne'], 0);
    }
}

function majValideursPersonne($enableLog)
{
    global $db;

    $delete = $db->query('DELETE CENTRE_COUTS_PERSONNES FROM CENTRE_COUTS_PERSONNES INNER JOIN VIEW_HABILITATIONS ON CENTRE_COUTS_PERSONNES.idPersonne = VIEW_HABILITATIONS.idPersonne WHERE VIEW_HABILITATIONS.cout_etreEnCharge = 0 OR VIEW_HABILITATIONS.cout_etreEnCharge IS NULL;');
    
    if ($enableLog == 1)
    {
        writeInLogs("Nettoyage des personnes des valideurs par d??faut des commandes", '1', NULL);
    }
}

function cmdTotal ($idCommande)
{
    global $db;

    $query2 = $db->prepare('
        SELECT
            CAST(IFNULL(SUM(prixProduitTTC*quantiteCommande),0) AS DECIMAL(10,2)) AS total
        FROM
            COMMANDES_MATERIEL c
            LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue 
        WHERE
            idCommande = :idCommande
    ;');
    $query2->execute(array('idCommande' => $idCommande));
    $total = $query2->fetch();

    return $total['total'];
}
function cmdEstAffectee ($idPersonne, $idCommande)
{
    global $db;
    $query = $db->prepare('SELECT idAffectee FROM COMMANDES_AFFECTEES WHERE idCommande = :idCommande;');
    $query -> execute(array('idCommande' => $idCommande));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idAffectee')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function cmdEstValideur ($idPersonne, $idCommande)
{
    global $db;

    $cmdTotal = cmdTotal($idCommande);
    
    $query2 = $db->prepare('SELECT idCommande FROM COMMANDES WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND integreCentreCouts = 0 AND idEtat != 8 AND idEtat != 1 AND idEtat != 2;');
    $query2->execute(array('idCommande'=>$idCommande));
    $validees = 0;
    while($commande = $query2->fetch())
    {
        $validees += cmdTotal($commande['idCommande']);
    }
    $query = $db->prepare('SELECT COALESCE(SUM(montantEntrant),0)-COALESCE(SUM(montantSortant),0) as total FROM CENTRE_COUTS_OPERATIONS WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande)');
    $query->execute(array('idCommande'=>$idCommande));
    $data = $query->fetch();
    $enCours = $data['total']-$validees;

    $query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande)');
    $query->execute(array('idCommande'=>$idCommande));
    $data = $query->fetch();
    if($data['dateFermeture'] == Null)
    {
        if($data['dateOuverture'] <= date('Y-m-d'))
        {
            $ouvert = 1;
        }
        else
        {
            $ouvert = 0;
        }
    }
    else
    {
        if($data['dateFermeture'] < date('Y-m-d'))
        {
            $ouvert = 0;
        }
        else
        {
            if($data['dateOuverture'] <= date('Y-m-d'))
            {
                $ouvert = 1;
            }
            else
            {
                $ouvert = 0;
            }
        }
    }
            

    $query = $db->prepare('SELECT idPersonne, montantMaxValidation, depasseBudget, validerClos FROM CENTRE_COUTS_PERSONNES WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND idPersonne = :idPersonne;');
    $query -> execute(array('idCommande'=>$idCommande, 'idPersonne'=>$idPersonne));
    
    while($data = $query->fetch())
    {
        if($data['montantMaxValidation'] == Null)
        {
            if($ouvert == 0 AND $data['validerClos'] == 0)
            {
                return 0;
            }

            if($cmdTotal >= $enCours AND $data['depasseBudget'] == 0)
            {
                return 0;
            }

            return 1;
        }
        else
        {
            if($data['montantMaxValidation'] >= $cmdTotal)
            {
                if($ouvert == 0 AND $data['validerClos'] == 0)
                {
                    return 0;
                }

                if($cmdTotal > $enCours AND $data['depasseBudget'] == 0)
                {
                    return 0;
                }

                return 1;
            }
            else
            {
                return -1;
            }
        }
    }
    
    return 0;
}
function cmdEstValideurUniversel ($idPersonne, $idCommande)
{
	global $db;
	$user = $db->prepare('SELECT commande_valider_delegate FROM VIEW_HABILITATIONS WHERE idPersonne = :idPersonne');
	$user->execute(array('idPersonne' => $idPersonne));
	$user = $user->fetch();
	
	if($user['commande_valider_delegate'] != 1)
	{
		return 0;
	}
	
	$cmdTotal = cmdTotal($idCommande);
	
	$nbValideursStandards = $db->prepare('
		SELECT
			COUNT(*) as nb
		FROM
			CENTRE_COUTS_PERSONNES c
			INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne
		WHERE
			idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande)
			AND
			(montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL)
	');
	$nbValideursStandards->execute(array(
		'idCommande' => $idCommande,
		'montantTotalCommande' => $cmdTotal,
	));
	$nbValideursStandards = $nbValideursStandards->fetch();
	if($nbValideursStandards['nb'] == 0)
	{
		return 1;
	}
	else
	{
		return 0;
	}
}
function cmdEstObservateur ($idPersonne, $idCommande)
{
    global $db;
    $query = $db->prepare('SELECT idObservateur FROM COMMANDES_OBSERVATEURS WHERE idCommande = :idCommande;');
    $query -> execute(array('idCommande' => $idCommande));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idObservateur')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function cmdEstDemandeur ($idPersonne, $idCommande)
{
    global $db;
    $query = $db->prepare('SELECT idDemandeur FROM COMMANDES_DEMANDEURS WHERE idCommande = :idCommande;');
    $query -> execute(array('idCommande' => $idCommande));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idDemandeur')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function centreCoutsEstCharge ($idPersonne, $idCentreDeCout)
{
    global $db;

    $query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array('idCentreDeCout'=>$idCentreDeCout));
    $data = $query->fetch();
    if($data['dateFermeture'] == Null)
    {
        if($data['dateOuverture'] <= date('Y-m-d'))
        {
            $ouvert = 1;
        }
        else
        {
            $ouvert = 0;
        }
    }
    else
    {
        if($data['dateFermeture'] < date('Y-m-d'))
        {
            $ouvert = 0;
        }
        else
        {
            if($data['dateOuverture'] <= date('Y-m-d'))
            {
                $ouvert = 1;
            }
            else
            {
                $ouvert = 0;
            }
        }
    }

    $query = $db->prepare('SELECT COALESCE(SUM(montantEntrant),0)-COALESCE(SUM(montantSortant),0) as total FROM CENTRE_COUTS_OPERATIONS WHERE idCentreDeCout = :idCentreDeCout');
    $query->execute(array('idCentreDeCout'=>$idCentreDeCout));
    $data = $query->fetch();
    $enCours = $data['total'];

    $query = $db->prepare('SELECT c.* FROM CENTRE_COUTS_PERSONNES c LEFT OUTER JOIN VIEW_HABILITATIONS v ON c.idPersonne = v.idPersonne WHERE c.idCentreDeCout = :idCentreDeCout AND v.cout_etreEnCharge=1 AND c.idPersonne = :idPersonne;');
    $query -> execute(array('idCentreDeCout' => $idCentreDeCout, 'idPersonne' => $idPersonne));

    while($data = $query->fetch())
    {    
        if($ouvert==0 AND $data['validerClos']==0)
        {
            return 0;
        }

        if($enCours<0 AND $data['depasseBudget']==0)
        {
            return 0;
        }

        return 1;
    }

    return 0;
}
function tdlEstExecutant ($idPersonne, $idTache)
{
    global $db;
    $query = $db->prepare('SELECT idExecutant FROM TODOLIST_PERSONNES WHERE idTache = :idTache;');
    $query -> execute(array('idTache' => $idTache));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idExecutant')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function cmdEtatCentreCouts ($idCommande)
{
	global $db;
	$query = $db->prepare('SELECT * FROM COMMANDES WHERE idCommande = :idCommande;');
	$query -> execute(array('idCommande' => $idCommande));
	$commande = $query->fetch();
	
	if($commande['idEtat']==1 OR $commande['idEtat']==8 OR $commande['idCentreDeCout']==Null OR $commande['idCentreDeCout']=='')
	{
		return '<span class="badge bg-grey">N/A</span>';
	}
	
	if($commande['idEtat']<4)
	{
		return '<span class="badge bg-grey">Int??gration non-disponible</span>';
	}
	
	if($commande['integreCentreCouts']==0)
	{
		return '<span class="badge bg-orange">En attente d\'int??gration</span>';
	}
	
	$query = $db->prepare('SELECT COUNT(*) as nb FROM CENTRE_COUTS_OPERATIONS WHERE idCommande = :idCommande;');
	$query -> execute(array('idCommande' => $idCommande));
	$operations = $query->fetch();
	$operations = $operations['nb'];
	
	if($operations>0)
	{
		return '<span class="badge bg-green">Int??gr??e</span>';
	}
	else
	{
		return '<span class="badge bg-red">Int??gration refus??e</span>';
	}
}
function cmdEtatCentreCoutsCouleur ($idCommande)
{
	global $db;
	$query = $db->prepare('SELECT * FROM COMMANDES WHERE idCommande = :idCommande;');
	$query -> execute(array('idCommande' => $idCommande));
	$commande = $query->fetch();
	
	if($commande['idEtat']==1 OR $commande['idEtat']==8 OR $commande['idCentreDeCout']==Null OR $commande['idCentreDeCout']=='')
	{
		return 'grey';
	}
	
	if($commande['idEtat']<4)
	{
		return 'grey';
	}
	
	if($commande['integreCentreCouts']==0)
	{
		return 'orange';
	}
	
	$query = $db->prepare('SELECT COUNT(*) as nb FROM CENTRE_COUTS_OPERATIONS WHERE idCommande = :idCommande;');
	$query -> execute(array('idCommande' => $idCommande));
	$operations = $query->fetch();
	$operations = $operations['nb'];
	
	if($operations>0)
	{
		return 'green';
	}
	else
	{
		return 'red';
	}
}

function getTDLdateColor ($idTache)
{
	global $db;
	
	$tache = $db->prepare('SELECT * FROM TODOLIST WHERE idTache = :idTache;');
	$tache->execute(array('idTache'=>$idTache));
	$tache = $tache->fetch();
	
	if(date("Y-m-d", strtotime($tache['dateExecution'])) == date("Y-m-d"))
	return 'warning';
	
	if(date("Y-m-d", strtotime($tache['dateExecution'])) < date("Y-m-d"))
	return 'danger';
	
	return 'success';
}

function replaceString($string,$replacementArray)
{
    foreach ($replacementArray as $chercher => $remplacer) {
        $string = str_replace(":".$chercher, $remplacer, $string);
    }
    return $string;
}

function figeInventaireLot($idLot, $idInventaire)
{
    global $db;

    $elements = $db->prepare('
        INSERT INTO
            INVENTAIRES_CONTENUS(idInventaire, idMaterielCatalogue, quantiteInventaire, peremptionInventaire)
        SELECT
            :idInventaire as idInventaire,
            e.idMaterielCatalogue as idMaterielCatalogue,
            SUM(e.quantite) as quantiteInventaire,
            MIN(e.peremption) as peremptionInventaire
        FROM
            MATERIEL_ELEMENT e
            LEFT OUTER JOIN MATERIEL_EMPLACEMENT emp ON e.idEmplacement = emp.idEmplacement
            LEFT OUTER JOIN MATERIEL_SAC s ON emp.idSac = s.idSac
        WHERE
            s.idLot = :idLot
        GROUP BY
            e.idMaterielCatalogue
    ');
    $elements->execute(array('idLot'=>$idLot, 'idInventaire'=>$idInventaire));
}

function figeInventaireReserve($idConteneur, $idReserveInventaire)
{
    global $db;

    $elements = $db->prepare('
        INSERT INTO
            RESERVES_INVENTAIRES_CONTENUS(idReserveInventaire, idMaterielCatalogue, quantiteInventaire, peremptionInventaire)
        SELECT
            :idReserveInventaire as idReserveInventaire,
            idMaterielCatalogue as idMaterielCatalogue,
            SUM(quantiteReserve) as quantiteInventaire,
            MIN(peremptionReserve) as peremptionInventaire
        FROM
            RESERVES_MATERIEL
        WHERE
            idConteneur = :idConteneur
        GROUP BY
            idMaterielCatalogue
    ');
    $elements->execute(array('idConteneur'=>$idConteneur, 'idReserveInventaire'=>$idReserveInventaire));
}

function cnilAnonyme($idPersonne)
{
	global $db;

	$personne = $db->prepare('
        SELECT
        	*
        FROM
            PERSONNE_REFERENTE
        WHERE
            idPersonne = :idPersonne
    ');
    $personne->execute(array('idPersonne' => $idPersonne));
    $personne = $personne->fetch();

	$profilCleaning = $db->prepare('DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne');
    $profilCleaning->execute(array('idPersonne' => $idPersonne));

    $cmdTimeLineCleaning = $db->prepare('
    	UPDATE
    		COMMANDES_TIMELINE
    	SET
    		detailsEvtCommande = REPLACE(detailsEvtCommande, :identifiant, :anonyme)
    ');
    $cmdTimeLineCleaning->execute(array(
    	'identifiant' => $personne['identifiant'],
    	'anonyme' => "ANONYME ".$personne['idPersonne'],
    ));
	
	$updateUser = $db->prepare('
        UPDATE
            PERSONNE_REFERENTE
        SET
            identifiant = CONCAT("ANONYME ", idPersonne),
            nomPersonne = "ANONYME",
            prenomPersonne = "ANONYME",
            mailPersonne = Null,
            telPersonne = Null,
            isActiveDirectory = FALSE,
            cnil_anonyme = true
        WHERE
            idPersonne = :idPersonne
    ');
    $updateUser->execute(array('idPersonne' => $idPersonne));

    majIndicateursPersonne($idPersonne,1);
    majNotificationsPersonne($idPersonne,1);
    majValideursPersonne(1);
}

function cnilAnonymeAlerte6()
{
	require_once 'mailFunction.php';
	
	global $db;
	global $APPNAME;
	
	$nbusers = $db->query('
    	SELECT
    		COUNT(vue.idPersonne) as nb
    	FROM
	    	(SELECT
	    		p.idPersonne,
	    		p.identifiant,
	    		p.derniereConnexion,
	    		COUNT(pp.idProfil) as nbProfil
	    	FROM
	    		VIEW_PERSONNE_REFERENTE p
	    		LEFT OUTER JOIN PROFILS_PERSONNES pp ON p.idPersonne = pp.idPersonne
	    	GROUP BY
	    		p.idPersonne
	    	) vue
	    WHERE
	    	vue.nbProfil = 0
	    	AND
	    	DATE_ADD(vue.derniereConnexion, INTERVAL 3 YEAR) <= DATE_ADD(NOW(), INTERVAL 6 MONTH)
	    	AND
	    	DATE_ADD(vue.derniereConnexion, INTERVAL 3 YEAR) > DATE_ADD(NOW(), INTERVAL 1 MONTH)
    ');
    $nbusers = $nbusers->fetch();
    if($nbusers['nb']>0)
    {
	
		$RETOURLIGNE = "\r\n";
		
		$query = $db->query('SELECT * FROM CONFIG;');
		$config = $query->fetch();
		
		$sujet = "[" . $APPNAME . "] Anonymisation de comptes sous 6 mois";
	    $message = "Bonjour,<br/><br/>Ceci est une notification automatique pour vous informer que les comptes suivants seront anonymis??s d'ici 6 mois maximum:<br/><ul>";
	    
	    $users = $db->query('
	    	SELECT
	    		vue.*
	    	FROM
		    	(SELECT
		    		p.idPersonne,
		    		p.identifiant,
		    		p.derniereConnexion,
		    		COUNT(pp.idProfil) as nbProfil
		    	FROM
		    		VIEW_PERSONNE_REFERENTE p
		    		LEFT OUTER JOIN PROFILS_PERSONNES pp ON p.idPersonne = pp.idPersonne
		    	GROUP BY
		    		p.idPersonne
		    	) vue
		    WHERE
		    	vue.nbProfil = 0
		    	AND
		    	DATE_ADD(vue.derniereConnexion, INTERVAL 3 YEAR) <= DATE_ADD(NOW(), INTERVAL 6 MONTH)
		    	AND
		    	DATE_ADD(vue.derniereConnexion, INTERVAL 3 YEAR) > DATE_ADD(NOW(), INTERVAL 1 MONTH)
	    ');
	    while($user = $users->fetch())
	    {
	    	$message = $message . "<li>".$user['idPersonne'].' - '.$user['identifiant'].' (derni??re connexion: '.$user['derniereConnexion'].')</li>';
	    }
	    
	    $message = $message . "</ul><br/><br/>L'anonymisation se d??roulera de mani??re automatique sur la base de donn??es active, les logs ne sont pas impact??s. Cette anonymisation est rendue obligatoire par le code civil (article 9) ??non??ant un droit ?? l'oubli. Les deux crit??res utilis??s pour d??finir cette liste sont les suivants: compte d??sactiv?? (pas de profils) et derni??re connexion datant de plus de 3 ans.";
	    $message = $message . "<br/><br/>Cordialement<br/><br/>L'??quipe administrative de " . $APPNAME;
	
	    $message = $RETOURLIGNE.$message.$RETOURLIGNE;
	    queueMail("Mail CNIL 6 mois", $config['mailcnil'], $sujet, 2, $message);
    }
    else
    {
    	writeInLogs("CRON - Pas de mail d'alerte d'anonymisation CNIL sous 6 mois ?? envoyer", '1', NULL);
    }
}

function cnilAnonymeAlerte1()
{
	require_once 'mailFunction.php';
	
	global $db;
	global $APPNAME;
	
	$nbusers = $db->query('
    	SELECT
    		COUNT(vue.idPersonne) as nb
    	FROM
	    	(SELECT
	    		p.idPersonne,
	    		p.identifiant,
	    		p.derniereConnexion,
	    		COUNT(pp.idProfil) as nbProfil
	    	FROM
	    		VIEW_PERSONNE_REFERENTE p
	    		LEFT OUTER JOIN PROFILS_PERSONNES pp ON p.idPersonne = pp.idPersonne
	    	GROUP BY
	    		p.idPersonne
	    	) vue
	    WHERE
	    	vue.nbProfil = 0
	    	AND
	    	DATE_ADD(vue.derniereConnexion, INTERVAL 3 YEAR) <= DATE_ADD(NOW(), INTERVAL 1 MONTH)
    ');
    $nbusers = $nbusers->fetch();
    if($nbusers['nb']>0)
    {
	
		$RETOURLIGNE = "\r\n";
		
		$query = $db->query('SELECT * FROM CONFIG;');
		$config = $query->fetch();
		
		$sujet = "[" . $APPNAME . "] Anonymisation de comptes sous 1 mois";
	    $message = "Bonjour,<br/><br/>Ceci est une notification automatique pour vous informer que les comptes suivants seront anonymis??s d'ici 1 mois maximum:<br/><ul>";
	    
	    $users = $db->query('
	    	SELECT
	    		vue.*
	    	FROM
		    	(SELECT
		    		p.idPersonne,
		    		p.identifiant,
		    		p.derniereConnexion,
		    		COUNT(pp.idProfil) as nbProfil
		    	FROM
		    		VIEW_PERSONNE_REFERENTE p
		    		LEFT OUTER JOIN PROFILS_PERSONNES pp ON p.idPersonne = pp.idPersonne
		    	GROUP BY
		    		p.idPersonne
		    	) vue
		    WHERE
		    	vue.nbProfil = 0
		    	AND
		    	DATE_ADD(vue.derniereConnexion, INTERVAL 3 YEAR) <= DATE_ADD(NOW(), INTERVAL 1 MONTH)
	    ');
	    while($user = $users->fetch())
	    {
	    	$message = $message . "<li>".$user['idPersonne'].' - '.$user['identifiant'].' (derni??re connexion: '.$user['derniereConnexion'].')</li>';
	    }
	    
	    $message = $message . "</ul><br/><br/>L'anonymisation se d??roulera de mani??re automatique sur la base de donn??es active, les logs ne sont pas impact??s. Cette anonymisation est rendue obligatoire par le code civil (article 9) ??non??ant un droit ?? l'oubli. Les deux crit??res utilis??s pour d??finir cette liste sont les suivants: compte d??sactiv?? (pas de profils) et derni??re connexion datant de plus de 3 ans.";
	    $message = $message . "<br/><br/>Cordialement<br/><br/>L'??quipe administrative de " . $APPNAME;
	
	    $message = $RETOURLIGNE.$message.$RETOURLIGNE;
	    queueMail("Mail CNIL 1 mois", $config['mailcnil'], $sujet, 2, $message);
    }
    else
    {
    	writeInLogs("CRON - Pas de mail d'alerte d'anonymisation CNIL sous 1 mois ?? envoyer", '1', NULL);
    }
}

function cnilAnonymeCron()
{
	global $db;
	
	$users = $db->query('
		SELECT
			vue.*
		FROM
	    	(SELECT
	    		p.idPersonne,
	    		p.identifiant,
	    		p.derniereConnexion,
	    		COUNT(pp.idProfil) as nbProfil
	    	FROM
	    		PERSONNE_REFERENTE p
	    		LEFT OUTER JOIN PROFILS_PERSONNES pp ON p.idPersonne = pp.idPersonne
	    	GROUP BY
	    		p.idPersonne
	    	) vue
	    WHERE
	    	vue.nbProfil = 0
	    	AND
	    	DATE_ADD(vue.derniereConnexion, INTERVAL 3 YEAR) <= NOW()
    ');
	while($user = $users->fetch())
    {
    	writeInLogs("CRON - Anonymisation de l'utilisateur " . $user['idPersonne'] . " - " . $user['identifiant'], '1', NULL);
    	cnilAnonyme($user['idPersonne']);
    }
}

function notifications()
{
	global $db;

	require_once 'config.php';
	require_once 'mailFunction.php';

	global $APPNAME;

	writeInLogs("D??but du traitement automatiques des alertes par mail.", '1', NULL);

	$query = $db->query('
		SELECT
			COUNT(*) as nb
		FROM
			PERSONNE_REFERENTE
		WHERE
			notifications_abo_cejour = 1
			AND
				(notif_lots_manquants = 1
				OR notif_lots_peremptions = 1
				OR notif_lots_inventaires = 1
				OR notif_lots_conformites = 1
				OR notif_reserves_manquants = 1
				OR notif_reserves_peremptions = 1
				OR notif_reserves_inventaires = 1
				OR notif_vehicules_desinfections = 1
				OR notif_vehicules_health = 1
				OR notif_tenues_stock = 1
				OR notif_tenues_retours = 1)
		;');
	$data = $query->fetch();
	$nbDest = $data['nb'];

	if ($nbDest > 0)
	{
	    $query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE alerteConfRef = 1 AND idEtat = 1;');
		$data = $query->fetch();
		$nbLotsNOK = $data['nb'];

		$query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
		$data = $query->fetch();
		$nbManquant = $data['nb'];

		$query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE) AND idEtat = 1;');
		$data = $query->fetch();
		$nbPerime = $data['nb'];
		
		$query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve;');
		$data = $query->fetch();
		$nbManquantReserve = $data['nb'];
		
		$query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE peremptionNotificationReserve < CURRENT_DATE OR peremptionNotificationReserve = CURRENT_DATE;');
		$data = $query->fetch();
		$nbPerimeReserve = $data['nb'];

		$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND alerteDesinfection = 1;');
		$data = $query->fetch();
		$nbDesinfections = $data['nb'];

		$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND alerteMaintenance = 1;');
		$data = $query->fetch();
		$nbHealth = $data['nb'];
		
		$query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE idEtat = 1 AND (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
		$data = $query->fetch();
		$nbInventaires = $data['nb'];
		
		$query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_CONTENEUR WHERE (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
		$data = $query->fetch();
		$nbInventairesReserve = $data['nb'];

		$query = $db->query('SELECT COUNT(*) as nb FROM TENUES_CATALOGUE WHERE stockCatalogueTenue < stockAlerteCatalogueTenue OR stockCatalogueTenue = stockAlerteCatalogueTenue;');
		$data = $query->fetch();
		$nbManquantTenues = $data['nb'];
		
		$query = $db->query('SELECT COUNT(*) as nb FROM TENUES_AFFECTATION WHERE dateRetour < CURRENT_DATE OR dateRetour = CURRENT_DATE;');
		$data = $query->fetch();
		$nbRetoursTenues = $data['nb'];

	    $nbAlertes = $nbManquant + $nbPerime + $nbLotsNOK + $nbManquantReserve + $nbPerimeReserve + $nbInventaires + $nbInventairesReserve + $nbDesinfections + $nbHealth + $nbManquantTenues + $nbRetoursTenues;

	    writeInLogs($nbManquant." alertes d??tect??es sur la requete nbManquant (??l??ments en quantit?? insuffisante dans les lots).", '1', NULL);
	    writeInLogs($nbPerime." alertes d??tect??es sur la requete nbPerime (??l??ments p??rim??s dans les lots).", '1', NULL);
	    writeInLogs($nbLotsNOK." alertes d??tect??es sur la requete nbLotsNOK (conformit?? des lots).", '1', NULL);
	    writeInLogs($nbManquantReserve." alertes d??tect??es sur la requete nbManquantReserve (??l??ments en quantit?? insuffisante dans la r??serve).", '1', NULL);
	    writeInLogs($nbPerimeReserve." alertes d??tect??es sur la requete nbPerimeReserve (??l??ments p??rim??s dans la r??serve).", '1', NULL);
	    writeInLogs($nbInventaires." alertes d??tect??es sur la requete nbInventaires (inventaires des lots).", '1', NULL);
	    writeInLogs($nbInventairesReserve." alertes d??tect??es sur la requete nbInventairesReserve (inventaires de la r??serve).", '1', NULL);
	    writeInLogs($nbHealth." alertes d??tect??es sur la requete nbHealth (maintenances r??guli??res).", '1', NULL);
	    writeInLogs($nbDesinfections." alertes d??tect??es sur la requete nbDesinfections (d??sinfections des v??hicules).", '1', NULL);
	    writeInLogs($nbManquantTenues." alertes d??tect??es sur la requete nbManquantTenues (??l??ments de tenue en quantit?? insuffisante dans les stocks).", '1', NULL);
	    writeInLogs($nbRetoursTenues." alertes d??tect??es sur la requete nbRetoursTenues (??l??ments de tenue non-rendus).", '1', NULL);
	    
	    writeInLogs($nbAlertes." alertes d??tect??es par le syst??me de notifications journali??res.", '1', NULL);

	    if ($nbAlertes == 0)
	    {
	        writeInLogs("Pas de notification journali??re ?? envoyer.", '1', NULL);
	        exit;
	    }

	    


	    if ($nbPerime > 0)
		{
		    $message_Perime = "Alertes de p??remption des consommables dans les lots:<br/><ul>";
		    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE) AND idEtat = 1;');
		    while($data = $query->fetch())
		    {
		        $message_Perime = $message_Perime . "<li>".$data['libelleLot'] . " > " . $data['libelleSac'] . " > " . $data['libelleEmplacement'] . " > " . $data['libelleMateriel']."</li>";
		    }
		    $message_Perime = $message_Perime."</ul><br/><br/>";
		}
		
		if ($nbManquant > 0)
		{
		    $message_Manquant = "Alertes de quantit?? des lots:<br/><ul>";
		    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
		    while($data = $query->fetch())
		    {
		        $message_Manquant = $message_Manquant . "<li>".$data['libelleLot'] . " > " . $data['libelleSac'] . " > " . $data['libelleEmplacement'] . " > " . $data['libelleMateriel']."</li>";
		    }
		    $message_Manquant = $message_Manquant."</ul><br/><br/>";
		}
		
		if ($nbLotsNOK > 0)
		{
		    $message_Conf = "Alertes de conformit?? des lots:<br/><ul>";
		    $query = $db->query('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot WHERE alerteConfRef = 1 AND idEtat = 1;');
		    while($data = $query->fetch())
		    {
		        $message_Conf = $message_Conf . "<li>".$data['libelleLot'] . "</li>";
		    }
		    $message_Conf = $message_Conf."</ul><br/><br/>";
		}
		
		if ($nbInventaires > 0)
		{
		    $message_InventaireLots = "Alertes d'inventaires des lots:<br/><ul>";
		    $query = $db->query('SELECT * FROM LOTS_LOTS WHERE idEtat = 1 AND (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
		    while($data = $query->fetch())
		    {
		        $message_InventaireLots = $message_InventaireLots . "<li>".$data['libelleLot'] . "</li>";
		    }
		    $message_InventaireLots = $message_InventaireLots."</ul><br/><br/>";
		}
		
		if ($nbPerimeReserve > 0)
		{
		    $message_PerimeReserve = "Alertes de p??remption de la r??serve:<br/><ul>";
		    $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE peremptionNotificationReserve < CURRENT_DATE OR peremptionNotificationReserve = CURRENT_DATE;');
		    while($data = $query->fetch())
		    {
		        $message_PerimeReserve = $message_PerimeReserve . "<li>".$data['libelleConteneur'] . " > " . $data['libelleMateriel']."</li>";
		    }
		    $message_PerimeReserve = $message_PerimeReserve."</ul><br/><br/>";
		}
	    
	    if ($nbManquantReserve > 0)
		{
		    $message_ManquantReserve = "Alertes de quantit?? de la r??serve:<br/><ul>";
		    $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve;');
		    while($data = $query->fetch())
		    {
		        $message_ManquantReserve = $message_ManquantReserve . "<li>".$data['libelleConteneur'] . " > " . $data['libelleMateriel']."</li>";
		    }
		    $message_ManquantReserve = $message_ManquantReserve."</ul><br/><br/>";
		}
		
		if ($nbInventairesReserve > 0)
		{
		    $message_InventaireReserve = "Alertes d'inventaires des conteneurs de r??serve:<br/><ul>";
		    $query = $db->query('SELECT * FROM RESERVES_CONTENEUR WHERE (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
		    while($data = $query->fetch())
		    {
		        $message_InventaireReserve = $message_InventaireReserve . "<li>".$data['libelleConteneur'] . "</li>";
		    }
		    $message_InventaireReserve = $message_InventaireReserve."</ul><br/><br/>";
		}

		if ($nbDesinfections > 0)
		{
		    $message_Desinfection = "V??hicules ?? d??sinfecter:<br/><ul>";
		    $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND alerteDesinfection = 1;');
		    while($data = $query->fetch())
		    {
		        $message_Desinfection = $message_Desinfection . "<li>".$data['libelleVehicule'] . "</li>";
		    }
		    $message_Desinfection = $message_Desinfection."</ul><br/><br/>";
		}

		if ($nbHealth > 0)
		{
		    $message_Health = "V??hicules pour lesquels une tache de maintenance est en attente:<br/><ul>";
		    $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND alerteMaintenance = 1;');
		    while($data = $query->fetch())
		    {
		        $message_Health = $message_Health . "<li>".$data['libelleVehicule'] . "</li>";
		    }
		    $message_Health = $message_Health."</ul><br/><br/>";
		}
		
		if ($nbManquantTenues > 0)
		{
		    $message_TenuesManquantes = "Elements de tenue dont le stock est insuffisant:<br/><ul>";
		    $query = $db->query('SELECT * FROM TENUES_CATALOGUE WHERE stockCatalogueTenue < stockAlerteCatalogueTenue OR stockCatalogueTenue = stockAlerteCatalogueTenue;');
		    while($data = $query->fetch())
		    {
		        $message_TenuesManquantes = $message_TenuesManquantes . "<li>".$data['libelleCatalogueTenue'] . "</li>";
		    }
		    $message_TenuesManquantes = $message_TenuesManquantes."</ul><br/><br/>";
		}
		
		if ($nbRetoursTenues > 0)
		{
		    $message_TenuesRetour = "Tenues ?? r??cup??rer:<br/><ul>";
		    $query = $db->query('SELECT * FROM TENUES_AFFECTATION ta JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue LEFT OUTER JOIN PERSONNE_REFERENTE p ON ta.idPersonne = p.idPersonne WHERE dateRetour < CURRENT_DATE OR dateRetour = CURRENT_DATE;');
		    while($data = $query->fetch())
		    {
		        $message_TenuesRetour = $message_TenuesRetour . "<li>".$data['nomPersonne'] . ' ' . $data['prenomPersonne'] . $data['personneNonGPM'] . ' - ' . $data['libelleCatalogueTenue'] . "</li>";
		    }
		    $message_TenuesRetour = $message_TenuesRetour."</ul><br/><br/>";
		}





	    $query = $db->query('
	    	SELECT
	    		*
	    	FROM
	    		PERSONNE_REFERENTE
	    	WHERE
	    		notifications_abo_cejour = 1
	    		AND
		    		(notif_lots_manquants = 1
		    		OR notif_lots_peremptions = 1
		    		OR notif_lots_inventaires = 1
		    		OR notif_lots_conformites = 1
		    		OR notif_reserves_manquants = 1
		    		OR notif_reserves_peremptions = 1
		    		OR notif_reserves_inventaires = 1
		    		OR notif_vehicules_health = 1
		    		OR notif_vehicules_desinfections = 1
		    		OR notif_tenues_stock = 1
		    		OR notif_tenues_retours = 1)
	    ;');
		while($data = $query->fetch())
		{
			$nbAlertes = 0;
			$nbAlertes += ($data['notif_lots_manquants']          == 1) ? $nbManquant : 0;
			$nbAlertes += ($data['notif_lots_peremptions']        == 1) ? $nbPerime : 0;
			$nbAlertes += ($data['notif_lots_inventaires']        == 1) ? $nbInventaires : 0;
			$nbAlertes += ($data['notif_lots_conformites']        == 1) ? $nbLotsNOK : 0;
			$nbAlertes += ($data['notif_reserves_manquants']      == 1) ? $nbManquantReserve : 0;
			$nbAlertes += ($data['notif_reserves_peremptions']    == 1) ? $nbPerimeReserve : 0;
			$nbAlertes += ($data['notif_vehicules_desinfections'] == 1) ? $nbDesinfections : 0;
			$nbAlertes += ($data['notif_vehicules_health']        == 1) ? $nbHealth : 0;
			$nbAlertes += ($data['notif_reserves_inventaires']    == 1) ? $nbInventairesReserve : 0;
			$nbAlertes += ($data['notif_tenues_stock']            == 1) ? $nbManquantTenues : 0;
			$nbAlertes += ($data['notif_tenues_retours']          == 1) ? $nbRetoursTenues : 0;

			if($nbAlertes > 0)
			{
				writeInLogs($nbAlertes." alertes ?? envoyer ?? la personne r??f??rence ".$data['idPersonne']." sur l'adresse ".$data['mailPersonne'], '1', NULL);
				if ($nbAlertes == 1)
			    {
			        $sujet = "[" . $APPNAME . "] Bilan journalier - 1 alerte en cours sur votre parc materiel";
			    }
			    else
			    {
			        $sujet = "[" . $APPNAME . "] Bilan journalier - " . $nbAlertes  . " alertes en cours sur votre parc materiel";
			    }


			    $message_html = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/>Ceci est une notification journali??re d'alerte sur " . $APPNAME . ".<br/><br/>";

			    if(($data['notif_lots_manquants']) AND ($nbManquant>0))
			    {
			    	$message_html .= $message_Manquant;
			    }
			    if(($data['notif_lots_peremptions']) AND ($nbPerime>0))
			    {
			    	$message_html .= $message_Perime;
			    }
			    if(($data['notif_lots_inventaires']) AND ($nbInventaires>0))
			    {
			    	$message_html .= $message_InventaireLots;
			    }
			    if(($data['notif_lots_conformites']) AND ($nbLotsNOK>0))
			    {
			    	$message_html .= $message_Conf;
			    }
			    if(($data['notif_reserves_manquants']) AND ($nbManquantReserve>0))
			    {
			    	$message_html .= $message_ManquantReserve;
			    }
			    if(($data['notif_reserves_peremptions']) AND ($nbPerimeReserve>0))
			    {
			    	$message_html .= $message_PerimeReserve;
			    }
			    if(($data['notif_reserves_inventaires']) AND ($nbInventairesReserve>0))
			    {
			    	$message_html .= $message_InventaireReserve;
			    }
			    if(($data['notif_vehicules_desinfections']) AND ($nbDesinfections>0))
			    {
			    	$message_html .= $message_Desinfection;
			    }
			    if(($data['notif_vehicules_health']) AND ($nbHealth>0))
			    {
			    	$message_html .= $message_Health;
			    }
			    if(($data['notif_tenues_stock']) AND ($nbManquantTenues>0))
			    {
			    	$message_html .= $message_TenuesManquantes;
			    }
			    if(($data['notif_tenues_retours']) AND ($nbRetoursTenues>0))
			    {
			    	$message_html .= $message_TenuesRetour;
			    }
			    
			    $message_html = $message_html . "Cordialement<br/><br/>L'??quipe administrative de " . $APPNAME . "<br/><br/>";
			    $message.= $RETOURLIGNE.$message_html.$RETOURLIGNE;
			    $prio = 3;

			    queueMail("Notification journal??re", $data['mailPersonne'], $sujet, $prio, $message);
		        
		        unset($message_html);
		        unset($message);
		        unset($sujet);
			}
			else
			{
				writeInLogs("Aucune alerte ?? envoyer ?? la personne r??f??rence ".$data['idPersonne']." sur l'adresse ".$data['mailPersonne']." . Pas de mail g??n??r??.", '1', NULL);
			}
			
		}
	}
	else
	{
		writeInLogs("Aucun destinataire potentiel d'alertes par email, abandon du process d'alerte.", '1', NULL);
	}

	writeInLogs("Fin du traitement automatiques des alertes par mail.", '1', NULL);
}

function sendQueueMail()
{
    global $db;

	require_once 'config.php';
	require_once 'mailFunction.php';

    global $MAILISSMTP;

    $queue = $db->query('
        SELECT
            *
        FROM
            NOTIFICATIONS_MAILS
        WHERE
            dateEnvoiMail IS NULL
            AND
            nombreRetry < 4
    ;');
    while($queueItem = $queue->fetch())
    {
        if($MAILISSMTP)
        {
            $result = sendMailSMTP($queueItem['adresseDest'], $queueItem['sujet'], $queueItem['niveau'], $queueItem['contenu']);
        }
        else
        {
            $result = sendMailPHP($queueItem['adresseDest'], $queueItem['sujet'], $queueItem['niveau'], $queueItem['contenu']);
        }

        if($result)
        {
            $ok = $db->prepare('UPDATE NOTIFICATIONS_MAILS SET dateEnvoiMail = CURRENT_TIMESTAMP WHERE idMail = :idMail');
            $ok->execute(array('idMail' => $queueItem['idMail']));
            writeInLogs("Mail ".$queueItem['idMail']." envoy?? avec succ??s", '1', NULL);
        }
        else
        {
            $ok = $db->prepare('UPDATE NOTIFICATIONS_MAILS SET nombreRetry = nombreRetry + 1 WHERE idMail = :idMail');
            $ok->execute(array('idMail' => $queueItem['idMail']));
            writeInLogs("Echec de l'envoi du mail ".$queueItem['idMail'], '3', NULL);
        }
    }
}

function queueMail($contexte, $adresseDest, $sujet, $niveau, $contenu)
{
    global $db;

    $queue = $db->prepare('
        INSERT INTO
            NOTIFICATIONS_MAILS
        SET
            contexte        = :contexte,
            adresseDest     = :adresseDest,
            sujet           = :sujet,
            niveau          = :niveau,
            contenu         = :contenu,
            dateEntreeQueue = CURRENT_TIMESTAMP,
            nombreRetry     = 0
    ;');
    $queue->execute(array(
        'contexte'    => $contexte,
        'adresseDest' => $adresseDest,
        'sujet'       => $sujet,
        'niveau'      => $niveau,
        'contenu'     => $contenu,
    ));
}

?>