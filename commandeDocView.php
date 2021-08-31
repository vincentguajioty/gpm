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

header('Content-type: application/pdf');
header('Content-Disposition: inline; filename='.$data['nomDocCommande'].'.'.$data['formatDocCommande']);
@readfile($data['urlFichierDocCommande']);


?>
