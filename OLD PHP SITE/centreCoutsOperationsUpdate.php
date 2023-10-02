<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if (centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['idCentreDeCout'])==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['dateOperation'] = ($_POST['dateOperation'] == Null) ? Null : $_POST['dateOperation'];
    $_POST['idPersonne'] = ($_POST['idPersonne'] == Null) ? Null : $_POST['idPersonne'];
    $_POST['montantEntrant'] = ($_POST['montantEntrant'] == Null) ? Null : $_POST['montantEntrant'];
    $_POST['montantSortant'] = ($_POST['montantSortant'] == Null) ? Null : $_POST['montantSortant'];

    $query = $db->prepare('
        UPDATE
            CENTRE_COUTS_OPERATIONS
        SET
            dateOperation           = :dateOperation,
            libelleOperation        = :libelleOperation,
            montantEntrant          = :montantEntrant,
            montantSortant          = :montantSortant,
            detailsMoyenTransaction = :detailsMoyenTransaction,
            idPersonne              = :idPersonne
        WHERE
            idOperations = :idOperations;');
    $query->execute(array(
        'dateOperation'           => $_POST['dateOperation'],
        'libelleOperation'        => $_POST['libelleOperation'],
        'montantEntrant'          => $_POST['montantEntrant'],
        'montantSortant'          => $_POST['montantSortant'],
        'detailsMoyenTransaction' => $_POST['detailsMoyenTransaction'],
        'idPersonne'              => $_POST['idPersonne'],
        'idOperations'            => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification d'une opération dans le centre de couts " . $_GET['idCentreDeCout'], '1', NULL);
            $_SESSION['returnMessage'] = 'Opération modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification d'une opération dans le centre de couts " . $_GET['idCentreDeCout'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification de l'opération.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script type='text/javascript'>document.location.replace('centreCoutsContenu.php?id=" . $_GET['idCentreDeCout'] . "');</script>";
  
}
?>