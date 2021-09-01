<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['materiel_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['libelleMateriel'] = ($_POST['libelleMateriel'] == Null) ? Null : $_POST['libelleMateriel'];
    $_POST['nomFournisseur'] = ($_POST['nomFournisseur'] == Null) ? Null : $_POST['nomFournisseur'];
	$_POST['libelleEmplacement'] = ($_POST['libelleEmplacement'] == Null) ? Null : $_POST['libelleEmplacement'];
	$_POST['peremption'] = ($_POST['peremption'] == Null) ? Null : $_POST['peremption'];
	$_POST['peremptionNotification'] = ($_POST['peremptionNotification'] == Null) ? Null : $_POST['peremptionNotification'];
    $_POST['idMaterielsEtat'] = ($_POST['idMaterielsEtat'] == Null) ? Null : $_POST['idMaterielsEtat'];

    $query = $db->prepare('INSERT INTO MATERIEL_ELEMENT(idMaterielCatalogue, idEmplacement, idFournisseur, quantite, quantiteAlerte, peremption, peremptionNotification, commentairesElement, idMaterielsEtat)VALUES(:idMaterielCatalogue, :idEmplacement, :idFournisseur, :quantite, :quantiteAlerte, :peremption, :peremptionNotification, :commentairesElement, :idMaterielsEtat);');
    $query->execute(array(
        'idMaterielCatalogue' => $_POST['libelleMateriel'],
        'idEmplacement' => $_POST['libelleEmplacement'],
        'idFournisseur' => $_POST['nomFournisseur'],
        'quantite' => $_POST['quantite'],
        'quantiteAlerte' => $_POST['quantiteAlerte'],
        'peremption' => $_POST['peremption'],
        'peremptionNotification' => $_POST['peremptionNotification'],
        'commentairesElement' => $_POST['commentairesElement'],
        'idMaterielsEtat' => $_POST['idMaterielsEtat']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du materiel " . $_POST['libelleMateriel'], '2');
            $_SESSION['returnMessage'] = 'Matériel ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du matériel " . $_POST['libelleMateriel'], '5');
            $_SESSION['returnMessage'] = 'Un matériel existe déjà dans cet emplacement. Au lieu d\'ajouter à nouveau le matériel, veuillez changer sa quantité.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du matériel " . $_POST['libelleMateriel'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du matériel.";
            $_SESSION['returnType'] = '2';
    }

	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";
}
?>