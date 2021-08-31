<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

$query = $db->prepare('SELECT * FROM DOCUMENTS_COMMANDES WHERE idDocCommande = :idDocCommande;');
$query->execute(array(
    'idDocCommande' => $_GET['idDoc']
));
$data = $query->fetch();

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($data['nomDocCommande'].'.'.$data['formatDocCommande']) . "\"");
readfile($data['urlFichierDocCommande']); // do the double-download-dance (dirty but worky)

/*NE RIEN ECRIRE APRES SINON BUG DANS L'OUVERTURE DU FICHIER !*/

?>