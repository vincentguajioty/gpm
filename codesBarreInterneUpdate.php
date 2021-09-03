<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['codeBarre_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['peremptionConsommable'] = ($_POST['peremptionConsommable'] == Null) ? Null : $_POST['peremptionConsommable'];

    $query = $db->prepare('
        UPDATE
            CODES_BARRE
        SET
            peremptionConsommable = :peremptionConsommable,
            commentairesCode      = :commentairesCode,
            idMaterielCatalogue   = :idMaterielCatalogue
        WHERE
            idCode                = :idCode
        ;');
    $query->execute(array(
        'peremptionConsommable' => $_POST['peremptionConsommable'],
        'commentairesCode'      => $_POST['commentairesCode'],
        'idMaterielCatalogue'   => $_POST['idMaterielCatalogue'],
        'idCode'                => $_GET['id'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du code barre interne " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Code barre modifié avec succès';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du code barre interne " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification du code barre.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>