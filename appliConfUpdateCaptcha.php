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
    $_POST['reCaptcha_enable']      = ($_POST['reCaptcha_enable'] ==1) ? 1 : 0;

    $query = $db->prepare('
        UPDATE
            CONFIG
        SET 
            reCaptcha_enable    = :reCaptcha_enable,
            reCaptcha_siteKey   = :reCaptcha_siteKey,
            reCaptcha_secretKey = :reCaptcha_secretKey,
            reCaptcha_scoreMin  = :reCaptcha_scoreMin
    ;');
    $query->execute(array(
        'reCaptcha_enable'    => $_POST['reCaptcha_enable'],
        'reCaptcha_siteKey'   => $_POST['reCaptcha_siteKey'],
        'reCaptcha_secretKey' => $_POST['reCaptcha_secretKey'],
        'reCaptcha_scoreMin'  => $_POST['reCaptcha_scoreMin'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la configuration reCaptcha du site", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Echec de la modification de la configuration reCaptcha du site", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>