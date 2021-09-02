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
	
    $query = $db->prepare('
        UPDATE
            VEHICULES_MAINTENANCE
        SET
            idExecutant        = :idExecutant,
            dateMaintenance    = :dateMaintenance,
            idTypeMaintenance  = :idTypeMaintenance,
            detailsMaintenance = :detailsMaintenance
        WHERE
            idMaintenance      = :idMaintenance
        ;');
    $query->execute(array(
        'idMaintenance'      => $_GET['id'],
        'idExecutant'        => $_POST['idExecutant'],
        'dateMaintenance'    => $_POST['dateMaintenance'],
        'idTypeMaintenance'  => $_POST['idTypeMaintenance'],
        'detailsMaintenance' => $_POST['detailsMaintenance']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification d'une maintenance pour le véhicule " . $_GET['idVehicule'], '1', NULL);
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la maintenance pour le véhicule " . $_GET['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la tache de maintenance.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>