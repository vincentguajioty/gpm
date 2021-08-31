<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['materiel_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue WHERE idElement = :idElement;');
    $query->execute(array(
        'idElement' => $_GET['id']
    ));
    $data = $query -> fetch();

    $query = $db->prepare('DELETE FROM MATERIEL_ELEMENT WHERE idElement = :idElement;');
    $query->execute(array(
        'idElement' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du materiel " . $data['libelleMateriel'], '4');
            $_SESSION['returnMessage'] = 'Matériel supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du matériel " . $data['libelleMateriel'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression du matériel.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>