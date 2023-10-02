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

if ($_SESSION['tenuesCatalogue_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query2 = $db->prepare('SELECT * FROM TENUES_CATALOGUE WHERE idCatalogueTenue = :idCatalogueTenue ;');
    $query2->execute(array(
        'idCatalogueTenue' => $_GET['id']
    ));
    $data = $query2->fetch();


    $query = $db->prepare('DELETE FROM TENUES_AFFECTATION WHERE idCatalogueTenue = :idCatalogueTenue');
    $query->execute([
        ':idCatalogueTenue' => $_GET['id']
    ]);

    $query = $db->prepare('DELETE FROM TENUES_CATALOGUE WHERE idCatalogueTenue = :idCatalogueTenue;');
    $query->execute(array(
        'idCatalogueTenue' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du catalogue des tenues: " . $data['libelleCatalogueTenue'], '1', NULL);
            $_SESSION['returnMessage'] = 'Entrée supprimée avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du catalogue des tenues: " . $data['libelleCatalogueTenue'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de l\'entrée.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>