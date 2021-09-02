<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['tenuesCatalogue_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['stockCatalogueTenue'] = ($_POST['stockCatalogueTenue'] == Null) ? Null : $_POST['stockCatalogueTenue'];
	$_POST['stockAlerteCatalogueTenue'] = ($_POST['stockAlerteCatalogueTenue'] == Null) ? Null : $_POST['stockAlerteCatalogueTenue'];
    $_POST['idFournisseur'] = ($_POST['idFournisseur'] == Null) ? Null : $_POST['idFournisseur'];

    $query = $db->prepare('
        INSERT INTO
            TENUES_CATALOGUE
        SET
            libelleCatalogueTenue     = :libelleCatalogueTenue,
            tailleCatalogueTenue      = :tailleCatalogueTenue,
            serigraphieCatalogueTenue = :serigraphieCatalogueTenue,
            idFournisseur             = :idFournisseur,
            stockCatalogueTenue       = :stockCatalogueTenue,
            stockAlerteCatalogueTenue = :stockAlerteCatalogueTenue
        ;');
    $query->execute(array(
        'libelleCatalogueTenue'     => $_POST['libelleCatalogueTenue'],
        'tailleCatalogueTenue'      => $_POST['tailleCatalogueTenue'],
        'serigraphieCatalogueTenue' => $_POST['serigraphieCatalogueTenue'],
        'idFournisseur'             => $_POST['idFournisseur'],
        'stockCatalogueTenue'       => $_POST['stockCatalogueTenue'],
        'stockAlerteCatalogueTenue' => $_POST['stockAlerteCatalogueTenue']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout dans le catalogue des tenues de " . $_POST['libelleCatalogueTenue'], '1', NULL);
            $_SESSION['returnMessage'] = 'Element ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout dans le catalogue des tenues de " . $_POST['libelleCatalogueTenue'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout de l'élément.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>