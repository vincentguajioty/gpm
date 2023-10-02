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
    $_POST['idFournisseur'] = ($_POST['idFournisseur'] == Null) ? Null : $_POST['idFournisseur'];
	$_POST['idConteneur'] = ($_POST['idConteneur'] == Null) ? Null : $_POST['idConteneur'];
	$_POST['peremptionReserve'] = ($_POST['peremptionReserve'] == Null) ? Null : $_POST['peremptionReserve'];
	$_POST['peremptionReserveAnticipation'] = ($_POST['peremptionReserveAnticipation'] == Null) ? Null : $_POST['peremptionReserveAnticipation'];


    $query = $db->prepare('
        UPDATE
            RESERVES_MATERIEL
        SET
            idConteneur                   = :idConteneur,
            idFournisseur                 = :idFournisseur,
            quantiteReserve               = :quantiteReserve,
            quantiteAlerteReserve         = :quantiteAlerteReserve,
            peremptionReserve             = :peremptionReserve,
            peremptionReserveAnticipation = :peremptionReserveAnticipation,
            commentairesReserveElement    = :commentairesReserveElement
        WHERE
            idReserveElement              = :idReserveElement
        ;');
    $query->execute(array(
        'idConteneur'                   => $_POST['idConteneur'],
        'idFournisseur'                 => $_POST['idFournisseur'],
        'quantiteReserve'               => $_POST['quantiteReserve'],
        'quantiteAlerteReserve'         => $_POST['quantiteAlerteReserve'],
        'peremptionReserve'             => $_POST['peremptionReserve'],
        'peremptionReserveAnticipation' => $_POST['peremptionReserveAnticipation'],
        'commentairesReserveElement'    => $_POST['commentairesReserveElement'],
        'idReserveElement'              => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modificaiton du materiel de la réserve: " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Matériel modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du matériel de la réserve " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification du matériel.";
            $_SESSION['returnType'] = '2';
    }

    updatePeremptionsAnticipations();

    echo "<script>window.location = document.referrer;</script>";
}
?>