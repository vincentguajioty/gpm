<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> strtoupper($CONFSUPPRESSION))
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['lieux_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM LIEUX WHERE idLieu = :idLieu;');
    $query->execute(array(
        'idLieu' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE LOTS_LOTS SET idLieu = Null WHERE idLieu = :idLieu;');
    $query->execute(array(
        'idLieu' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE COMMANDES SET idLieuLivraison = Null WHERE idLieuLivraison = :idLieuLivraison;');
    $query->execute(array(
        'idLieuLivraison' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE RESERVES_CONTENEUR SET idLieu = Null WHERE idLieu = :idLieu;');
    $query->execute(array(
        'idLieu' => $_GET['id']
    ));
    
    $query = $db->prepare('UPDATE VEHICULES SET idLieu = Null WHERE idLieu = :idLieu;');
    $query->execute(array(
        'idLieu' => $_GET['id']
    ));


    $query = $db->prepare('DELETE FROM LIEUX WHERE idLieu = :idLieu;');
    $query->execute(array(
        'idLieu' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du lieu " . $data['libelleLieu'], '1', NULL);
            $_SESSION['returnMessage'] = 'Lieu supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du lieu " . $data['libelleLieu'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression du lieu.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>