<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['fournisseurs_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {

    $_POST['aesFournisseur'] = ($_POST['aesFournisseur'] == Null) ? Null : $_POST['aesFournisseur'];
    
    $query = $db->prepare('
        UPDATE
            FOURNISSEURS
        SET
            aesFournisseur = AES_ENCRYPT(:aesFournisseur, :aesFour)
        WHERE
            idFournisseur = :idFournisseur
        ;');
    $query->execute(array(
        'idFournisseur'  => $_GET['id'],
        'aesFournisseur' => $_POST['aesFournisseur'],
        'aesFour'        => $_SESSION['aesFour'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification des données chiffrées du fournisseur " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Fournisseur modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification des données chiffrées du fournisseur " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification du fournisseur.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script type='text/javascript'>document.location.replace('fournisseurs.php');</script>";
}
?>