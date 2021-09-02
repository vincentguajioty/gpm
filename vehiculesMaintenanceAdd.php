<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vehicules_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
	$_POST['idExecutant'] = ($_POST['idExecutant'] == Null) ? Null : $_POST['idExecutant'];
	$_POST['dateMaintenance'] = ($_POST['dateMaintenance'] == Null) ? Null : $_POST['dateMaintenance'];
	$_POST['idTypeMaintenance'] = ($_POST['idTypeMaintenance'] == Null) ? Null : $_POST['idTypeMaintenance'];
	$_POST['releveKilometrique'] = ($_POST['releveKilometrique'] == Null) ? Null : $_POST['releveKilometrique'];
	
    $query = $db->prepare('
        INSERT INTO
            VEHICULES_MAINTENANCE
        SET
            idVehicule         = :idVehicule,
            idExecutant        = :idExecutant,
            dateMaintenance    = :dateMaintenance,
            idTypeMaintenance  = :idTypeMaintenance,
            detailsMaintenance = :detailsMaintenance,
            releveKilometrique = :releveKilometrique
        ;');
    $query->execute(array(
        'idVehicule'         => $_GET['idVehicule'],
        'idExecutant'        => $_POST['idExecutant'],
        'dateMaintenance'    => $_POST['dateMaintenance'],
        'idTypeMaintenance'  => $_POST['idTypeMaintenance'],
        'detailsMaintenance' => $_POST['detailsMaintenance'],
        'releveKilometrique' => $_POST['releveKilometrique'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout d'une maintenance pour le véhicule " . $_GET['idVehicule'], '1', NULL);
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de la maintenance pour le véhicule " . $_GET['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de la tache de maintenance.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>