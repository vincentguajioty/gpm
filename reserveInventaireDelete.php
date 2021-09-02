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

if ($_SESSION['reserve_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM RESERVES_INVENTAIRES i LEFT OUTER JOIN RESERVES_CONTENEUR c ON i.idConteneur = c.idConteneur WHERE idReserveInventaire = :idReserveInventaire;');
    $query->execute(array(
        'idReserveInventaire' => $_GET['id']
    ));
    $data = $query -> fetch();

    $query = $db->prepare('DELETE FROM RESERVES_INVENTAIRES_CONTENUS WHERE idReserveInventaire = :idReserveInventaire;');
    $query->execute(array(
        'idReserveInventaire' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM RESERVES_INVENTAIRES WHERE idReserveInventaire = :idReserveInventaire;');
    $query->execute(array(
        'idReserveInventaire' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'inventaire du conteneur de réserve " . $data['libelleConteneur'] . " réalisé en date du " . $data['dateInventaire'], '1', NULL);
            $_SESSION['returnMessage'] = 'Inventaire supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'inventaire du lot " . $data['libelleConteneur'] . " réalisé en date du " . $data['dateInventaire'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression de l'inventaire.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>