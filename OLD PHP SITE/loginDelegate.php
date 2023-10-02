<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'logCheck.php';

if ($_SESSION['delegation']==0 OR $_SESSION['DELEGATION_ACTIVE']==1)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS v ON p.idPersonne = v.idPersonne WHERE p.idPersonne = :idPersonne;');
	$query->execute(array(
	    'idPersonne' => $_GET['idDelegate']
	));
	$data = $query->fetch();

	if($MAINTENANCE==1 AND $data['maintenance']==0)
	{
		$_SESSION['returnMessage'] = 'L\'utilisateur n\'est pas autorisé à se connecter en mode maintenance.';
        $_SESSION['returnType'] = '2';
        echo "<script>window.location = document.referrer;</script>";
		exit;
	}

	writeInLogs("Connection en délégation sur le compte de " . $data['identifiant'], '1', NULL);

	$_SESSION['LOGS_DELEGATION_PREFIXE']                   = '('.$_SESSION['identifiant'].')';
	$_SESSION['DELEGATION_ACTIVE']                         = 1;
	$_SESSION['idPersonne_OLD']                            = $_SESSION['idPersonne'];
	$_SESSION['identifiant_OLD']                           = $_SESSION['identifiant'];
	
	loadSession($_GET['idDelegate']);

	$_SESSION['LAST_ACTIVITY']                             = time();

	sleep(1);
	writeInLogs("Connexion réussie.", '1', NULL);
	echo "<script type='text/javascript'>document.location.replace('index.php');</script>";

}

?>