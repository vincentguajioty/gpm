<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['commande_abandonner']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 8 WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Abandon de la commande " . $_GET['id'], '1', NULL);
            
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " abandonne la commande.", "7");

            sendMailCmdStage($_GET['id'],33);
            sendMailCmdStage($_GET['id'],34);
            sendMailCmdStage($_GET['id'],35);
            sendMailCmdStage($_GET['id'],36);

            $_SESSION['returnMessage'] = 'Commande abandonnée.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'abandon de la commande ".$_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'abandon de la commande.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";

}
?>