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

if ($_SESSION['verrouIP']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	
	$query2 = $db->prepare('SELECT * FROM VERROUILLAGE_IP WHERE idIP = :idIP ;');
    $query2->execute(array(
        'idIP' => $_GET['id']
    ));
    $data = $query2->fetch();
	
    $query = $db->prepare('DELETE FROM VERROUILLAGE_IP WHERE idIP = :idIP;');
    $query->execute(array(
        'idIP' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Deverrouillage de l'adresse IP " . $data['adresseIPverr'], '4');
            $_SESSION['returnMessage'] = 'Adresse IP déverrouillée.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors du déverrouillage de " . $data['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors du déverouillage de l\'adresse IP.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-1);</script>";
}
?>