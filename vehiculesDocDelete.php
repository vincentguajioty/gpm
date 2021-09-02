<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vehicules_modification']==0)
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
    unlink($data['urlFichierDocVehicule']);

    $query = $db->prepare('DELETE FROM DOCUMENTS_VEHICULES WHERE idDocVehicules = :idDocVehicules;');
    $query->execute(array(
        'idDocVehicules' => $_GET['idDoc']
    ));

    writeInLogs("Suppression d'une pièce jointe référence " . $data['nomDocVehicule'], '1', NULL);


    echo "<script>javascript:history.go(-1);</script>";
}
?>