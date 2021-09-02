<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vehiculeHealth_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
    $_POST['idPersonne'] = ($_POST['idPersonne'] == Null) ? Null : $_POST['idPersonne'];
	$_POST['dateHealth'] = ($_POST['dateHealth'] == Null) ? Null : $_POST['dateHealth'];
	
    $query = $db->prepare('
        UPDATE
            VEHICULES_HEALTH
        SET
            dateHealth = :dateHealth,
            idPersonne = :idPersonne
        WHERE
            idVehiculeHealth = :idVehiculeHealth
        ;');
    $query->execute(array(
        'idVehiculeHealth' => $_GET['id'],
        'dateHealth' => $_POST['dateHealth'],
        'idPersonne' => $_POST['idPersonne'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la maintenance " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Maintenance modifiée avec succès.';
            $_SESSION['returnType'] = '1';

            $delete = $db->prepare('DELETE FROM VEHICULES_HEALTH_CHECKS WHERE idVehiculeHealth = :idVehiculeHealth;');
            $delete->execute(array('idVehiculeHealth'=>$_GET['id']));

            switch($delete->errorCode())
            {
                case '00000':
                    foreach ($_POST['formArray'] as $idVehicule => $types)
                    {
                        foreach($types as $idHealthType => $tache)
                        {
                            if($tache['done'])
                            {
                                writeInLogs("Ajout pour le vehicule " . $idVehicule . " de la réalisation de la tache de maintenance " . $idHealthType . " - rattaché à l'opération de maintenance " . $idVehiculeHealth, '1', NULL);
                                $insert = $db->prepare('
                                    INSERT INTO
                                        VEHICULES_HEALTH_CHECKS
                                    SET
                                        idVehiculeHealth = :idVehiculeHealth,
                                        idHealthType     = :idHealthType,
                                        remarquesCheck   = :remarquesCheck
                                    ;');
                                $insert->execute(array(
                                    'idVehiculeHealth' => $_GET['id'],
                                    'idHealthType'     => $idHealthType,
                                    'remarquesCheck'   => $tache['remarquesCheck'],
                                ));
                            }
                            else
                            {
                                writeInLogs("Non-ajout pour le vehicule " . $idVehicule . " de la réalisation de la tache de maintenance " . $idHealthType . " - rattaché à l'opération de maintenance " . $idVehiculeHealth . " car la case de réalisation n'est pas cochée", '1', NULL);
                            }
                        }
                    }

                break;

                default:
                    writeInLogs("Erreur inconnue lors du nettoyage des taches de maintenance pour la maintenance " . $_GET['id'], '3', NULL);
            }

            
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout d'une maintenance pour le véhicule " . $_GET['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de la maintenance.';
            $_SESSION['returnType'] = '2';
    }

    checkOneMaintenance($_GET['idVehicule']);

    echo "<script>window.location = document.referrer;</script>";
}
?>