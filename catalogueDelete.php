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

if ($_SESSION['catalogue_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['id']
    ));
    $data = $query->fetch();

	$query = $db->prepare('DELETE FROM COMMANDES_MATERIEL WHERE idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM MATERIEL_ELEMENT WHERE idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM REFERENTIELS WHERE idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM INVENTAIRES_CONTENUS WHERE idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM RESERVES_MATERIEL WHERE idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du catalogue de " . $data['libelleMateriel'], '1', NULL);
            $_SESSION['returnMessage'] = 'Item supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du catalogue de " . $data['libelleMateriel'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la suppression.';
            $_SESSION['returnType'] = '2';
    }

	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";
}
?>