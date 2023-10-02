<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['typesDesinfections_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $_POST['affichageSynthese'] = ($_POST['affichageSynthese'] == 1) ? 1 : 0;
    
    $query = $db->prepare('INSERT INTO VEHICULES_DESINFECTIONS_TYPES SET libelleVehiculesDesinfectionsType = :libelleVehiculesDesinfectionsType, affichageSynthese = :affichageSynthese;');
    $query->execute(array(
        'libelleVehiculesDesinfectionsType' => $_POST['libelleVehiculesDesinfectionsType'],
        'affichageSynthese' => $_POST['affichageSynthese']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du type de désinfections de véhicules de " . $_POST['libelleVehiculesDesinfectionsType'], '1', NULL);
            $_SESSION['returnMessage'] = 'Type ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du type de désinfection de véhicule " . $_POST['libelleVehiculesDesinfectionsType'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout du type.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>