<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['DELEGATION_ACTIVE'] == 1)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT motDePasse FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne ;');
    $query->execute(array('idPersonne' => $_SESSION['idPersonne']));
    $data = $query->fetch();

    if (!(password_verify($SELPRE.$_POST['old'].$SELPOST, $data['motDePasse'])))
    {
        $_SESSION['returnMessage'] = 'La vérification de l\'ancien mot de passe a échoué.';
        $_SESSION['returnType'] = '2';
        echo "<script type='text/javascript'>document.location.replace('user.php');</script>";
    }
    else
    {
        if ($_POST['new1'] != $_POST['new2'])
        {
            $_SESSION['returnMessage'] = 'La vérification du nouveau mot de passe a échoué (les saisies ne sont pas identiques).';
            $_SESSION['returnType'] = '2';
            echo "<script type='text/javascript'>document.location.replace('user.php');</script>";
        }
        else
        {
            if ($_POST['new1'] == $_SESSION['identifiant'])
            {
                $_SESSION['returnMessage'] = 'Le mot de passe ne peut pas être identique à votre identifiant.';
                $_SESSION['returnType'] = '2';
                echo "<script type='text/javascript'>document.location.replace('user.php');</script>";
            }
            else
            {
                $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET motDePasse = :motDePasse WHERE idPersonne = :idPersonne ;');
                $query->execute(array(
                    'motDePasse' => password_hash($SELPRE.$_POST['new1'].$SELPOST, PASSWORD_DEFAULT),
                    'idPersonne' => $_SESSION['idPersonne']
                ));

                switch($query->errorCode())
                {
                    case '00000':
                        writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a modifié son mot de passe.", '1', NULL);
                        $_SESSION['returnMessage'] = 'Mot de passe modifié avec succès.';
                        $_SESSION['returnType'] = '1';
                        break;

                    default:
                        writeInLogs("Erreur inconnue lors de la tentative de modification de mot de passe de sa session par " . $_SESSION['identifiant'], '3', NULL);
                        $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du mot de passe.";
                        $_SESSION['returnType'] = '2';
                }

                echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
            }

        }

    }
}

?>
