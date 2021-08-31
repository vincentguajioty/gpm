<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['annuaire_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    if ($_POST['libelleProfil'] == Null)
    {
        $idProfil = Null;
    }
    else
    {
        $idProfil = $_POST['libelleProfil'];
    }

    $query = $db->prepare('INSERT INTO PERSONNE_REFERENTE(idProfil, identifiant, motDePasse, nomPersonne, prenomPersonne, mailPersonne, telPersonne, fonction) VALUES(:idProfil, :identifiant, :motDePasse, :nomPersonne, :prenomPersonne, :mailPersonne, :telPersonne, :fonction);');
    $query->execute(array(
        'idProfil' => $idProfil,
        'identifiant' => $_POST['identifiant'],
        'motDePasse' => password_hash($_POST['identifiant'], PASSWORD_DEFAULT),
        'nomPersonne' => $_POST['nomPersonne'],
        'prenomPersonne' => $_POST['prenomPersonne'],
        'mailPersonne' => $_POST['mailPersonne'],
        'telPersonne' => $_POST['telPersonne'],
        'fonction' => $_POST['fonction']
    ));
    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'utilisateur " . $_POST['identifiant'], '2');
            $_SESSION['returnMessage'] = 'Utilisateur ajouté avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout de l'utilisateur " . $_POST['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Un utilisateur existe déjà avec le même identifiant. Merci de changer l\'identifiant.';
            $_SESSION['returnType'] = '2';
        break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'utilisateur " . $_POST['identifiant'], '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de l\'ajout de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-2);</script>";


}
?>