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

if ($_SESSION['lots_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM INVENTAIRES WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $_GET['id']
    ));
    while ($data = $query->fetch())
    {
        $query2 = $db->prepare('DELETE FROM INVENTAIRES_CONTENUS WHERE idInventaire = :idInventaire;');
        $query2->execute(array(
            'idInventaire' => $data['idInventaire']
        ));
    }
    $query2 = $db->prepare('DELETE FROM INVENTAIRES WHERE idLot = :idLot;');
    $query2->execute(array(
        'idLot' => $_GET['id']
    ));

    $query = $db->prepare('SELECT * FROM LOTS_LOTS WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE MATERIEL_SAC SET idLot = Null WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM LOTS_LOTS WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du lot " . $data['libelleLot'], '1', NULL);
            $_SESSION['returnMessage'] = 'Lot supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du lot " . $data['libelleLot'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression du lot.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>