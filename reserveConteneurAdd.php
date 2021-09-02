<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['reserve_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idLieu'] = ($_POST['idLieu'] == Null) ? Null : $_POST['idLieu'];

    $query = $db->prepare('
        INSERT INTO
            RESERVES_CONTENEUR
        SET
            idLieu                = :idLieu,
            libelleConteneur      = :libelleConteneur,
            dateDernierInventaire = :dateDernierInventaire,
            frequenceInventaire   = :frequenceInventaire
        ;');
    $query->execute(array(
        'idLieu'                => $_POST['idLieu'],
        'libelleConteneur'      => $_POST['libelleConteneur'],
        'dateDernierInventaire' => $_POST['dateDernierInventaire'],
        'frequenceInventaire'   => $_POST['frequenceInventaire']
    ));
    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du conteneur de réserve " . $_POST['libelleConteneur'], '1', NULL);
            $_SESSION['returnMessage'] = 'Conteneur ajouté avec succès.';
            $_SESSION['returnType'] = '1';
        break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du conteneur de réserve " . $_POST['libelleConteneur'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de l\'ajout du conteneur.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";


}
?>