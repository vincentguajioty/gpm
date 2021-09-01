<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> 'CONFIRMATION')
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['vehicules_types_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query2 = $db->prepare('SELECT * FROM VEHICULES_TYPES WHERE idVehiculesType = :idVehiculesType ;');
    $query2->execute(array(
        'idVehiculesType' => $_GET['id']
    ));
    $data = $query2->fetch();

    $query = $db->prepare('UPDATE VEHICULES SET idVehiculesType = Null WHERE idVehiculesType = :idVehiculesType ;');
    $query->execute(array(
        'idVehiculesType' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM VEHICULES_TYPES WHERE idVehiculesType = :idVehiculesType;');
    $query->execute(array(
        'idVehiculesType' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du type de véhicule " . $data['libelleType'], '4');
            $_SESSION['returnMessage'] = 'Type supprimé avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du type de vehicule " . $data['libelleType'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression du type.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-1);</script>";
}
?>