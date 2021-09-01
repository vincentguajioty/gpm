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

if ($_SESSION['typesLots_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot;');
    $query->execute(array(
        'idTypeLot' => $_GET['idLot']
    ));
    $data2 = $query->fetch();

    $query = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['idMateriel']
    ));
    $data3 = $query->fetch();

    $query = $db->prepare('DELETE FROM REFERENTIELS WHERE idMaterielCatalogue = :idMaterielCatalogue AND idTypeLot = :idTypeLot ;');
    $query->execute(array(
        'idMaterielCatalogue' => $_GET['idMateriel'],
        'idTypeLot' => $_GET['idLot']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du référentiel " . $data2['libelleTypeLot'] . " avec suppression du materiel " . $data3['libelleMateriel'], '3');
            $_SESSION['returnMessage'] = 'Item retiré du référentiel avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du référentiel " . $data2['libelleTypeLot'] . " avec retrait du materiel " . $data3['libelleMateriel'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors du retrait de l'item.";
            $_SESSION['returnType'] = '2';
    }

	checkAllConf();
	
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>