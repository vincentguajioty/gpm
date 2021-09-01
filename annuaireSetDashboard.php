<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['annuaire_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {


    $query = $db->prepare('UPDATE PERSONNE_REFERENTE
                                        SET
                                            conf_indicateur1Accueil = 1,
                                            conf_indicateur2Accueil = 1,
                                            conf_indicateur3Accueil = 1,
                                            conf_indicateur4Accueil = 1,
                                            conf_indicateur5Accueil = 1,
                                            conf_indicateur6Accueil = 1,
                                            conf_indicateur7Accueil = 1,
                                            conf_indicateur8Accueil = 1,
                                            conf_indicateur9Accueil = 1,
                                            conf_indicateur10Accueil = 1
                                        WHERE
                                            idPersonne     = :idPersonne ;');
    $query->execute(array(
        'idPersonne'     => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            majIndicateursPersonne($_GET['id'],1);

            writeInLogs("Réinitialisation des indicateur du dashboard de l'utilisateur " . $_GET['id'], '3');
            $_SESSION['returnMessage'] = 'Dashboard utilisateur réinitialisé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la réinitialisation des indicateurs du dashboard de l'utilisateur " . $_GET['id'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la réinitialisation du dashboard de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }

    

    echo "<script>window.location = document.referrer;</script>";
}
?>