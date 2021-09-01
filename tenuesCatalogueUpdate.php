<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['tenuesCatalogue_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['stockCatalogueTenue'] = ($_POST['stockCatalogueTenue'] == Null) ? Null : $_POST['stockCatalogueTenue'];
    $_POST['stockAlerteCatalogueTenue'] = ($_POST['stockAlerteCatalogueTenue'] == Null) ? Null : $_POST['stockAlerteCatalogueTenue'];
    $_POST['idFournisseur'] = ($_POST['idFournisseur'] == Null) ? Null : $_POST['idFournisseur'];

    $query = $db->prepare('UPDATE TENUES_CATALOGUE SET libelleCatalogueTenue = :libelleCatalogueTenue, tailleCatalogueTenue = :tailleCatalogueTenue, serigraphieCatalogueTenue = :serigraphieCatalogueTenue, idFournisseur = :idFournisseur, stockCatalogueTenue = :stockCatalogueTenue, stockAlerteCatalogueTenue = :stockAlerteCatalogueTenue WHERE idCatalogueTenue = :idCatalogueTenue;');
    $query->execute(array(
        'libelleCatalogueTenue' => $_POST['libelleCatalogueTenue'],
        'tailleCatalogueTenue' => $_POST['tailleCatalogueTenue'],
        'serigraphieCatalogueTenue' => $_POST['serigraphieCatalogueTenue'],
        'idFournisseur' => $_POST['idFournisseur'],
        'stockCatalogueTenue' => $_POST['stockCatalogueTenue'],
        'stockAlerteCatalogueTenue' => $_POST['stockAlerteCatalogueTenue'],
        'idCatalogueTenue' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification dans le catalogue des tenues de " . $_POST['libelleCatalogueTenue'], '3');
            $_SESSION['returnMessage'] = 'Element modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification dans le catalogue des tenues de " . $_POST['libelleCatalogueTenue'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification de l'élément.";
            $_SESSION['returnType'] = '2';
    }
    
    checkAllConf();

    echo "<script>window.location = document.referrer;</script>";
}
?>