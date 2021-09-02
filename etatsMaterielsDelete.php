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
    $query = $db->prepare('SELECT * FROM MATERIEL_ETATS WHERE idMaterielsEtat = :idMaterielsEtat;');
    $query->execute(array(
        'idMaterielsEtat' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE MATERIEL_ELEMENT SET idMaterielsEtat = Null WHERE idMaterielsEtat = :idMaterielsEtat ;');
    $query->execute(array(
        'idMaterielsEtat' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM MATERIEL_ETATS WHERE idMaterielsEtat = :idMaterielsEtat;');
    $query->execute(array(
        'idMaterielsEtat' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de l'état de lot " . $data['libelleMaterielsEtat'], '1', NULL);
            $_SESSION['returnMessage'] = 'Etat de lot supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de l'état de lot " . $data['libelleMaterielsEtat'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la suppression de l\'état de lot.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>