<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['commande_valider']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    if ($_POST['button'] == 'ok')
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 3, remarquesValidation = :remarquesValidation, dateValidation = CURRENT_TIMESTAMP WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesValidation' => $_POST['remarquesValidation']
        ));
        addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " valide la commande avec le commentaire: " . $_POST['remarquesValidation'], "13");

        $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idObservateur = p.idPersonne WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id']
        ));
        $data = $query->fetch();

        $sujet = "[" . $APPNAME . "] Validation POSITIVE de la commande " .$_GET['id'];
        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes observateur vient d'être acceptée lors de son étape de validation.";
        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
        {
            writeInLogs("Mail d'abandon envoyé à l'observateur pour la commande " . $_GET['id'], '2');
        }
        else
        {
            writeInLogs("Erreur lors de l'envoi du mail du mail d'abandon à l'observateur pour la commande " . $_GET['id'], '5');
        }


        $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idDemandeur = p.idPersonne WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id']
        ));
        $data = $query->fetch();

        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être acceptée lors de son étape de validation.";
        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
        {
            writeInLogs("Mail d'abandon envoyé au demandeur pour la commande " . $_GET['id'], '2');
        }
        else
        {
            writeInLogs("Erreur lors de l'envoi du mail du mail d'abandon au demandeur pour la commande " . $_GET['id'], '5');
        }


        $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idAffectee = p.idPersonne WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id']
        ));
        $data = $query->fetch();

        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous le réalisateur/responsable vient d'être acceptée lors de son étape de validation.";
        $message = $message . "<br/><br/>Vous pouvez désormais passer la commande auprès du fournisseur en tenant bien le dossier " . $APPNAME . " à jour.";
        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
        {
            writeInLogs("Mail d'abandon envoyé au realisateur pour la commande " . $_GET['id'], '2');
        }
        else
        {
            writeInLogs("Erreur lors de l'envoi du mail du mail d'abandon au realisateur pour la commande " . $_GET['id'], '5');
        }
    }
    else
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 1, remarquesValidation = :remarquesValidation, dateValidation = CURRENT_TIMESTAMP WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesValidation' => $_POST['remarquesValidation']
        ));
        addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " refuse la commande avec le commentaire: " . $_POST['remarquesValidation'], "19");

        $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idObservateur = p.idPersonne WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id']
        ));
        $data = $query->fetch();

        $sujet = "[" . $APPNAME . "] Validation NEGATIVE de la commande " .$_GET['id'];
        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes observateur vient d'être refusée lors de son étape de validation.";
        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
        {
            writeInLogs("Mail d'abandon envoyé à l'observateur pour la commande " . $_GET['id'], '2');
        }
        else
        {
            writeInLogs("Erreur lors de l'envoi du mail du mail d'abandon à l'observateur pour la commande " . $_GET['id'], '5');
        }


        $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idDemandeur = p.idPersonne WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id']
        ));
        $data = $query->fetch();

        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être refusée lors de son étape de validation.";
        $message = $message . "<br/><br/>Vous avez à nouveau le contrôle sur cette commande afin de la modifier et de la soumettre à nouveau à validation. Le valideur a laissé un commentaire qui vous accessible depuis la frise chronologique de la commande.";
        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
        {
            writeInLogs("Mail d'abandon envoyé au demandeur pour la commande " . $_GET['id'], '2');
        }
        else
        {
            writeInLogs("Erreur lors de l'envoi du mail du mail d'abandon au demandeur pour la commande " . $_GET['id'], '5');
        }


        $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idAffectee = p.idPersonne WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id']
        ));
        $data = $query->fetch();

        $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous le réalisateur/responsable vient d'être refusée lors de son étape de validation.";
        $message = $message . "<br/><br/>Vous avez à nouveau le contrôle sur cette commande afin de la modifier et de la soumettre à nouveau à validation. Le valideur a laissé un commentaire qui vous accessible depuis la frise chronologique de la commande.";
        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
        if(sendmail($data['mailPersonne'], $sujet, 2, $message))
        {
            writeInLogs("Mail d'abandon envoyé au realisateur pour la commande " . $_GET['id'], '2');
        }
        else
        {
            writeInLogs("Erreur lors de l'envoi du mail du mail d'abandon au realisateur pour la commande " . $_GET['id'], '5');
        }
    }

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'enregistrement de la validation.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>