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
    $_POST['resetPassword'] = ($_POST['resetPassword'] ==1) ? 1 : 0;
	
    $query = $db->prepare('
        UPDATE
            CONFIG
        SET
            appname                 = :appname,
            sitecolor               = :sitecolor,
            urlsite                 = :urlsite,
            mailserver              = :mailserver,
            logouttemp              = :logouttemp,
            mailcopy                = :mailcopy,
            confirmationSuppression = :confirmationSuppression,
            resetPassword           = :resetPassword
    ;');
    $query->execute(array(
        'appname'                 => $_POST['appname'],
        'sitecolor'               => $_POST['sitecolor'],
        'urlsite'                 => $_POST['urlsite'],
        'mailserver'              => $_POST['mailserver'],
        'logouttemp'              => $_POST['logouttemp'],
        'mailcopy'                => $_POST['mailcopy'],
        'resetPassword'           => $_POST['resetPassword'],
        'confirmationSuppression' => $_POST['confirmationSuppression']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la configuration du site", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            if($_POST['resetPassword']==0)
            {
                $truncate = $db->query('TRUNCATE TABLE RESETPASSWORD;');
            }
            break;
			
        default:
            writeInLogs("Echec de la modification de la configuration du site", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script type='text/javascript'>document.location.replace('appliConf.php');</script>";
}
?>