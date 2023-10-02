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
            writeInLogs("Modification de la commande " . $_GET['id']." sur la page des renseignements sur le passage auprès des fournisseurs.", '1', NULL);
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " modifie les informations de commande données par le fournisseur.", "12");
            $_SESSION['returnMessage'] = 'Informations fournisseur modifiées avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande ".$_GET['id']." sur la page des renseignements sur le passage auprès des fournisseurs.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification des informations fournisseur.";
            $_SESSION['returnType'] = '2';

    }

    

    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 4 WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Passage de la commande " . $_GET['id']." auprès du fournisseur.", '1', NULL);
            
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " a lancer la commande chez le fournisseur. La commande est désormais en attente de livraison.", "21");

            sendMailCmdStage($_GET['id'],13);
            sendMailCmdStage($_GET['id'],14);
            sendMailCmdStage($_GET['id'],15);
            sendMailCmdStage($_GET['id'],16);
            sendMailCmdStage($_GET['id'],37);
            break;

        default:
            writeInLogs("Erreur inconnue lors du passage de la commande ".$_GET['id']." auprès du fournisseur.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de la commande.";
            $_SESSION['returnType'] = '2';

    }


    

    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";

}
?>