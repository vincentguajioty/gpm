<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['codeBarre_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('
        INSERT INTO
            CODES_BARRE
        SET
            codeBarre             = :codeBarre,
            internalReference     = 0,
            peremptionConsommable = Null,
            commentairesCode      = Null,
            idMaterielCatalogue   = :idMaterielCatalogue
        ;');
    $query->execute(array(
        'codeBarre'           => $_POST['codeBarre'],
        'idMaterielCatalogue' => $_POST['idMaterielCatalogue'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du du code barre fournisseur " . $_POST['codeBarre'], '1', NULL);
            $_SESSION['returnMessage'] = 'Code barre ajouté avec succès';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du code barre fournisseur " . $_POST['codeBarre'], '2', NULL);
            $_SESSION['returnMessage'] = 'Ce code barre est déjà enregistré. Merci de vérifier à quel entrée du catalogue il est lié et corriger au besoin.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du code barre fournisseur " . $_POST['codeBarre'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du code barre.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>