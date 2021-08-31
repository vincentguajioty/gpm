<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['categories_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('INSERT INTO MATERIEL_CATEGORIES(libelleCategorie) VALUES(:libelleCategorie);');
    $query->execute(array(
        'libelleCategorie' => $_POST['libelleCategorie']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de la catégorie " . $_POST['libelleCategorie'], '2');
            $_SESSION['returnMessage'] = 'Catégorie ajoutée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout de la catégorie " . $_POST['libelleCategorie'], '5');
            $_SESSION['returnMessage'] = 'Une catégorie existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de la catégorie " . $_POST['libelleCategorie'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de la catégorie.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>