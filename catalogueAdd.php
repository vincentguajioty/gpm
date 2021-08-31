<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['catalogue_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    if($_POST['sterilite'] == 'option1')
    {
        $ster = '0';
    }
    else
    {
        $ster = '1';
    }

    if ($_POST['libelleCategorie'] == Null)
    {
        $idCategorie = Null;
    }
    else
    {
        $idCategorie = $_POST['libelleCategorie'];
    }

    $query = $db->prepare('INSERT INTO MATERIEL_CATALOGUE(libelleMateriel, idCategorie, taille, sterilite, conditionnementMultiple, commentairesMateriel) VALUES(:libelleMateriel, :idCategorie, :taille, :sterilite, :conditionnementMultiple, :commentairesMateriel);');
    $query->execute(array(
        'libelleMateriel' => $_POST['libelleMateriel'],
        'idCategorie' => $idCategorie,
        'taille' => $_POST['taille'],
        'sterilite' => $ster,
        'conditionnementMultiple' => $_POST['conditionnementMultiple'],
        'commentairesMateriel' => $_POST['commentairesMateriel']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout dans le catalogue de " . $_POST['libelleMateriel'], '2');
            $_SESSION['returnMessage'] = 'Item ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détection lors de l'ajout dans le catalogue de " . $_POST['libelleMateriel'], '5');
            $_SESSION['returnMessage'] = 'Un item dans le catalogue existe déjà avec le même libellé. Merci de changer le libellé.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'item " . $_POST['libelleMateriel'] . " dans le catalogue.", '5');
            $_SESSION['returnMessage'] = 'Erreur inconnue lors l\'ajout de l\'item.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-2);</script>";
}
?>