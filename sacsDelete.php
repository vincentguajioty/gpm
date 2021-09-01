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

if ($_SESSION['sac_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM MATERIEL_SAC WHERE idSac = :idSac;');
    $query->execute(array(
        'idSac' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE MATERIEL_EMPLACEMENT SET idSac = Null WHERE idSac = :idSac;');
    $query->execute(array(
        'idSac' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM MATERIEL_SAC WHERE idSac = :idSac;');
    $query->execute(array(
        'idSac' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du sac " . $data['libelleSac'], '4');
            $_SESSION['returnMessage'] = 'Sac supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du sac " . $data['libelleSac'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression du sac.";
            $_SESSION['returnType'] = '2';
    }
    
    checkAllConf();

    echo "<script>javascript:history.go(-1);</script>";
}
?>