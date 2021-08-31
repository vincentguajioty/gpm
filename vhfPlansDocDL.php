<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_plan_lecture']==0)
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

    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . basename($data['nomDocPlanVHF'] . '.' . $data['formatDocPlanVHF']) . "\"");
    readfile($data['urlFichierDocPlanVHF']); // do the double-download-dance (dirty but worky)

    /*NE RIEN ECRIRE APRES SINON BUG DANS L'OUVERTURE DU FICHIER !*/
}
?>