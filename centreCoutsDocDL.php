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

    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . basename($data['nomDocCouts'] . '.' . $data['formatDocCouts']) . "\"");
    readfile($data['urlFichierDocCouts']); // do the double-download-dance (dirty but worky)

    /*NE RIEN ECRIRE APRES SINON BUG DANS L'OUVERTURE DU FICHIER !*/
}
?>