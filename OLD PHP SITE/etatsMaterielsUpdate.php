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

    $query = $db->prepare('
        UPDATE
            MATERIEL_ETATS
        SET
            libelleMaterielsEtat = :libelleMaterielsEtat
        WHERE
            idMaterielsEtat = :idMaterielsEtat
        ;');
    $query->execute(array(
        'libelleMaterielsEtat' => $_POST['libelleMaterielsEtat'],
        'idMaterielsEtat'      => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de l'état de matériel " . $_POST['libelleMaterielsEtat'], '1', NULL);
            $_SESSION['returnMessage'] = 'Etat modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de l'état de matériel " . $_POST['libelleMaterielsEtat'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de l\'état.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>