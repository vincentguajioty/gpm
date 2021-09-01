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

if ($_SESSION['typesLots_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot;');
    $query->execute(array(
        'idTypeLot' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE LOTS_LOTS SET idTypeLot = Null WHERE idTypeLot = :idTypeLot ;');
    $query->execute(array(
        'idTypeLot' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM REFERENTIELS WHERE idTypeLot = :idTypeLot ;');
    $query->execute(array(
        'idTypeLot' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot;');
    $query->execute(array(
        'idTypeLot' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du référentiel " . $data['libelleTypeLot'], '4');
            $_SESSION['returnMessage'] = 'Référentiel supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du référentiel " . $data['libelleTypeLot'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression du référentiel.";
            $_SESSION['returnType'] = '2';
    }
    
    checkAllConf();

    echo "<script>javascript:history.go(-1);</script>";
}
?>