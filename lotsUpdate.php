<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['lots_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $_POST['libelleTypeLot'] = ($_POST['libelleTypeLot'] == Null) ? Null : $_POST['libelleTypeLot'];
	$_POST['libelleEtat'] = ($_POST['libelleEtat'] == Null) ? Null : $_POST['libelleEtat'];
	$_POST['libelleLieu'] = ($_POST['libelleLieu'] == Null) ? Null : $_POST['libelleLieu'];
	$_POST['identifiant'] = ($_POST['identifiant'] == Null) ? Null : $_POST['identifiant'];
	$_POST['idVehicule'] = ($_POST['idVehicule'] == Null) ? Null : $_POST['idVehicule'];
    $_POST['idLotsEtat'] = ($_POST['idLotsEtat'] == Null) ? Null : $_POST['idLotsEtat'];

    $query = $db->prepare('
        UPDATE
            LOTS_LOTS
        SET
            libelleLot            = :libelleLot,
            idTypeLot             = :idTypeLot,
            idEtat                = :idEtat,
            idLieu                = :idLieu,
            idPersonne            = :idPersonne,
            dateDernierInventaire = :dateDernierInventaire,
            frequenceInventaire   = :frequenceInventaire,
            commentairesLots      = :commentairesLots,
            idVehicule            = :idVehicule,
            idLotsEtat            = :idLotsEtat
        WHERE
            idLot                 = :idLot
        ;');
    $query->execute(array(
        'idLot'                 => $_GET['id'],
        'libelleLot'            => $_POST['libelleLot'],
        'idTypeLot'             => $_POST['libelleTypeLot'],
        'idEtat'                => $_POST['libelleEtat'],
        'idLieu'                => $_POST['libelleLieu'],
        'idPersonne'            => $_POST['identifiant'],
        'dateDernierInventaire' => $_POST['dateDernierInventaire'],
        'frequenceInventaire'   => $_POST['frequenceInventaire'],
        'commentairesLots'      => $_POST['commentairesLots'],
        'idVehicule'            => $_POST['idVehicule'],
        'idLotsEtat'            => $_POST['idLotsEtat']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du lot " . $_POST['libelleLot'], '1', NULL);
            $_SESSION['returnMessage'] = 'Lot modifi?? avec succ??s.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon d??tect?? lors de la modification du lot " . $_POST['libelleLot'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un lot existe d??j?? avec le m??me libell??, stock?? au m??me lieu, et d??pendant du m??me r??f??rentiel. Merci de corriger le formulaire.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du lot " . $_POST['libelleLot'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification du lot.";
            $_SESSION['returnType'] = '2';
    }

	checkOneConf($_GET['id']);
    echo "<script>window.location = document.referrer;</script>";
}
?>