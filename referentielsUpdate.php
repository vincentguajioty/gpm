<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['typesLots_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {

    $query = $db->prepare('UPDATE LOTS_TYPES SET libelleTypeLot = :libelleTypeLot WHERE idTypeLot = :idTypeLot ;');
    $query->execute(array(
        'idTypeLot' => $_GET['id'],
        'libelleTypeLot' => $_POST['libelleTypeLot']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du référentiel " . $_POST['libelleTypeLot'], '3');
            $_SESSION['returnMessage'] = 'Référentiel modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du référentiel " . $_POST['libelleTypeLot'], '5');
            $_SESSION['returnMessage'] = "Un référentiel existe déjà avec le même libellé. Merci de changer le libellé";
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du référentiel " . $_POST['libelleTypeLot'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du référentiel.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-2);</script>";
}
?>