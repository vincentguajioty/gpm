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

if ($_SESSION['cout_supprimer']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('DELETE FROM CENTRE_COUTS_PERSONNES WHERE idCentreDeCout = :idCentreDeCout ;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));
    
    $query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('SELECT idCommande FROM COMMANDES WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));
    while($data = $query->fetch())
    {
    	$query = $db->prepare('UPDATE COMMANDES SET integreCentreCouts = 0 WHERE idCommande = :idCommande;');
	    $query->execute(array(
	        'idCommande' => $data['idCommande']
	    ));
    }
    
    $query = $db->prepare('UPDATE COMMANDES SET idCentreDeCout = Null WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));
    
    $query = $db->prepare('DELETE FROM CENTRE_COUTS_OPERATIONS WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du centre de couts " . $data['libelleCentreDecout'], '1', NULL);
            $_SESSION['returnMessage'] = 'Centre de couts supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du centre de couts " . $data['libelleCentreDecout'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression du centre de couts.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>