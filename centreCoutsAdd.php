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

    $query = $db->prepare('INSERT INTO CENTRE_COUTS(libelleCentreDecout, commentairesCentreCout) VALUES(:libelleCentreDecout, :commentairesCentreCout);');
    $query->execute(array(
        'libelleCentreDecout' => $_POST['libelleCentreDecout'],
        'commentairesCentreCout' => $_POST['commentairesCentreCout']
    ));

    switch($query->errorCode())
    {
        case '00000':
            $query = $db->query('SELECT MAX(idCentreDeCout) as idCentreDeCout FROM CENTRE_COUTS;');
            $data = $query ->fetch();
            
	        if (!empty($_POST['idPersonne'])) {
		        $insertSQL = 'INSERT INTO CENTRE_COUTS_PERSONNES (idPersonne, idCentreDeCout) VALUES';
		        foreach ($_POST['idPersonne'] as $idPersonne) {
		            $insertSQL .= ' ('. (int)$idPersonne.', '. (int)$data['idCentreDeCout'] .'),';
		        }
		
		        $insertSQL = substr($insertSQL, 0, -1);
		
		        $db->query($insertSQL);
		    }
		    
		    writeInLogs("Ajout du centre de couts " . $_POST['libelleCentreDecout'], '2');
            $_SESSION['returnMessage'] = 'Centre de couts ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du centre de couts " . $_POST['libelleCentreDecout'], '5');
            $_SESSION['returnMessage'] = 'Un centre de cout existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du centre de couts " . $_POST['libelleCentreDecout'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du centre de couts.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>