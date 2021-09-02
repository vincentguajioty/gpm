<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['actionsMassives']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $querySelect = $db->query('
    	SELECT
    		c.idLot
    	FROM
    		(
    		SELECT
    			em.idLot,
    			COUNT(el.idSac) as nbSacs
    		FROM
    			LOTS_LOTS em
    			LEFT OUTER JOIN MATERIEL_SAC el ON em.idLot = el.idLot
    		GROUP BY em.idLot
    		) c
    	WHERE
    		c.nbSacs = 0
		;');
    
    while($lot = $querySelect->fetch())
    {
    	$query = $db->prepare('SELECT * FROM INVENTAIRES WHERE idLot = :idLot;');
	    $query->execute(array(
	        'idLot' => $lot['idLot']
	    ));
	    while ($data = $query->fetch())
	    {
	        $query2 = $db->prepare('DELETE FROM INVENTAIRES_CONTENUS WHERE idInventaire = :idInventaire;');
	        $query2->execute(array(
	            'idInventaire' => $data['idInventaire']
	        ));
	    }
	    $query2 = $db->prepare('DELETE FROM INVENTAIRES WHERE idLot = :idLot;');
	    $query2->execute(array(
	        'idLot' => $lot['idLot']
	    ));

	    $query = $db->prepare('SELECT * FROM LOTS_LOTS WHERE idLot = :idLot;');
	    $query->execute(array(
	        'idLot' => $lot['idLot']
	    ));
	    $data = $query->fetch();

	    $query = $db->prepare('UPDATE MATERIEL_SAC SET idLot = Null WHERE idLot = :idLot;');
	    $query->execute(array(
	        'idLot' => $lot['idLot']
	    ));

	    $query = $db->prepare('DELETE FROM LOTS_LOTS WHERE idLot = :idLot;');
	    $query->execute(array(
	        'idLot' => $lot['idLot']
	    ));
    }

    writeInLogs("Action massive 41", '4');
    $_SESSION['returnMessage'] = 'Requète lancée et terminée';
    $_SESSION['returnType'] = '1';

	checkAllConf();

    $_SESSION['actionsMassives_authent_ok'] = 1;
    echo "<script type='text/javascript'>document.location.replace('actionsMassives.php');</script>";
}
?>