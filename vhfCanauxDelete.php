<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> 'CONFIRMATION')
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['vhf_canal_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM VHF_CANAL WHERE idVhfCanal = :idVhfCanal;');
    $query->execute(array(
        'idVhfCanal' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('SELECT * FROM DOCUMENTS_CANAL_VHF WHERE idVhfCanal = :idVhfCanal;');
    $query->execute(array(
        'idVhfCanal' => $_GET['id']
    ));
    while($data2 = $query->fetch())
    {
        unlink($data2['urlFichierDocCanalVHF']);

        $query3 = $db->prepare('DELETE FROM DOCUMENTS_CANAL_VHF WHERE idDocCanalVHF = :idDocCanalVHF;');
        $query3->execute(array(
            'idDocCanalVHF' => $data2['idDocCanalVHF']
        ));
    }

    $query = $db->prepare('DELETE FROM VHF_PLAN_CANAL WHERE idVhfCanal = :idVhfCanal;');
    $query->execute(array(
        'idVhfCanal' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM VHF_CANAL WHERE idVhfCanal = :idVhfCanal;');
    $query->execute(array(
        'idVhfCanal' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du canal " . $data['chName'], '4');
            $_SESSION['returnMessage'] = 'Canal supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;


        default:
            writeInLogs("Erreur inconnue lors de la suppression du canal " . $data['chName'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression du canal.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>