<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['alertesBenevolesLots_affectation']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('SELECT * FROM LOTS_ALERTES WHERE idAlerte = :idAlerte;');
    $query->execute(array('idAlerte'=>$_GET['id']));
    $declaration = $query->fetch();

    $query = $db->prepare('INSERT INTO VERROUILLAGE_IP(adresseIPverr, dateVerr, commentaire)VALUES(:adresseIPverr, :dateVerr, :commentaire);');
    $query->execute(array(
        'dateVerr' => date('Y-m-d H:i:s'),
        'adresseIPverr' => $declaration['ipDeclarant'],
        'commentaire' => 'Verrouillage par '.$_SESSION['identifiant'].' car IP estimée frauduleuse depuis le module de remontées d\'alertes bénévoles sur les lots.',
    ));

    $query = $db->prepare('DELETE FROM LOTS_ALERTES WHERE ipDeclarant = :ipDeclarant AND dateCreationAlerte > :dateCreationAlerte AND idLotsAlertesEtat = 1;');
    $query->execute(array(
        'ipDeclarant'        => $declaration['ipDeclarant'],
        'dateCreationAlerte' => date('Y-m-d', strtotime('-1 day', strtotime($declaration['dateCreationAlerte']))),
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Verrouillage définitif de l'IP " . $declaration['ipDeclarant'] . " car estimée frauduleuse depuis le module de remontées d'alerte bénévoles des lots.", '1', NULL);
            $_SESSION['returnMessage'] = 'Action enregistrée';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors du verrouillage définitif de l'IP " . $declaration['ipDeclarant'] . " car estimée frauduleuse depuis le module de remontées d'alerte bénévoles des lots.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}

?>