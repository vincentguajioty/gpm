<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['typesDesinfections_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('UPDATE VEHICULES_DESINFECTIONS_TYPES SET libelleVehiculesDesinfectionsType = :libelleVehiculesDesinfectionsType WHERE idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType;');
    $query->execute(array(
        'libelleVehiculesDesinfectionsType' => $_POST['libelleVehiculesDesinfectionsType'],
        'idVehiculesDesinfectionsType' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du type de désinfections de véhicules de " . $_POST['libelleVehiculesDesinfectionsType'], '1', NULL);
            $_SESSION['returnMessage'] = 'Type modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du type de désinfections de véhicule " . $_POST['libelleVehiculesDesinfectionsType'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification du type..';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>