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

    switch ($data['formatDocVehicule']) {
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
    header('Content-Disposition: inline; filename=' . $data['nomDocVehicule'] . '.' . $data['formatDocVehicule']);
    @readfile($data['urlFichierDocVehicule']);
}

?>
