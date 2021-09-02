<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['cout_lecture']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{ 

	$query = $db->prepare('
		SELECT
			op.dateOperation,
			p.identifiant,
			op.libelleOperation,
			op.montantEntrant,
			op.montantSortant,
			op.detailsMoyenTransaction
		FROM
			CENTRE_COUTS_OPERATIONS op
			LEFT OUTER JOIN PERSONNE_REFERENTE p ON op.idPersonne = p.idPersonne
		WHERE
			idCentreDeCout = :idCentreDeCout
		ORDER BY
			dateOperation ASC
	;');
	$query->execute(array('idCentreDeCout' => $_GET['id']));
	$datas = $query->fetchAll(PDO::FETCH_ASSOC);

	$out = fopen('documents/temp/export.csv', 'w');
	fputcsv($out, array('Date_operation','Personne','Libelle','Entrant','Sortant','Details'));
	foreach($datas as $data)
	{
		fputcsv($out, $data);
	}
	fclose($out);

	header('Content-disposition: attachment; filename=export.csv');
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary");
	readfile('documents/temp/export.csv');
	unlink('documents/temp/export.csv');

}
?>