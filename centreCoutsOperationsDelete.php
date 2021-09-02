<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if (centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['idCentreDeCout'])==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query2 = $db->prepare('SELECT * FROM CENTRE_COUTS_OPERATIONS WHERE idOperations = :idOperations ;');
    $query2->execute(array(
        'idOperations' => $_GET['id']
    ));
    $data = $query2->fetch();

    if($data['idCommande'] != Null AND $data['idCommande']!= '')
    {
        $query = $db->prepare('UPDATE COMMANDES SET integreCentreCouts = 0 WHERE idCommande = :idCommande ;');
        $query->execute(array(
        'idCommande' => $data['idCommande']
        ));
    }

    $query = $db->prepare('DELETE FROM CENTRE_COUTS_OPERATIONS WHERE idOperations = :idOperations;');
    $query->execute(array(
        'idOperations' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'opération de coûts " . $data['libelleOperation'], '1', NULL);
            $_SESSION['returnMessage'] = 'Opération supprimée avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'opération de coûts " . $data['libelleOperation'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script type='text/javascript'>document.location.replace('centreCoutsContenu.php?id=" . $_GET['idCentreDeCout'] . "');</script>";
}
?>