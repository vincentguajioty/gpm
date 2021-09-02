<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_plan_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $query = $db->prepare('SELECT * FROM DOCUMENTS_PLAN_VHF WHERE idDocPlanVHF = :idDocPlanVHF;');
    $query->execute(array(
        'idDocPlanVHF' => $_GET['idDoc']
    ));
    $data = $query->fetch();
    unlink($data['urlFichierDocPlanVHF']);

    $query = $db->prepare('DELETE FROM DOCUMENTS_PLAN_VHF WHERE idDocPlanVHF = :idDocPlanVHF;');
    $query->execute(array(
        'idDocPlanVHF' => $_GET['idDoc']
    ));

    writeInLogs("Suppression d'une pièce jointe référence " . $data['nomDocPlanVHF'] . " au plan " . $data['idVhfPlan'], '1', NULL);


    echo "<script>javascript:history.go(-1);</script>";
}
?>