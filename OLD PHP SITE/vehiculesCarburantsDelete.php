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

if ($_SESSION['carburants_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query2 = $db->prepare('SELECT * FROM CARBURANTS WHERE idCarburant = :idCarburant ;');
    $query2->execute(array(
        'idCarburant' => $_GET['id']
    ));
    $data = $query2->fetch();

    $query = $db->prepare('UPDATE VEHICULES SET idCarburant = Null WHERE idCarburant = :idCarburant ;');
    $query->execute(array(
        'idCarburant' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM CARBURANTS WHERE idCarburant = :idCarburant;');
    $query->execute(array(
        'idCarburant' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du carburant " . $data['libelleType'], '1', NULL);
            $_SESSION['returnMessage'] = 'Carburant supprimé avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du carburant " . $data['libelleType'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression du carburant.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>