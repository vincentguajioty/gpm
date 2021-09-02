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

    $query = $db->prepare('UPDATE COMMANDES_MATERIEL SET quantiteAtransferer = quantiteCommande WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));
    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 5 WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Cloture du SAV de la commande " . $_GET['id'], '1', NULL);
            
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " clôture le SAV. La commande n'a plus qu'à être clôturée.", "25");

            sendMailCmdStage($_GET['id'],17);
            sendMailCmdStage($_GET['id'],18);
            sendMailCmdStage($_GET['id'],19);
            sendMailCmdStage($_GET['id'],20);
            break;

        default:
            writeInLogs("Erreur inconnue lors de la cloture du SAV de la commande ". $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de la commande.";
            $_SESSION['returnType'] = '2';

    }

    

    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";

}
?>