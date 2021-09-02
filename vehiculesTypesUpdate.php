<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vehicules_types_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('UPDATE VEHICULES_TYPES SET libelleType = :libelleType WHERE idVehiculesType = :idVehiculesType;');
    $query->execute(array(
        'libelleType' => $_POST['libelleType'],
		'idVehiculesType' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du type de véhicules de " . $_POST['libelleType'], '1', NULL);
            $_SESSION['returnMessage'] = 'Type modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du type de véhicule " . $_POST['libelleType'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification du type.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>