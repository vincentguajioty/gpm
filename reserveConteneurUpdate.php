<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['reserve_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idLieu'] = ($_POST['idLieu'] == Null) ? Null : $_POST['idLieu'];
    $_POST['dispoBenevoles'] = ($_POST['dispoBenevoles'] == 1) ? 1 : 0;

    $query = $db->prepare('
        UPDATE
            RESERVES_CONTENEUR
        SET
            idLieu                = :idLieu,
            libelleConteneur      = :libelleConteneur,
            dateDernierInventaire = :dateDernierInventaire,
            frequenceInventaire   = :frequenceInventaire,
            dispoBenevoles        = :dispoBenevoles
        WHERE
            idConteneur = :idConteneur;');
    $query->execute(array(
        'idLieu'                => $_POST['idLieu'],
        'libelleConteneur'      => $_POST['libelleConteneur'],
        'idConteneur'           => $_GET['id'],
        'dateDernierInventaire' => $_POST['dateDernierInventaire'],
        'frequenceInventaire'   => $_POST['frequenceInventaire'],
        'dispoBenevoles'        => $_POST['dispoBenevoles'],
    ));
    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du conteneur de réserve " . $_POST['libelleConteneur'], '1', NULL);
            $_SESSION['returnMessage'] = 'Conteneur modifié avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du conteneur de réserve " . $_POST['libelleConteneur'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la modification du conteneur.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";


}
?>