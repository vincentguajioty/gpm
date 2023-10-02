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
    $_POST['libelleLot'] = ($_POST['libelleLot'] == Null) ? Null : $_POST['libelleLot'];
	$_POST['nomFournisseur'] = ($_POST['nomFournisseur'] == Null) ? Null : $_POST['nomFournisseur'];

    $query = $db->prepare('
        INSERT INTO
            MATERIEL_SAC
        SET
            libelleSac    = :libelleSac,
            idLot         = :idLot,
            taille        = :taille,
            couleur       = :couleur,
            idFournisseur = :idFournisseur
        ;');
    $query->execute(array(
        'libelleSac'    => $_POST['libelleSac'],
        'idLot'         => $_POST['libelleLot'],
        'taille'        => $_POST['taille'],
        'couleur'       => $_POST['couleur'],
        'idFournisseur' => $_POST['nomFournisseur']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du sac " . $_POST['libelleSac'], '1', NULL);
            $_SESSION['returnMessage'] = 'Sac ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du sac " . $_POST['libelleSac'], '2', NULL);
            $_SESSION['returnMessage'] = "Un sac existe déjà avec le même libellé et appartenant au même lot. Merci de changer le libellé ou le lot de rattachement.";
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du sac " . $_POST['libelleSac'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout du sac.";
            $_SESSION['returnType'] = '2';
    }
    
    checkAllConf();

    echo "<script>window.location = document.referrer;</script>";
}
?>