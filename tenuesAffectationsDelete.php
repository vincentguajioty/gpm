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

if ($_SESSION['tenues_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('SELECT idCatalogueTenue FROM TENUES_AFFECTATION WHERE idTenue = :idTenue');
    $query->execute(array('idTenue' => $_GET['id']));
    $data = $query->fetch();
    $query = $db->prepare('UPDATE TENUES_CATALOGUE SET stockCatalogueTenue = stockCatalogueTenue + 1 WHERE idCatalogueTenue = :idCatalogueTenue');
    $query->execute(array('idCatalogueTenue' => $data['idCatalogueTenue']));

    $query = $db->prepare('DELETE FROM TENUES_AFFECTATION WHERE idTenue = :idTenue');
    $query->execute([
        ':idTenue' => $_GET['id']
    ]);

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression d'une affectation de tenue.", '1', NULL);
            $_SESSION['returnMessage'] = 'Affectation supprimée avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'affectation de tenue.", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de l\'affectation de tenue.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>