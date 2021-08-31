<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

$sujet = "[GMP] - Formulaire de contact - " . $_POST['motif'] . " - " . $_POST['module'];

$message = "Nom: " . $_POST['nom'] . "<br/>";
$message = $message . "Prénom: " . $_POST['prenom'] . "<br/>";
$message = $message . "Mail: " . $_POST['mail'] . "<br/>";
$message = $message . "Tel: " . $_POST['tel'] . "<br/>";
$message = $message . "Societe: " . $_POST['entite'] . "<br/>";
$message = $message . "Fonction: " . $_POST['fonction'] . "<br/><br/>";
$message = $message . "Demande: " . $_POST['motif'] . "<br/>";
$message = $message . "Module: " . $_POST['module'] . "<br/>";
$message = $message . "Version: " . $_POST['version'] . "<br/><br/>";
$message = $message . "Message:<br/>" . $_POST['contenu'];

$message = $RETOURLIGNE.$message.$RETOURLIGNE;
if(sendmail('contact@guajioty.fr', $sujet, 2, $message))
{
    $_SESSION['returnMessage'] = 'Demande envoyée avec succès.';
    $_SESSION['returnType'] = '1';
}
else
{
    $_SESSION['returnMessage'] = 'Echec lors de l\'envoi de la demande.';
    $_SESSION['returnType'] = '2';
}

echo "<script type='text/javascript'>document.location.replace('index.php');</script>";

?>