<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['categories_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('UPDATE MATERIEL_CATEGORIES SET libelleCategorie = :libelleCategorie WHERE idCategorie = :idCategorie;');
    $query->execute(array(
        'libelleCategorie' => $_POST['libelleCategorie'],
        'idCategorie' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la catégorie " . $_POST['libelleCategorie'], '3');
            $_SESSION['returnMessage'] = 'Catégorie modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification de la catégorie " . $_POST['libelleCategorie'], '5');
            $_SESSION['returnMessage'] = 'Une catégorie existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la catégorie " . $_POST['libelleCategorie'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la catégorie.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-2);</script>";
}
?>