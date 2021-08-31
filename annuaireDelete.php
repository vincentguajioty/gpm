<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['annuaire_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query2 = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne ;');
    $query2->execute(array(
        'idPersonne' => $_GET['id']
    ));
    $data = $query2->fetch();

    $query = $db->prepare('UPDATE LOTS_LOTS SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE MESSAGES SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM CHAT WHERE idPersonneEnvoi = :idPersonneEnvoi OR idPersonneDestinataire = :idPersonneDestinataire;');
    $query->execute(array(
        'idPersonneEnvoi' => $_GET['id'],
        'idPersonneDestinataire' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'utilisateur " . $data['identifiant'], '4');
            $_SESSION['returnMessage'] = 'Utilisateur supprimé avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'utilisateur " . $data['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-1);</script>";
}
?>