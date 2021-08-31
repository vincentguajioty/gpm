<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';

if ($_SESSION['commande_valider']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    if ($_POST['button'] == 'ok')
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 3, remarquesValidation = :remarquesValidation, dateValidation = CURRENT_TIMESTAMP WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesValidation' => $_POST['remarquesValidation']
        ));
        addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " valide la commande avec le commentaire: " . $_POST['remarquesValidation'], "13");
    }
    else
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 1, remarquesValidation = :remarquesValidation, dateValidation = CURRENT_TIMESTAMP WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesValidation' => $_POST['remarquesValidation']
        ));
        addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " refuse la commande avec le commentaire: " . $_POST['remarquesValidation'], "19");
    }

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'enregistrement de la validation.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>