<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['etats_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('
        INSERT INTO
            MATERIEL_ETATS
        SET
            libelleMaterielsEtat = :libelleMaterielsEtat
        ;');
    $query->execute(array(
        'libelleMaterielsEtat' => $_POST['libelleMaterielsEtat']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'état de matériel " . $_POST['libelleMaterielsEtat'], '1', NULL);
            $_SESSION['returnMessage'] = 'Etat ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'état de matériel " . $_POST['libelleMaterielsEtat'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de l\'état de matériel.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>