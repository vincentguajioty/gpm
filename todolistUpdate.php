<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['todolist_modification']==0 AND (tdlEstExecutant($_SESSION['idPersonne'], $_GET['id'])==0 OR (tdlEstExecutant($_SESSION['idPersonne'], $_GET['id']) AND $_SESSION['todolist_perso']==0)))
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['dateExecution'] = ($_POST['dateExecution'] == Null) ? Null : $_POST['dateExecution'];
    $_POST['idTDLpriorite'] = ($_POST['idTDLpriorite'] == Null) ? Null : $_POST['idTDLpriorite'];

    $query = $db->prepare('UPDATE TODOLIST SET 
                                                dateExecution = :dateExecution,
                                                titre = :titre,
                                                details = :details,
                                                idTDLpriorite = :idTDLpriorite
                                            WHERE
                                                idTache = :idTache
                                            ;'
                        );
    $query->execute(array(
        'dateExecution' => $_POST['dateExecution'],
        'titre'         => $_POST['titre'],
        'details'       => $_POST['details'],
        'idTDLpriorite'      => $_POST['idTDLpriorite'],
        'idTache'       => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification d'une TDL.", '3');
            $_SESSION['returnMessage'] = 'Tache modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            
            $queryDelete = $db->prepare('DELETE FROM TODOLIST_PERSONNES WHERE idTache = :idTache');
            $queryDelete->execute([
                ':idTache' => $_GET['id']
            ]);
            if (!empty($_POST['idExecutant'])) {
                $insertSQL = 'INSERT INTO TODOLIST_PERSONNES (idExecutant, idTache) VALUES';
                foreach ($_POST['idExecutant'] as $idExecutant) {
                    $insertSQL .= ' ('. (int)$idExecutant.', '. (int)$_GET['id'] .'),';
                }

                $insertSQL = substr($insertSQL, 0, -1);

                $db->query($insertSQL);
            }
        break;


        default:
            writeInLogs("Erreur inconnue lors de la modification d'une TDL.", '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la modification de la tache.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";


}
?>