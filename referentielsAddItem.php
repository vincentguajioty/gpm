<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['typesLots_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['obligatoire'] = ($_POST['obligatoire'] == 'option1') ? 1 : 0;
    $_POST['libelleMateriel'] = ($_POST['libelleMateriel'] == Null) ? Null : $_POST['libelleMateriel'];

    $query = $db->prepare('SELECT * FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot;');
    $query->execute(array(
        'idTypeLot' => $_GET['idLot']
    ));
    $data2 = $query->fetch();

    $query = $db->prepare('INSERT INTO REFERENTIELS(idMaterielCatalogue, idTypeLot, quantiteReferentiel, obligatoire, commentairesReferentiel) VALUES(:idMaterielCatalogue, :idTypeLot, :quantiteReferentiel, :obligatoire, :commentairesReferentiel);');
    $query->execute(array(
        'idMaterielCatalogue' => $_POST['libelleMateriel'],
        'idTypeLot' => $_GET['idLot'],
        'quantiteReferentiel' => $_POST['quantiteReferentiel'],
        'obligatoire' => $_POST['obligatoire'],
        'commentairesReferentiel' => $_POST['commentairesReferentiel'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du référentiel " . $data2['libelleTypeLot'] . " avec ajout du materiel " . $_POST['libelleMateriel'], '3');
            $_SESSION['returnMessage'] = 'Item ajouté avec succès au référentiel.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon déteté lors de la modification du référentiel " . $data2['libelleTypeLot'] . " avec ajout du materiel " . $_POST['libelleMateriel'], '5');
            $_SESSION['returnMessage'] = "Cet item est déjà présent dans le référentiel. Pour changer sa quantié, veuillez d'abord le supprimer du référentiel pour le remettre avec la nouvelle quantité.";
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du référentiel " . $data2['libelleTypeLot'] . " avec ajout du materiel " . $_POST['libelleMateriel'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout de l'item.";
            $_SESSION['returnType'] = '2';
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>