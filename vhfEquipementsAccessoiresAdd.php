<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vhf_equipement_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idVhfAccessoireType'] = ($_POST['idVhfAccessoireType'] == Null) ? Null : $_POST['idVhfAccessoireType'];

    $query = $db->prepare('
        INSERT INTO
            VHF_ACCESSOIRES
        SET
            libelleVhfAccessoire      = :libelleVhfAccessoire,
            marqueModeleVhfAccessoire = :marqueModeleVhfAccessoire,
            idVhfAccessoireType       = :idVhfAccessoireType,
            idVhfEquipement           = :idVhfEquipement,
            SnVhfAccessoire           = :SnVhfAccessoire,
            remarquesVhfAccessoire    = :remarquesVhfAccessoire
        ;');
    $query->execute(array(
        'libelleVhfAccessoire'      => $_POST['libelleVhfAccessoire'],
        'marqueModeleVhfAccessoire' => $_POST['marqueModeleVhfAccessoire'],
        'idVhfAccessoireType'       => $_POST['idVhfAccessoireType'],
        'idVhfEquipement'           => $_GET['idVhfEquipement'],
        'SnVhfAccessoire'           => $_POST['SnVhfAccessoire'],
        'remarquesVhfAccessoire'    => $_POST['remarquesVhfAccessoire'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'accessoire ".$_POST['libelleVhfAccessoire']." à l'équipement radio " . $_GET['idVhfEquipement'], '1', NULL);
            $_SESSION['returnMessage'] = 'Accessoire ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'accessoire ".$_POST['libelleVhfAccessoire']." à équipement radio " . $_GET['idVhfEquipement'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout de l'équipement radio.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>