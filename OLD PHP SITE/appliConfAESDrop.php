<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> strtoupper($CONFSUPPRESSION))
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    unset($_SESSION['aesFour']);
    
    $query = $db->query('UPDATE FOURNISSEURS SET aesFournisseur = Null;');
    $query = $db->query('UPDATE CONFIG SET aesFournisseurTemoin = Null;');

    writeInLogs("Config - Suppression de la clef de chiffrement fournisseurs - Données fournisseurs supprimées et témoin réinitialisé", '1', NULL);


    echo "<script type='text/javascript'>document.location.replace('appliConf.php');</script>";
}
?>