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
    $_POST['verrouillage_ip_occurances']      = ($_POST['verrouillage_ip_occurances']      == Null) ? 1 : $_POST['verrouillage_ip_occurances'];
    $_POST['verrouillage_ip_temps']      = ($_POST['verrouillage_ip_temps']      == Null) ? 1 : $_POST['verrouillage_ip_temps'];

    $query = $db->prepare('
        UPDATE
            CONFIG
        SET 
            verrouillage_ip_occurances = :verrouillage_ip_occurances,
            verrouillage_ip_temps      = :verrouillage_ip_temps
    ;');
    $query->execute(array(
        'verrouillage_ip_occurances' => $_POST['verrouillage_ip_occurances'],
        'verrouillage_ip_temps'      => $_POST['verrouillage_ip_temps'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la configuration des verouillages IP du site", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Echec de la modification de la configuration des verouillages IP du site", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>