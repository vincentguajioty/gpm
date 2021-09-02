<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if (centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['idSource'])==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('
        INSERT INTO
            CENTRE_COUTS_OPERATIONS
        SET
            dateOperation           = :dateOperation,
            libelleOperation        = :libelleOperation,
            idCentreDeCout          = :idCentreDeCout,
            montantSortant          = :montantSortant,
            detailsMoyenTransaction = :detailsMoyenTransaction,
            idPersonne              = :idPersonne
        ;');
    $query->execute(array(
        'dateOperation'           => date('Y-m-d H:i'),
        'libelleOperation'        => 'Solde du centre de coûts',
        'idCentreDeCout'          => $_GET['idSource'],
        'montantSortant'          => $_GET['montant'],
        'detailsMoyenTransaction' => 'Transfert des fonds sur le centre de couts '.$_POST['idCible'],
        'idPersonne'              => $_SESSION['idPersonne']
    ));


    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression des fonds restant sur le centre de couts " . $_GET['idSource'], '1', NULL);

            $query = $db->prepare('
		        INSERT INTO
		            CENTRE_COUTS_OPERATIONS
		        SET
		            dateOperation           = :dateOperation,
		            libelleOperation        = :libelleOperation,
		            idCentreDeCout          = :idCentreDeCout,
		            montantEntrant          = :montantEntrant,
		            detailsMoyenTransaction = :detailsMoyenTransaction,
		            idPersonne              = :idPersonne
		        ;');
		    $query->execute(array(
		        'dateOperation'           => date('Y-m-d H:i:s'),
		        'libelleOperation'        => 'Import depuis un autre centre de coûts',
		        'idCentreDeCout'          => $_POST['idCible'],
		        'montantEntrant'          => $_GET['montant'],
		        'detailsMoyenTransaction' => 'Transfert des fonds depuis le centre de couts '.$_GET['idSource'],
		        'idPersonne'              => $_SESSION['idPersonne']
		    ));

		    switch($query->errorCode())
		    {
		        case '00000':
		        	writeInLogs("Ajout des fonds sur le centre de couts cible " . $_POST['idCible'], '1', NULL);
		        	$_SESSION['returnMessage'] = 'Fonds transférés avec succès.';
            		$_SESSION['returnType'] = '1';
            		break;

            	default:
		            writeInLogs("Erreur inconnue lors de l'arrivée des fonds sur le centre de couts " . $_POST['idCible'], '3', NULL);
		            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout des nouveaux fonds sur la cible.";
		            $_SESSION['returnType'] = '2';
		            break;
		    }
		    break;

        default:
            writeInLogs("Erreur inconnue lors du départ des fonds du centre de couts " . $_GET['idSource'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la suppression des fonds.";
            $_SESSION['returnType'] = '2';
            break;
    }

    echo "<script type='text/javascript'>document.location.replace('centreCoutsContenu.php?id=" . $_POST['idCible'] . "');</script>";
  
}
?>