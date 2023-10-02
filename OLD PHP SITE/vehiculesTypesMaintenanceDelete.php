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

if ($_SESSION['typesDesinfections_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query2 = $db->prepare('SELECT * FROM VEHICULES_HEALTH_TYPES WHERE idHealthType = :idHealthType ;');
    $query2->execute(array(
        'idHealthType' => $_GET['id']
    ));
    $data = $query2->fetch();

    $query = $db->prepare('DELETE FROM VEHICULES_HEALTH_CHECKS WHERE idHealthType = :idHealthType;');
    $query->execute(array(
        'idHealthType' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM VEHICULES_HEALTH_ALERTES WHERE idHealthType = :idHealthType;');
    $query->execute(array(
        'idHealthType' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM VEHICULES_HEALTH_TYPES WHERE idHealthType = :idHealthType;');
    $query->execute(array(
        'idHealthType' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du type de maintenance de véhicule " . $data['libelleHealthType'], '1', NULL);
            $_SESSION['returnMessage'] = 'Type supprimé avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du type de maintenance de vehicule " . $data['libelleHealthType'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression du type.';
            $_SESSION['returnType'] = '2';
    }

    checkAllDesinfection();

    echo "<script>window.location = document.referrer;</script>";
}
?>