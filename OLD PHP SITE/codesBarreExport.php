<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['codeBarre_lecture']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{ 

	$query = $db->query('
		SELECT
            c.idCode,
			m.libelleMateriel,
			c.codeBarre,
			c.peremptionConsommable,
			c.commentairesCode
        FROM
            CODES_BARRE c
            LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue
        ORDER BY
        	m.libelleMateriel ASC
	;');
	$datas = $query->fetchAll(PDO::FETCH_ASSOC);

	$out = fopen('documents/temp/exportCodesBarre.csv', 'w');
	fputcsv($out, array('idCode','libelle','codeBarre','peremption','commentaires'));
	foreach($datas as $data)
	{
		fputcsv($out, $data);
	}
	fclose($out);

	header('Content-disposition: attachment; filename=exportCodesBarre.csv');
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary");
	readfile('documents/temp/exportCodesBarre.csv');
	unlink('documents/temp/exportCodesBarre.csv');

}
?>