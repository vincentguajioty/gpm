<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 2, dateDemandeValidation = CURRENT_TIMESTAMP, dateValidation = Null WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " envoie la demande de validation.", "14");
            $_SESSION['returnMessage'] = 'Demande de validation enregistrée.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la demande de validation.";
            $_SESSION['returnType'] = '2';

    }

    $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idValideur = p.idPersonne WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    $data = $query->fetch();

    $sujet = "[" . $APPNAME . "] Demande de validation de commande de la part de " . $_SESSION['identifiant'] . " pour la commande " .$_GET['id'];
    $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> La commande " . $_GET['id'] . " dont vous êtes le valideur vient de passer au stade de demande de validation.";
    $message = $message . "<br/><br/>Merci de vous connecter sur " . $APPNAME . " afin de donner une réponse à la demande de validation.";
    $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

    $message = $RETOURLIGNE.$message.$RETOURLIGNE;
    if(sendmail($data['mailPersonne'], $sujet, 2, $message))
    {
        writeInLogs("Mail de demande de validation envoyée pour la commande " . $_GET['id'], '2');
    }
    else
    {
        writeInLogs("Erreur lors de l'envoi du mail de demande de validation pour la commande " . $_GET['id'], '5');
    }


    $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idObservateur = p.idPersonne WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    $data = $query->fetch();

    $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes observateur vient de passer au stade de demande de validation.";
    $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

    $message = $RETOURLIGNE.$message.$RETOURLIGNE;
    if(sendmail($data['mailPersonne'], $sujet, 2, $message))
    {
        writeInLogs("Mail d'information de demande de validation envoyée à l'observateur pour la commande " . $_GET['id'], '2');
    }
    else
    {
        writeInLogs("Erreur lors de l'envoi du mail d'information de demande de validation à l'observateur pour la commande " . $_GET['id'], '5');
    }

    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>