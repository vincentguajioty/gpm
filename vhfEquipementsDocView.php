<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_equipement_lecture']==0)
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

    switch ($data['formatDocVHF']) {
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
    header('Content-Disposition: inline; filename=' . $data['nomDocVHF'] . '.' . $data['formatDocVHF']);
    @readfile($data['urlFichierDocVHF']);
}

?>
