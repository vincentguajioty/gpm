<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';

if (($_SESSION['commande_ajout']==0)AND($_SESSION['commande_etreEnCharge']==0))
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    if ($_GET['idMaterielCatalogue']==-1)
    {
        $query = $db->prepare('DELETE FROM COMMANDES_MATERIEL WHERE idMaterielCatalogue Is Null AND idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['idCommande']
        ));
    }
    else
    {
        $query = $db->prepare('DELETE FROM COMMANDES_MATERIEL WHERE idMaterielCatalogue = :idMaterielCatalogue AND idCommande = :idCommande;');
        $query->execute(array(
            'idMaterielCatalogue' => $_GET['idMaterielCatalogue'],
            'idCommande' => $_GET['idCommande']
        ));
    }

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression d'un item de la commande " . $_GET['idCommande'], '1', NULL);
			addCommandeComment($_GET['idCommande'], $_SESSION['identifiant'] . " modifie le contenu de la commande.", "12");
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppresion d'un item de la commande " . $_GET['idCommande'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de la commande.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-1);</script>";
}
?>