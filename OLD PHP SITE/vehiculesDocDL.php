<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vehicules_lecture']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $query = $db->prepare('SELECT * FROM DOCUMENTS_VEHICULES WHERE idDocVehicules = :idDocVehicules;');
    $query->execute(array(
        'idDocVehicules' => $_GET['idDoc']
    ));
    $data = $query->fetch();

    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . basename($data['nomDocVehicule'] . '.' . $data['formatDocVehicule']) . "\"");
    readfile($data['urlFichierDocVehicule']); // do the double-download-dance (dirty but worky)

    /*NE RIEN ECRIRE APRES SINON BUG DANS L'OUVERTURE DU FICHIER !*/
}
?>