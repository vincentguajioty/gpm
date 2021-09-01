<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"Modele_Import_Utilisateurs.csv\"");
readfile('documents/gpmTechnique/userImportTemplate.csv'); // do the double-download-dance (dirty but worky)

/*NE RIEN ECRIRE APRES SINON BUG DANS L'OUVERTURE DU FICHIER !*/
?>