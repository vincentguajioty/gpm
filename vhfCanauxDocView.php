<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_canal_lecture']==0)
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

    switch ($data['formatDocCanalVHF']) {
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
    header('Content-Disposition: inline; filename=' . $data['nomDocCanalVHF'] . '.' . $data['formatDocCanalVHF']);
    @readfile($data['urlFichierDocCanalVHF']);
}


?>
