<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['profils_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {

    $queryDelete = $db->prepare('DELETE FROM PROFILS_PERSONNES WHERE idProfil = :idProfil');
    $queryDelete->execute([
        ':idProfil' => $_GET['idProfil']
    ]);
    if (!empty($_POST['idPersonne'])) {
        $insertSQL = 'INSERT INTO PROFILS_PERSONNES (idPersonne, idProfil) VALUES';
        foreach ($_POST['idPersonne'] as $idPersonne) {
            $insertSQL .= ' ('. (int)$idPersonne.', '. (int)$_GET['idProfil'] .'),';
        }

        $insertSQL = substr($insertSQL, 0, -1);

        $db->query($insertSQL);
    }

    writeInLogs("Modification des utilisateurs affectés au profil " . $_GET['idProfil'], '3');
    $_SESSION['returnMessage'] = 'Attribution de profils effectuée.';
    $_SESSION['returnType'] = '1';

    majIndicateursProfil($_GET['idProfil']);
    majNotificationsProfil($_GET['idProfil']);

    echo "<script>window.location = document.referrer;</script>";
}
?>