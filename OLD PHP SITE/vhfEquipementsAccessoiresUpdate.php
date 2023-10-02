<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vhf_equipement_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idVhfAccessoireType'] = ($_POST['idVhfAccessoireType'] == Null) ? Null : $_POST['idVhfAccessoireType'];

    $query = $db->prepare('
        UPDATE
            VHF_ACCESSOIRES
        SET
            libelleVhfAccessoire      = :libelleVhfAccessoire,
            marqueModeleVhfAccessoire = :marqueModeleVhfAccessoire,
            idVhfAccessoireType       = :idVhfAccessoireType,
            SnVhfAccessoire           = :SnVhfAccessoire,
            remarquesVhfAccessoire    = :remarquesVhfAccessoire
        WHERE
            idVhfAccessoire           = :idVhfAccessoire
        ;');
    $query->execute(array(
        'libelleVhfAccessoire'      => $_POST['libelleVhfAccessoire'],
        'marqueModeleVhfAccessoire' => $_POST['marqueModeleVhfAccessoire'],
        'idVhfAccessoireType'       => $_POST['idVhfAccessoireType'],
        'SnVhfAccessoire'           => $_POST['SnVhfAccessoire'],
        'remarquesVhfAccessoire'    => $_POST['remarquesVhfAccessoire'],
        'idVhfAccessoire'           => $_GET['id'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de l'accessoire ".$_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Accessoire modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de l'accessoire ".$_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout de l'équipement radio.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>