<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	$_POST['mailcopy'] = ($_POST['mailcopy'] ==1) ? 1 : 0;
	
    $query = $db->prepare('UPDATE CONFIG SET appname = :appname, sitecolor = :sitecolor, urlsite = :urlsite, mailserver = :mailserver, logouttemp = :logouttemp, mailcopy = :mailcopy;');
    $query->execute(array(
        'appname' => $_POST['appname'],
        'sitecolor' => $_POST['sitecolor'],
        'urlsite' => $_POST['urlsite'],
        'mailserver' => $_POST['mailserver'],
        'logouttemp' => $_POST['logouttemp'],
        'mailcopy' => $_POST['mailcopy']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la configuration du site par " . $_SESSION['identifiant'], '3');
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;
			
        default:
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-2);</script>";
}
?>