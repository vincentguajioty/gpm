<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['carburants_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('UPDATE CARBURANTS SET libelleCarburant = :libelleCarburant WHERE idCarburant = :idCarburant;');
    $query->execute(array(
        'libelleCarburant' => $_POST['libelleCarburant'],
		'idCarburant' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du carburant " . $_POST['libelleCarburant'], '1', NULL);
            $_SESSION['returnMessage'] = 'Carburant modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du carburant " . $_POST['libelleCarburant'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification du carburant.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>