<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'commandesCommentAdd.php';

if ($_SESSION['commande_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {

    $query = $db->prepare('SELECT * FROM DOCUMENTS_COMMANDES WHERE idDocCommande = :idDocCommande;');
    $query->execute(array(
        'idDocCommande' => $_GET['idDoc']
    ));
    $data = $query->fetch();
    unlink($data['urlFichierDocCommande']);

    $query = $db->prepare('DELETE FROM DOCUMENTS_COMMANDES WHERE idDocCommande = :idDocCommande;');
    $query->execute(array(
        'idDocCommande' => $_GET['idDoc']
    ));

    writeInLogs("Suppression d'une pièce jointe référence " . $data['nomDocCommande'] . " à la commande " . $data['idCommande'], '1', NULL);
    addCommandeComment($data['idCommande'], $_SESSION['identifiant'] . " supprime la pièce jointe " . $data['nomDocCommande'], "11");


    echo "<script>javascript:history.go(-1);</script>";
}
?>