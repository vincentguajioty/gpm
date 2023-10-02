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
        INSERT INTO
            CENTRE_COUTS_OPERATIONS
        SET
            dateOperation           = :dateOperation,
            libelleOperation        = :libelleOperation,
            idCentreDeCout          = :idCentreDeCout,
            montantEntrant          = :montantEntrant,
            montantSortant          = :montantSortant,
            detailsMoyenTransaction = :detailsMoyenTransaction,
            idPersonne              = :idPersonne
        ;');
    $query->execute(array(
        'dateOperation'           => $_POST['dateOperation'],
        'libelleOperation'        => $_POST['libelleOperation'],
        'idCentreDeCout'          => $_GET['idCentreDeCout'],
        'montantEntrant'          => $_POST['montantEntrant'],
        'montantSortant'          => $_POST['montantSortant'],
        'detailsMoyenTransaction' => $_POST['detailsMoyenTransaction'],
        'idPersonne'              => $_POST['idPersonne']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout d'une opération dans le centre de couts " . $_GET['idCentreDeCout'], '1', NULL);
            $_SESSION['returnMessage'] = 'Opération ajoutée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout d'une opération dans le centre de couts " . $_GET['idCentreDeCout'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout de l'opération.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script type='text/javascript'>document.location.replace('centreCoutsContenu.php?id=" . $_GET['idCentreDeCout'] . "');</script>";
  
}
?>