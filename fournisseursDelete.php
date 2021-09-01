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

if ($_SESSION['fournisseurs_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM FOURNISSEURS WHERE idFournisseur = :idFournisseur;');
    $query->execute(array(
        'idFournisseur' => $_GET['id']
    ));
    $data = $query -> fetch();

    $query = $db->prepare('UPDATE MATERIEL_SAC SET idFournisseur = Null WHERE idFournisseur = :idFournisseur ;');
    $query->execute(array(
        'idFournisseur' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE MATERIEL_ELEMENT SET idFournisseur = Null WHERE idFournisseur = :idFournisseur ;');
    $query->execute(array(
        'idFournisseur' => $_GET['id']
    ));

	$query = $db->prepare('UPDATE COMMANDES SET idFournisseur = Null WHERE idFournisseur = :idFournisseur ;');
    $query->execute(array(
        'idFournisseur' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE RESERVES_MATERIEL SET idFournisseur = Null WHERE idFournisseur = :idFournisseur ;');
    $query->execute(array(
        'idFournisseur' => $_GET['id']
    ));

    $query = $db->prepare('UPDATE TENUES_CATALOGUE SET idFournisseur = Null WHERE idFournisseur = :idFournisseur ;');
    $query->execute(array(
        'idFournisseur' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM FOURNISSEURS WHERE idFournisseur = :idFournisseur;');
    $query->execute(array(
        'idFournisseur' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du fournisseur " . $data['nomFournisseur'], '4');
            $_SESSION['returnMessage'] = 'Fournisseur supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du fournisseur " . $data['nomFournisseur'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression du fournisseur.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>