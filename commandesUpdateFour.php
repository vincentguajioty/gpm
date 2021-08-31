<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';

if ($_SESSION['commande_etreEnCharge']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['datePassage'] = ($_POST['datePassage'] == Null) ? Null : $_POST['datePassage'];
    $_POST['dateLivraisonPrevue'] = ($_POST['dateLivraisonPrevue'] == Null) ? Null : $_POST['dateLivraisonPrevue'];

    $query = $db->prepare('UPDATE COMMANDES SET numCommandeFournisseur = :numCommandeFournisseur, datePassage = :datePassage, dateLivraisonPrevue = :dateLivraisonPrevue WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id'],
        'numCommandeFournisseur' => $_POST['numCommandeFournisseur'],
        'datePassage' => $_POST['datePassage'],
        'dateLivraisonPrevue' => $_POST['dateLivraisonPrevue']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " modifie les informations de commande données par le fournisseur.", "12");
            $_SESSION['returnMessage'] = 'Informations fournisseur modifiées avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification des informations fournisseur.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script>javascript:history.go(-1);</script>";

}
?>