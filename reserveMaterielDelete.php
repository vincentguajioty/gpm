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

if ($_SESSION['reserve_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM RESERVES_MATERIEL WHERE idReserveElement = :idReserveElement;');
    $query->execute(array(
        'idReserveElement' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('DELETE FROM RESERVES_MATERIEL WHERE idReserveElement = :idReserveElement;');
    $query->execute(array(
        'idReserveElement' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'élément de réserve " . $data['idReserveElement'], '4');
            $_SESSION['returnMessage'] = 'Element de réserve supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;


        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'élément de réserve " . $data['idReserveElement'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression de l'élément de réserve.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>