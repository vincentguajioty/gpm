<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';

if ($_SESSION['commande_ajout']==0)
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


    $query = $db->prepare('INSERT INTO COMMANDES(idEtat, idDemandeur, idObservateur, idValideur, idAffectee, idCentreDeCout, idFournisseur, idLieuLivraison, dateCreation, remarquesGenerales) VALUES(1, :idDemandeur, :idObservateur, :idValideur, :idAffectee, :idCentreDeCout, :idFournisseur, :idLieuLivraison, CURRENT_TIMESTAMP, :remarquesGenerales);');
    $query->execute(array(
        'idDemandeur' => $_SESSION['idPersonne'],
        'idObservateur' => $_POST['idObservateur'],
        'idValideur' => $_POST['idValideur'],
        'idAffectee' => $_POST['idAffectee'],
        'idCentreDeCout' => $_POST['idCentreDeCout'],
        'idFournisseur' => $_POST['idFournisseur'],
        'idLieuLivraison' => $_POST['idLieuLivraison'],
        'remarquesGenerales' => $_POST['remarquesGenerales']
    ));
    
    $query = $db->query('SELECT MAX(idCommande) as idCommande FROM COMMANDES;');
    $data = $query ->fetch();

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de la commande " . $data['idCommande'], '2');
            addCommandeComment($data['idCommande'], $_SESSION['identifiant'] . " crée la commande.", "1");
            echo "<script type='text/javascript'>document.location.replace('commandeView.php?id=" . $data['idCommande'] . "');</script>";
            break;

        default:
            writeInLogs("Erreur inconnue lors de la création de la commande.", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la création de la commande.";
            $_SESSION['returnType'] = '2';
            echo "<script>javascript:history.go(-2);</script>";
    }

}
?>