<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['annuaire_mdp']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {
    $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));
    $data = $query->fetch();

    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET motDePasse = :motDePasse WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'motDePasse' => password_hash($data['identifiant'], PASSWORD_DEFAULT),
        'idPersonne' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("RAZ du mot de passe de " . $data['identifiant'], '1', NULL);
            $_SESSION['returnMessage'] = 'Mot de passe réinitialisé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la réinitialisation du mot de passe de " . $data['identifiant'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la réinitialisation du mot de passe.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>