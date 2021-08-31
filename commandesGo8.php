<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['commande_abandonner']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 8 WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " abandonne la commande.", "7");
            $_SESSION['returnMessage'] = 'Commande abandonnée.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'abandon de la commande.";
            $_SESSION['returnType'] = '2';

    }

    $sujet = "[" . $APPNAME . "] Abandon de la commande " .$_GET['id'];

    if($config['notifications_commandes_demandeur_abandon']==1)
    {
        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
        $data = $query->fetch();
        if(cmdEstDemandeur($data['idPersonne'],$_GET['id']))
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être abandonnée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information d'abandon de commande envoyé au demandeur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information d'abandon de commande au demandeur pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_valideur_abandon']==1)
    {
        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
        $data = $query->fetch();
        if(cmdEstValideur($data['idPersonne'],$_GET['id']))
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> La commande " . $_GET['id'] . " dont vous êtes le valideur vient d'être abandonnée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information d'abandon de commande envoyé au valideur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information d'abandon de commande au valideur pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_affectee_abandon']==1)
    {
        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
        $data = $query->fetch();
        if(cmdEstAffectee($data['idPersonne'],$_GET['id']))
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " qui vous est affectée vient d'être abandonnée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information d'abandon de commande envoyé au gérant pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage d'abandon de commande au gérant pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_observateur_abandon']==1)
    {
        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
        $data = $query->fetch();
        if(cmdEstObservateur($data['idPersonne'],$_GET['id']))
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes l'observateur vient d'être abandonnée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information d'abandon de commande envoyé à l'observateur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage d'abandon de commande à l'observateur pour la commande " . $_GET['id'], '5');
            }
        }
    }



    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>