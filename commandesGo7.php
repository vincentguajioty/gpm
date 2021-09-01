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

    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 7, dateCloture = CURRENT_TIMESTAMP WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " clôture la commande.", "16");
            $_SESSION['returnMessage'] = 'Commande clôturée.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la clôture de la commande.";
            $_SESSION['returnType'] = '2';

    }

    $sujet = "[" . $APPNAME . "] Clôture de la commande " .$_GET['id'];

    if($config['notifications_commandes_demandeur_cloture']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être clôturée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de clôture de commande envoyé au demandeur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information de clôture de commande au demandeur pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_valideur_cloture']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_VALIDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idValideur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> La commande " . $_GET['id'] . " dont vous êtes le valideur vient d'être clôturée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de clôture de commande envoyé au valideur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information de clôture de commande au valideur pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_affectee_cloture']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " qui vous est affectée vient d'être clôturée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de clôture de commande envoyé au gérant pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage de clôture de commande au gérant pour la commande " . $_GET['id'], '5');
            }
        }
    }
    if($config['notifications_commandes_observateur_cloture']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes l'observateur vient d'être clôturée.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de clôture de commande envoyé à l'observateur pour la commande " . $_GET['id'], '2');
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage de clôture de commande à l'observateur pour la commande " . $_GET['id'], '5');
            }
        }
    }


    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>