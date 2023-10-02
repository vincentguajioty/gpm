<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if (centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['idCentreDeCout'])==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur WHERE idCommande = :idCommande');
    $query->execute(array('idCommande' => $_GET['idCommande']));
    $commande = $query->fetch();

    $query2 = $db->prepare('SELECT * FROM COMMANDES_MATERIEL c WHERE idCommande = :idCommande;');
    $query2->execute(array('idCommande' => $_GET['idCommande']));
    $totalCMD = 0;
    while ($data2 = $query2->fetch())
    {
        $totalCMD = $totalCMD + ($data2['prixProduitTTC']*$data2['quantiteCommande']);
    }

    $query = $db->prepare('
        INSERT INTO
            CENTRE_COUTS_OPERATIONS
        SET
            dateOperation           = :dateOperation,
            libelleOperation        = :libelleOperation,
            idCentreDeCout          = :idCentreDeCout,
            montantSortant          = :montantSortant,
            idCommande              = :idCommande,
            detailsMoyenTransaction = :detailsMoyenTransaction,
            idPersonne              = :idPersonne
        ;');
    $query->execute(array(
        'dateOperation'           => date('Y-m-d H:i:s'),
        'libelleOperation'        => 'Commande '.$_GET['idCommande'].' ('.$commande['nomFournisseur'].')',
        'idCentreDeCout'          => $_GET['idCentreDeCout'],
        'montantSortant'          => $totalCMD,
        'idPersonne'              => $_SESSION['idPersonne'],
        'idCommande'              => $_GET['idCommande'],
        'detailsMoyenTransaction' => $commande['nomCommande'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Intégration de la commande ".$_GET['idCommande']." dans le centre de couts " . $_GET['idCentreDeCout'], '1', NULL);
            $_SESSION['returnMessage'] = 'Commande intégrée avec succès.';
            $_SESSION['returnType'] = '1';

            $query = $db->prepare('UPDATE COMMANDES SET integreCentreCouts = 1 WHERE idCommande = :idCommande;');
            $query->execute(array('idCommande' => $_GET['idCommande']));

            break;

        default:
            writeInLogs("Erreur inconnue lors de l'intégration de la commande ".$_GET['idCommande']." dans le centre de couts " . $_GET['idCentreDeCout'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'intégration de la commande.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
  
}
?>