<?php

session_start();
if ($_SESSION['identifiant'] == Null)
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
require_once 'config/bdd.php';

if ($_POST['new1'] != $_POST['new2'])
{
    $_SESSION['returnMessage'] = 'La vérification du nouveau mot de passe a échoué (les saisies ne sont pas identiques).';
    $_SESSION['returnType'] = '2';
    echo "<script type='text/javascript'>document.location.replace('loginChangePWDstart.php');</script>";
}
else
{
    if ($_POST['new1'] == $_SESSION['identifiant'])
    {
        $_SESSION['returnMessage'] = 'Le mot de passe ne peut pas être identique à votre identifiant.';
        $_SESSION['returnType'] = '2';
        echo "<script type='text/javascript'>document.location.replace('loginChangePWDstart.php');</script>";
    }
    else
    {
        $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET motDePasse = :motDePasse WHERE idPersonne = :idPersonne ;');
        $query->execute(array(
            'motDePasse' => password_hash($_POST['new1'], PASSWORD_DEFAULT),
            'idPersonne' => $_SESSION['idPersonne']
        ));

        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a modifié son mot de passe.", '3');
                $_SESSION['returnMessage'] = 'Mot de passe modifié avec succès.';
                $_SESSION['returnType'] = '1';
                break;

            default:
                writeInLogs("Erreur inconnue lors de la tentative de modification de mot de passe de sa session par " . $_SESSION['identifiant'], '5');
                $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du mot de passe.";
                $_SESSION['returnType'] = '2';
        }

        $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS a ON p.idPersonne = a.idPersonne WHERE p.identifiant= :identifiant;');
        $query->execute(array(
            'identifiant' => $_SESSION['identifiant']
        ));
        $data = $query->fetch();
        $_SESSION['connexion_connexion'] = $data['connexion_connexion'];

        echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
    }

}
?>