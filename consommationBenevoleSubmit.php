<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'verrouIPcheck.php';

if (checkIP($_SERVER['REMOTE_ADDR'])==1)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

if ($MAINTENANCE)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}

if ($CONSOMMATION_BENEVOLES==0)
{
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
}
else
{
	
	$_POST['commentairesConsommation'] = str_replace($XSS_SECURITY, "", $_POST['commentairesConsommation']);

	$query = $db->prepare('
		INSERT INTO
			LOTS_CONSOMMATION
		SET
			nomDeclarantConsommation = :nomDeclarantConsommation,
			dateConsommation         = :dateConsommation,
			evenementConsommation    = :evenementConsommation,
			commentairesConsommation = :commentairesConsommation,
			ipDeclarantConsommation  = :ipDeclarantConsommation
	');
	$query->execute(array(
		'nomDeclarantConsommation' => $_SESSION['nomDeclarantConsommation'],
		'dateConsommation'         => $_SESSION['dateConsommation'],
		'evenementConsommation'    => $_SESSION['evenementConsommation'],
		'commentairesConsommation' => $_POST['commentairesConsommation'],
		'ipDeclarantConsommation'  => $_SESSION['ipDeclarantConsommation'],
	));

	$query = $db->query('SELECT MAX(idConsommation) as idConsommation FROM LOTS_CONSOMMATION;');
	$data = $query->fetch();
	$idConsommation = $data['idConsommation'];

	foreach($_SESSION['consoArray'] as $line => $content)
	{
		$content[3] = ($content[3] == Null) ? Null : $content[3];

		$query = $db->prepare('
			INSERT INTO
				LOTS_CONSOMMATION_MATERIEL
			SET
				idConsommation       = :idConsommation,
				idMaterielCatalogue  = :idMaterielCatalogue,
				idLot                = :idLot,
				quantiteConsommation = :quantiteConsommation,
				idConteneur          = :idConteneur
		');
		$query->execute(array(
			'idConsommation'       => $idConsommation,
			'idMaterielCatalogue'  => $content[0],
			'idLot'                => $content[1],
			'quantiteConsommation' => $content[2],
			'idConteneur'          => $content[3],
		));
	}
	
    $_SESSION['EXIT'] = true;

    echo "<script type='text/javascript'>document.location.replace('consommationBenevole.php');</script>";
    
}
?>