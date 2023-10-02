<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['reserve_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $lock = $db->prepare('UPDATE RESERVES_CONTENEUR SET inventaireEnCours = Null WHERE idConteneur = :idConteneur;');
    $lock->execute(array(
        'idConteneur' => $_GET['id']
    ));

    $clean = $db->prepare('DELETE FROM RESERVES_INVENTAIRES_TEMP WHERE idConteneur = :idConteneur;');
    $clean->execute(array(
        'idConteneur' => $_GET['id']
    ));

    $_SESSION['returnMessage'] = 'Annulation de l\'inventaire.';
    $_SESSION['returnType'] = '2';
    
    echo "<script type='text/javascript'>document.location.replace('reserveConteneurContenu.php?id=".$_GET['id']."');</script>";
}
?>