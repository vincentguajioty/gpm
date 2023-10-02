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

    $query = $db->prepare('
        INSERT INTO
            LIEUX
        SET
            libelleLieu  = :libelleLieu,
            adresseLieu  = :adresseLieu,
            detailsLieu  = :detailsLieu,
            accesReserve = :accesReserve
        ;');
    $query->execute(array(
        'libelleLieu'  => $_POST['libelleLieu'],
        'adresseLieu'  => $_POST['adresseLieu'],
        'detailsLieu'  => $_POST['detailsLieu'],
        'accesReserve' => $_POST['accesReserve']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du lieu " . $_POST['libelleLieu'], '1', NULL);
            $_SESSION['returnMessage'] = 'Lieu ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du lieu " . $_POST['libelleLieu'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un lieu existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du lieu " . $_POST['libelleLieu'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du lieu.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>