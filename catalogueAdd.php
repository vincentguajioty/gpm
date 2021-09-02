<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['catalogue_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['sterilite'] = ($_POST['sterilite'] == 'option1') ? 0 : 1;
    $_POST['libelleCategorie'] = ($_POST['libelleCategorie'] == Null) ? Null : $_POST['libelleCategorie'];
    $_POST['idFournisseur'] = ($_POST['idFournisseur'] == Null) ? Null : $_POST['idFournisseur'];

    $query = $db->prepare('
        INSERT INTO
            MATERIEL_CATALOGUE
        SET
            libelleMateriel         = :libelleMateriel,
            idCategorie             = :idCategorie,
            taille                  = :taille,
            sterilite               = :sterilite,
            conditionnementMultiple = :conditionnementMultiple,
            commentairesMateriel    = :commentairesMateriel,
            idFournisseur           = :idFournisseur
    ;');
    $query->execute(array(
        'libelleMateriel'         => $_POST['libelleMateriel'],
        'idCategorie'             => $_POST['libelleCategorie'],
        'taille'                  => $_POST['taille'],
        'sterilite'               => $_POST['sterilite'],
        'conditionnementMultiple' => $_POST['conditionnementMultiple'],
        'commentairesMateriel'    => $_POST['commentairesMateriel'],
        'idFournisseur'           => $_POST['idFournisseur'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout dans le catalogue de " . $_POST['libelleMateriel'], '1', NULL);
            $_SESSION['returnMessage'] = 'Item ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détection lors de l'ajout dans le catalogue de " . $_POST['libelleMateriel'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un item dans le catalogue existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'item " . $_POST['libelleMateriel'] . " dans le catalogue.", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de l\'item.';
            $_SESSION['returnType'] = '2';
    }

	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";
}
?>