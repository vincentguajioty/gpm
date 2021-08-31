<?php
session_start();
require_once('logCheck.php');

require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_POST['idExecutant'] == Null)
{
	$query = $db->prepare('SELECT * FROM TODOLIST WHERE idTache = :idTache');
	$query->execute(array(
	    'idTache'       => $_GET['id']
	));
	$data = $query->fetch();
	$_POST['idExecutant'] = $data['idExecutant'];
}


if ($_SESSION['todolist_modification']==0 AND $_POST['idExecutant']!=$_SESSION['idPersonne'])
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('UPDATE TODOLIST SET 
                                                realisee = 1
                                            WHERE
                                                idTache = :idTache
                                            ;'
                        );
    $query->execute(array(
        'idTache'       => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification d'une TDL.", '3');
            $_SESSION['returnMessage'] = 'Tache modifiée avec succès.';
            $_SESSION['returnType'] = '1';       
        break;


        default:
            writeInLogs("Erreur inconnue lors de la modification d'une TDL.", '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la modification de la tache.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";


}
?>