<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['desinfections_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
	$_GET['idVehicule'] = ($_GET['idVehicule'] == Null) ? Null : $_GET['idVehicule'];
    $_POST['idExecutant'] = ($_POST['idExecutant'] == Null) ? Null : $_POST['idExecutant'];
    $_POST['idVehiculesDesinfectionsType'] = ($_POST['idVehiculesDesinfectionsType'] == Null) ? Null : $_POST['idVehiculesDesinfectionsType'];
	$_POST['dateDesinfection'] = ($_POST['dateDesinfection'] == Null) ? Null : $_POST['dateDesinfection'];
	
    $query = $db->prepare('
        INSERT INTO
            VEHICULES_DESINFECTIONS
        SET
            idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType,
            idVehicule                   = :idVehicule,
            dateDesinfection             = :dateDesinfection,
            idExecutant                  = :idExecutant,
            remarquesDesinfection        = :remarquesDesinfection
        ;');
    $query->execute(array(
        'idVehiculesDesinfectionsType' => $_POST['idVehiculesDesinfectionsType'],
        'idVehicule'                   => $_GET['idVehicule'],
        'dateDesinfection'             => $_POST['dateDesinfection'],
        'idExecutant'                  => $_POST['idExecutant'],
        'remarquesDesinfection'        => $_POST['remarquesDesinfection'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout d'une désinfection pour le véhicule " . $_GET['idVehicule'], '1', NULL);
            $_SESSION['returnMessage'] = 'Désinfection ajoutée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout d'une désinfection pour le véhicule " . $_GET['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de la désinfection.';
            $_SESSION['returnType'] = '2';
    }

    checkOneDesinfection($_GET['idVehicule']);

    echo "<script>window.location = document.referrer;</script>";
}
?>