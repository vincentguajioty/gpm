<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vehicules_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query2 = $db->prepare('SELECT * FROM VEHICULES WHERE idVehicule = :idVehicule ;');
    $query2->execute(array(
        'idVehicule' => $_GET['id']
    ));
    $data = $query2->fetch();
	
	$query = $db->prepare('UPDATE LOTS_LOTS SET idVehicule = Null WHERE idVehicule = :idVehicule;');
    $query->execute(array(
        'idVehicule' => $_GET['id']
    ));
    
    $query = $db->prepare('SELECT * FROM DOCUMENTS_VEHICULES WHERE idVehicule = :idVehicule;');
    $query->execute(array(
        'idVehicule' => $_GET['id']
    ));
    while($data2 = $query->fetch())
    {
        unlink($data2['urlFichierDocVehicule']);

        $query3 = $db->prepare('DELETE FROM DOCUMENTS_VEHICULES WHERE idDocVehicules = :idDocVehicules;');
        $query3->execute(array(
            'idDocVehicules' => $data2['idDocVehicules']
        ));
    }
    
    $query = $db->prepare('DELETE FROM VEHICULES_MAINTENANCE WHERE idVehicule = :idVehicule;');
    $query->execute(array(
        'idVehicule' => $_GET['id']
    ));
	
    $query = $db->prepare('DELETE FROM VEHICULES WHERE idVehicule = :idVehicule;');
    $query->execute(array(
        'idVehicule' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du véhicule " . $data['libelleVehicule'], '4');
            $_SESSION['returnMessage'] = 'Véhicule supprimé avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du vehicule " . $data['libelleVehicule'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression du véhicule.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-1);</script>";
}
?>