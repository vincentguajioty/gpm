<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['cout_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{    
	
	$queryDelete = $db->prepare('DELETE FROM CENTRE_COUTS_PERSONNES WHERE idCentreDeCout = :idCentreDeCout');
    $queryDelete->execute([
        ':idCentreDeCout' => $_GET['id']
    ]);
    if (!empty($_POST['idPersonne'])) {
        $insertSQL = 'INSERT INTO CENTRE_COUTS_PERSONNES (idPersonne, idCentreDeCout) VALUES';
        foreach ($_POST['idPersonne'] as $idPersonne) {
            $insertSQL .= ' ('. (int)$idPersonne.', '. (int)$_GET['id'] .'),';
        }

        $insertSQL = substr($insertSQL, 0, -1);

        $db->query($insertSQL);
    }
	
	$_POST['dateOuverture'] = ($_POST['dateOuverture'] == Null) ? Null : $_POST['dateOuverture'];
	$_POST['dateFermeture'] = ($_POST['dateFermeture'] == Null) ? Null : $_POST['dateFermeture'];
	
    $query = $db->prepare('
        UPDATE
            CENTRE_COUTS
        SET
            libelleCentreDecout    = :libelleCentreDecout,
            commentairesCentreCout = :commentairesCentreCout,
            dateOuverture          = :dateOuverture,
			dateFermeture          = :dateFermeture
        WHERE
            idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout'         => $_GET['id'],
        'libelleCentreDecout'    => $_POST['libelleCentreDecout'],
        'commentairesCentreCout' => $_POST['commentairesCentreCout'],
        'dateOuverture'         => $_POST['dateOuverture'],
		'dateFermeture'         => $_POST['dateFermeture'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de du centre de couts " . $_POST['libelleCentreDecout'], '1', NULL);
            $_SESSION['returnMessage'] = 'Centre de couts modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du centre de couts " . $_POST['libelleCentreDecout'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un centre de couts existe déjà avec le même libelle. Merci de changer le libelle.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modifciation du centre de couts " . $_POST['libelleCentreDecout'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la modification du centre de couts.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>