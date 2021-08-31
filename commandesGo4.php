<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['commande_etreEnCharge']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 4 WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " a lancer la commande chez le fournisseur. La commande est désormais en attente de livraison.", "21");
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de la commande.";
            $_SESSION['returnType'] = '2';

    }

    $sujet = "[" . $APPNAME . "] La commande " .$_GET['id']. " a été passée auprès du fournisseur.";

    if($config['notifications_commandes_demandeur_passee']==1)
    {
        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
        $data = $query->fetch();
        if(cmdEstDemandeur($data['idPersonne'],$_GET['id']))
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être passée après du fournisseur.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de passage de commande fournisseur envoyé au demandeur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information de passage de commande fournisseur au demandeur pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_valideur_passee']==1)
    {
        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
        $data = $query->fetch();
        if(cmdEstValideur($data['idPersonne'],$_GET['id']))
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> La commande " . $_GET['id'] . " dont vous êtes le valideur vient d'être passée après du fournisseur.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de passage de commande fournisseur envoyé au valideur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information de passage de commande fournisseur au valideur pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_affectee_passee']==1)
    {
        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
        $data = $query->fetch();
        if(cmdEstAffectee($data['idPersonne'],$_GET['id']))
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " qui vous est affectée vient d'être passée après du fournisseur.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de passage de commande fournisseur envoyé au gérant pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage de commande fournisseur au gérant pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_observateur_passee']==1)
    {
        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
        $data = $query->fetch();
        if(cmdEstObservateur($data['idPersonne'],$_GET['id']))
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes l'observateur vient d'être passée après du fournisseur.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de passage de commande fournisseur envoyé à l'observateur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage de commande fournisseur à l'observateur pour la commande " . $_GET['id'], '5');
            }
        }
    }



    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>