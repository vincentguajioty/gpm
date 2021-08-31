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

    switch ($data['formatDocPlanVHF']) {
        case "pdf":
            $application = "application/pdf";
            break;
        case "png":
            $ctype = "image/png";
            break;
        case "jpg":
            $ctype = "image/jpeg";
            break;
        case "jpeg":
            $ctype = "image/jpeg";
            break;
        default:
    }

    header('Content-type: ' . $application);
    header('Content-Disposition: inline; filename=' . $data['nomDocPlanVHF'] . '.' . $data['formatDocPlanVHF']);
    @readfile($data['urlFichierDocPlanVHF']);
}

?>
