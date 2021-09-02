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

    $query2 = $db->prepare('SELECT * FROM VEHICULES_DESINFECTIONS_TYPES WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType ;');
    $query2->execute(array(
        'idVehiculesDesinfectionsType' => $_GET['id']
    ));
    $data = $query2->fetch();

    $query = $db->prepare('DELETE FROM VEHICULES_DESINFECTIONS WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType;');
    $query->execute(array(
        'idVehiculesDesinfectionsType' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM VEHICULES_DESINFECTIONS_ALERTES WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType;');
    $query->execute(array(
        'idVehiculesDesinfectionsType' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM VEHICULES_DESINFECTIONS_TYPES WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType;');
    $query->execute(array(
        'idVehiculesDesinfectionsType' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du type de désinfection de véhicule " . $data['libelleType'], '1', NULL);
            $_SESSION['returnMessage'] = 'Type supprimé avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du type de désinfection de vehicule " . $data['libelleType'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression du type.';
            $_SESSION['returnType'] = '2';
    }

    checkAllDesinfection();

    echo "<script>javascript:history.go(-1);</script>";
}
?>