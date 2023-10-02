<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    if($_POST['key1'] != $_POST['key2'])
    {
    	writeInLogs("Config - Mise en place de la clef de chiffrement fournisseurs - Erreur dans la vérification des deux clefs saisies", '1', NULL);
        $_SESSION['returnMessage'] = 'Erreur dans les clefs: les deux clefs saisies ne sont pas identiques.';
        $_SESSION['returnType'] = '2';
    	echo "<script type='text/javascript'>document.location.replace('appliConf.php');</script>";
    	exit;
    }
	
    $query = $db->prepare('
        UPDATE
            CONFIG
        SET
            aesFournisseurTemoin = AES_ENCRYPT(\'temoin\', :key1)
    ;');
    $query->execute(array(
        'key1' => $_POST['key1'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Config - Mise en place de la clef de chiffrement fournisseurs - Clef validée et activée", '1', NULL);
            $_SESSION['returnMessage'] = 'Fonctionnalité activée. La clef suivante est activée: <br/>'.$_POST['key1'].'<br/><b>Attention, ceci est le dernier message sur lequel la clef est affichée ! Pour rappel, la clef n\'est pas enregistrée et ne peut plus être retrouvée en cas de perte.</b>';
            $_SESSION['returnType'] = '1';
            break;
			
        default:
            writeInLogs("Config - Mise en place de la clef de chiffrement fournisseurs - Erreur dans la validation de la clef", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la validation de la clef.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script type='text/javascript'>document.location.replace('appliConf.php');</script>";
}
?>