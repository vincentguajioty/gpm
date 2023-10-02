<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['commande_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();


    $query = $db->query('
    	INSERT INTO
    		COMMANDES
    	SET
    		idEtat             = 1,
    		dateCreation       = CURRENT_TIMESTAMP,
    		integreCentreCouts = 0
    	;');
    
    switch($query->errorCode())
    {
        case '00000':
            $query = $db->query('SELECT MAX(idCommande) as idCommande FROM COMMANDES;');
    		$data = $query ->fetch();
    		echo "<script type='text/javascript'>document.location.replace('commandeView.php?id=" . $data['idCommande'] . "');</script>";
    		
            writeInLogs("Ajout de la commande " . $data['idCommande'], '1', NULL);
            addCommandeComment($data['idCommande'], $_SESSION['identifiant'] . " crée la commande.", "1");

            $query = $db->prepare('INSERT INTO COMMANDES_DEMANDEURS (idDemandeur, idCommande) VALUES (:idDemandeur, :idCommande);');
            $query->execute(array('idDemandeur'=>$_SESSION['idPersonne'], 'idCommande'=>(int)$data['idCommande']));
    
            break;

        default:
            writeInLogs("Erreur inconnue lors de la création de la commande.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la création de la commande.";
            $_SESSION['returnType'] = '2';
            echo "<script>window.location = document.referrer;</script>";
    }

    

}
?>