<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['materiel_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['nomFournisseur'] = ($_POST['nomFournisseur'] == Null) ? Null : $_POST['nomFournisseur'];
	$_POST['libelleEmplacement'] = ($_POST['libelleEmplacement'] == Null) ? Null : $_POST['libelleEmplacement'];
	$_POST['peremption'] = ($_POST['peremption'] == Null) ? Null : $_POST['peremption'];
	$_POST['peremptionAnticipation'] = ($_POST['peremptionAnticipation'] == Null) ? Null : $_POST['peremptionAnticipation'];
    $_POST['idMaterielsEtat'] = ($_POST['idMaterielsEtat'] == Null) ? Null : $_POST['idMaterielsEtat'];

    $query = $db->prepare('
    	UPDATE
    		MATERIEL_ELEMENT
    	SET
    		idEmplacement = :idEmplacement,
    		idFournisseur = :idFournisseur,
    		quantite = :quantite,
    		quantiteAlerte = :quantiteAlerte,
    		peremption = :peremption,
    		peremptionAnticipation = :peremptionAnticipation,
    		commentairesElement = :commentairesElement,
    		idMaterielsEtat = :idMaterielsEtat
    	WHERE
    		idElement = :idElement
    ;');
    $query->execute(array(
        'idElement' => $_GET['id'],
        'idEmplacement' => $_POST['libelleEmplacement'],
        'idFournisseur' => $_POST['nomFournisseur'],
        'quantite' => $_POST['quantite'],
        'quantiteAlerte' => $_POST['quantiteAlerte'],
        'peremption' => $_POST['peremption'],
        'peremptionAnticipation' => $_POST['peremptionAnticipation'],
        'commentairesElement' => $_POST['commentairesElement'],
        'idMaterielsEtat' => $_POST['idMaterielsEtat']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du materiel " . $_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Matériel modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du matériel " . $_GET['id'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un matériel existe déjà dans cet emplacement. Au lieu d\'ajouter à nouveau le matériel, veuillez changer sa quantité.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du matériel " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la modification du matériel.";
            $_SESSION['returnType'] = '2';
    }
	updatePeremptionsAnticipations();
	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";
}
?>