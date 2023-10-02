<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'logCheck.php';

if ($_SESSION['DELEGATION_ACTIVE']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS v ON p.idPersonne = v.idPersonne WHERE p.idPersonne = :idPersonne;');
	$query->execute(array(
	    'idPersonne' => $_SESSION['idPersonne_OLD']
	));
	$data = $query->fetch();

	writeInLogs("Déconnection en délégation sur le compte de " . $data['identifiant'], '1', NULL);

	$_SESSION['LOGS_DELEGATION_PREFIXE'] = '';
	$_SESSION['DELEGATION_ACTIVE'] = 0;
	
	loadSession($_SESSION['idPersonne_OLD']);
	
	unset($_SESSION['idPersonne_OLD']);
	unset($_SESSION['identifiant_OLD']);

	$_SESSION['LAST_ACTIVITY']                             = time();

	sleep(1);
	writeInLogs("Connexion réussie.", '1', NULL);
	echo "<script type='text/javascript'>document.location.replace('index.php');</script>";

}

?>