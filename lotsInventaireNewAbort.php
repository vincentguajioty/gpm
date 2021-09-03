<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['lots_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $lock = $db->prepare('UPDATE LOTS_LOTS SET inventaireEnCours = Null WHERE idLot = :idLot;');
    $lock->execute(array(
        'idLot' => $_GET['id']
    ));

    $clean = $db->prepare('DELETE FROM LOTS_INVENTAIRES_TEMP WHERE idLot = :idLot;');
    $clean->execute(array(
        'idLot' => $_GET['id']
    ));

    $_SESSION['returnMessage'] = 'Annulation de l\'inventaire.';
    $_SESSION['returnType'] = '2';
    
    echo "<script type='text/javascript'>document.location.replace('lotsContenu.php?id=".$_GET['id']."');</script>";
}
?>