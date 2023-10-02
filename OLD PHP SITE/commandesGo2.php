<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $query = $db->prepare('UPDATE COMMANDES SET idEtat = 2, dateDemandeValidation = CURRENT_TIMESTAMP, dateValidation = Null, remarquesValidation = Null WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Soumission à validation de la commande " . $_GET['id'], '1', NULL);
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " envoie la demande de validation.", "14");

            sendMailCmdStage($_GET['id'],9);
            sendMailCmdStage($_GET['id'],10);
            sendMailCmdStage($_GET['id'],11);
            sendMailCmdStage($_GET['id'],12);

            $_SESSION['returnMessage'] = 'Demande de validation enregistrée.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la soumission à validation de la commande " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la demande de validation.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";

}
?>