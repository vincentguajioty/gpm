<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> strtoupper($CONFSUPPRESSION))
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['cautions_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('DELETE FROM CAUTIONS WHERE idCaution = :idCaution');
    $query->execute([
        ':idCaution' => $_GET['id']
    ]);

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression d'une caution.", '1', NULL);
            $_SESSION['returnMessage'] = 'Caution supprimée avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression d'une caution.", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de la caution.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-1);</script>";
}
?>