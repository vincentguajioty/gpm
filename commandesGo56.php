<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['commande_etreEnCharge']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $_POST['dateLivraisoneffective'] = ($_POST['dateLivraisoneffective'] == Null) ? Null : $_POST['dateLivraisoneffective'];
    
    if ($_POST['button'] == 'ok')
    {
        $query = $db->prepare('UPDATE COMMANDES_MATERIEL SET quantiteAtransferer = quantiteCommande WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id']
        ));
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 5, remarquesLivraison = :remarquesLivraison, dateLivraisoneffective = :dateLivraisoneffective WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesLivraison' => $_POST['remarquesLivraison'],
            'dateLivraisoneffective' => $_POST['dateLivraisoneffective']
        ));
        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("Réception de la commande " . $_GET['id']." sans SAV.", '1', NULL);

                addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " réceptionne la commande sans déclencher de SAV. La commande n'a plus qu'à être clôturée.", "25");

                sendMailCmdStage($_GET['id'],21);
                sendMailCmdStage($_GET['id'],22);
                sendMailCmdStage($_GET['id'],23);
                sendMailCmdStage($_GET['id'],24);
                break;

            default:
                writeInLogs("Erreur inconnue lors de la réception de la commande ".$_GET['id']." sans SAV.", '3', NULL);
                $_SESSION['returnMessage'] = "Erreur inconnue lors la réception de la commande.";
                $_SESSION['returnType'] = '2';

        }
        
    }
    else
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 6, savHistorique = 1, remarquesLivraison = :remarquesLivraison, dateLivraisoneffective = :dateLivraisoneffective WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesLivraison' => $_POST['remarquesLivraison'],
            'dateLivraisoneffective' => $_POST['dateLivraisoneffective']
        ));
        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("Réception de la commande " . $_GET['id']." avec SAV.", '1', NULL);

                addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " réceptionne la commande et engage un SAV.", "30");

                sendMailCmdStage($_GET['id'],25);
                sendMailCmdStage($_GET['id'],26);
                sendMailCmdStage($_GET['id'],27);
                sendMailCmdStage($_GET['id'],28);
                break;

            default:
                writeInLogs("Erreur inconnue lors de la réception de la commande ".$_GET['id']." avec SAV.", '3', NULL);
                $_SESSION['returnMessage'] = "Erreur inconnue lors la réception de la commande.";
                $_SESSION['returnType'] = '2';

        }
        
    }

    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";

}
?>