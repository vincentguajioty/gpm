<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['annuaire_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['libelleProfil'] = ($_POST['libelleProfil'] == Null) ? Null : $_POST['libelleProfil'];

    $query = $db->prepare('INSERT INTO PERSONNE_REFERENTE(
                                                idProfil,
                                                identifiant,
                                                motDePasse,
                                                nomPersonne,
                                                prenomPersonne,
                                                mailPersonne,
                                                telPersonne,
                                                fonction,
                                                conf_indicateur1Accueil,
                                                conf_indicateur2Accueil,
                                                conf_indicateur3Accueil,
                                                conf_indicateur4Accueil,
                                                conf_indicateur5Accueil,
                                                conf_indicateur6Accueil ,
                                                conf_indicateur7Accueil ,
                                                conf_indicateur8Accueil) VALUES(:idProfil,
                                                :identifiant,
                                                :motDePasse,
                                                :nomPersonne,
                                                :prenomPersonne,
                                                :mailPersonne,
                                                :telPersonne,
                                                :fonction,
                                                1, 1, 1, 1, 1, 1, 1, 1);'
                        );
    $query->execute(array(
        'idProfil'       => $_POST['libelleProfil'],
        'identifiant'    => $_POST['identifiant'],
        'motDePasse'     => password_hash($_POST['identifiant'], PASSWORD_DEFAULT),
        'nomPersonne'    => $_POST['nomPersonne'],
        'prenomPersonne' => $_POST['prenomPersonne'],
        'mailPersonne'   => $_POST['mailPersonne'],
        'telPersonne'    => $_POST['telPersonne'],
        'fonction'       => $_POST['fonction']
    ));
    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'utilisateur " . $_POST['identifiant'], '2');
            $_SESSION['returnMessage'] = 'Utilisateur ajouté avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout de l'utilisateur " . $_POST['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Un utilisateur existe déjà avec le même identifiant. Merci de changer l\'identifiant.';
            $_SESSION['returnType'] = '2';
        break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'utilisateur " . $_POST['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de l\'ajout de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }

    if ($_POST['notificationMailCreation']==1)
    {
        $sujet = "[" . $APPNAME . "] Bienvenue sur " . $APPNAME;
        $message = "Bonjour " . $_POST['prenomPersonne'] . ", <br/><br/> Votre session a été créée. Voici vos identifiants:<br/>Nom d'utilisateur: " . $_POST['identifiant'] . "<br/>Mot de passe: ". $_POST['identifiant'];
        $message = $message . "<br/><br/>Vous serez invité(e) à changer votre mot de passe à votre première connexion.<br/>Voici le lien d'accès à l'outil: " . $URLSITE;
        $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;

        $message = $RETOURLIGNE.$message.$RETOURLIGNE;
        if(sendmail($_POST['mailPersonne'], $sujet, 2, $message))
        {
            writeInLogs("Mail d'accueil envoyé à " . $_POST['identifiant'], '2');
        }
        else
        {
            writeInLogs("Erreur lors de l'envoi du mail d'accueil à " . $_POST['identifiant'], '5');
        }
    }

    echo "<script>window.location = document.referrer;</script>";


}
?>