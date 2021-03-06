<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['sac2_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['libelleSac'] = ($_POST['libelleSac'] == Null) ? Null : $_POST['libelleSac'];

    $query = $db->prepare('
        UPDATE
            MATERIEL_EMPLACEMENT
        SET
            libelleEmplacement = :libelleEmplacement,
            idSac              = :idSac
        WHERE
            idEmplacement = :idEmplacement;');
    $query->execute(array(
        'idEmplacement'      => $_GET['id'],
        'libelleEmplacement' => $_POST['libelleEmplacement'],
        'idSac'              => $_POST['libelleSac']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de l'emplacement " . $_POST['libelleEmplacement'], '1', NULL);
            $_SESSION['returnMessage'] = 'Emplacement modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification de l'emplacement " . $_POST['libelleEmplacement'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un emplacement existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de l'emplacement " . $_POST['libelleEmplacement'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de l'emplacement.";
            $_SESSION['returnType'] = '2';
    }
	
	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";}
?>