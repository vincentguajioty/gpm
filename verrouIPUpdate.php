<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['verrouIP']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $_POST['commentaire'] = ($_POST['commentaire'] == Null) ? Null : $_POST['commentaire'];

    $query = $db->prepare('
        UPDATE
            VERROUILLAGE_IP
        SET
            commentaire = :commentaire
        WHERE
            idIP        = :idIP
        ;');
    $query->execute(array(
        'idIP'        => $_GET['id'],
        'commentaire' => $_POST['commentaire'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Commentaire sur le verrouillage IP " . $_GET['id'], '1', NULL);
            break;

        default:
            writeInLogs("Erreur inconnue lors du commentaire du verrouillage IP " . $_GET['id'], '3', NULL);
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>