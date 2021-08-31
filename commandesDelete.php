<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

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
    $query = $db->prepare('DELETE FROM COMMANDES_VALIDEURS WHERE idCommande = :idCommande;');
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
    
    $query = $db->prepare('DELETE FROM COMMANDES WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de la commande " . $_GET['id'], '4');
            $_SESSION['returnMessage'] = 'Commande supprimée avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de la commande " . $_GET['id'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de la commande.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";
}
?>