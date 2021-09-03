<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['annuaire_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query = $db->query('SELECT * FROM PERSONNE_REFERENTE_TEMP;');
    while($data = $query->fetch())
    {
    	$query2 = $db->prepare('
    		INSERT INTO
    			PERSONNE_REFERENTE
    		SET
				identifiant                         = :identifiant,
				motDePasse                          = :motDePasse,
				nomPersonne                         = :nomPersonne,
				prenomPersonne                      = :prenomPersonne,
				mailPersonne                        = :mailPersonne,
				telPersonne                         = :telPersonne,
				fonction                            = :fonction,
				notif_lots_manquants                = 1,
				notif_lots_peremptions              = 1,
				notif_lots_inventaires              = 1,
				notif_lots_conformites              = 1,
				notif_reserves_manquants            = 1,
				notif_reserves_peremptions          = 1,
				notif_reserves_inventaires          = 1,
				notif_vehicules_assurances          = 1,
				notif_vehicules_revisions           = 1,
				notif_vehicules_desinfections       = 1,
				notif_vehicules_ct                  = 1,
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
				conf_indicateur6Accueil             = 1 ,
				conf_indicateur7Accueil             = 1 ,
				conf_indicateur8Accueil             = 1,
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
				isActiveDirectory                   = false
			;');
	    $query2->execute(array(
	        'identifiant'    => $data['identifiant'],
	        'motDePasse'     => password_hash($data['identifiant'], PASSWORD_DEFAULT),
	        'nomPersonne'    => $data['nomPersonne'],
	        'prenomPersonne' => $data['prenomPersonne'],
	        'mailPersonne'   => $data['mailPersonne'],
	        'telPersonne'    => $data['telPersonne'],
	        'fonction'       => $data['fonction']
	    ));

	    switch($query2->errorCode())
	    {
	        case '00000':
	            writeInLogs("Ajout de l'utilisateur " . $data['identifiant'] . " via l'import de masse.", '1', NULL);
				
				$query2 = $db->query('SELECT MAX(idPersonne) as idPersonne FROM PERSONNE_REFERENTE;');
	            $data2 = $query2->fetch();
	            
	            $notifications_abonnements = $db->prepare('INSERT INTO NOTIFICATIONS_ABONNEMENTS SET idPersonne = :idPersonne, idCondition = 1;');
	            $notifications_abonnements->execute(array(
	            	'idPersonne' => $data2['idPersonne'],
	            ));
	
	            if (!empty($_POST['idProfil'])) {
	                $insertSQL = 'INSERT INTO PROFILS_PERSONNES (idProfil, idPersonne) VALUES';
	                foreach ($_POST['idProfil'] as $idProfil) {
	                    $insertSQL .= ' ('. (int)$idProfil.', '. (int)$data2['idPersonne'] .'),';
	                }
	
	                $insertSQL = substr($insertSQL, 0, -1);
	
	                $db->query($insertSQL);
	            }
				
	            if ($data['mailCreation']==1)
			    {
			        $sujet = "[" . $APPNAME . "] Bienvenue sur " . $APPNAME;
			        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Votre session a été créée. Voici vos identifiants:<br/>Nom d'utilisateur: " . $data['identifiant'] . "<br/>Mot de passe: ". $data['identifiant'];
			        $message = $message . "<br/><br/>Vous serez invité(e) à changer votre mot de passe à votre première connexion.<br/>Voici le lien d'accès à l'outil: " . $URLSITE;
			        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

			        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
			        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
			        {
			            writeInLogs("Mail d'accueil envoyé à " . $data['identifiant'], '1', NULL);
			        }
			        else
			        {
			            writeInLogs("Erreur lors de l'envoi du mail d'accueil à " . $data['identifiant'], '1', NULL);
			        }
			    }

			    $query2 = $db->query('SELECT MAX(idPersonne) as idPersonne FROM PERSONNE_REFERENTE;');
			    $data2 = $query2->fetch();
			    majIndicateursPersonne($data2['idPersonne'],1);
			    majNotificationsPersonne($data2['idPersonne'],1);
	        break;

	        case '23000':
	            writeInLogs("Doublon détecté lors de l'ajout de l'utilisateur " . $data['identifiant'] . " via l'import de masse.", '2', NULL);
	        break;

	        default:
	            writeInLogs("Erreur inconnue lors de l'ajout de l'utilisateur " . $data['identifiant'] . " via l'import de masse.", '3', NULL);
	    }

	    
    }

	$_SESSION['importStade'] = 3;

	$_SESSION['returnMessage'] = 'Import effectué. Reportez vous aux logs de la base pour connaitre les détails de l\'importation.';
    $_SESSION['returnType'] = '1';
	
    echo "<script type='text/javascript'>document.location.replace('annuaireImportReset.php');</script>";

}
?>