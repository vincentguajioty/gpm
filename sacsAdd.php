<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['sac_ajout']==0)
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

    $query = $db->prepare('INSERT INTO MATERIEL_SAC(libelleSac, idLot, taille, couleur, idFournisseur) VALUES(:libelleSac, :idLot, :taille, :couleur, :idFournisseur);');
    $query->execute(array(
        'libelleSac' => $_POST['libelleSac'],
        'idLot' => $idLot,
        'taille' => $_POST['taille'],
        'couleur' => $_POST['couleur'],
        'idFournisseur' => $idFournisseur
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du sac " . $_POST['libelleSac'], '2');
            $_SESSION['returnMessage'] = 'Sac ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du sac " . $_POST['libelleSac'], '5');
            $_SESSION['returnMessage'] = "Un sac existe déjà avec le même libellé et appartenant au même lot. Merci de changer le libellé ou le lot de rattachement.";
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du sac " . $_POST['libelleSac'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout du sac.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-2);</script>";
}
?>