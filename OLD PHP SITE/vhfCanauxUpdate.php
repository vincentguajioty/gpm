<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vhf_canal_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idVhfTechno'] = ($_POST['idVhfTechno'] == Null) ? Null : $_POST['idVhfTechno'];
    $_POST['rxCtcss'] = ($_POST['rxCtcss'] == Null) ? Null : $_POST['rxCtcss'];
    $_POST['txCtcss'] = ($_POST['txCtcss'] == Null) ? Null : $_POST['txCtcss'];
    $_POST['niveauCtcss'] = ($_POST['niveauCtcss'] == Null) ? Null : $_POST['niveauCtcss'];
    $_POST['txPower'] = ($_POST['txPower'] == Null) ? Null : $_POST['txPower'];
    $_POST['appelSelectifPorteuse'] = ($_POST['appelSelectifPorteuse'] == Null) ? Null : $_POST['appelSelectifPorteuse'];
    $_POST['let'] = ($_POST['let'] == Null) ? Null : $_POST['let'];
    $_POST['notone'] = ($_POST['notone'] == Null) ? Null : $_POST['notone'];

    $query = $db->prepare('
        UPDATE
            VHF_CANAL
        SET
            rxFreq                = :rxFreq,
            txFreq                = :txFreq,
            rxCtcss               = :rxCtcss,
            txCtcss               = :txCtcss,
            niveauCtcss           = :niveauCtcss,
            txPower               = :txPower,
            chName                = :chName,
            appelSelectifCode     = :appelSelectifCode,
            appelSelectifPorteuse = :appelSelectifPorteuse,
            let                   = :let,
            notone                = :notone,
            idVhfTechno           = :idVhfTechno,
            remarquesCanal        = :remarquesCanal
        WHERE
            idVhfCanal            = :idVhfCanal
        ;');
    $query->execute(array(
        'rxFreq'                => $_POST['rxFreq'],
        'txFreq'                => $_POST['txFreq'],
        'rxCtcss'               => $_POST['rxCtcss'],
        'txCtcss'               => $_POST['txCtcss'],
        'niveauCtcss'           => $_POST['niveauCtcss'],
        'txPower'               => $_POST['txPower'],
        'chName'                => $_POST['chName'],
        'appelSelectifCode'     => $_POST['appelSelectifCode'],
        'appelSelectifPorteuse' => $_POST['appelSelectifPorteuse'],
        'let'                   => $_POST['let'],
        'notone'                => $_POST['notone'],
        'idVhfTechno'           => $_POST['idVhfTechno'],
        'remarquesCanal'        => $_POST['remarquesCanal'],
        'idVhfCanal'            => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du canal " . $_POST['chName'], '1', NULL);
            $_SESSION['returnMessage'] = 'Canal modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du canal " . $_POST['chName'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du canal.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>