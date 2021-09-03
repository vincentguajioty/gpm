<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['annuaire_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {
    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET isActiveDirectory = false WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Désactivation du lien AD pour l'utilisateur " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Lien AD désactivé. L\'utilisateur devra passer par la fonction "Mot de passe oublié" pour définir un nouveau mot de passe de compte.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la désactivation du lien AD pour l'utilisateur " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la désactivation du lien AD.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>