<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_equipement_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $query = $db->prepare('SELECT * FROM DOCUMENTS_VHF WHERE idDocVHF = :idDocVHF;');
    $query->execute(array(
        'idDocVHF' => $_GET['idDoc']
    ));
    $data = $query->fetch();
    unlink($data['urlFichierDocVHF']);

    $query = $db->prepare('DELETE FROM DOCUMENTS_VHF WHERE idDocVHF = :idDocVHF;');
    $query->execute(array(
        'idDocVHF' => $_GET['idDoc']
    ));

    writeInLogs("Suppression d'une pièce jointe référence " . $data['nomDocVHF'], '4');


    echo "<script>javascript:history.go(-1);</script>";
}
?>