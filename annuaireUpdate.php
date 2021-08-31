<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['annuaire_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {

    if ($_POST['libelleProfil'] == Null)
    {
        $idProfil = Null;
    }
    else
    {
        $idProfil = $_POST['libelleProfil'];
    }

    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET idProfil = :idProfil, identifiant = :identifiant, nomPersonne = :nomPersonne, prenomPersonne = :prenomPersonne, mailPersonne = :mailPersonne, telPersonne = :telPersonne, fonction = :fonction WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id'],
        'idProfil' => $idProfil,
        'identifiant' => $_POST['identifiant'],
        'nomPersonne' => $_POST['nomPersonne'],
        'prenomPersonne' => $_POST['prenomPersonne'],
        'mailPersonne' => $_POST['mailPersonne'],
        'telPersonne' => $_POST['telPersonne'],
        'fonction' => $_POST['fonction']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de l'utilisateur " . $_POST['identifiant'], '3');
            $_SESSION['returnMessage'] = 'Utilisateur modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification de l'utilisateur " . $_POST['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Un utilisateur existe déjà avec le même identifiant. Merci de changer l\'identifiant.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modifciation de l'utilisateur " . $_POST['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la modification de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-2);</script>";
}
?>