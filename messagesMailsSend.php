<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

$echec = 0;
$envois = 0;
$message = $RETOURLIGNE.$_POST['contenu'].$RETOURLIGNE;

$sujet = "[" . $APPNAME . "] - " . $_POST['sujet'];
if(sendmail($_SESSION['mailPersonne'], $sujet, 2, $message))
{
    writeInLogs("Contact des équipes par mail - Copie envoyée à l'expéditeur " . $_SESSION['mailPersonne'], '1', NULL);
}
else
{
    writeInLogs("Contact des équipes par mail - Echec de l'envoi d'une copie à l'expéditeur", '3', NULL);
    $echec += 1;
}

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
	if(sendmail($data['mailPersonne'], $sujet, 2, $message))
	{
	    writeInLogs("Contact des équipes par mail - Message envoyé à " . $data['mailPersonne'], '1', NULL);
	}
	else
	{
	    writeInLogs("Contact des équipes par mail - Echec de l'envoi du message à " . $data['mailPersonne'], '3', NULL);
	    $echec += 1;
	}
}

if ($echec == 0)
{
	$_SESSION['returnMessage'] = 'Les ' . $envois . ' messages ont été envoyés avec succès.';
    $_SESSION['returnType'] = '1';
}
else
{
	$_SESSION['returnMessage'] = 'Il y a ' . $echec . '/' . $envois . ' erreurs dans l\'envoi des messages.';
    $_SESSION['returnType'] = '2';
}


echo "<script type='text/javascript'>document.location.replace('index.php');</script>";

?>