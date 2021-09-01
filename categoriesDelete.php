<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> 'CONFIRMATION')
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['categories_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM MATERIEL_CATEGORIES WHERE idCategorie = :idCategorie;');
    $query->execute(array(
        'idCategorie' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE MATERIEL_CATALOGUE SET idCategorie = Null WHERE idCategorie = :idCategorie ;');
    $query->execute(array(
        'idCategorie' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM MATERIEL_CATEGORIES WHERE idCategorie = :idCategorie;');
    $query->execute(array(
        'idCategorie' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression de la catégorie " . $data['libelleCategorie'], '4');
            $_SESSION['returnMessage'] = 'Catégorie supprimée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression de la catégorie " . $data['libelleCategorie'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la suppression de la catégorie.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>