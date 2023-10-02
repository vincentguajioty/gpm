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

    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET motDePasse = :motDePasse, doubleAuthSecret = Null WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'motDePasse' => password_hash($SELPRE.$data['identifiant'].$SELPOST, PASSWORD_DEFAULT),
        'idPersonne' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("RAZ du mot de passe et désactivation MFA de " . $data['identifiant'], '1', NULL);
            $_SESSION['returnMessage'] = 'Mot de passe réinitialisé avec succès et double authentification désactivée.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la réinitialisation du mot de passe et désactivation MFA de " . $data['identifiant'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la réinitialisation du mot de passe et désactivation de la double authentification.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>