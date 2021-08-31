<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['lots_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    if ($_POST['libelleTypeLot'] == Null)
    {
        $idTypeLot = Null;
    }
    else
    {
        $idTypeLot = $_POST['libelleTypeLot'];
    }

    if ($_POST['libelleEtat'] == Null)
    {
        $idEtat = Null;
    }
    else
    {
        $idEtat = $_POST['libelleEtat'];
    }

    if ($_POST['libelleLieu'] == Null)
    {
        $idLieu = Null;
    }
    else
    {
        $idLieu = $_POST['libelleLieu'];
    }

    if ($_POST['identifiant'] == Null)
    {
        $idPersonne = Null;
    }
    else
    {
        $idPersonne = $_POST['identifiant'];
    }

    $query = $db->prepare('INSERT INTO LOTS_LOTS(libelleLot, idTypeLot, idEtat, idLieu, idPersonne, dateDernierInventaire, frequenceInventaire, commentairesLots) VALUES(:libelleLot, :idTypeLot, :idEtat, :idLieu, :idPersonne, :dateDernierInventaire, :frequenceInventaire, :commentairesLots);');
    $query->execute(array(
        'libelleLot' => $_POST['libelleLot'],
        'idTypeLot' => $idTypeLot,
        'idEtat' => $idEtat,
        'idLieu' => $idLieu,
        'idPersonne' => $idPersonne,
        'dateDernierInventaire' => $_POST['dateDernierInventaire'],
        'frequenceInventaire' => $_POST['frequenceInventaire'],
        'commentairesLots' => $_POST['commentairesLots']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du lot " . $_POST['libelleLot'], '2');
            $_SESSION['returnMessage'] = 'Lot ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du lot " . $_POST['libelleLot'], '5');
            $_SESSION['returnMessage'] = 'Un lot existe déjà avec le même libellé, stocké au même lieu, et dépendant du même référentiel. Merci de corriger le formulaire.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du lot " . $_POST['libelleLot'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du lot.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-2);</script>";
}
?>