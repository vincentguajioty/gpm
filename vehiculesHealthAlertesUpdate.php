<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if($_SESSION['vehiculeHealth_ajout']==0 OR $_SESSION['vehiculeHealth_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
    $query = $db->prepare('
        DELETE FROM
            VEHICULES_HEALTH_ALERTES
        WHERE
            idVehicule      = :idVehicule
        ;');
    $query->execute(array(
        'idVehicule'      => $_GET['idVehicule'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression des alertes de maintenance pour le véhicule " . $_GET['idVehicule'], '1', NULL);
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression des alertes de maintenance pour le véhicule " . $_GET['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\' enregistrement.';
            $_SESSION['returnType'] = '2';
            exit;
    }


    foreach ($_POST['formArray'] as $idVehicule => $types)
    {
        foreach ($types as $type => $frequence)
        {
            if(isset($frequence['frequenceHealth']) AND $frequence['frequenceHealth']!=Null AND $frequence['frequenceHealth'] >= 0)
            {
                $query = $db->prepare('
                    INSERT INTO
                        VEHICULES_HEALTH_ALERTES
                    SET
                        idVehicule      = :idVehicule,
                        idHealthType    = :idHealthType,
                        frequenceHealth = :frequenceHealth
                    ;');
                $query->execute(array(
                    'idVehicule'      => $_GET['idVehicule'],
                    'idHealthType'    => $type,
                    'frequenceHealth' => $frequence['frequenceHealth'],
                ));
                switch($query->errorCode())
                {
                    case '00000':
                        writeInLogs("Ajout de l'alerte de maintenance ".$type." pour le véhicule " . $_GET['idVehicule']." à une fréquence de ".$frequence['frequenceHealth']." jours", '1', NULL);
                        break;

                    default:
                        writeInLogs("Erreur inconnue lors de l'ajout de l'alerte de maintenance ".$type." pour le véhicule " . $_GET['idVehicule']." à une fréquence de ".$frequence['frequenceHealth']." jours", '3', NULL);
                }
            }
        }
    }

    checkOneMaintenance($_GET['idVehicule']);
    
    echo "<script>window.location = document.referrer;</script>";
}
?>