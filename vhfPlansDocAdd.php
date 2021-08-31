<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_plan_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $_POST['idTypeDocument'] = ($_POST['idTypeDocument'] == Null) ? Null : $_POST['idTypeDocument'];

    $ext = strtolower(substr(strrchr($_FILES['urlFichierDocPlanVHF']['name'], '.'), 1));

    $query = $db->prepare('INSERT INTO DOCUMENTS_PLAN_VHF(idVhfPlan, nomDocPlanVHF, formatDocPlanVHF, dateDocPlanVHF, urlFichierDocPlanVHF, idTypeDocument) VALUES(:idVhfPlan, :nomDocPlanVHF, :formatDocPlanVHF, CURRENT_TIMESTAMP, :urlFichierDocPlanVHF, :idTypeDocument);');
    $query->execute(array(
        'idVhfPlan' => $_GET['idVhfPlan'],
        'urlFichierDocPlanVHF' => Null,
        'nomDocPlanVHF' => $_POST['nomDocPlanVHF'],
        'formatDocPlanVHF' => $ext,
        'idTypeDocument' => $_POST['idTypeDocument']
    ));


    $query = $db->query('SELECT MAX(idDocPlanVHF) as idDocPlanVHF FROM DOCUMENTS_PLAN_VHF;');
    $data = $query->fetch();

    $nom = "documents/vhfPlans/{$data['idDocPlanVHF']}.{$ext}";

    $resultat = move_uploaded_file($_FILES['urlFichierDocPlanVHF']['tmp_name'], $nom);
    if ($resultat) {
        $query = $db->prepare('UPDATE DOCUMENTS_PLAN_VHF SET urlFichierDocPlanVHF = :urlFichierDocPlanVHF WHERE idDocPlanVHF = :idDocPlanVHF;');
        $query->execute(array(
            'idDocPlanVHF' => $data['idDocPlanVHF'],
            'urlFichierDocPlanVHF' => $nom
        ));
        writeInLogs("Ajout d'une pièce jointe référence " . $_POST['nomDocPlanVHF'] . " au plan radio " . $_GET['idVhfPlan'], '2');
    } else {
        $query = $db->prepare('DELETE FROM DOCUMENTS_PLAN_VHF WHERE idDocPlanVHF = :idDocPlanVHF;');
        $query->execute(array(
            'idDocPlanVHF' => $data['idDocPlanVHF']
        ));
        writeInLogs("Erreur inconnue lors de l'ajout d'une pièce jointe au plan radio " . $_GET['idVhfPlan'], '5');
        $_SESSION['returnMessage'] = "Erreur inconnue lors du chargement du document.";
        $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>