<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['cout_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{    
	$_POST['montantMaxValidation'] = ($_POST['montantMaxValidation'] == Null) ? Null : $_POST['montantMaxValidation'];
    $_POST['depasseBudget'] = ($_POST['depasseBudget'] == 1) ? 1 : 0;
    $_POST['validerClos'] = ($_POST['validerClos'] == 1) ? 1 : 0;
	
    $query = $db->prepare('
        UPDATE
            CENTRE_COUTS_PERSONNES
        SET
            idPersonne           = :idPersonne,
            montantMaxValidation = :montantMaxValidation,
            depasseBudget        = :depasseBudget,
            validerClos          = :validerClos
        WHERE
            idGerant             = :idGerant
        ;');
    $query->execute(array(
        'idPersonne'           => $_POST['idPersonne'],
        'montantMaxValidation' => $_POST['montantMaxValidation'],
        'idGerant'             => $_GET['id'],
        'depasseBudget'        => $_POST['depasseBudget'],
        'validerClos'          => $_POST['validerClos'],
    ));

    switch($query->errorCode())
    {
        case '00000':		    
		    writeInLogs("Modification d'un responsable pour un centre de couts. ", '1', NULL);
            $_SESSION['returnMessage'] = 'Personne modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification d'une personne entant que responsable d'un centre de couts.", '2', NULL);
            $_SESSION['returnMessage'] = 'Cette personne est déjà responsable de ce centre de couts.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification d'une personne entant que responsable pour un centre de couts.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout.";
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>