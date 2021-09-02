<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['reserve_modification']==0)
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


    $query = $db->prepare('
        UPDATE
            RESERVES_MATERIEL
        SET
            idMaterielCatalogue           = :idMaterielCatalogue,
            idConteneur                   = :idConteneur,
            idFournisseur                 = :idFournisseur,
            quantiteReserve               = :quantiteReserve,
            quantiteAlerteReserve         = :quantiteAlerteReserve,
            peremptionReserve             = :peremptionReserve,
            peremptionNotificationReserve = :peremptionNotificationReserve,
            commentairesReserveElement    = :commentairesReserveElement
        WHERE
            idReserveElement              = :idReserveElement
        ;');
    $query->execute(array(
        'idMaterielCatalogue'           => $_POST['idMaterielCatalogue'],
        'idConteneur'                   => $_POST['idConteneur'],
        'idFournisseur'                 => $_POST['idFournisseur'],
        'quantiteReserve'               => $_POST['quantiteReserve'],
        'quantiteAlerteReserve'         => $_POST['quantiteAlerteReserve'],
        'peremptionReserve'             => $_POST['peremptionReserve'],
        'peremptionNotificationReserve' => $_POST['peremptionNotificationReserve'],
        'commentairesReserveElement'    => $_POST['commentairesReserveElement'],
        'idReserveElement'              => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modificaiton du materiel de la réserve: " . $_POST['idMaterielCatalogue'], '1', NULL);
            $_SESSION['returnMessage'] = 'Matériel modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du matériel de la réserve " . $_POST['idMaterielCatalogue'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification du matériel.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>