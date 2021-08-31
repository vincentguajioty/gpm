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
    $_POST['idDemandeur'] = ($_POST['idDemandeur'] == Null) ? Null : $_POST['idDemandeur'];
    $_POST['idObservateur'] = ($_POST['idObservateur'] == Null) ? Null : $_POST['idObservateur'];
    $_POST['idValideur'] = ($_POST['idValideur'] == Null) ? Null : $_POST['idValideur'];
    $_POST['idAffectee'] = ($_POST['idAffectee'] == Null) ? Null : $_POST['idAffectee'];
    $_POST['idCentreDeCout'] = ($_POST['idCentreDeCout'] == Null) ? Null : $_POST['idCentreDeCout'];
    $_POST['idLieuLivraison'] = ($_POST['idLieuLivraison'] == Null) ? Null : $_POST['idLieuLivraison'];
    $_POST['idFournisseur'] = ($_POST['idFournisseur'] == Null) ? Null : $_POST['idFournisseur'];


    $query = $db->prepare('UPDATE COMMANDES SET idDemandeur = :idDemandeur, idObservateur = :idObservateur, idValideur = :idValideur, idAffectee = :idAffectee, idCentreDeCout = :idCentreDeCout, idFournisseur = :idFournisseur, idLieuLivraison = :idLieuLivraison, remarquesGenerales = :remarquesGenerales WHERE idCommande = :idCommande;');
    $query->execute(array(
        'idCommande' => $_GET['id'],
        'idDemandeur' => $_POST['idDemandeur'],
        'idObservateur' => $_POST['idObservateur'],
        'idValideur' => $_POST['idValideur'],
        'idAffectee' => $_POST['idAffectee'],
        'idCentreDeCout' => $_POST['idCentreDeCout'],
        'idFournisseur' => $_POST['idFournisseur'],
        'idLieuLivraison' => $_POST['idLieuLivraison'],
        'remarquesGenerales' => $_POST['remarquesGenerales']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '3');
            addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " modifie la commande.", "12");
            $_SESSION['returnMessage'] = 'Commande modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de la commande.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script>javascript:history.go(-1);</script>";

}
?>