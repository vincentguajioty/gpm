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

if ($_SESSION['lieux_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE COMMANDES SET idCentreDeCout = Null WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du centre de couts " . $data['libelleCentreDecout'], '4');
            $_SESSION['returnMessage'] = 'Centre de couts supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du centre de couts " . $data['libelleCentreDecout'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression du centre de couts.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>