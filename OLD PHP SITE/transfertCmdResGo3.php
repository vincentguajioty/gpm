<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['reserve_cmdVersReserve']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

	!isset($_SESSION['transfertIdMaterielCatalogue']) ? $_SESSION['transfertIdMaterielCatalogue']=$_POST['idMaterielCatalogue'] : '';
	$_SESSION['transfertPeremption'] = ($_POST['peremption']==Null ? Null : $_POST['peremption']);
	
	$query = $db->prepare('
		SELECT
			*
		FROM
			COMMANDES_MATERIEL m
			LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
		WHERE
			idCommande = :idCommande
			AND
			m.idMaterielCatalogue = :idMaterielCatalogue
		;');
	$query->execute(array(
		'idCommande'          => $_SESSION['transfertCmd'],
		'idMaterielCatalogue' => $_SESSION['transfertIdMaterielCatalogue']
	));
	$data = $query->fetch();
	
	$_SESSION['transfertQttMax'] = $data['quantiteAtransferer'];
	
	$_SESSION['transfertStade'] = 3;
	
    echo "<script>window.location = document.referrer;</script>";


}
?>