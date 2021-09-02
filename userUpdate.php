<?php
session_start();
require_once('logCheck.php');
?>


<?php
    require_once 'config/bdd.php';
    $query = $db->prepare('
        UPDATE
            PERSONNE_REFERENTE
        SET
            nomPersonne    = :nomPersonne,
            prenomPersonne = :prenomPersonne,
            mailPersonne   = :mailPersonne,
            telPersonne    = :telPersonne,
            fonction       = :fonction
        WHERE
            idPersonne     = :idPersonne
        ;');
    $query->execute(array(
        'nomPersonne'    => $_POST['nomPersonne'],
        'prenomPersonne' => $_POST['prenomPersonne'],
        'mailPersonne'   => $_POST['mailPersonne'],
        'telPersonne'    => $_POST['telPersonne'],
        'fonction'       => $_POST['fonction'],
        'idPersonne'     => $_SESSION['idPersonne']
    ));

    $_SESSION['nomPersonne']    = $_POST['nomPersonne'];
    $_SESSION['prenomPersonne'] = $_POST['prenomPersonne'];
    $_SESSION['mailPersonne']   = $_POST['mailPersonne'];
    $_SESSION['telPersonne']    = $_POST['telPersonne'];
    $_SESSION['fonction']       = $_POST['fonction'];


switch($query->errorCode())
{
    case '00000':
        writeInLogs("L'utilisateur " . $_SESSION['identifiant'] . " a modifié son profil.", '1', NULL);
        $_SESSION['returnMessage'] = 'Profil mis à jour avec succès.';
        $_SESSION['returnType'] = '1';
        break;

    default:
        writeInLogs("Erreur inconnue lors de la modification de son profil par l'utilisateur " . $_SESSION['identifiant'], '3', NULL);
        $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du profil.";
        $_SESSION['returnType'] = '2';
}


    echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
?>