<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['annuaire_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {


    $query = $db->prepare('SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil');
    $query->execute(array(
        'idProfil'     => $_GET['id']
    ));
    while($data = $query->fetch())
    {
        $query2 = $db->prepare('
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
                notif_vehicules_desinfections = 1,
                notif_vehicules_health        = 1,
                notif_tenues_stock            = 1,
                notif_tenues_retours          = 1,
                notif_benevoles_lots          = 1,
                notif_benevoles_vehicules     = 1
            WHERE
                idPersonne                 = :idPersonne
            ;');
        $query2->execute(array(
            'idPersonne'     => $data['idPersonne']
        ));
    }


    switch($query->errorCode())
    {
        case '00000':
            majNotificationsProfil($_GET['id']);
            
            writeInLogs("Réinitialisation des notifications de les utilisateurs du profil " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Notifications utilisateurs réinitialisées avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la réinitialisation des notifications des utilisateurs du profil " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la réinitialisation des notifications des utilisateurs.';
            $_SESSION['returnType'] = '2';
    }

    

    echo "<script>window.location = document.referrer;</script>";
}
?>