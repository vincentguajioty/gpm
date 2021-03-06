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

if ($_SESSION['sac2_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT WHERE idEmplacement = :idEmplacement;');
    $query->execute(array(
        'idEmplacement' => $_GET['id']
    ));
    $data = $query ->fetch();

    $query = $db->prepare('UPDATE MATERIEL_ELEMENT SET idEmplacement = Null WHERE idEmplacement = :idEmplacement;');
    $query->execute(array(
        'idEmplacement' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE LOTS_CONSOMMATION_MATERIEL SET idEmplacement = Null WHERE idEmplacement = :idEmplacement;');
    $query->execute(array(
        'idEmplacement' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM MATERIEL_EMPLACEMENT WHERE idEmplacement = :idEmplacement;');
    $query->execute(array(
        'idEmplacement' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'emplacement " . $data['libelleEmplacement'], '1', NULL);
            $_SESSION['returnMessage'] = 'Emplacement supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'emplacement " . $data['libelleEmplacement'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression de l'emplacement.";
            $_SESSION['returnType'] = '2';
    }
    
	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";
}
?>