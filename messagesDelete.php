<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if(strtoupper($_POST['confirmation']) <> 'CONFIRMATION')
{
	$_SESSION['returnMessage'] = "Vérification échouée. Suppression annulée";
    $_SESSION['returnType'] = '2';
    echo "<script>window.location = document.referrer;</script>";
    exit;
}

if ($_SESSION['messages_suppression']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('SELECT * FROM MESSAGES m LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idPersonne = p.idPersonne WHERE idMessage = :idMessage;');
    $query->execute(array(
        'idMessage' => $_GET['id']
    ));
    $data = $query -> fetch();

    $query = $db->prepare('DELETE FROM MESSAGES WHERE idMessage = :idMessage;');
    $query->execute(array(
        'idMessage' => $_GET['id']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Suppression du message " . $data['titreMessage'], '4');
            $_SESSION['returnMessage'] = 'Message supprimé avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la suppression du message " . $data['titreMessage'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors la suppression du message.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script type='text/javascript'>document.location.replace('messages.php');</script>";
}
?>