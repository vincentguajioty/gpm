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

    $query = $db->prepare('INSERT INTO CARBURANTS SET libelleCarburant = :libelleCarburant;');
    $query->execute(array(
        'libelleCarburant' => $_POST['libelleCarburant']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du carburant: " . $_POST['libelleCarburant'], '1', NULL);
            $_SESSION['returnMessage'] = 'Carburant avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du carburant " . $_POST['libelleCarburant'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout du carburant.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>