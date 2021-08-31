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
    
    $query = $db->prepare('UPDATE CENTRE_COUTS SET idResponsable = Null WHERE idResponsable = :idResponsable ;');
    $query->execute(array(
        'idResponsable' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE COMMANDES SET idDemandeur = Null WHERE idDemandeur = :idDemandeur ;');
    $query->execute(array(
        'idDemandeur' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE COMMANDES SET idObservateur = Null WHERE idObservateur = :idObservateur ;');
    $query->execute(array(
        'idObservateur' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE COMMANDES SET idValideur = Null WHERE idValideur = :idValideur ;');
    $query->execute(array(
        'idValideur' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE COMMANDES SET idAffectee = Null WHERE idAffectee = :idAffectee ;');
    $query->execute(array(
        'idAffectee' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE MESSAGES SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE INVENTAIRES SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE VHF_EQUIPEMENTS SET idResponsable = Null WHERE idResponsable = :idResponsable ;');
    $query->execute(array(
        'idResponsable' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE VEHICULES SET idResponsable = Null WHERE idResponsable = :idResponsable ;');
    $query->execute(array(
        'idResponsable' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE VEHICULES_MAINTENANCE SET idExecutant = Null WHERE idExecutant = :idExecutant ;');
    $query->execute(array(
        'idExecutant' => $_GET['id']
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