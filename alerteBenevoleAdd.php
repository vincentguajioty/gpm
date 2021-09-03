<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';
require_once 'verrouIPcheck.php';

function alerteBenevoleMail($idAlerteLot, $idAlerteVehicule)
{
    global $db;
    global $RETOURLIGNE;
    global $APPNAME;

    if($idAlerteLot > 0)
    {
        $alerte = $db->prepare('
            SELECT
                a.*,
                l.libelleLot,
                COUNT(p.identifiant) as nbNotif
            FROM
                LOTS_ALERTES a
                LEFT OUTER JOIN LOTS_LOTS l ON a.idLot = l.idLot
                LEFT JOIN PERSONNE_REFERENTE p ON 1=1
            WHERE 
                a.idAlerte = :idAlerte
                AND
                p.notif_benevoles_lots = 1
            GROUP BY
                a.idAlerte
            ;
        ');
        $alerte->execute(array('idAlerte' => $idAlerteLot));
        $alerte = $alerte->fetch();

        if($alerte['nbNotif']>0)
        {
            writeInLogs($alerte['nbNotif']." notifications lots d'alerte à envoyer.", '1', NULL);

            $query = $db->query('
                SELECT
                    *
                FROM
                    PERSONNE_REFERENTE
                WHERE
                    notif_benevoles_lots = 1
            ;');
            while($data = $query->fetch())
            {
                $sujet = "[" . $APPNAME . "] Alerte bénévole sur un lot";

                $message_html = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/>Un membre de la structure a remonté une alerte lot via l'interface publique de " . $APPNAME . ". Voici le détail de son message:<br/><br/>";
                $message_html .= "Déclarant: ".$alerte['nomDeclarant']." (".$alerte['mailDeclarant'].")<br/>";
                $message_html .= "Date: ".$alerte['dateCreationAlerte']."<br/>";
                $message_html .= "Lot: ".$alerte['libelleLot']."<br/>";
                $message_html .= "Message:<br/>".nl2br($alerte['messageAlerteLot'])."<br/><br/>";

                $message_html .= "Veuillez vous connecter sur ".$APPNAME." afin d'y traiter l'alerte.<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME . "<br/>";

                $message.= $RETOURLIGNE.$message_html.$RETOURLIGNE;
                $prio = 3;

                queueMail("Alerte bénévole lots", $data['mailPersonne'], $sujet, $prio, $message);
                
                unset($message_html);
                unset($message);
                unset($sujet);
            }
        }
        else
        {
            writeInLogs("Aucune notification d'alerte à envoyer.", '1', NULL);
        }

    }

    if($idAlerteVehicule > 0)
    {
        $alerte = $db->prepare('
            SELECT
                a.*,
                v.libelleVehicule,
                COUNT(p.identifiant) as nbNotif
            FROM
                VEHICULES_ALERTES a
                LEFT OUTER JOIN VEHICULES v ON a.idVehicule = v.idVehicule
                LEFT JOIN PERSONNE_REFERENTE p ON 1=1
            WHERE 
                a.idAlerte = :idAlerte
                AND
                p.notif_benevoles_vehicules = 1
            GROUP BY
                a.idAlerte
            ;
        ');
        $alerte->execute(array('idAlerte' => $idAlerteVehicule));
        $alerte = $alerte->fetch();

        if($alerte['nbNotif']>0)
        {
            writeInLogs($alerte['nbNotif']." notifications vehicules d'alerte à envoyer.", '1', NULL);

            $query = $db->query('
                SELECT
                    *
                FROM
                    PERSONNE_REFERENTE
                WHERE
                    notif_benevoles_vehicules = 1
            ;');
            while($data = $query->fetch())
            {
                $sujet = "[" . $APPNAME . "] Alerte bénévole sur un vehicule";

                $message_html = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/>Un membre de la structure a remonté une alerte véhicule via l'interface publique de " . $APPNAME . ". Voici le détail de son message:<br/><br/>";
                $message_html .= "Déclarant: ".$alerte['nomDeclarant']." (".$alerte['mailDeclarant'].")<br/>";
                $message_html .= "Date: ".$alerte['dateCreationAlerte']."<br/>";
                $message_html .= "Lot: ".$alerte['libelleVehicule']."<br/>";
                $message_html .= "Message:<br/>".nl2br($alerte['messageAlerteVehicule'])."<br/><br/>";

                $message_html .= "Veuillez vous connecter sur ".$APPNAME." afin d'y traiter l'alerte.<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME . "<br/>";

                $message.= $RETOURLIGNE.$message_html.$RETOURLIGNE;
                $prio = 3;

                queueMail("Alerte bénévole véhicules", $data['mailPersonne'], $sujet, $prio, $message);
                
                unset($message_html);
                unset($message);
                unset($sujet);
            }
        }
        else
        {
            writeInLogs("Aucune notification d'alerte à envoyer.", '1', NULL);
        }
    }
}

if (checkIP($_SERVER['REMOTE_ADDR'])==1)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

if ($MAINTENANCE)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

if ($ALERTES_BENEVOLES_LOTS==0 AND $ALERTES_BENEVOLES_VEHICULES==0)
{
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
}
else
{
	
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
			echo "<script type='text/javascript'>document.location.replace('alerteBenevoleFailure.php');</script>";
			exit;
		}
	}
	
	
	$_POST['nomDeclarant']          = str_replace($XSS_SECURITY, "", $_POST['nomDeclarant']);
	$_POST['mailDeclarant']         = str_replace($XSS_SECURITY, "", $_POST['mailDeclarant']);
	$_POST['ipDeclarant']           = str_replace($XSS_SECURITY, "", $_POST['ipDeclarant']);
	$_POST['idLot']                 = str_replace($XSS_SECURITY, "", $_POST['idLot']);
	$_POST['messageAlerteLot']      = str_replace($XSS_SECURITY, "", $_POST['messageAlerteLot']);
	$_POST['idVehicule']            = str_replace($XSS_SECURITY, "", $_POST['idVehicule']);
	$_POST['messageAlerteVehicule'] = str_replace($XSS_SECURITY, "", $_POST['messageAlerteVehicule']);

	$_POST['ipDeclarant']           = $_SERVER['REMOTE_ADDR'];

    if($ALERTES_BENEVOLES_LOTS)
    {
    	$_POST['idLot'] = ($_POST['idLot'] == Null) ? Null : $_POST['idLot'];
    	if($_POST['idLot'] > 0 OR $_POST['messageAlerteLot'] != "")
    	{
    		$query = $db->prepare('
		        INSERT INTO
		            LOTS_ALERTES
		        SET
					idLotsAlertesEtat       = 1,
					dateCreationAlerte      = CURRENT_TIMESTAMP(),
					nomDeclarant            = :nomDeclarant,
					mailDeclarant           = :mailDeclarant,
					ipDeclarant             = :ipDeclarant,
					idLot                   = :idLot,
					messageAlerteLot        = :messageAlerteLot
		        ;');
		    $query->execute(array(
				'nomDeclarant'      => $_POST['nomDeclarant'],
				'mailDeclarant'     => $_POST['mailDeclarant'],
				'ipDeclarant'       => $_POST['ipDeclarant'],
				'idLot'             => $_POST['idLot'],
				'messageAlerteLot'  => $_POST['messageAlerteLot'],
			));
			switch($query->errorCode())
			{
			    case '00000':
			        writeInLogs("Ajout d'une alerte bénévoles sur les lots au nom de ".$_POST['nomDeclarant']." depuis l'IP ".$_POST['ipDeclarant'], '1', NULL);

			        $alerte = $db->query('SELECT MAX(idAlerte) as idAlerte FROM LOTS_ALERTES;');
			        $alerte = $alerte->fetch();
			        alerteBenevoleMail($alerte['idAlerte'], Null);
			        
			        break;

			    default:
			        writeInLogs("Erreur inconnue lors de l'ajout d'une alerte bénévoles sur les lots au nom de ".$_POST['nomDeclarant']." depuis l'IP ".$_POST['ipDeclarant'], '3', NULL);
			}
    	}
    }

    if($ALERTES_BENEVOLES_VEHICULES)
    {
    	$_POST['idVehicule'] = ($_POST['idVehicule'] == Null) ? Null : $_POST['idVehicule'];
    	if($_POST['idVehicule'] > 0 OR $_POST['messageAlerteVehicule'] != "")
    	{
    		$query = $db->prepare('
		        INSERT INTO
		            VEHICULES_ALERTES
		        SET
					idVehiculesAlertesEtat = 1,
					dateCreationAlerte     = CURRENT_TIMESTAMP(),
					nomDeclarant           = :nomDeclarant,
					mailDeclarant          = :mailDeclarant,
					ipDeclarant            = :ipDeclarant,
					idVehicule             = :idVehicule,
					messageAlerteVehicule  = :messageAlerteVehicule
		        ;');
		    $query->execute(array(
				'nomDeclarant'          => $_POST['nomDeclarant'],
				'mailDeclarant'         => $_POST['mailDeclarant'],
				'ipDeclarant'           => $_POST['ipDeclarant'],
				'idVehicule'            => $_POST['idVehicule'],
				'messageAlerteVehicule' => $_POST['messageAlerteVehicule'],
			));
			switch($query->errorCode())
			{
			    case '00000':
			        writeInLogs("Ajout d'une alerte bénévoles sur les véhicules au nom de ".$_POST['nomDeclarant']." depuis l'IP ".$_POST['ipDeclarant'], '1', NULL);

			        $alerte = $db->query('SELECT MAX(idAlerte) as idAlerte FROM VEHICULES_ALERTES;');
			        $alerte = $alerte->fetch();
			        alerteBenevoleMail(Null, $alerte['idAlerte']);
			        
			        break;

			    default:
			        writeInLogs("Erreur inconnue lors de l'ajout d'une alerte bénévoles sur les véhicules au nom de ".$_POST['nomDeclarant']." depuis l'IP ".$_POST['ipDeclarant'], '3', NULL);
			}
    	}
    }

    
    echo "<script type='text/javascript'>document.location.replace('alerteBenevoleSuccess.php');</script>";
    
}
?>