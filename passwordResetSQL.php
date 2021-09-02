<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($RESETPASSWORD == 0)
{ 
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    exit;
}

$query = $db->prepare('SELECT * FROM VERROUILLAGE_IP WHERE adresseIPverr= :adresseIPverr;');
$query->execute(array(
    'adresseIPverr' => $_SERVER['REMOTE_ADDR']
));
$data = $query->fetch();

if ($data['idIP'] == "")
{
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS v ON p.idPersonne = v.idPersonne WHERE p.identifiant = :identifiant AND p.mailPersonne = :mailPersonne;');
	$query->execute(array(
	    'identifiant' => $_POST['identifiant'],
	    'mailPersonne' => $_POST['mailPersonne']
	));
	$data = $query->fetch();
	
	if (empty($data['idPersonne']) OR $data['idPersonne'] == "")
	{
	    //pas bon
	    $query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
	    $query->execute(array(
	        'dateEvt' => date('Y-m-d H:i:s'),
	        'adresseIP' => $_SERVER['REMOTE_ADDR'],
	        'utilisateurEvt' => $_POST['identifiant'],
	        'idLogLevel' => '5',
	        'detailEvt' => 'Echec de la tentative de reset du mot de passe oublié.'
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
	    
	    echo "<script type='text/javascript'>document.location.replace('passwordResetKO.php');</script>";
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
        $query = $db->prepare('DELETE FROM RESETPASSWORD WHERE idPersonne = :idPersonne;');
        $query->execute(array(
            'idPersonne' => $data['idPersonne']
        ));
		
		$query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
	    $query->execute(array(
	        'dateEvt' => date('Y-m-d H:i:s'),
	        'adresseIP' => $_SERVER['REMOTE_ADDR'],
	        'utilisateurEvt' => $_POST['identifiant'],
	        'idLogLevel' => '2',
	        'detailEvt' => 'Génération d\'un token de réinitialisation du mot de passe'
	    ));

		$token = bin2hex(random_bytes(30));
		$tokenHash = password_hash($token, PASSWORD_DEFAULT);

		$query = $db->prepare('INSERT INTO RESETPASSWORD SET
			idPersonne = :idPersonne,
			dateDemande = :dateDemande,
			tokenReset = :tokenReset,
			dateValidite = :dateValidite;');
	    $query->execute(array(
	    	'idPersonne' => $data['idPersonne'],
			'dateDemande' => date('Y-m-d H:i:s'),
			'tokenReset' => $tokenHash,
			'dateValidite' => date('Y-m-d H:i:s', strtotime('+1 hour'))
	    ));
	    $resetSession = $db->query('SELECT MAX(idReset) as idReset FROM RESETPASSWORD;');
	    $resetSession=$resetSession->fetch();

	    $lienReset = $URLSITE."/passwordResetResult.php?idReset=".$resetSession['idReset']."&token=".$token;
	    $lienKill = $URLSITE."/passwordResetKill.php?idReset=".$resetSession['idReset']."&token=".$token;

	    $sujet = "[" . $APPNAME . "] Réinitialisation de votre mot de passe.";
	    $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Vous venez de faire une demande de réinitialisation de mot de passe oublié.<br/><br/>Si cette demande provient bien de vous, cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe:<br/>".$lienReset."<br/><br/>Si cette demande vous semble fauduleuse, cliquez sur le lien ci-dessous pour la neutraliser:<br/>".$lienKill;
        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
        if(sendmail($_POST['mailPersonne'], $sujet, 2, $message))
        {
            $query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
		    $query->execute(array(
		        'dateEvt' => date('Y-m-d H:i:s'),
		        'adresseIP' => $_SERVER['REMOTE_ADDR'],
		        'utilisateurEvt' => $_POST['identifiant'],
		        'idLogLevel' => '2',
		        'detailEvt' => "Mail de réinitialisation de mot de passe envoyé à " . $_POST['mailPersonne'] . " pour le compte référence ".$data['idPersonne']
		    ));
        }
        else
        {
            $query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
		    $query->execute(array(
		        'dateEvt' => date('Y-m-d H:i:s'),
		        'adresseIP' => $_SERVER['REMOTE_ADDR'],
		        'utilisateurEvt' => $_POST['identifiant'],
		        'idLogLevel' => '2',
		        'detailEvt' => "Erreur lors de l'envoi du mail de réinitialisation du mot de passe à " . $_POST['mailPersonne'] . " pour le compte référence ".$data['idPersonne']
		    ));
        }

		echo "<script type='text/javascript'>document.location.replace('passwordResetOK.php');</script>";

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
	        'detailEvt' => 'Reset de mot de passe oublié refusé par le filtrage IP.'
	    ));
	    echo "<script type='text/javascript'>document.location.replace('login.php');</script>";
}

?>