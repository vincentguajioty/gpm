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

if ($_SESSION['vhf_plan_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM VHF_PLAN WHERE idVhfPlan = :idVhfPlan;');
    $query->execute(array(
        'idVhfPlan' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('SELECT * FROM DOCUMENTS_PLAN_VHF WHERE idVhfPlan = :idVhfPlan;');
    $query->execute(array(
        'idVhfPlan' => $_GET['id']
    ));
    while($data2 = $query->fetch())
    {
        unlink($data2['urlFichierDocPlanVHF']);

        $query3 = $db->prepare('DELETE FROM DOCUMENTS_PLAN_VHF WHERE idDocPlanVHF = :idDocPlanVHF;');
        $query3->execute(array(
            'idDocPlanVHF' => $data2['idDocPlanVHF']
        ));
    }

    $query = $db->prepare('DELETE FROM VHF_PLAN_CANAL WHERE idVhfPlan = :idVhfPlan;');
    $query->execute(array(
        'idVhfPlan' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE VHF_EQUIPEMENTS SET idVhfPlan = Null WHERE idVhfPlan = :idVhfPlan;');
    $query->execute(array(
        'idVhfPlan' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM VHF_PLAN WHERE idVhfPlan = :idVhfPlan;');
    $query->execute(array(
        'idVhfPlan' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du plan radio " . $data['libellePlan'], '1', NULL);
            $_SESSION['returnMessage'] = 'Plan radio supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;


        default:
            writeInLogs("Erreur inconnue lors de la suppression du plan radio " . $data['libellePlan'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression du plan radio.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>