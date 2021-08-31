<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['commande_lecture']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $query = $db->prepare('SELECT * FROM DOCUMENTS_COMMANDES WHERE idDocCommande = :idDocCommande;');
    $query->execute(array(
        'idDocCommande' => $_GET['idDoc']
    ));
    $data = $query->fetch();

    switch ($data['formatDocCommande']) {
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
    header('Content-Disposition: inline; filename=' . $data['nomDocCommande'] . '.' . $data['formatDocCommande']);
    @readfile($data['urlFichierDocCommande']);
}


?>
