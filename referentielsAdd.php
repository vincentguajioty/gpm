<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['typesLots_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('INSERT INTO LOTS_TYPES SET libelleTypeLot = :libelleTypeLot;');
    $query->execute(array(
        'libelleTypeLot' => $_POST['libelleTypeLot']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du référentiel " . $_POST['libelleTypeLot'], '1', NULL);
            $_SESSION['returnMessage'] = 'Référentiel ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            $query = $db->query('SELECT MAX(idTypeLot) as idTypeLot FROM LOTS_TYPES;');
            $data = $query->fetch();
            echo "<script type='text/javascript'>document.location.replace('referentielsContenu.php?id=" . $data['idTypeLot'] . "');</script>";
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du référentiel " . $_POST['libelleTypeLot'], '2', NULL);
            $_SESSION['returnMessage'] = "Un référentiel existe déjà avec le même libellé. Merci de changer le libellé";
            $_SESSION['returnType'] = '2';
            echo "<script>window.location = document.referrer;</script>";
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du référentiel " . $_POST['libelleTypeLot'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout du référentiel.";
            $_SESSION['returnType'] = '2';
            echo "<script>window.location = document.referrer;</script>";
    }
  
}
?>