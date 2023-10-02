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

if ($_SESSION['desinfections_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query2 = $db->prepare('SELECT * FROM VEHICULES_DESINFECTIONS WHERE idVehiculesDesinfection = :idVehiculesDesinfection ;');
    $query2->execute(array(
        'idVehiculesDesinfection' => $_GET['id']
    ));
    $data = $query2->fetch();

    $query = $db->prepare('DELETE FROM VEHICULES_DESINFECTIONS WHERE idVehiculesDesinfection = :idVehiculesDesinfection;');
    $query->execute(array(
        'idVehiculesDesinfection' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression d'une désinfection pour le véhicule " . $data['idVehicule'], '1', NULL);
            $_SESSION['returnMessage'] = 'Désinfection supprimée avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression d'une désinfection pour le véhicule " . $data['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de la désinfection.';
            $_SESSION['returnType'] = '2';
    }

    checkOneDesinfection($data['idVehicule']);

    echo "<script>window.location = document.referrer;</script>";
}
?>