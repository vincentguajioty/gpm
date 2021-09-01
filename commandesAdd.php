<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['commande_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $_POST['idCentreDeCout'] = ($_POST['idCentreDeCout'] == Null) ? Null : $_POST['idCentreDeCout'];
    $_POST['idLieuLivraison'] = ($_POST['idLieuLivraison'] == Null) ? Null : $_POST['idLieuLivraison'];
    $_POST['idFournisseur'] = ($_POST['idFournisseur'] == Null) ? Null : $_POST['idFournisseur'];


    $query = $db->prepare('INSERT INTO COMMANDES(idEtat, idCentreDeCout, idFournisseur, idLieuLivraison, dateCreation, remarquesGenerales) VALUES(1, :idCentreDeCout, :idFournisseur, :idLieuLivraison, CURRENT_TIMESTAMP, :remarquesGenerales);');
    $query->execute(array(
        'idCentreDeCout' => $_POST['idCentreDeCout'],
        'idFournisseur' => $_POST['idFournisseur'],
        'idLieuLivraison' => $_POST['idLieuLivraison'],
        'remarquesGenerales' => $_POST['remarquesGenerales']
    ));
    
    switch($query->errorCode())
    {
        case '00000':
            $query = $db->query('SELECT MAX(idCommande) as idCommande FROM COMMANDES;');
    		$data = $query ->fetch();
    		echo "<script type='text/javascript'>document.location.replace('commandeView.php?id=" . $data['idCommande'] . "');</script>";
    		
            writeInLogs("Ajout de la commande " . $data['idCommande'], '2');
            addCommandeComment($data['idCommande'], $_SESSION['identifiant'] . " crée la commande.", "1");
            
            if (!empty($_POST['idDemandeur'])) {
                $insertSQL = 'INSERT INTO COMMANDES_DEMANDEURS (idDemandeur, idCommande) VALUES';
                foreach ($_POST['idDemandeur'] as $idDemandeur) {
                    $insertSQL .= ' ('. (int)$idDemandeur.', '. (int)$data['idCommande'] .'),';
                }

                $insertSQL = substr($insertSQL, 0, -1);

                $db->query($insertSQL);
            }
            if (!empty($_POST['idValideur'])) {
                $insertSQL = 'INSERT INTO COMMANDES_VALIDEURS (idValideur, idCommande) VALUES';
                foreach ($_POST['idValideur'] as $idValideur) {
                    $insertSQL .= ' ('. (int)$idValideur.', '. (int)$data['idCommande'] .'),';
                }

                $insertSQL = substr($insertSQL, 0, -1);

                $db->query($insertSQL);
            }
            if (!empty($_POST['idObservateur'])) {
                $insertSQL = 'INSERT INTO COMMANDES_OBSERVATEURS (idObservateur, idCommande) VALUES';
                foreach ($_POST['idObservateur'] as $idObservateur) {
                    $insertSQL .= ' ('. (int)$idObservateur.', '. (int)$data['idCommande'] .'),';
                }

                $insertSQL = substr($insertSQL, 0, -1);

                $db->query($insertSQL);
            }
            if (!empty($_POST['idAffectee'])) {
                $insertSQL = 'INSERT INTO COMMANDES_AFFECTEES (idAffectee, idCommande) VALUES';
                foreach ($_POST['idAffectee'] as $idAffectee) {
                    $insertSQL .= ' ('. (int)$idAffectee.', '. (int)$data['idCommande'] .'),';
                }

                $insertSQL = substr($insertSQL, 0, -1);

                $db->query($insertSQL);
            }

            $COMMANDE=$data['idCommande'];
            
            if($config['notifications_commandes_demandeur_creation']==1)
		    {
		        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
				$query->execute(array('idCommande'=>$COMMANDE));
				while($data = $query->fetch())
				{
			        $sujet = "[" . $APPNAME . "] Nouvelle commande créée par " . $_SESSION['identifiant'];
			        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Nous vous informons qu'une nouvelle commande a été créée en votre nom.";
			        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
			        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
			        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
			        {
			            writeInLogs("Mail de création de commande envoyé au demandeur.", '2');
			        }
			        else
			        {
			            writeInLogs("Erreur lors de l'envoi du mail de création de commande au demandeur.", '5');
			        }
		        }
		    }
		    if($config['notifications_commandes_valideur_creation']==1)
		    {
		        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_VALIDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idValideur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
				$query->execute(array('idCommande'=>$COMMANDE));
				while($data = $query->fetch())
				{
			        $sujet = "[" . $APPNAME . "] Nouvelle commande créée par " . $_SESSION['identifiant'];
			        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Nous vous informons qu'une nouvelle commande a été créée et qu'elle vous a été affectée en tant que valideur. La commande n'est pas prète pour validation actuellement. Un email vous sera envoyé lorsque ce sera le cas.";
			        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
			        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
			        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
			        {
			            writeInLogs("Mail de création de commande envoyé au valideur.", '2');
			        }
			        else
			        {
			            writeInLogs("Erreur lors de l'envoi du mail de création de commande au valideur.", '5');
			        }
			    }
	    	}
		    if($config['notifications_commandes_affectee_creation']==1)
		    {
		        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
				$query->execute(array('idCommande'=>$COMMANDE));
				while($data = $query->fetch())
				{
			        $sujet = "[" . $APPNAME . "] Nouvelle commande créée par " . $_SESSION['identifiant'];
			        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Nous vous informons qu'une nouvelle commande a été créée et qu'elle vous a été affectée.";
			        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
			        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
			        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
			        {
			            writeInLogs("Mail de création de commande envoyé au gérant.", '2');
			        }
			        else
			        {
			            writeInLogs("Erreur lors de l'envoi du mail de création de commande au gérant.", '5');
			        }
			    }
	   		}
		    if($config['notifications_commandes_observateur_creation']==1)
		    {
		        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
				$query->execute(array('idCommande'=>$COMMANDE));
				while($data = $query->fetch())
				{
			        $sujet = "[" . $APPNAME . "] Nouvelle commande créée par " . $_SESSION['identifiant'];
			        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Nous vous informons qu'une nouvelle commande a été créée et que vous avez été affecté en tant qu'observateur.";
			        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
			        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
			        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
			        {
			            writeInLogs("Mail de création de commande envoyé à l'observateur.", '2');
			        }
			        else
			        {
			            writeInLogs("Erreur lors de l'envoi du mail de création de commande à l'observateur.", '5');
			        }
			    }
	    	}
    
            break;

        default:
            writeInLogs("Erreur inconnue lors de la création de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la création de la commande.";
            $_SESSION['returnType'] = '2';
            echo "<script>javascript:history.go(-2);</script>";
    }

    

}
?>