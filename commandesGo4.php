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
    $_POST['datePassage'] = ($_POST['datePassage'] == Null) ? Null : $_POST['datePassage'];
    $_POST['dateLivraisonPrevue'] = ($_POST['dateLivraisonPrevue'] == Null) ? Null : $_POST['dateLivraisonPrevue'];

    $query = $db->prepare('UPDATE COMMANDES SET numCommandeFournisseur = :numCommandeFournisseur, datePassage = :datePassage, dateLivraisonPrevue = :dateLivraisonPrevue WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id'],
        'numCommandeFournisseur' => $_POST['numCommandeFournisseur'],
        'datePassage' => $_POST['datePassage'],
        'dateLivraisonPrevue' => $_POST['dateLivraisonPrevue']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id']." sur la page des renseignements sur le passage auprès des fournisseurs.", '1', NULL);
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " modifie les informations de commande données par le fournisseur.", "12");
            $_SESSION['returnMessage'] = 'Informations fournisseur modifiées avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande ".$_GET['id']." sur la page des renseignements sur le passage auprès des fournisseurs.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification des informations fournisseur.";
            $_SESSION['returnType'] = '2';

    }

    

    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 4 WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Passage de la commande " . $_GET['id']." auprès du fournisseur.", '1', NULL);
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " a lancer la commande chez le fournisseur. La commande est désormais en attente de livraison.", "21");
            break;

        default:
            writeInLogs("Erreur inconnue lors du passage de la commande ".$_GET['id']." auprès du fournisseur.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de la commande.";
            $_SESSION['returnType'] = '2';

    }

    $sujet = "[" . $APPNAME . "] La commande " .$_GET['id']. " a été passée auprès du fournisseur.";

    if($config['notifications_commandes_demandeur_passee']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être passée après du fournisseur.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de passage de commande fournisseur envoyé au demandeur pour la commande " . $_GET['id'], '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information de passage de commande fournisseur au demandeur pour la commande " . $_GET['id'], '3', NULL);
            }
        }
    }
    if($config['notifications_commandes_valideur_passee']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_VALIDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idValideur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> La commande " . $_GET['id'] . " dont vous êtes le valideur vient d'être passée après du fournisseur.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de passage de commande fournisseur envoyé au valideur pour la commande " . $_GET['id'], '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail d'information de passage de commande fournisseur au valideur pour la commande " . $_GET['id'], '3', NULL);
            }
        }
    }
    if($config['notifications_commandes_affectee_passee']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " qui vous est affectée vient d'être passée après du fournisseur.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de passage de commande fournisseur envoyé au gérant pour la commande " . $_GET['id'], '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage de commande fournisseur au gérant pour la commande " . $_GET['id'], '3', NULL);
            }
        }
    }
    if($config['notifications_commandes_observateur_passee']==1)
    {
        $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
        $query->execute(array('idCommande'=>$_GET['id']));
        while($data = $query->fetch())
        {
            $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes l'observateur vient d'être passée après du fournisseur.";
            $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
            $message = $RETOURLIGNE.$message.$RETOURLIGNE;
            if(sendmail($data['mailPersonne'], $sujet, 2, $message))
            {
                writeInLogs("Mail d'information de passage de commande fournisseur envoyé à l'observateur pour la commande " . $_GET['id'], '1', NULL);
            }
            else
            {
                writeInLogs("Erreur lors de l'envoi du mail de passage de commande fournisseur à l'observateur pour la commande " . $_GET['id'], '3', NULL);
            }
        }
    }

    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>