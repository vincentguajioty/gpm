<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

$envois = 0;
$message = $RETOURLIGNE.$_POST['contenu'].$RETOURLIGNE;

$sujet = "[" . $APPNAME . "] " . $_POST['sujet'];
queueMail("Messages mail aux équipes", $_SESSION['mailPersonne'], $sujet, 2, $message);

if (!empty($_POST['idPersonne'])) {
	$listePersonnes = 'SELECT DISTINCT mailPersonne FROM PERSONNE_REFERENTE WHERE ';
	foreach ($_POST['idPersonne'] as $idPersonne) {
	    $listePersonnes .= 'idPersonne='. (int)$idPersonne.' OR ';
	}

	$listePersonnes = substr($listePersonnes, 0, -3);
}
else
{
	$listePersonnes = 'SELECT mailPersonne FROM PERSONNE_REFERENTE WHERE idPersonne IS NULL';
}


if (!empty($_POST['idProfil'])) {
	$listeProfils = 'SELECT DISTINCT pe.mailPersonne FROM PROFILS_PERSONNES po LEFT OUTER JOIN PERSONNE_REFERENTE pe ON po.idPersonne = pe.idPersonne WHERE ';
	foreach ($_POST['idProfil'] as $idProfil) {
	    $listeProfils .= 'po.idProfil='. (int)$idProfil.' OR ';
	}

	$listeProfils = substr($listeProfils, 0, -3);
}
else
{
	$listeProfils = 'SELECT mailPersonne FROM PERSONNE_REFERENTE WHERE idPersonne IS NULL';
}


$requeteFinale = '(' . $listePersonnes . ') UNION (' . $listeProfils . ');';

$query = $db->query($requeteFinale);

while ($data = $query->fetch())
{
	$envois += 1 ;
	queueMail("Messages mail aux équipes", $data['mailPersonne'], $sujet, 2, $message);
}

$_SESSION['returnMessage'] = 'Les ' . $envois . ' messages ont été enregistrés dans la file d\'envoi.';
$_SESSION['returnType'] = '1';


echo "<script type='text/javascript'>document.location.replace('index.php');</script>";

?>