<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['lots_ajout']==0)
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

    $query = $db->prepare('INSERT INTO LOTS_LOTS(libelleLot, idTypeLot, idEtat, idLieu, idPersonne, dateDernierInventaire, frequenceInventaire, commentairesLots, idVehicule, idLotsEtat) VALUES(:libelleLot, :idTypeLot, :idEtat, :idLieu, :idPersonne, :dateDernierInventaire, :frequenceInventaire, :commentairesLots, :idVehicule, :idLotsEtat);');
    $query->execute(array(
        'libelleLot' => $_POST['libelleLot'],
        'idTypeLot' => $_POST['libelleTypeLot'],
        'idEtat' => $_POST['libelleEtat'],
        'idLieu' => $_POST['libelleLieu'],
        'idPersonne' => $_POST['identifiant'],
        'dateDernierInventaire' => $_POST['dateDernierInventaire'],
        'frequenceInventaire' => $_POST['frequenceInventaire'],
        'commentairesLots' => $_POST['commentairesLots'],
        'idVehicule' => $_POST['idVehicule'],
        'idLotsEtat' => $_POST['idLotsEtat']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du lot " . $_POST['libelleLot'], '2');
            $_SESSION['returnMessage'] = 'Lot ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du lot " . $_POST['libelleLot'], '5');
            $_SESSION['returnMessage'] = 'Un lot existe déjà avec le même libellé, stocké au même lieu, et dépendant du même référentiel. Merci de corriger le formulaire.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du lot " . $_POST['libelleLot'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du lot.";
            $_SESSION['returnType'] = '2';
    }

	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";
}
?>