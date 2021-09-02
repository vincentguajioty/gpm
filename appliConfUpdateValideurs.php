<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $queryDelete = $db->query('TRUNCATE COMMANDES_VALIDEURS_DEFAULT;');
    if (!empty($_POST['idPersonne'])) {
        $insertSQL = 'INSERT INTO COMMANDES_VALIDEURS_DEFAULT (idPersonne) VALUES';
        foreach ($_POST['idPersonne'] as $idPersonne) {
            $insertSQL .= ' ('. (int)$idPersonne.'),';
        }

        $insertSQL = substr($insertSQL, 0, -1);

        $db->query($insertSQL);
    }

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la configuration des valideurs par défaut des commandes", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Echec de la modification de la configuration des valideurs par défaut des commandes", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>