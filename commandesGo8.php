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
            writeInLogs("Abandon de la commande " . $_GET['id'], '1', NULL);
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " abandonne la commande.", "7");
            $_SESSION['returnMessage'] = 'Commande abandonnée.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'abandon de la commande ".$_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'abandon de la commande.";
            $_SESSION['returnType'] = '2';

    }

    $sujet = "[" . $APPNAME . "] Abandon de la commande " .$_GET['id'];

    if($config['notifications_commandes_demandeur_abandon']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être abandonnée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information d'abandon de commande envoyé au demandeur pour la commande " . $_GET['id'], '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information d'abandon de commande au demandeur pour la commande " . $_GET['id'], '3', NULL);
            }
        }
    }
    if($config['notifications_commandes_valideur_abandon']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_VALIDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idValideur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> La commande " . $_GET['id'] . " dont vous êtes le valideur vient d'être abandonnée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information d'abandon de commande envoyé au valideur pour la commande " . $_GET['id'], '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information d'abandon de commande au valideur pour la commande " . $_GET['id'], '3', NULL);
            }
        }
    }
    if($config['notifications_commandes_affectee_abandon']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " qui vous est affectée vient d'être abandonnée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information d'abandon de commande envoyé au gérant pour la commande " . $_GET['id'], '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage d'abandon de commande au gérant pour la commande " . $_GET['id'], '3', NULL);
            }
        }
    }
    if($config['notifications_commandes_observateur_abandon']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes l'observateur vient d'être abandonnée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information d'abandon de commande envoyé à l'observateur pour la commande " . $_GET['id'], '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage d'abandon de commande à l'observateur pour la commande " . $_GET['id'], '3', NULL);
            }
        }
    }

    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";

}
?>