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

    $query = $db->prepare('INSERT INTO CENTRE_COUTS(libelleCentreDecout, idResponsable, commentairesCentreCout) VALUES(:libelleCentreDecout, :idResponsable, :commentairesCentreCout);');
    $query->execute(array(
        'libelleCentreDecout' => $_POST['libelleCentreDecout'],
        'idResponsable' => $_POST['idResponsable'],
        'commentairesCentreCout' => $_POST['commentairesCentreCout']
    ));

    switch($query->errorCode())
    {
        case '00000':
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