<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';

if ($_SESSION['commande_ajout']==0 AND $_SESSION['commande_etreEnCharge']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idMaterielCatalogue'] = ($_POST['idMaterielCatalogue'] == Null) ? Null : $_POST['idMaterielCatalogue'];
    $_POST['quantiteCommande'] = ($_POST['quantiteCommande'] == Null) ? Null : $_POST['quantiteCommande'];
    $_POST['referenceProduitFournisseur'] = ($_POST['referenceProduitFournisseur'] == Null) ? Null : $_POST['referenceProduitFournisseur'];
    $_POST['remiseProduit'] = ($_POST['remiseProduit'] == Null) ? Null : $_POST['remiseProduit'];
    $_POST['prixProduitHT'] = ($_POST['prixProduitHT'] == Null) ? Null : $_POST['prixProduitHT'];
    $_POST['taxeProduit'] = ($_POST['taxeProduit'] == Null) ? Null : $_POST['taxeProduit'];
    $_POST['prixProduitTTC'] = ($_POST['prixProduitTTC'] == Null) ? Null : $_POST['prixProduitTTC'];
    
    if ($_POST['idMaterielCatalogue'] == -1)
    {
        $query = $db->prepare('UPDATE COMMANDES_MATERIEL SET quantiteCommande = :quantiteCommande, referenceProduitFournisseur = :referenceProduitFournisseur, remiseProduit = :remiseProduit, prixProduitHT = :prixProduitHT, taxeProduit = :taxeProduit, prixProduitTTC = :prixProduitTTC, remarqueArticle = :remarqueArticle WHERE idCommande = :idCommande AND idMaterielCatalogue = :idMaterielCatalogue;');
        $query->execute(array(
            'idCommande' => $_GET['idCommande'],
            'idMaterielCatalogue' => Null,
            'quantiteCommande' => $_POST['quantiteCommande'],
            'referenceProduitFournisseur' => 'Frais de port',
            'remiseProduit' => $_POST['remiseProduit'],
            'prixProduitHT' => $_POST['prixProduitHT'],
            'taxeProduit' => $_POST['taxeProduit'],
            'prixProduitTTC' => $_POST['prixProduitTTC'],
            'remarqueArticle' => $_POST['remarqueArticle']
        ));
    }
    else
    {
        $query2 = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue');
        $query2->execute(array(
            'idMaterielCatalogue' => $_GET['idElement']
        ));
        $data2 = $query2->fetch();

        $query = $db->prepare('UPDATE COMMANDES_MATERIEL SET quantiteCommande = :quantiteCommande, referenceProduitFournisseur = :referenceProduitFournisseur, remiseProduit = :remiseProduit, prixProduitHT = :prixProduitHT, taxeProduit = :taxeProduit, prixProduitTTC = :prixProduitTTC, remarqueArticle = :remarqueArticle WHERE idCommande = :idCommande AND idMaterielCatalogue = :idMaterielCatalogue;');
        $query->execute(array(
            'idCommande' => $_GET['idCommande'],
            'idMaterielCatalogue' => $_GET['idElement'],
            'quantiteCommande' => $_POST['quantiteCommande'],
            'referenceProduitFournisseur' => $_POST['referenceProduitFournisseur'],
            'remiseProduit' => $_POST['remiseProduit'],
            'prixProduitHT' => $_POST['prixProduitHT'],
            'taxeProduit' => $_POST['taxeProduit'],
            'prixProduitTTC' => $_POST['prixProduitTTC'],
            'remarqueArticle' => $_POST['remarqueArticle']
        ));
    }

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['idCommande'], '3');
            addCommandeComment($_GET['idCommande'], $_SESSION['identifiant'] . " modifie " . $data2['libelleMateriel'], "12");
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de la commande.";
            $_SESSION['returnType'] = '2';
    }



    echo "<script>window.location = document.referrer;</script>";


}
?>