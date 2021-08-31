<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['profils_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM PROFILS WHERE idProfil = :idProfil;');
    $query->execute(array(
        'idProfil' => $_GET['id']
    ));
    $data = $query->fetch();

    

    $query2 = $db->prepare('SELECT * FROM PROFILS_PERSONNES WHERE idProfil = :idProfil ;');
    $query2->execute(array(
        'idProfil' => $_GET['id']
    ));
    $query = $db->prepare('DELETE FROM PROFILS_PERSONNES WHERE idProfil = :idProfil');
    $query->execute([
        ':idProfil' => $_GET['id']
    ]);
    while ($data2 = $query2->fetch())
    {
        majIndicateursPersonne($data2['idPersonne']);
    }

    $query = $db->prepare('DELETE FROM PROFILS WHERE idProfil = :idProfil;');
    $query->execute(array(
        'idProfil' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du profil " . $data['libelleProfil'], '4');
            $_SESSION['returnMessage'] = 'Profil supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;


        default:
            writeInLogs("Erreur inconnue lors de la suppression du profil " . $data['libelleProfil'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression du profil.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>