<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['etats_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM ETATS WHERE idEtat = :idEtat;');
    $query->execute(array(
        'idEtat' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE LOTS_LOTS SET idEtat = Null WHERE idEtat = :idEtat;');
    $query->execute(array(
        'idEtat' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM ETATS WHERE idEtat = :idEtat;');
    $query->execute(array(
        'idEtat' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'état " . $data['libelleEtat'], '4');
            $_SESSION['returnMessage'] = 'Etat supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'état " . $data['libelleEtat'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression de l'état.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>