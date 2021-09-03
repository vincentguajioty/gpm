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
    $_POST['LDAP_ISWINAD'] = ($_POST['LDAP_ISWINAD'] ==1) ? 1 : 0;
    
    $query = $db->prepare('
        UPDATE
            CONFIG
        SET 
            LDAP_DOMAIN  = :LDAP_DOMAIN,
            LDAP_BASEDN  = :LDAP_BASEDN,
            LDAP_ISWINAD = :LDAP_ISWINAD,
            LDAP_SSL     = :LDAP_SSL
    ;');
    $query->execute(array(
        'LDAP_DOMAIN'  => $_POST['LDAP_DOMAIN'],
        'LDAP_BASEDN'  => $_POST['LDAP_BASEDN'],
        'LDAP_ISWINAD' => $_POST['LDAP_ISWINAD'],
        'LDAP_SSL'     => $_POST['LDAP_SSL'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du paramétrage AD/LDAP du site", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            
            break;

        default:
            writeInLogs("Echec de la modification du paramétrage AD/LDAP du site", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>