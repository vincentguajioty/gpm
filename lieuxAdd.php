<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['lieux_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{    
    $_POST['accesReserve'] = ($_POST['accesReserve'] == 'option1') ? 0 : 1;

    $query = $db->prepare('INSERT INTO LIEUX(libelleLieu, adresseLieu, detailsLieu, accesReserve) VALUES(:libelleLieu, :adresseLieu, :detailsLieu, :accesReserve);');
    $query->execute(array(
        'libelleLieu' => $_POST['libelleLieu'],
        'adresseLieu' => $_POST['adresseLieu'],
        'detailsLieu' => $_POST['detailsLieu'],
        'accesReserve' => $_POST['accesReserve']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du lieu " . $_POST['libelleLieu'], '2');
            $_SESSION['returnMessage'] = 'Lieu ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du lieu " . $_POST['libelleLieu'], '5');
            $_SESSION['returnMessage'] = 'Un lieu existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du lieu " . $_POST['libelleLieu'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du lieu.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>