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
	$_POST['peremptionReserveAnticipation'] = ($_POST['peremptionReserveAnticipation'] == Null) ? Null : $_POST['peremptionReserveAnticipation'];

    $query = $db->prepare('
        INSERT INTO
            RESERVES_MATERIEL
        SET
            idMaterielCatalogue           = :idMaterielCatalogue,
            idConteneur                   = :idConteneur,
            idFournisseur                 = :idFournisseur,
            quantiteReserve               = :quantiteReserve,
            quantiteAlerteReserve         = :quantiteAlerteReserve,
            peremptionReserve             = :peremptionReserve,
            peremptionReserveAnticipation = :peremptionReserveAnticipation,
            commentairesReserveElement    = :commentairesReserveElement
        ;');
    $query->execute(array(
        'idMaterielCatalogue'           => $_POST['idMaterielCatalogue'],
        'idConteneur'                   => $_POST['idConteneur'],
        'idFournisseur'                 => $_POST['idFournisseur'],
        'quantiteReserve'               => $_POST['quantiteReserve'],
        'quantiteAlerteReserve'         => $_POST['quantiteAlerteReserve'],
        'peremptionReserve'             => $_POST['peremptionReserve'],
        'peremptionReserveAnticipation' => $_POST['peremptionReserveAnticipation'],
        'commentairesReserveElement'    => $_POST['commentairesReserveElement']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du materiel à la réserve: " . $_POST['idMaterielCatalogue'], '1', NULL);
            $_SESSION['returnMessage'] = 'Matériel ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du matériel à la réserve " . $_POST['idMaterielCatalogue'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un matériel existe déjà dans ce conteneur. Au lieu d\'ajouter à nouveau le matériel, veuillez changer sa quantité.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du matériel à la réserve " . $_POST['idMaterielCatalogue'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du matériel.";
            $_SESSION['returnType'] = '2';
    }

    updatePeremptionsAnticipations();

    echo "<script>window.location = document.referrer;</script>";
}
?>