<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vhf_plan_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idVhfCanal'] = ($_POST['idVhfCanal'] == Null) ? Null : $_POST['idVhfCanal'];


    $query = $db->prepare('INSERT INTO VHF_PLAN_CANAL(idVhfPlan, idVhfCanal, numeroCanal) VALUES(:idVhfPlan, :idVhfCanal, :numeroCanal);');
    $query->execute(array(
        'idVhfPlan' => $_GET['idVhfPlan'],
        'idVhfCanal' => $_POST['idVhfCanal'],
        'numeroCanal' => $_POST['numeroCanal']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du canal " . $_POST['idVhfCanal'].' au plan '.$_GET['idVhfPlan'], '2');
            $_SESSION['returnMessage'] = 'Canal ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du canal " . $_POST['idVhfCanal'].' au plan '.$_GET['idVhfPlan'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout du canal.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>