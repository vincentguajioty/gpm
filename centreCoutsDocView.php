<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

$query = $db->prepare('SELECT * FROM DOCUMENTS_CENTRE_COUTS WHERE idDocCouts = :idDocCouts;');
$query->execute(array(
    'idDocCouts' => $_GET['idDoc']
));
$data = $query->fetch();

if (centreCoutsEstCharge($_SESSION['idPersonne'],$data['idCentreDeCout'])==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $query = $db->prepare('SELECT * FROM DOCUMENTS_CENTRE_COUTS WHERE idDocCouts = :idDocCouts;');
    $query->execute(array(
        'idDocCouts' => $_GET['idDoc']
    ));
    $data = $query->fetch();

    switch ($data['formatDocCouts']) {
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
    header('Content-Disposition: inline; filename=' . $data['nomDocCouts'] . '.' . $data['formatDocCouts']);
    @readfile($data['urlFichierDocCouts']);
}

?>
