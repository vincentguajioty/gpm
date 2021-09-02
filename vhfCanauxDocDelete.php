<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_canal_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $query = $db->prepare('SELECT * FROM DOCUMENTS_CANAL_VHF WHERE idDocCanalVHF = :idDocCanalVHF;');
    $query->execute(array(
        'idDocCanalVHF' => $_GET['idDoc']
    ));
    $data = $query->fetch();
    unlink($data['urlFichierDocCanalVHF']);

    $query = $db->prepare('DELETE FROM DOCUMENTS_CANAL_VHF WHERE idDocCanalVHF = :idDocCanalVHF;');
    $query->execute(array(
        'idDocCanalVHF' => $_GET['idDoc']
    ));

    writeInLogs("Suppression d'une pièce jointe référence " . $data['nomDocCanalVHF'] . " au canal " . $data['idVhfCanal'], '1', NULL);


    echo "<script>javascript:history.go(-1);</script>";
}
?>