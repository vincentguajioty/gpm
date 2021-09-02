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
    $query = $db->prepare('SELECT * FROM VHF_EQUIPEMENTS WHERE idVhfEquipement = :idVhfEquipement;');
    $query->execute(array(
        'idVhfEquipement' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('SELECT * FROM DOCUMENTS_VHF WHERE idVhfEquipement = :idVhfEquipement;');
    $query->execute(array(
        'idVhfEquipement' => $_GET['id']
    ));
    while($data2 = $query->fetch())
    {
        unlink($data2['urlFichierDocVHF']);

        $query3 = $db->prepare('DELETE FROM DOCUMENTS_VHF WHERE idDocVHF = :idDocVHF;');
        $query3->execute(array(
            'idDocVHF' => $data2['idDocVHF']
        ));
    }

    $query = $db->prepare('DELETE FROM VHF_EQUIPEMENTS WHERE idVhfEquipement = :idVhfEquipement;');
    $query->execute(array(
        'idVhfEquipement' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'équipement radio " . $data['vhfIndicatif'], '1', NULL);
            $_SESSION['returnMessage'] = 'Equipement radio supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;


        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'équipement radio " . $data['vhfIndicatif'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression de l'équipement radio.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>