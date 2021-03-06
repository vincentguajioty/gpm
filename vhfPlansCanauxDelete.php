<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> strtoupper($CONFSUPPRESSION))
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['vhf_plan_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('DELETE FROM VHF_PLAN_CANAL WHERE idVhfPlan = :idVhfPlan AND idVhfCanal = :idVhfCanal ;');
    $query->execute(array(
        'idVhfPlan' => $_GET['idVhfPlan'],
        'idVhfCanal' => $_GET['idVhfCanal']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du plan de fréquences " . $_GET['idVhfPlan'] . " avec suppression de la fréquence " . $_GET['idVhfCanal'], '1', NULL);
            $_SESSION['returnMessage'] = 'Fréquence retiré du plan avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du plan de fréquences " . $_GET['idVhfPlan'] . " avec retrait de la fréquence " . $_GET['idVhfCanal'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors du retrait de la fréquence.";
            $_SESSION['returnType'] = '2';
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>