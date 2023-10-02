<?php
session_start();
require_once('logCheck.php');

require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['todolist_modification']==0 AND ($_GET['idExecutant']!=$_SESSION['idPersonne'] OR ($_GET['idExecutant']==$_SESSION['idPersonne'] AND $_SESSION['todolist_perso']==0)))
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['dateExecution'] = ($_POST['dateExecution'] == Null) ? Null : $_POST['dateExecution'];
    $_POST['idTDLpriorite'] = ($_POST['idTDLpriorite'] == Null) ? Null : $_POST['idTDLpriorite'];

    $query = $db->prepare('
        INSERT INTO
            TODOLIST
        SET
            idCreateur    = :idCreateur,
            dateCreation  = :dateCreation,
            dateExecution = :dateExecution,
            titre         = :titre,
            details       = :details,
            idTDLpriorite = :idTDLpriorite,
            realisee      = 0
        ;');
    $query->execute(array(
        'idCreateur'    => $_GET['idCreateur'],
        'dateCreation'  => date('Y-m-d H:i:s'),
        'dateExecution' => $_POST['dateExecution'],
        'titre'         => $_POST['titre'],
        'details'       => $_POST['details'],
        'idTDLpriorite' => $_POST['idTDLpriorite']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout d'une TDL.", '1', NULL);
            $_SESSION['returnMessage'] = 'Tache ajoutée avec succès.';
            $_SESSION['returnType'] = '1';
            
            $query = $db->query('SELECT MAX(idTache) as idTache FROM TODOLIST;');
            $data = $query->fetch();
            
            $query = $db->prepare('INSERT INTO TODOLIST_PERSONNES(idTache, idExecutant) VALUES(:idTache, :idExecutant);');
		    $query->execute(array(
		        'idExecutant'    => $_GET['idExecutant'],
		        'idTache'  => $data['idTache']
		    ));
            
            
        break;


        default:
            writeInLogs("Erreur inconnue lors de l'ajout d'une TDL.", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de l\'ajout de la tache.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";


}
?>