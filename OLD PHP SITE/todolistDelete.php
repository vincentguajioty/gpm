<?php
session_start();
require_once('logCheck.php');

require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if(strtoupper($_POST['confirmation']) <> strtoupper($CONFSUPPRESSION))
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['todolist_modification']==0 AND (tdlEstExecutant($_SESSION['idPersonne'], $_GET['id'])==0 OR (tdlEstExecutant($_SESSION['idPersonne'], $_GET['id']) AND $_SESSION['todolist_perso']==0)))
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('DELETE FROM TODOLIST_PERSONNES WHERE idTache = :idTache;');
    $query->execute(array(
        'idTache'       => $_GET['id']
    ));
    
    $query = $db->prepare('DELETE FROM TODOLIST WHERE idTache = :idTache;');
    $query->execute(array(
        'idTache'       => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression d'une TDL.", '1', NULL);
            $_SESSION['returnMessage'] = 'Tache supprimée avec succès.';
            $_SESSION['returnType'] = '1';       
        break;


        default:
            writeInLogs("Erreur inconnue lors de la suppression d'une TDL.", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la suppression de la tache.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";


}
?>