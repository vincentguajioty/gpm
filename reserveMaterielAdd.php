<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['reserve_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idMaterielCatalogue'] = ($_POST['idMaterielCatalogue'] == Null) ? Null : $_POST['idMaterielCatalogue'];
    $_POST['idFournisseur'] = ($_POST['idFournisseur'] == Null) ? Null : $_POST['idFournisseur'];
	$_POST['idConteneur'] = ($_POST['idConteneur'] == Null) ? Null : $_POST['idConteneur'];
	$_POST['peremptionReserve'] = ($_POST['peremptionReserve'] == Null) ? Null : $_POST['peremptionReserve'];
	$_POST['peremptionNotificationReserve'] = ($_POST['peremptionNotificationReserve'] == Null) ? Null : $_POST['peremptionNotificationReserve'];

    $query = $db->prepare('INSERT INTO RESERVES_MATERIEL(idMaterielCatalogue, idConteneur, idFournisseur, quantiteReserve, quantiteAlerteReserve, peremptionReserve, peremptionNotificationReserve, commentairesReserveElement)VALUES(:idMaterielCatalogue, :idConteneur, :idFournisseur, :quantiteReserve, :quantiteAlerteReserve, :peremptionReserve, :peremptionNotificationReserve, :commentairesReserveElement);');
    $query->execute(array(
        'idMaterielCatalogue' => $_POST['idMaterielCatalogue'],
        'idConteneur' => $_POST['idConteneur'],
        'idFournisseur' => $_POST['idFournisseur'],
        'quantiteReserve' => $_POST['quantiteReserve'],
        'quantiteAlerteReserve' => $_POST['quantiteAlerteReserve'],
        'peremptionReserve' => $_POST['peremptionReserve'],
        'peremptionNotificationReserve' => $_POST['peremptionNotificationReserve'],
        'commentairesReserveElement' => $_POST['commentairesReserveElement']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du materiel à la réserve: " . $_POST['idMaterielCatalogue'], '2');
            $_SESSION['returnMessage'] = 'Matériel ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du matériel à la réserve " . $_POST['idMaterielCatalogue'], '5');
            $_SESSION['returnMessage'] = 'Un matériel existe déjà dans ce conteneur. Au lieu d\'ajouter à nouveau le matériel, veuillez changer sa quantité.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du matériel à la réserve " . $_POST['idMaterielCatalogue'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du matériel.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>