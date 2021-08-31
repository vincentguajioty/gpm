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
    $_POST['dateLivraisoneffective'] = ($_POST['dateLivraisoneffective'] == Null) ? Null : $_POST['dateLivraisoneffective'];
    
    if ($_POST['button'] == 'ok')
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 5, remarquesLivraison = :remarquesLivraison, dateLivraisoneffective = :dateLivraisoneffective WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesLivraison' => $_POST['remarquesLivraison'],
            'dateLivraisoneffective' => $_POST['dateLivraisoneffective']
        ));
        addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " réceptionne la commande sans déclencher de SAV. La commande n'a plus qu'à être clôturée.", "25");
    }
    else
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 6, remarquesLivraison = :remarquesLivraison, dateLivraisoneffective = :dateLivraisoneffective WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesLivraison' => $_POST['remarquesLivraison'],
            'dateLivraisoneffective' => $_POST['dateLivraisoneffective']
        ));
        addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " réceptionne la commande et engage un SAV.", "30");
    }

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la réception de la commande.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>