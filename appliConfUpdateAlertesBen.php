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

    $_POST['alertes_benevoles_lots']      = ($_POST['alertes_benevoles_lots'] ==1) ? 1 : 0;
    $_POST['alertes_benevoles_vehicules'] = ($_POST['alertes_benevoles_vehicules'] ==1) ? 1 : 0;
    $_POST['consommation_benevoles']      = ($_POST['consommation_benevoles'] ==1) ? 1 : 0;


    $query = $db->prepare('
        UPDATE
            CONFIG
        SET 
            alertes_benevoles_lots       = :alertes_benevoles_lots ,
            alertes_benevoles_vehicules  = :alertes_benevoles_vehicules,
            consommation_benevoles       = :consommation_benevoles
    ;');
    $query->execute(array(
        'alertes_benevoles_lots'       => $_POST['alertes_benevoles_lots'],
        'alertes_benevoles_vehicules'  => $_POST['alertes_benevoles_vehicules'],
        'consommation_benevoles'       => $_POST['consommation_benevoles'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la configuration des alertes bénévoles du site", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Echec de la modification de la configuration des notifications alertes bénévoles du site", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>