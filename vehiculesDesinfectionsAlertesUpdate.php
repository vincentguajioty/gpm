<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if($_SESSION['desinfections_ajout']==0 OR $_SESSION['desinfections_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
    $query = $db->prepare('
        DELETE FROM
            VEHICULES_DESINFECTIONS_ALERTES
        WHERE
            idVehicule      = :idVehicule
        ;');
    $query->execute(array(
        'idVehicule'      => $_GET['idVehicule'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression des alertes de désinfection pour le véhicule " . $_GET['idVehicule'], '1', NULL);
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression des alertes de désinfection pour le véhicule " . $_GET['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\' enregistrement.';
            $_SESSION['returnType'] = '2';
            exit;
    }


    foreach ($_POST['formArray'] as $idVehicule => $typeDesinfs)
    {
        foreach ($typeDesinfs as $typeDesinf => $frequence)
        {
            if(isset($frequence['frequenceDesinfection']) AND $frequence['frequenceDesinfection']!=Null AND $frequence['frequenceDesinfection'] >= 0)
            {
                $query = $db->prepare('
                    INSERT INTO
                        VEHICULES_DESINFECTIONS_ALERTES
                    SET
                        idVehicule                   = :idVehicule,
                        idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType,
                        frequenceDesinfection        = :frequenceDesinfection
                    ;');
                $query->execute(array(
                    'idVehicule'      => $_GET['idVehicule'],
                    'idVehiculesDesinfectionsType' => $typeDesinf,
                    'frequenceDesinfection' => $frequence['frequenceDesinfection'],
                ));
                switch($query->errorCode())
                {
                    case '00000':
                        writeInLogs("Ajout de l'alerte de désinfection ".$typeDesinf." pour le véhicule " . $_GET['idVehicule']." à une fréquence de ".$frequence['frequenceDesinfection']." jours", '1', NULL);
                        break;

                    default:
                        writeInLogs("Erreur inconnue lors de l'ajout de l'alerte de désinfection ".$typeDesinf." pour le véhicule " . $_GET['idVehicule']." à une fréquence de ".$frequence['frequenceDesinfection']." jours", '3', NULL);
                }
            }
        }
    }

    checkOneDesinfection($_GET['idVehicule']);
    
    echo "<script>window.location = document.referrer;</script>";
}
?>