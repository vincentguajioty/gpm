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

    $query = $db->prepare('
        UPDATE
            VHF_PLAN
        SET
            libellePlan   = :libellePlan,
            remarquesPlan = :remarquesPlan
        WHERE
            idVhfPlan     = :idVhfPlan
        ;');
    $query->execute(array(
        'libellePlan'   => $_POST['libellePlan'],
        'remarquesPlan' => $_POST['remarquesPlan'],
        'idVhfPlan'     => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du plan " . $_POST['libellePlan'], '1', NULL);
            $_SESSION['returnMessage'] = 'Plan modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du plan " . $_POST['libellePlan'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du plan.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>