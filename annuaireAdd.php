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

    $query = $db->prepare('
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
			isActiveDirectory                   = false
        ;');
    $query->execute(array(
        'identifiant'    => $_POST['identifiant'],
        'motDePasse'     => password_hash($SELPRE.$_POST['identifiant'].$SELPOST, PASSWORD_DEFAULT),
        'nomPersonne'    => $_POST['nomPersonne'],
        'prenomPersonne' => $_POST['prenomPersonne'],
        'mailPersonne'   => $_POST['mailPersonne'],
        'telPersonne'    => $_POST['telPersonne'],
        'fonction'       => $_POST['fonction']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'utilisateur " . $_POST['identifiant'], '1', NULL);
            $_SESSION['returnMessage'] = 'Utilisateur ajout?? avec succ??s.';
            $_SESSION['returnType'] = '1';

            $query = $db->query('SELECT MAX(idPersonne) as idPersonne FROM PERSONNE_REFERENTE;');
            $data = $query->fetch();
            
            $notifications_abonnements = $db->prepare('INSERT INTO NOTIFICATIONS_ABONNEMENTS SET idPersonne = :idPersonne, idCondition = 1;');
            $notifications_abonnements->execute(array(
            	'idPersonne' => $data['idPersonne'],
            ));

            if (!empty($_POST['idProfil'])) {
                $insertSQL = 'INSERT INTO PROFILS_PERSONNES (idProfil, idPersonne) VALUES';
                foreach ($_POST['idProfil'] as $idProfil) {
                    $insertSQL .= ' ('. (int)$idProfil.', '. (int)$data['idPersonne'] .'),';
                }

                $insertSQL = substr($insertSQL, 0, -1);

                $db->query($insertSQL);
            }
            
            if ($_POST['notificationMailCreation']==1)
		    {
		        $sujet = "[" . $APPNAME . "] Bienvenue sur " . $APPNAME;
		        $message = "Bonjour " . $_POST['prenomPersonne'] . ", <br/><br/> Votre session a ??t?? cr????e. Voici vos identifiants:<br/>Nom d'utilisateur: " . $_POST['identifiant'] . "<br/>Mot de passe: ". $_POST['identifiant'];
		        $message = $message . "<br/><br/>Vous serez invit??(e) ?? changer votre mot de passe ?? votre premi??re connexion.<br/>Voici le lien d'acc??s ?? l'outil: " . $URLSITE;
		        $message = $message . "<br/><br/>Cordialement<br/><br/>L'??quipe administrative de " . $APPNAME;
		
		        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
		        queueMail("Cr??ation compte utilisateur", $_POST['mailPersonne'], $sujet, 2, $message);
		    }
		
		    $query = $db->query('SELECT MAX(idPersonne) as idPersonne FROM PERSONNE_REFERENTE;');
		    $data = $query->fetch();
		    majIndicateursPersonne($data['idPersonne'],1);
		    majNotificationsPersonne($data['idPersonne'],1);
		    
		    echo "<script type='text/javascript'>document.location.replace('annuaireContenu.php?id=" . $data['idPersonne'] . "');</script>";
		    
        break;

        case '23000':
            writeInLogs("Doublon d??tect?? lors de l'ajout de l'utilisateur " . $_POST['identifiant'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un utilisateur existe d??j?? avec le m??me identifiant. Merci de changer l\'identifiant.';
            $_SESSION['returnType'] = '2';
            echo "<script>window.location = document.referrer;</script>";
        break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'utilisateur " . $_POST['identifiant'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de l\'ajout de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
            echo "<script>window.location = document.referrer;</script>";
    }


}
?>