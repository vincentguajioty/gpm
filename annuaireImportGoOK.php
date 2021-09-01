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
    	$query2 = $db->prepare('INSERT INTO PERSONNE_REFERENTE(
                                                identifiant,
                                                motDePasse,
                                                nomPersonne,
                                                prenomPersonne,
                                                mailPersonne,
                                                telPersonne,
                                                fonction,
                                                notif_lots_manquants,
												notif_lots_peremptions,
												notif_lots_inventaires,
												notif_lots_conformites,
												notif_reserves_manquants,
												notif_reserves_peremptions,
												notif_reserves_inventaires,
												notif_vehicules_assurances,
												notif_vehicules_revisions,
												notif_vehicules_ct,
												notif_tenues_stock,
												notif_tenues_retours,
                                                conf_indicateur1Accueil,
                                                conf_indicateur2Accueil,
                                                conf_indicateur3Accueil,
                                                conf_indicateur4Accueil,
                                                conf_indicateur5Accueil,
                                                conf_indicateur6Accueil ,
                                                conf_indicateur7Accueil ,
                                                conf_indicateur8Accueil,
                                                conf_indicateur9Accueil,
                                                conf_indicateur10Accueil,
                                                conf_accueilRefresh,
                                                tableRowPerso) VALUES(:identifiant,
                                                :motDePasse,
                                                :nomPersonne,
                                                :prenomPersonne,
                                                :mailPersonne,
                                                :telPersonne,
                                                :fonction,
                                                1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                                                1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                                                120,
                                                25);'
	                        );
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
	            writeInLogs("Ajout de l'utilisateur " . $data['identifiant'] . " via l'import de masse.", '2');
				
				$query2 = $db->query('SELECT MAX(idPersonne) as idPersonne FROM PERSONNE_REFERENTE;');
	            $data2 = $query2->fetch();
	
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
			            writeInLogs("Mail d'accueil envoyé à " . $data['identifiant'], '2');
			        }
			        else
			        {
			            writeInLogs("Erreur lors de l'envoi du mail d'accueil à " . $data['identifiant'], '5');
			        }
			    }

			    $query2 = $db->query('SELECT MAX(idPersonne) as idPersonne FROM PERSONNE_REFERENTE;');
			    $data2 = $query2->fetch();
			    majIndicateursPersonne($data2['idPersonne'],1);
			    majNotificationsPersonne($data2['idPersonne'],1);
	        break;

	        case '23000':
	            writeInLogs("Doublon détecté lors de l'ajout de l'utilisateur " . $data['identifiant'] . " via l'import de masse.", '5');
	        break;

	        default:
	            writeInLogs("Erreur inconnue lors de l'ajout de l'utilisateur " . $data['identifiant'] . " via l'import de masse.", '5');
	    }

	    
    }

	$_SESSION['importStade'] = 3;

	$_SESSION['returnMessage'] = 'Import effectué. Reportez vous aux logs de la base pour connaitre les détails de l\'importation.';
    $_SESSION['returnType'] = '1';
	
    echo "<script type='text/javascript'>document.location.replace('annuaireImportReset.php');</script>";

}
?>