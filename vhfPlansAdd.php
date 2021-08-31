<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vhf_plan_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('INSERT INTO VHF_PLAN(libellePlan, remarquesPlan) VALUES(:libellePlan, :remarquesPlan);');
    $query->execute(array(
        'libellePlan' => $_POST['libellePlan'],
        'remarquesPlan' => $_POST['remarquesPlan']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du plan " . $_POST['libellePlan'], '2');
            $_SESSION['returnMessage'] = 'Plan ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du plan " . $_POST['libellePlan'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout du plan.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>