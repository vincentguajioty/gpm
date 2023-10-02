<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['materiel_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $ok = 0;
    $doublons = 0;
    $nok = 0;
    
    foreach(array_keys($_POST['formArray']) as $idMateriel)
    {

        $query = $db->prepare('
            INSERT INTO
                MATERIEL_ELEMENT
            SET
                idMaterielCatalogue = :idMaterielCatalogue,
                idEmplacement       = :idEmplacement,
                quantite            = :quantite,
                quantiteAlerte      = :quantiteAlerte
            ;');
        $query->execute(array(
            'idMaterielCatalogue' => $idMateriel,
            'idEmplacement'       => $_POST['formArray'][$idMateriel]['idEmplacement'],
            'quantite'            => $_POST['formArray'][$idMateriel]['qtt'],
            'quantiteAlerte'      => $_POST['formArray'][$idMateriel]['qttAlerte']
        ));
        
        switch($query->errorCode())
	    {
	        case '00000':
	            $ok = $ok + 1;
	            break;
	
	        case '23000':
	            $doublons = $doublons + 1;
	            break;
	
	        default:
	            $nok = $nok + 1;
	    }

    }

	checkOneConf($_GET['id']);
	
	writeInLogs("Import du référentiel dans le lot " . $_GET['id'] . " avec ". $ok ." imports réussi, ". $doublons ." imports annulés car doublons, ". $nok ." imports annulés car erreur inconnue.", '1', NULL);
    $_SESSION['returnMessage'] = 'Import terminé. '.$ok.' éléments importés avec succès, '.$doublons.' éléments non-importés car déjà existants, '.$nok.' éléments non-importés pour raisons inconnues.';
    if ($doublons + $nok == 0){$_SESSION['returnType'] = '1';}else{$_SESSION['returnType'] = '2';}

    echo "<script type='text/javascript'>document.location.replace('lotsContenu.php?id=" . $_GET['id'] . "');</script>";

}
?>