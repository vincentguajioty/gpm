<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['annuaire_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {


    $query = $db->prepare('
        UPDATE
            PERSONNE_REFERENTE
        SET
            notif_lots_manquants          = 1,
            notif_lots_peremptions        = 1,
            notif_lots_inventaires        = 1,
            notif_lots_conformites        = 1,
            notif_reserves_manquants      = 1,
            notif_reserves_peremptions    = 1,
            notif_reserves_inventaires    = 1,
            notif_vehicules_assurances    = 1,
            notif_vehicules_revisions     = 1,
            notif_vehicules_desinfections = 1,
            notif_vehicules_ct            = 1,
            notif_vehicules_health        = 1,
            notif_tenues_stock            = 1,
            notif_tenues_retours          = 1
        WHERE
            idPersonne     = :idPersonne
        ;');
    $query->execute(array(
        'idPersonne'     => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            majNotificationsPersonne($_GET['id'],1);
            
            writeInLogs("Réinitialisation des notifications de l'utilisateur " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Notifications utilisateur réinitialisées avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la réinitialisation des notifications de l'utilisateur " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la réinitialisation des notifications de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }

    

    echo "<script>window.location = document.referrer;</script>";
}
?>