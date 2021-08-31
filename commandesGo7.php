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



    $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idValideur = p.idPersonne WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    $data = $query->fetch();

    $sujet = "[" . $APPNAME . "] Clôture de la commande " .$_GET['id'];
    $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le valideur vient d'être clôturée.";
    $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

    $message = $RETOURLIGNE.$message.$RETOURLIGNE;
    if(sendmail($data['mailPersonne'], $sujet, 2, $message))
    {
        writeInLogs("Mail de clôture envoyé au valideur pour la commande " . $_GET['id'], '2');
    }
    else
    {
        writeInLogs("Erreur lors de l'envoi du mail du mail de clôture au valideur pour la commande " . $_GET['id'], '5');
    }




    $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idObservateur = p.idPersonne WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    $data = $query->fetch();

    $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes observateur vient d'être clôturée.";
    $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

    $message = $RETOURLIGNE.$message.$RETOURLIGNE;
    if(sendmail($data['mailPersonne'], $sujet, 2, $message))
    {
        writeInLogs("Mail de clôture envoyé à l'observateur pour la commande " . $_GET['id'], '2');
    }
    else
    {
        writeInLogs("Erreur lors de l'envoi du mail du mail de clôture à l'observateur pour la commande " . $_GET['id'], '5');
    }


    $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idDemandeur = p.idPersonne WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    $data = $query->fetch();

    $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être clôturée.";
    $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

    $message = $RETOURLIGNE.$message.$RETOURLIGNE;
    if(sendmail($data['mailPersonne'], $sujet, 2, $message))
    {
        writeInLogs("Mail de clôture envoyé au demandeur pour la commande " . $_GET['id'], '2');
    }
    else
    {
        writeInLogs("Erreur lors de l'envoi du mail du mail de clôture au demandeur pour la commande " . $_GET['id'], '5');
    }



    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>