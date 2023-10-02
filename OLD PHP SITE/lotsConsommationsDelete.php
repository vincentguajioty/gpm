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

if ($_SESSION['consommationLots_supression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query = $db->prepare('DELETE FROM LOTS_CONSOMMATION_MATERIEL WHERE idConsommation = :idConsommation;');
    $query->execute(array(
        'idConsommation' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM LOTS_CONSOMMATION WHERE idConsommation = :idConsommation;');
    $query->execute(array(
        'idConsommation' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du rapport de consommation " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Rapport supprimé avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du rapport de consommation " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression du rapport de consommation.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>