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

    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 7, dateCloture = CURRENT_TIMESTAMP WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Clôture de la commande " . $_GET['id'], '1', NULL);
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " clôture la commande.", "16");

            sendMailCmdStage($_GET['id'],29);
            sendMailCmdStage($_GET['id'],30);
            sendMailCmdStage($_GET['id'],31);
            sendMailCmdStage($_GET['id'],32);

            $_SESSION['returnMessage'] = 'Commande clôturée.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la clôture de la commande ". $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la clôture de la commande.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";

}
?>