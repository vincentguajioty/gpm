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
    $_POST['idResponsable'] = ($_POST['idResponsable'] == Null) ? Null : $_POST['idResponsable'];

    $query = $db->prepare('UPDATE CENTRE_COUTS SET libelleCentreDecout = :libelleCentreDecout, idResponsable = :idResponsable WHERE idCentreDeCout = :idCentreDeCout;');
    $query->execute(array(
        'idCentreDeCout' => $_GET['id'],
		'libelleCentreDecout' => $_POST['libelleCentreDecout'],
        'idResponsable' => $_POST['idResponsable']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de du centre de couts " . $_POST['libelleCentreDecout'], '3');
            $_SESSION['returnMessage'] = 'Centre de couts modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du centre de couts " . $_POST['libelleCentreDecout'], '5');
            $_SESSION['returnMessage'] = 'Un centre de couts existe déjà avec le même libelle. Merci de changer le libelle.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modifciation du centre de couts " . $_POST['libelleCentreDecout'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la modification du centre de couts.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>