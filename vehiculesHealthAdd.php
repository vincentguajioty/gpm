<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vehiculeHealth_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
	$_GET['idVehicule'] = ($_GET['idVehicule'] == Null) ? Null : $_GET['idVehicule'];
    $_POST['idPersonne'] = ($_POST['idPersonne'] == Null) ? Null : $_POST['idPersonne'];
	$_POST['dateHealth'] = ($_POST['dateHealth'] == Null) ? Null : $_POST['dateHealth'];
	
    $query = $db->prepare('
        INSERT INTO
            VEHICULES_HEALTH
        SET
            idVehicule = :idVehicule,
            dateHealth = :dateHealth,
            idPersonne = :idPersonne
        ;');
    $query->execute(array(
        'idVehicule' => $_GET['idVehicule'],
        'dateHealth' => $_POST['dateHealth'],
        'idPersonne' => $_POST['idPersonne'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout d'une maintenance pour le véhicule " . $_GET['idVehicule'], '1', NULL);
            $_SESSION['returnMessage'] = 'Maintenance ajoutée avec succès.';
            $_SESSION['returnType'] = '1';

            $idVehiculeHealth = $db->query('SELECT MAX(idVehiculeHealth) as idVehiculeHealth FROM VEHICULES_HEALTH;');
            $idVehiculeHealth = $idVehiculeHealth->fetch();
            $idVehiculeHealth = $idVehiculeHealth['idVehiculeHealth'];

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
                            'idVehiculeHealth' => $idVehiculeHealth,
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
            writeInLogs("Erreur inconnue lors de l'ajout d'une maintenance pour le véhicule " . $_GET['idVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de la maintenance.';
            $_SESSION['returnType'] = '2';
    }

    checkOneMaintenance($_GET['idVehicule']);

    echo "<script>window.location = document.referrer;</script>";
}
?>