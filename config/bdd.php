<?php
session_start();

function createDB()
{
    //---------------------- BASE DE DONNEES ----------------------
    $SERVEURDB = 'x.x.x.x:xxxx'; //adresse IP du serveur entre simple cote, ex: $SERVEUR = '192.169.1.5';
    $DBNAME = 'GPM'; //nom de la base de données, ex: $DB = 'GPM';
    $CHARSET = 'utf8'; //type d'interclassement, utf8 étant recommandé, ex: $CHARSET = 'utf8';
    $USER = 'xxx'; //nom d'utilisateur d'accès à la base de données, ex: $USER = 'utilisateur';
    $PASSWORD = 'xxx'; //mot de passe d'accès à la base de données, ex: $PASSWORD = 'motDePasse';
    //-------------------- FIN BASE DE DONNEES ---------------------

    try {
        $db = new PDO('mysql:host=' . $SERVEURDB . ';dbname=' . $DBNAME . ';charset=' . $CHARSET, $USER, $PASSWORD);
        $query = $db->prepare('SELECT COUNT(*) as nb FROM information_schema.tables WHERE table_schema = :table_schema;');
        $query->execute(array(
            'table_schema' => $DBNAME));
        $data = $query->fetch();
        if ($data['nb'] == 0) {
            echo "<script type='text/javascript'>document.location.replace('distmaj/INSTALLFROMSCRATCH.php');</script>";
        }

    } catch (PDOException $ex) {
        echo "<script type='text/javascript'>document.location.replace('bdderror.php');</script>";
    }

    return $db;

}

$db = createDB();

function writeInLogs($contentEVT, $levelEVT)
{
    global $db;
    $query = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
    $query->execute(array(
        'dateEvt' => date('Y-m-d H:i:s'),
        'adresseIP' => $_SERVER['REMOTE_ADDR'],
        'utilisateurEvt' => $_SESSION['identifiant'],
        'idLogLevel' => $levelEVT,
        'detailEvt' => $contentEVT
    ));
}


function checkLotsConf($idLot)
{
    global $db;
    $errorsConf = 0;
    $query = $db->prepare('SELECT idTypeLot FROM LOTS_LOTS WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $idLot
    ));
    $data = $query->fetch();

    $query = $db->prepare('SELECT * FROM REFERENTIELS r LEFT OUTER JOIN LOTS_TYPES t on r.idTypeLot=t.idTypeLot LEFT OUTER JOIN MATERIEL_CATALOGUE m on r.idMaterielCatalogue = m.idMaterielCatalogue WHERE r.idTypeLot = :idTypeLot;');
    $query->execute(array(
        'idTypeLot' => $data['idTypeLot']
    ));

    while ($data = $query->fetch())
    {
        $query2 = $db->prepare('SELECT MIN(peremption) as peremption FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE idMaterielCatalogue = :idMaterielCatalogue AND s.idLot = :idLot;');
        $query2->execute(array(
            'idMaterielCatalogue' => $data['idMaterielCatalogue'],
            'idLot' => $idLot
        ));
        $data2 = $query2->fetch();

        if (($data['obligatoire'] == 1) AND ($data['sterilite'] == 1) AND ($data2['peremption'] < date('Y-m-d')))
        {
            $errorsConf = $errorsConf +1;
        }

        if (($data['obligatoire'] == 1) AND ($data['sterilite'] == 1) AND ($data2['peremption'] == 0))
        {
            $errorsConf = $errorsConf +1;
        }

        if (($data['obligatoire'] == 1) AND ($data['sterilite'] == 1) AND ($data2['peremption'] == '0000-00-00'))
        {
            $errorsConf = $errorsConf +1;
        }

        if (($data['obligatoire'] == 1) AND ($data['sterilite'] == 1) AND ($data2['peremption'] == Null))
        {
            $errorsConf = $errorsConf +1;
        }

        $query3 = $db->prepare('SELECT SUM(quantite) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE idMaterielCatalogue = :idMaterielCatalogue AND s.idLot = :idLot;');
        $query3->execute(array(
            'idMaterielCatalogue' => $data['idMaterielCatalogue'],
            'idLot' => $idLot
        ));
        $data3 = $query3->fetch();

        if (($data['obligatoire'] == 1) AND ($data3['nb']<$data['quantiteReferentiel']))
        {
            $errorsConf = $errorsConf +1;
        }


    }

    if ($errorsConf>0)
    {
        return 1;
    }
    else
    {
        return 0;
    }
}

function checkAllConf()
{
	global $db;
	$query = $db->query('SELECT * FROM LOTS_LOTS;');
	
	while ($data = $query->fetch())
	{
		if ($data['idTypeLot'] > 0)
		{
			if (checkLotsConf($data['idLot']) == 1)
			{
				$query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = True WHERE idLot = :idLot;');
				$query2->execute(array(
	            	'idLot' => $data['idLot']
	        	));
			}
			else
			{
				$query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = False WHERE idLot = :idLot;');
				$query2->execute(array(
	            	'idLot' => $data['idLot']
	        	));
			}
		}
		else
		{
			$query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = Null WHERE idLot = :idLot;');
			$query2->execute(array(
            	'idLot' => $data['idLot']
        	));
		}
	}
}


function checkOneConf($idLot)
{
	global $db;
	$query = $db->prepare('SELECT * FROM LOTS_LOTS WHERE idLot = :idLot;');
	$query->execute(array(
    	'idLot' => $idLot
	));
	
	$data = $query->fetch();
	
	if ($data['idTypeLot'] > 0)
	{
		if (checkLotsConf($data['idLot']) == 1)
		{
			$query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = True WHERE idLot = :idLot;');
			$query2->execute(array(
            	'idLot' => $data['idLot']
        	));
		}
		else
		{
			$query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = False WHERE idLot = :idLot;');
			$query2->execute(array(
            	'idLot' => $data['idLot']
        	));
		}
	}
	else
	{
		$query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = Null WHERE idLot = :idLot;');
		$query2->execute(array(
        	'idLot' => $data['idLot']
    	));
	}
}



?>