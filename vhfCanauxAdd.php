<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vhf_canal_ajout']==0)
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


    $query = $db->prepare('INSERT INTO VHF_CANAL(rxFreq, txFreq, rxCtcss, txCtcss, niveauCtcss, txPower, chName, appelSelectifCode, appelSelectifPorteuse, let, notone, idVhfTechno, remarquesCanal) VALUES(:rxFreq, :txFreq, :rxCtcss, :txCtcss, :niveauCtcss, :txPower, :chName, :appelSelectifCode, :appelSelectifPorteuse, :let, :notone, :idVhfTechno, :remarquesCanal);');
    $query->execute(array(
        'rxFreq' => $_POST['rxFreq'],
        'txFreq' => $_POST['txFreq'],
        'rxCtcss' => $_POST['rxCtcss'],
        'txCtcss' => $_POST['txCtcss'],
        'niveauCtcss' => $_POST['niveauCtcss'],
        'txPower' => $_POST['txPower'],
        'chName' => $_POST['chName'],
        'appelSelectifCode' => $_POST['appelSelectifCode'],
        'appelSelectifPorteuse' => $_POST['appelSelectifPorteuse'],
        'let' => $_POST['let'],
        'notone' => $_POST['notone'],
        'idVhfTechno' => $_POST['idVhfTechno'],
        'remarquesCanal' => $_POST['remarquesCanal']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du canal " . $_POST['chName'], '2');
            $_SESSION['returnMessage'] = 'Canal ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du canal " . $_POST['chName'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout du canal.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>