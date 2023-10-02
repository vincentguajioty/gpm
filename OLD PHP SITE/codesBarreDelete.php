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

if ($_SESSION['codeBarre_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('DELETE FROM CODES_BARRE WHERE idCode = :idCode;');
    $query->execute(array(
        'idCode' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du code barre id " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Code barre supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du code barre id " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la suppression.';
            $_SESSION['returnType'] = '2';
    }

	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";
}
?>