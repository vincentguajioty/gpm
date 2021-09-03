<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->prepare('
        UPDATE
            CONFIG
        SET 
            cnilDisclaimer       = :cnilDisclaimer
    ;');
    $query->execute(array(
        'cnilDisclaimer'       => $_POST['cnilDisclaimer'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du texte disclaimer du site", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            
            $query = $db->query('
		        UPDATE
		            PERSONNE_REFERENTE
		        SET 
		            disclaimerAccept = Null
		    ;');
		    switch($query->errorCode())
		    {
		        case '00000':
		        	writeInLogs("Reset des acceptation de disclaimer faite sur tous les comptes utilisateurs", '1', NULL);
		        	break;
		        
		        default:
            	writeInLogs("Echec du reset des acceptation de disclaimer sur tous les comptes utilisateurs", '3', NULL);
		    }
            
            break;

        default:
            writeInLogs("Echec de la modification du texte disclaimer du site", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>window.location = document.referrer;</script>";
}
?>