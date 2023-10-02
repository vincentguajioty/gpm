<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

$query = $db->prepare('SELECT * FROM DOCUMENTS_CENTRE_COUTS WHERE idDocCouts = :idDocCouts;');
$query->execute(array(
    'idDocCouts' => $_GET['idDoc']
));
$data = $query->fetch();

if (centreCoutsEstCharge($_SESSION['idPersonne'],$data['idCentreDeCout'])==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $query = $db->prepare('SELECT * FROM DOCUMENTS_CENTRE_COUTS WHERE idDocCouts = :idDocCouts;');
    $query->execute(array(
        'idDocCouts' => $_GET['idDoc']
    ));
    $data = $query->fetch();
    unlink($data['urlFichierDocCouts']);

    $query = $db->prepare('DELETE FROM DOCUMENTS_CENTRE_COUTS WHERE idDocCouts = :idDocCouts;');
    $query->execute(array(
        'idDocCouts' => $_GET['idDoc']
    ));

    writeInLogs("Suppression d'une pièce jointe référence " . $data['nomDocCouts'], '1', NULL);


    echo "<script>window.location = document.referrer;</script>";
}
?>