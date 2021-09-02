<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['actionsMassives']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->query('UPDATE MATERIEL_ELEMENT SET peremption = Null, peremptionNotification = Null;');

    $codeSQL = $query->errorCode();

    writeInLogs("Action massive 12 - Code MySQL retourné: ".$codeSQL, '4');
    $_SESSION['returnMessage'] = 'Requète lancée et terminée avec le code '.$codeSQL;
    $_SESSION['returnType'] = '1';

	checkAllConf();

    $_SESSION['actionsMassives_authent_ok'] = 1;
    echo "<script type='text/javascript'>document.location.replace('actionsMassives.php');</script>";
}
?>