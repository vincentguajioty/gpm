<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['etats_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('UPDATE VEHICULES_ETATS SET libelleVehiculesEtat = :libelleVehiculesEtat WHERE idVehiculesEtat = :idVehiculesEtat;');
    $query->execute(array(
        'libelleVehiculesEtat' => $_POST['libelleVehiculesEtat'],
        'idVehiculesEtat' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de l'état de véhicule " . $_POST['libelleVehiculesEtat'], '3');
            $_SESSION['returnMessage'] = 'Etat modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de l'état de véhicule " . $_POST['libelleVehiculesEtat'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de l\'état.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>