<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

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

$sujet = "[" . $APPNAME . "] - Votre demande a bien été enregistrée";
queueMail("Contact développeur", $_POST['mail'], $sujet, 2, $message);

$sujet = "[GPM] - Formulaire de contact - " . $_POST['motif'] . " - " . $_POST['module'];

queueMail("Contact développeur", 'contact@guajioty.fr', $sujet, 2, $message);

$_SESSION['returnMessage'] = 'Demande envoyée avec succès.';
$_SESSION['returnType'] = '1';

echo "<script type='text/javascript'>document.location.replace('index.php');</script>";

?>