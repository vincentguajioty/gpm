<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['etats_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('
        INSERT INTO
            VEHICULES_ETATS
        SET
            libelleVehiculesEtat = :libelleVehiculesEtat
        ;');
    $query->execute(array(
        'libelleVehiculesEtat' => $_POST['libelleVehiculesEtat']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'état de véhicule " . $_POST['libelleVehiculesEtat'], '1', NULL);
            $_SESSION['returnMessage'] = 'Etat ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'état de véhicule " . $_POST['libelleVehiculesEtat'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de l\'état de véhicule.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>