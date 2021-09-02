<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['desinfections_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
    $vehicule = $db->prepare('SELECT idVehicule FROM VEHICULES_DESINFECTIONS WHERE idVehiculesDesinfection = :idVehiculesDesinfection;');
    $vehicule->execute(array(
        'idVehiculesDesinfection' => $_GET['id'],
    ));
    $vehicule = $vehicule->fetch();

    $_POST['idExecutant'] = ($_POST['idExecutant'] == Null) ? Null : $_POST['idExecutant'];
    $_POST['idVehiculesDesinfectionsType'] = ($_POST['idVehiculesDesinfectionsType'] == Null) ? Null : $_POST['idVehiculesDesinfectionsType'];
	$_POST['dateDesinfection'] = ($_POST['dateDesinfection'] == Null) ? Null : $_POST['dateDesinfection'];
	
    $query = $db->prepare('
        UPDATE
            VEHICULES_DESINFECTIONS
        SET
            idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType,
            dateDesinfection             = :dateDesinfection,
            idExecutant                  = :idExecutant,
            remarquesDesinfection        = :remarquesDesinfection
        WHERE
            idVehiculesDesinfection      = :idVehiculesDesinfection
        ;');
    $query->execute(array(
        'idVehiculesDesinfectionsType' => $_POST['idVehiculesDesinfectionsType'],
        'dateDesinfection'             => $_POST['dateDesinfection'],
        'idExecutant'                  => $_POST['idExecutant'],
        'remarquesDesinfection'        => $_POST['remarquesDesinfection'],
        'idVehiculesDesinfection'      => $_GET['id'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification d'une désinfection pour le véhicule " . $vehicule['idVehicule'], '1', NULL);
            $_SESSION['returnMessage'] = 'Désinfection modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la désinfection " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la désinfection.';
            $_SESSION['returnType'] = '2';
    }

    checkOneDesinfection($vehicule['idVehicule']);
    
    echo "<script>window.location = document.referrer;</script>";
}
?>