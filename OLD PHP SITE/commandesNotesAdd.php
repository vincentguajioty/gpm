<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';

if ($_SESSION['commande_ajout']==0 AND $_SESSION['commande_etreEnCharge']==0 AND $_SESSION['commande_abandonner']==0 AND $_SESSION['commande_lecture']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    writeInLogs("Ajout d'un commentaire dans la timeline de la commande " . $_GET['id'], '1', NULL);
    addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " ajoute le commentaire: " . $_POST['notes'], "36");
    $_SESSION['commandesTab'] = 8;
    echo "<script>window.location = document.referrer;</script>";
}
?>