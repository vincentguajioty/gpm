<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['fournisseurs_lecture']==0 OR $_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $verif = $db->prepare('SELECT AES_DECRYPT(aesFournisseurTemoin, :key) as temoinDecrypte FROM CONFIG;');
    $verif->execute(array('key' => $_POST['aesKey']));
    $verif = $verif->fetch();

    if($verif['temoinDecrypte'] == 'temoin')
    {
    	$_SESSION['aesFour'] = $_POST['aesKey'];
    	writeInLogs("Clef de décryptage des données fournisseurs rentrée avec succès et validée par le contrôle sur temoin", '1', NULL);
    	
    	$query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
        $query->execute(array(
            'adresseIP' => $_SERVER['REMOTE_ADDR']
        ));
        $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
        $query->execute(array(
            'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - '.$VERROUILLAGE_IP_TEMPS.' days'))
        ));
    }
    else
    {
    	writeInLogs("Erreur dans la saisie de la clef de décryptage des données fournisseurs rentrée vu le décryptage infructueux du témoin", '1', NULL);
    	$_SESSION['returnMessage'] = 'Mauvaise clef saisie.';
        $_SESSION['returnType'] = '2';

        $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
        $query->execute(array(
            'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - '.$VERROUILLAGE_IP_TEMPS.' days'))
        ));
	    
	    $query = $db->prepare('SELECT COUNT(*) as nb FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
		$query->execute(array(
		    'adresseIP' => $_SERVER['REMOTE_ADDR']
		));
		$data = $query->fetch();
		
		if ($data['nb'] > $VERROUILLAGE_IP_OCCURANCES-2)
		{
			$query = $db->prepare('INSERT INTO VERROUILLAGE_IP(adresseIPverr, dateVerr, commentaire)VALUES(:adresseIPverr, :dateVerr, :commentaire);');
		    $query->execute(array(
		        'dateVerr' => date('Y-m-d H:i:s'),
		        'adresseIPverr' => $_SERVER['REMOTE_ADDR'],
		        'commentaire' => 'Erreur de saisie de la clef de decryptage fournisseur.',
		    ));
			
			writeInLogs("Verouillage définitif de l\'adresse IP suite à la tentative de saisie de la clef de decryptage fournisseur.", '2', NULL);

            $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
            $query->execute(array(
                'adresseIP' => $_SERVER['REMOTE_ADDR']
            ));
		}
		else
        {
            $query = $db->prepare('INSERT INTO VERROUILLAGE_IP_TEMP(adresseIP, dateEchec, commentaire)VALUES(:adresseIP, :dateEchec, :commentaire);');
            $query->execute(array(
                'dateEchec' => date('Y-m-d H:i:s'),
                'adresseIP' => $_SERVER['REMOTE_ADDR'],
                'commentaire' => 'Erreur de saisie de la clef de decryptage fournisseur.',
            ));

            writeInLogs("Verouillage temporaire de l\'adresse IP suite à la tentative de saisie de la clef de decryptage fournisseur.", '2', NULL);
        }
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>