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

    $query = $db->prepare('
        UPDATE
            CONFIG
        SET 
            vehicules_ct_delais_notif        = :vehicules_ct_delais_notif ,
            vehicules_revision_delais_notif  = :vehicules_revision_delais_notif ,
            vehicules_assurance_delais_notif = :vehicules_assurance_delais_notif
    ;');
    $query->execute(array(
        'vehicules_ct_delais_notif'        => $_POST['vehicules_ct_delais_notif'],
        'vehicules_revision_delais_notif'  => $_POST['vehicules_revision_delais_notif'],
        'vehicules_assurance_delais_notif' => $_POST['vehicules_assurance_delais_notif']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la configuration des notifications véhicules du site", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Echec de la modification de la configuration des notifications véhicules du site", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>