<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['etats_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('UPDATE ETATS SET libelleEtat = :libelleEtat WHERE idEtat = :idEtat;');
    $query->execute(array(
        'libelleEtat' => $_POST['libelleEtat'],
        'idEtat' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de l'état " . $_POST['libelleEtat'], '3');
            $_SESSION['returnMessage'] = 'Etat modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification de l'état " . $_POST['libelleEtat'], '5');
            $_SESSION['returnMessage'] = 'Un état existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de l'état " . $_POST['libelleEtat'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification de l'état.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-2);</script>";
}
?>