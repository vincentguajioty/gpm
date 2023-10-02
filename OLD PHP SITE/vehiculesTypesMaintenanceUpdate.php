<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vehiculeHealthType_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

	$_POST['affichageSynthese'] = ($_POST['affichageSynthese'] == 1) ? 1 : 0;
	
    $query = $db->prepare('UPDATE VEHICULES_HEALTH_TYPES SET libelleHealthType = :libelleHealthType, affichageSynthese = :affichageSynthese WHERE idHealthType = :idHealthType;');
    $query->execute(array(
        'libelleHealthType' => $_POST['libelleHealthType'],
        'affichageSynthese' => $_POST['affichageSynthese'],
        'idHealthType'      => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du type de maintenance de véhicules de " . $_POST['libelleHealthType'], '1', NULL);
            $_SESSION['returnMessage'] = 'Type modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du type de maintenance de véhicule " . $_POST['libelleHealthType'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification du type.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>