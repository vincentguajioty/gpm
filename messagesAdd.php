<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['messages_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $query = $db->prepare('
        INSERT INTO
            MESSAGES
        SET
            idPersonne   = :idPersonne,
            titreMessage = :titreMessage,
            corpsMessage = :corpsMessage
        ;');
    $query->execute(array(
        'idPersonne'   => $_SESSION['idPersonne'],
        'titreMessage' => $_POST['titreMessage'],
        'corpsMessage' => $_POST['corpsMessage']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du message " . $_POST['titreMessage'], '1', NULL);
            $_SESSION['returnMessage'] = 'Message ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du message " . $_POST['titreMessage'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du message.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>