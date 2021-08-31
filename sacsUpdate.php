<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['sac_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    if ($_POST['libelleLot'] == Null)
    {
        $idLot = Null;
    }
    else
    {
        $idLot = $_POST['libelleLot'];
    }

    if ($_POST['nomFournisseur'] == Null)
    {
        $idFournisseur = Null;
    }
    else
    {
        $idFournisseur = $_POST['nomFournisseur'];
    }

    $query = $db->prepare('UPDATE MATERIEL_SAC SET libelleSac = :libelleSac, idLot = :idLot, taille = :taille, couleur = :couleur, idFournisseur = :idFournisseur WHERE idSac = :idSac;');
    $query->execute(array(
        'idSac' => $_GET['id'],
        'libelleSac' => $_POST['libelleSac'],
        'idLot' => $idLot,
        'taille' => $_POST['taille'],
        'couleur' => $_POST['couleur'],
        'idFournisseur' => $idFournisseur
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du sac " . $_POST['libelleSac'], '3');
            $_SESSION['returnMessage'] = 'Sac modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du sac " . $_POST['libelleSac'], '5');
            $_SESSION['returnMessage'] = "Un sac existe déjà avec le même libellé et appartenant au même lot. Merci de changer le libellé ou le lot de rattachement.";
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du sac " . $_POST['libelleSac'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du sac.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-2);</script>";
}
?>