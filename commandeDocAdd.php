<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'commandesCommentAdd.php';

if ($_SESSION['commande_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {

    $_POST['idTypeDocument'] = ($_POST['idTypeDocument'] == Null) ? Null : $_POST['idTypeDocument'];

    $ext = strtolower(substr(strrchr($_FILES['urlFichierDocCommande']['name'], '.'), 1));

    $query = $db->prepare('INSERT INTO DOCUMENTS_COMMANDES(idCommande, nomDocCommande, formatDocCommande, dateDocCommande, urlFichierDocCommande, idTypeDocument) VALUES(:idCommande, :nomDocCommande, :formatDocCommande, CURRENT_TIMESTAMP, :urlFichierDocCommande, :idTypeDocument);');
    $query->execute(array(
        'idCommande' => $_GET['idCommande'],
        'urlFichierDocCommande' => Null,
        'nomDocCommande' => $_POST['nomDocCommande'],
        'formatDocCommande' => $ext,
        'idTypeDocument' => $_POST['idTypeDocument']
    ));


    $query = $db->query('SELECT MAX(idDocCommande) as idDocCommande FROM DOCUMENTS_COMMANDES;');
    $data = $query->fetch();

    $nom = "documents/commandes/{$data['idDocCommande']}.{$ext}";

    $resultat = move_uploaded_file($_FILES['urlFichierDocCommande']['tmp_name'], $nom);
    if ($resultat) {
        $query = $db->prepare('UPDATE DOCUMENTS_COMMANDES SET urlFichierDocCommande = :urlFichierDocCommande WHERE idDocCommande = :idDocCommande;');
        $query->execute(array(
            'idDocCommande' => $data['idDocCommande'],
            'urlFichierDocCommande' => $nom
        ));
        writeInLogs("Ajout d'une pièce jointe référence " . $_POST['nomDocCommande'] . " à la commande " . $_GET['idCommande'], '2');
        addCommandeComment($_GET['idCommande'], $_SESSION['identifiant'] . " ajoute la pièce jointe " . $_POST['nomDocCommande'], "9");
    } else {
        $query = $db->prepare('DELETE FROM DOCUMENTS_COMMANDES WHERE idDocCommande = :idDocCommande;');
        $query->execute(array(
            'idDocCommande' => $data['idDocCommande']
        ));
        writeInLogs("Erreur inconnue lors de l'ajout d'une pièce jointe à la commande " . $_GET['idCommande'], '5');
        $_SESSION['returnMessage'] = "Erreur inconnue lors du chargement du document.";
        $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>