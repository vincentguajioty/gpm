<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['fournisseurs_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('INSERT INTO FOURNISSEURS(nomFournisseur, adresseFournisseur, telephoneFournisseur, mailFournisseur) VALUES(:nomFournisseur, :adresseFournisseur, :telephoneFournisseur, :mailFournisseur);');
    $query->execute(array(
        'nomFournisseur' => $_POST['nomFournisseur'],
        'adresseFournisseur' => $_POST['adresseFournisseur'],
        'telephoneFournisseur' => $_POST['telephoneFournisseur'],
        'mailFournisseur' => $_POST['mailFournisseur']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du fournisseur " . $_POST['nomFournisseur'], '2');
            $_SESSION['returnMessage'] = 'Fournisseur ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du fournisseur " . $_POST['nomFournisseur'], '5');
            $_SESSION['returnMessage'] = 'Un fournisseur existe déjà avec le même nom. Merci de changer le nom.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du fournisseur " . $_POST['nomFournisseur'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du fournisseur.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>