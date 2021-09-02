<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> strtoupper($CONFSUPPRESSION))
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

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
    
    $query = $db->prepare('DELETE FROM CENTRE_COUTS_PERSONNES WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));
    
    $query = $db->prepare('DELETE FROM COMMANDES_AFFECTEES WHERE idAffectee = :idPersonne;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));
    $query = $db->prepare('DELETE FROM COMMANDES_VALIDEURS WHERE idValideur = :idPersonne;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));
    $query = $db->prepare('DELETE FROM COMMANDES_OBSERVATEURS WHERE idObservateur = :idPersonne;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));
    $query = $db->prepare('DELETE FROM COMMANDES_DEMANDEURS WHERE idDemandeur = :idPersonne;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE MESSAGES SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE INVENTAIRES SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE RESERVES_INVENTAIRES SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
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
    
    $query = $db->prepare('UPDATE VEHICULES_RELEVES SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE VEHICULES_DESINFECTIONS SET idExecutant = Null WHERE idExecutant = :idExecutant ;');
    $query->execute(array(
        'idExecutant' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE CENTRE_COUTS_OPERATIONS SET idPersonne = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));
    
    $query = $db->prepare('DELETE FROM TODOLIST_PERSONNES WHERE idExecutant = :idPersonne');
    $query->execute([
        ':idPersonne' => $_GET['id']
    ]);
    
    $query = $db->prepare('UPDATE TODOLIST SET idCreateur = Null WHERE idCreateur = :idPersonne');
    $query->execute([
        ':idPersonne' => $_GET['id']
    ]);

    $query = $db->prepare('DELETE FROM TENUES_AFFECTATION WHERE idPersonne = :idPersonne');
    $query->execute([
        ':idPersonne' => $_GET['id']
    ]);
    
    $query = $db->prepare('DELETE FROM CAUTIONS WHERE idPersonne = :idPersonne');
    $query->execute([
        ':idPersonne' => $_GET['id']
    ]);

    $query = $db->prepare('DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne');
    $query->execute([
        ':idPersonne' => $_GET['id']
    ]);

    $query = $db->prepare('DELETE FROM RESETPASSWORD WHERE idPersonne = :idPersonne;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'utilisateur " . $data['identifiant'], '1', NULL);
            $_SESSION['returnMessage'] = 'Utilisateur supprimé avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'utilisateur " . $data['identifiant'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script type='text/javascript'>document.location.replace('annuaire.php');</script>";
}
?>