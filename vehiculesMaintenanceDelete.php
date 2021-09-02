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

if ($_SESSION['vehicules_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query2 = $db->prepare('SELECT * FROM VEHICULES_MAINTENANCE WHERE idMaintenance = :idMaintenance ;');
    $query2->execute(array(
        'idMaintenance' => $_GET['id']
    ));
    $data = $query2->fetch();

    $query = $db->prepare('DELETE FROM VEHICULES_MAINTENANCE WHERE idMaintenance = :idMaintenance;');
    $query->execute(array(
        'idMaintenance' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression d'une maintenance sur le véhicule " . $data['idVehicule'], '1', NULL);
            $_SESSION['returnMessage'] = 'Maintenance supprimée avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression d'une maintenance sur le véhicule " . $data['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de la maintenance.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-1);</script>";
}
?>