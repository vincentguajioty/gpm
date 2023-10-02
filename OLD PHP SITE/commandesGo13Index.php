<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if (cmdEstValideur($_SESSION['idPersonne'],$_GET['id'])!=1 AND $_SESSION['commande_valider_delegate']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    if ($_GET['action'] == 'v')
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 3, remarquesValidation = :remarquesValidation, dateValidation = CURRENT_TIMESTAMP WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesValidation' => "Action directe"
        ));
        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("Validation positive de la commande " . $_GET['id'], '1', NULL);

                addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " valide la commande avec le commentaire: " . "Action directe", "13");

                sendMailCmdStage($_GET['id'],1);
                sendMailCmdStage($_GET['id'],2);
                sendMailCmdStage($_GET['id'],3);
                sendMailCmdStage($_GET['id'],4);
                break;

            default:
                writeInLogs("Erreur inconnue lors de la validation positive de la commande ".$_GET['id'], '3', NULL);
                $_SESSION['returnMessage'] = "Erreur inconnue lors l'enregistrement de la validation.";
                $_SESSION['returnType'] = '2';

        }
        
    }
    if ($_GET['action'] == 'vd')
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 3, remarquesValidation = :remarquesValidation, dateValidation = CURRENT_TIMESTAMP WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesValidation' => '('.$_SESSION['identifiant'].') '."Action directe"
        ));
        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("Validation positive par valideur omniscient de la commande " . $_GET['id'], '1', NULL);

                addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " valide via délégation la commande avec le commentaire: " . "Action directe", "13");

                sendMailCmdStage($_GET['id'],1);
                sendMailCmdStage($_GET['id'],2);
                sendMailCmdStage($_GET['id'],3);
                sendMailCmdStage($_GET['id'],4);
                break;

            default:
                writeInLogs("Erreur inconnue lors de la validation positive par valideur omniscient de la commande ".$_GET['id'], '3', NULL);
                $_SESSION['returnMessage'] = "Erreur inconnue lors l'enregistrement de la validation.";
                $_SESSION['returnType'] = '2';

        }

    }
    if ($_GET['action'] == 'r')
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 1 WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
        ));
        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("Validation négative de la commande " . $_GET['id'], '1', NULL);

                addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " refuse la commande avec le commentaire: " . "Action directe", "19");

                sendMailCmdStage($_GET['id'],5);
                sendMailCmdStage($_GET['id'],6);
                sendMailCmdStage($_GET['id'],7);
                sendMailCmdStage($_GET['id'],8);
                break;

            default:
                writeInLogs("Erreur inconnue lors de la validation négative de la commande ".$_GET['id'], '3', NULL);
                $_SESSION['returnMessage'] = "Erreur inconnue lors l'enregistrement de la validation.";
                $_SESSION['returnType'] = '2';

        }
        
    }
    if ($_GET['action'] == 'rd')
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 1 WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
        ));
        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("Validation négative par valideur omniscient de la commande " . $_GET['id'], '1', NULL);

                addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " refuse via délégation la commande avec le commentaire: " . "Action directe", "19");

                sendMailCmdStage($_GET['id'],5);
                sendMailCmdStage($_GET['id'],6);
                sendMailCmdStage($_GET['id'],7);
                sendMailCmdStage($_GET['id'],8);
                break;

            default:
                writeInLogs("Erreur inconnue lors de la validation négative par valideur omniscient de la commande ".$_GET['id'], '3', NULL);
                $_SESSION['returnMessage'] = "Erreur inconnue lors l'enregistrement de la validation.";
                $_SESSION['returnType'] = '2';

        }
    }

    echo "<script type='text/javascript'>document.location.replace('commandesNonCloses.php');</script>";

}
?>