<?php
session_start();
require_once 'bdd.php';
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(is_dir('plugins'))
{
    require 'plugins/phpmailer/src/Exception.php';
    require 'plugins/phpmailer/src/PHPMailer.php';
    require 'plugins/phpmailer/src/SMTP.php';
}
else
{
    if(is_dir('../plugins'))
    {
        require '../plugins/phpmailer/src/Exception.php';
        require '../plugins/phpmailer/src/PHPMailer.php';
        require '../plugins/phpmailer/src/SMTP.php';
    }
    else
    {
        chdir(dirname(__FILE__));
        if(is_dir('plugins'))
        {
            require 'plugins/phpmailer/src/Exception.php';
            require 'plugins/phpmailer/src/PHPMailer.php';
            require 'plugins/phpmailer/src/SMTP.php';
        }
        else
        {
            require '../plugins/phpmailer/src/Exception.php';
            require '../plugins/phpmailer/src/PHPMailer.php';
            require '../plugins/phpmailer/src/SMTP.php';
        }
    }
}

$RETOURLIGNE = "\r\n";

function sendMailSMTP($adresseDest, $sujet, $niveau, $contenu)
{
    global $db;

    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query->fetch();

    writeInLogs("Chargement sendMailSMTP", '1', NULL);

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->Host = $config['SMTPhost']; 
    $mail->Port = $config['SMTPport'];

    if($config['SMTPssl']){$mail->SMTPSecure = 'ssl';}
    if($config['SMTPtls']){$mail->SMTPSecure = 'tls';}

    if($config['SMTPauth'])
    {
        $mail->SMTPAuth = true;
        $mail->Username = $config['SMTPuser'];
        $mail->Password = $config['SMTPpwd'];
    }

    $mail->setFrom($config['mailserver'], $config['appname']);

    $mail->addAddress($adresseDest);

    if($config['mailcopy'] == 1)
    {
        $mail->addCC($config['mailserver']);
    }

    $mail->Subject = $sujet;
    $mail->msgHTML($contenu);
    $mail->AltBody = $contenu;

    return $mail->send();
}

function sendMailPHP($adresseDest, $sujet, $niveau, $contenu)
{
    global $APPNAME;
    global $MAILSERVER;
    global $MAILCOPY;

    writeInLogs("Chargement sendMailPHP", '1', NULL);
    
    $RETOURLIGNE = "\r\n";

    $replyTo = ($_SESSION['mailPersonne'] != '') ? $_SESSION['mailPersonne'] : $MAILSERVER;

    $boundary = "-----=".md5(rand());
    $header = "From: \"" . $APPNAME . "\"<" . $MAILSERVER . ">".$RETOURLIGNE;
    $header.= "Reply-to: ".$replyTo.$RETOURLIGNE;
    $header.= "MIME-Version: 1.0".$RETOURLIGNE;
    
    if($MAILCOPY == 1)
    {
        $header.= "Cc: ".$MAILSERVER.$RETOURLIGNE;
    }
    
    if ($niveau == 1)
    {
        $header .= "X-Priority: 5".$RETOURLIGNE;
    }
    else
    {
        if ($niveau == 2)
        {
            $header .= "X-Priority: 3".$RETOURLIGNE;
        }
        else
        {
            $header .= "X-Priority: 1".$RETOURLIGNE;
        }
    }

    $header.= "Content-Type: text/html; charset=UTF-8;".$RETOURLIGNE." boundary=\"$boundary\"".$RETOURLIGNE;

    return mail($adresseDest,$sujet,$contenu,$header);
}

function sendMailCmdStage($idCommande, $idNotif)
{
    global $db;

    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query->fetch();

    $notification = $db->prepare('SELECT * FROM COMMANDES_NOTIFICATIONS WHERE idNotif = :idNotif;');
    $notification->execute(array('idNotif'=>$idNotif));
    $notification = $notification->fetch();

    $commande = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur WHERE idCommande = :idCommande;');
    $commande->execute(array('idCommande'=>$idCommande));
    $commande=$commande->fetch();

    if($config[$notification['booleanConfigLibelle']]==1)
    {
        $notification['destinatairesQuery'] = replaceString($notification['destinatairesQuery'],array(
            'idCommande'           => $idCommande,
            'montantTotalCommande' => cmdTotal($idCommande),
        ));
        $query = $db->query($notification['destinatairesQuery']);
        while($data = $query->fetch())
        {
            $sujet = replaceString($notification['sujetNotif'],array(
                'idCommande'    => $idCommande,
                'APPNAME'       => $config['appname'],
                'sessionActive' => $_SESSION['identifiant'],
                'nomCommande'   => $commande['nomCommande'],
                'nomFournisseur'=> $commande['nomFournisseur'],

            ));
            $message = replaceString($notification['corpsNotif'],array(
                'idCommande'     => $idCommande,
                'prenomPersonne' => $data['prenomPersonne'],
                'APPNAME'        => $config['appname'],
            ));

            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            queueMail("Commandes", $data['mailPersonne'], $sujet, 2, $message);
        }
    }
}

?>