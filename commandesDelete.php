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

if ($_SESSION['commande_abandonner']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('DELETE FROM COMMANDES_MATERIEL WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM COMMANDES_TIMELINE WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    $query = $db->prepare('SELECT * FROM DOCUMENTS_COMMANDES WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    While($data = $query->fetch())
    {
        unlink($data['urlFichierDocCommande']);

        $query = $db->prepare('DELETE FROM DOCUMENTS_COMMANDES WHERE idDocCommande = :idDocCommande;');
        $query->execute(array(
            'idDocCommande' => $data['idDocCommande']
        ));
    }

    $query = $db->prepare('DELETE FROM COMMANDES_AFFECTEES WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    $query = $db->prepare('DELETE FROM COMMANDES_OBSERVATEURS WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    $query = $db->prepare('DELETE FROM COMMANDES_DEMANDEURS WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    
    $query = $db->prepare('DELETE FROM CENTRE_COUTS_OPERATIONS WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    
    $query = $db->prepare('DELETE FROM COMMANDES WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de la commande " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Commande supprimée avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de la commande " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de la commande.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";
}
?>