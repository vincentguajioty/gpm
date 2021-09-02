<?php
session_start();
require_once 'bdd.php';
require_once 'config.php';


$RETOURLIGNE = "\r\n";

function sendMail($adresseDest, $sujet, $niveau, $contenu)
{
    global $APPNAME;
    global $MAILSERVER;
    global $MAILCOPY;
    global $RETOURLIGNE;

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
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Notification ".$idNotif." envoyée à ".$data['mailPersonne']." pour la commande ".$idCommande, '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoie de la notification ".$idNotif." envoyée à ".$data['mailPersonne']." pour la commande ".$idCommande, '3', NULL);
            }
        }
    }
}

?>