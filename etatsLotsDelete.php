<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> strtoupper($CONFSUPPRESSION))
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['etats_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM LOTS_ETATS WHERE idLotsEtat = :idLotsEtat;');
    $query->execute(array(
        'idLotsEtat' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE LOTS_LOTS SET idLotsEtat = Null WHERE idLotsEtat = :idLotsEtat ;');
    $query->execute(array(
        'idLotsEtat' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM LOTS_ETATS WHERE idLotsEtat = :idLotsEtat;');
    $query->execute(array(
        'idLotsEtat' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'état de lot " . $data['libelleLotsEtat'], '4');
            $_SESSION['returnMessage'] = 'Etat de lot supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'état de lot " . $data['libelleLotsEtat'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la suppression de l\'état de lot.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>