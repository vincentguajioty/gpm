<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['fournisseurs_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {

    $query = $db->prepare('UPDATE FOURNISSEURS SET nomFournisseur = :nomFournisseur, adresseFournisseur = :adresseFournisseur, telephoneFournisseur = :telephoneFournisseur, mailFournisseur = :mailFournisseur WHERE idFournisseur = :idFournisseur ;');
    $query->execute(array(
        'idFournisseur' => $_GET['id'],
        'nomFournisseur' => $_POST['nomFournisseur'],
        'adresseFournisseur' => $_POST['adresseFournisseur'],
        'telephoneFournisseur' => $_POST['telephoneFournisseur'],
        'mailFournisseur' => $_POST['mailFournisseur']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du fournisseur " . $_POST['nomFournisseur'], '3');
            $_SESSION['returnMessage'] = 'Fournisseur modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du fournisseur " . $_POST['nomFournisseur'], '5');
            $_SESSION['returnMessage'] = 'Un fournisseur existe déjà avec le même nom. Merci de changer le nom.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du fournisseur " . $_POST['nomFournisseur'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification du fournisseur.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script type='text/javascript'>document.location.replace('fournisseurs.php');</script>";
}
?>