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

?>