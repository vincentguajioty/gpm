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
    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET motDePasse = Null, isActiveDirectory = true WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Activation du lien AD pour l'utilisateur " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Lien AD activé';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'activation du lien AD pour l'utilisateur " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de l\'activation du lien AD.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>