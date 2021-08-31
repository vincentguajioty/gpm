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

    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET idProfil = :idProfil WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_POST['identifiant'],
        'idProfil' => $_GET['idProfil']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de l'utilisateur " . $_POST['identifiant'], '3');
            $_SESSION['returnMessage'] = 'Utilisateur modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modifciation de l'utilisateur " . $_POST['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la modification de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }

    majIndicateursPersonne($_GET['idProfil']);
    majNotificationsPersonne($_GET['idProfil']);
    
    echo "<script>javascript:history.go(-1);</script>";
}
?>