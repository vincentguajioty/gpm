<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vehicules_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
	$_POST['idPersonne'] = ($_POST['idPersonne'] == Null) ? Null : $_POST['idPersonne'];
	$_POST['dateReleve'] = ($_POST['dateReleve'] == Null) ? Null : $_POST['dateReleve'];
	
    $query = $db->prepare('
        UPDATE
            VEHICULES_RELEVES
        SET
            idPersonne         = :idPersonne,
            dateReleve         = :dateReleve,
            releveKilometrique = :releveKilometrique
        WHERE
            idReleve           = :idReleve
        ;');
    $query->execute(array(
        'idReleve'           => $_GET['id'],
        'idPersonne'         => $_POST['idPersonne'],
        'dateReleve'         => $_POST['dateReleve'],
        'releveKilometrique' => $_POST['releveKilometrique']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification d'un relevé kilométrique pour le véhicule " . $_GET['idVehicule'], '1', NULL);
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du relevé kilométrique pour le véhicule " . $_GET['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification du relevé kilométrique.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>