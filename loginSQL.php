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
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS a ON p.idProfil = a.idProfil WHERE p.identifiant= :identifiant;');
	$query->execute(array(
	    'identifiant' => $_POST['identifiant']
	));
	$data = $query->fetch();
	
	if ($data['idPersonne'] == "" OR  !(password_verify($_POST['motDePasse'], $data['motDePasse'])))
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

	    $_SESSION['idPersonne'] = $data['idPersonne'];
	    $_SESSION['idProfil'] = $data['idProfil'];
	    $_SESSION['libelleProfil'] = $data['libelleProfil'];
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
	    
	    $_SESSION['LAST_ACTIVITY'] = time();
	    $_SESSION['derniereConnexion'] = $data['derniereConnexion'];
	    
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