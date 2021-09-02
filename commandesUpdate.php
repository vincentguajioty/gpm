<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';

if ($_SESSION['commande_ajout']==0 AND $_SESSION['commande_etreEnCharge']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idCentreDeCout'] = ($_POST['idCentreDeCout'] == Null) ? Null : $_POST['idCentreDeCout'];
    $_POST['idLieuLivraison'] = ($_POST['idLieuLivraison'] == Null) ? Null : $_POST['idLieuLivraison'];
    $_POST['idFournisseur'] = ($_POST['idFournisseur'] == Null) ? Null : $_POST['idFournisseur'];


    $query = $db->prepare('
        UPDATE
            COMMANDES
        SET
            nomCommande        = :nomCommande,
            idCentreDeCout     = :idCentreDeCout,
            idFournisseur      = :idFournisseur,
            idLieuLivraison    = :idLieuLivraison,
            remarquesGenerales = :remarquesGenerales
        WHERE
            idCommande = :idCommande
        ;');
    $query->execute(array(
        'idCommande'         => $_GET['id'],
        'nomCommande'        => $_POST['nomCommande'],
        'idCentreDeCout'     => $_POST['idCentreDeCout'],
        'idFournisseur'      => $_POST['idFournisseur'],
        'idLieuLivraison'    => $_POST['idLieuLivraison'],
        'remarquesGenerales' => $_POST['remarquesGenerales']
    ));
    
    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '1', NULL);
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " modifie la commande.", "12");
            $_SESSION['returnMessage'] = 'Commande modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de la commande.";
            $_SESSION['returnType'] = '2';

    }
    
    $queryDelete = $db->prepare('DELETE FROM COMMANDES_DEMANDEURS WHERE idCommande = :idCommande');
    $queryDelete->execute([
        ':idCommande' => $_GET['id']
    ]);
    if (!empty($_POST['idDemandeur'])) {
        $insertSQL = 'INSERT INTO COMMANDES_DEMANDEURS (idDemandeur, idCommande) VALUES';
        foreach ($_POST['idDemandeur'] as $idDemandeur) {
            $insertSQL .= ' ('. (int)$idDemandeur.', '. (int)$_GET['id'] .'),';
        }

        $insertSQL = substr($insertSQL, 0, -1);

        $db->query($insertSQL);
    }
    $queryDelete = $db->prepare('DELETE FROM COMMANDES_OBSERVATEURS WHERE idCommande = :idCommande');
    $queryDelete->execute([
        ':idCommande' => $_GET['id']
    ]);
    if (!empty($_POST['idObservateur'])) {
        $insertSQL = 'INSERT INTO COMMANDES_OBSERVATEURS (idObservateur, idCommande) VALUES';
        foreach ($_POST['idObservateur'] as $idObservateur) {
            $insertSQL .= ' ('. (int)$idObservateur.', '. (int)$_GET['id'] .'),';
        }

        $insertSQL = substr($insertSQL, 0, -1);

        $db->query($insertSQL);
    }
    $queryDelete = $db->prepare('DELETE FROM COMMANDES_AFFECTEES WHERE idCommande = :idCommande');
    $queryDelete->execute([
        ':idCommande' => $_GET['id']
    ]);
    if (!empty($_POST['idAffectee'])) {
        $insertSQL = 'INSERT INTO COMMANDES_AFFECTEES (idAffectee, idCommande) VALUES';
        foreach ($_POST['idAffectee'] as $idAffectee) {
            $insertSQL .= ' ('. (int)$idAffectee.', '. (int)$_GET['id'] .'),';
        }

        $insertSQL = substr($insertSQL, 0, -1);

        $db->query($insertSQL);
    }

    echo "<script>window.location = document.referrer;</script>";

}
?>