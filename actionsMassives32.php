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
    		c.idSac
    	FROM
    		(
    		SELECT
    			em.idSac,
    			COUNT(el.idEmplacement) as nbEmplacement
    		FROM
    			MATERIEL_SAC em
    			LEFT OUTER JOIN MATERIEL_EMPLACEMENT el ON em.idSac = el.idSac
    		GROUP BY em.idSac
    		) c
    	WHERE
    		c.nbEmplacement = 0
		;');
    
    $query = 'DELETE FROM MATERIEL_SAC WHERE idSac IN (';
    while($idSac = $querySelect->fetch())
    {
    	$query .= (int)$idSac['idSac'].', ';
    }

    $query = substr($query, 0, -2);

    $query .= ');';

    $db->query($query);

    writeInLogs("Action massive 32", '1', NULL);
    $_SESSION['returnMessage'] = 'Requète lancée et terminée';
    $_SESSION['returnType'] = '1';

	checkAllConf();

    $_SESSION['actionsMassives_authent_ok'] = 1;
    echo "<script type='text/javascript'>document.location.replace('actionsMassives.php');</script>";
}
?>