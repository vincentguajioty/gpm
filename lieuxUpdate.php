<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['lieux_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {
    if ($_POST['accesReserve']=='option1')
    {
        $acces = '0';
    }
    else
    {
        $acces = '1';
    }
    $query = $db->prepare('UPDATE LIEUX SET libelleLieu = :libelleLieu, adresseLieu = :adresseLieu, detailsLieu = :detailsLieu, accesReserve = :accesReserve WHERE idLieu = :idLieu ;');
    $query->execute(array(
        'idLieu' => $_GET['id'],
        'libelleLieu' => $_POST['libelleLieu'],
        'adresseLieu' => $_POST['adresseLieu'],
        'detailsLieu' => $_POST['detailsLieu'],
        'accesReserve' => $acces
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du lieu " . $_POST['libelleLieu'], '3');
            $_SESSION['returnMessage'] = 'Lieu modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du lieu " . $_POST['libelleLieu'], '5');
            $_SESSION['returnMessage'] = 'Un lieu existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du lieu " . $_POST['libelleLieu'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification du lieu.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-2);</script>";
}
?>