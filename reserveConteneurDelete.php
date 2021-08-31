<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['reserve_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM RESERVES_INVENTAIRES WHERE idConteneur = :idConteneur;');
    $query->execute(array(
        'idConteneur' => $_GET['id']
    ));
    while ($data = $query->fetch())
    {
        $query2 = $db->prepare('DELETE FROM RESERVES_INVENTAIRES_CONTENUS WHERE idReserveInventaire = :idReserveInventaire;');
        $query2->execute(array(
            'idReserveInventaire' => $data['idReserveInventaire']
        ));
    }
    $query2 = $db->prepare('DELETE FROM RESERVES_INVENTAIRES WHERE idConteneur = :idConteneur;');
    $query2->execute(array(
        'idConteneur' => $_GET['id']
    ));
    
    $query = $db->prepare('SELECT * FROM RESERVES_CONTENEUR WHERE idConteneur = :idConteneur;');
    $query->execute(array(
        'idConteneur' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE RESERVES_MATERIEL SET idConteneur = Null WHERE idConteneur = :idConteneur ;');
    $query->execute(array(
        'idConteneur' => $_GET['id']
    ));

    $query = $db->prepare('DELETE FROM RESERVES_CONTENEUR WHERE idConteneur = :idConteneur;');
    $query->execute(array(
        'idConteneur' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du conteneur " . $data['libelleConteneur'], '4');
            $_SESSION['returnMessage'] = 'Conteneur supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;


        default:
            writeInLogs("Erreur inconnue lors de la suppression du contenueur " . $data['libelleConteneur'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression du conteneur.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>