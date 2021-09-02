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
    		c.idEmplacement
    	FROM
    		(
    		SELECT
    			em.idEmplacement,
    			COUNT(el.idElement) as nbMateriel
    		FROM
    			MATERIEL_EMPLACEMENT em
    			LEFT OUTER JOIN MATERIEL_ELEMENT el ON em.idEmplacement = el.idEmplacement
    		GROUP BY em.idEmplacement
    		) c
    	WHERE
    		c.nbMateriel = 0
		;');
    
    $query = 'DELETE FROM MATERIEL_EMPLACEMENT WHERE idEmplacement IN (';
    while($idEmplacement = $querySelect->fetch())
    {
    	$query .= (int)$idEmplacement['idEmplacement'].', ';
    }

    $query = substr($query, 0, -2);

    $query .= ');';

    $db->query($query);

    writeInLogs("Action massive 22", '1', NULL);
    $_SESSION['returnMessage'] = 'Requète lancée et terminée';
    $_SESSION['returnType'] = '1';

	checkAllConf();

    $_SESSION['actionsMassives_authent_ok'] = 1;
    echo "<script type='text/javascript'>document.location.replace('actionsMassives.php');</script>";
}
?>