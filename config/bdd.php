<?php
session_start();

function createDB()
{
    //---------------------- BASE DE DONNEES ----------------------
    $SERVEURDB = 'x.x.x.x'; //adresse IP du serveur entre simple cote, ex: $SERVEUR = '192.169.1.5';
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
        if ($data['nb'] == 0 AND (strpos($_SERVER['HTTP_REFERER'], "INSTALLFROMSCRATCH.php") == false)) {
            echo "<script type='text/javascript'>document.location.replace('distmaj/INSTALLFROMSCRATCH.php');</script>";
        }

    } catch (PDOException $ex) {
        echo "<script type='text/javascript'>document.location.replace('bdderror.php');</script>";
    }

    return $db;

}

$db = createDB();

function writeInLogs($contentEVT, $levelEVT, $userSpecifique)
{
    switch($levelEVT)
    {
        case '1':
            $logsLevel = 'INFO';
        break;

        case '2':
            $logsLevel = 'WARN';
        break;

        case '3':
            $logsLevel = 'ERROR';
        break;

        case '4':
            $logsLevel = 'DEBUG';
        break;

        default:
            $logsLevel = 'UNDEFINED';
    }

    if(!isset($userSpecifique) OR $userSpecifique == Null OR $userSpecifique == '')
    {
        if(isset($_SESSION['identifiant']) AND $_SESSION['identifiant'] != '')
        {
            $userSpecifique = $_SESSION['identifiant'];
        }
        else
        {
            $userSpecifique = 'SYSTEM';
        }
    }
    $userSpecifique .= $_SESSION['LOGS_DELEGATION_PREFIXE'];

    if(isset($_SERVER['REMOTE_ADDR']) AND $_SERVER['REMOTE_ADDR'] != NULL AND $_SERVER['REMOTE_ADDR'] != '')
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    else
    {
        $ip = 'local';
    }

    $log = '['.date("Y-m-d H:i:s").']'."\t".'['.$ip.']'."\t".'['.$userSpecifique.']'."\t".'['.$logsLevel.'] '.$contentEVT.PHP_EOL;
    if(is_dir('logs'))
    {
        file_put_contents('logs/'.date("Ymd").'.log', $log, FILE_APPEND);
    }
    else
    {
        if(is_dir('../logs'))
        {
            file_put_contents('../logs/'.date("Ymd").'.log', $log, FILE_APPEND);
        }
        else
        {
            chdir(dirname(__FILE__));
            if(is_dir('logs'))
            {
                file_put_contents('logs/'.date("Ymd").'.log', $log, FILE_APPEND);
            }
            else
            {
                if(is_dir('../logs'))
                {
                    file_put_contents('../logs/'.date("Ymd").'.log', $log, FILE_APPEND);
                }
            }
        }
    }
}

function randomColor()
{
	$rand = array('#111111','#dd4b39','#f39c12','#00c0ef','#0073b7','#3c8dbc','#00a65a','#001f3f','#39cccc','#3d9970','#01ff70','#ff851b','#f012be','#605ca8','#d81b60','#000000','#d33724','#db8b0b','#00a7d0','#005384','#357ca5','#008d4c','#001a35','#30bbbb','#368763','#00e765','#ff7701','#db0ead','#555299','#ca195a','#606c84');
	$color = $rand[rand(0,34)];
	return $color;
}

function updatePeremptionsAnticipations()
{
    global $db;

    writeInLogs("Début de la mise à jour de la table de matériel opérationnel pour mise à jour du champ d'anticipation des péremption conformément au catalogue.", '1', NULL);
    $query = $db->query('UPDATE MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue SET e.peremptionAnticipation = c.peremptionAnticipationOpe WHERE c.peremptionAnticipationOpe IS NOT NULL');
    writeInLogs("Fin de la mise à jour de la table de matériel opérationnel pour mise à jour du champ d'anticipation des péremption conformément au catalogue.", '1', NULL);

    writeInLogs("Début de la mise à jour de la table de matériel en reserve pour mise à jour du champ d'anticipation des péremption conformément au catalogue.", '1', NULL);
    $query = $db->query('UPDATE RESERVES_MATERIEL e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue SET e.peremptionReserveAnticipation = c.peremptionAnticipationRes WHERE c.peremptionAnticipationRes IS NOT NULL');
    writeInLogs("Fin de la mise à jour de la table de matériel en reserve pour mise à jour du champ d'anticipation des péremption conformément au catalogue.", '1', NULL);
}

function notificationsMAJpersonne()
{
	global $db;
	
	writeInLogs("Début de la mise à jour des conditions de notification dans la table des personnes.", '1', NULL);
	
	$personnes = $db->query('
		SELECT
				p.idPersonne,
				MAX(actifCeJour) as actifCeJour
			FROM
				PERSONNE_REFERENTE p
				LEFT OUTER JOIN NOTIFICATIONS_ABONNEMENTS n ON p.idPersonne = n.idPersonne
				LEFT OUTER JOIN NOTIFICATIONS_CONDITIONS c ON n.idCondition = c.idCondition
			GROUP BY
				p.idPersonne
	;');
	while($personne = $personnes->fetch())
	{
		$update = $db->prepare('
			UPDATE
				PERSONNE_REFERENTE
			SET
				notifications_abo_cejour = :actifCeJour
			WHERE
				idPersonne = :idPersonne
		');
		$update->execute(array(
			'actifCeJour' => $personne['actifCeJour'],
			'idPersonne' => $personne['idPersonne'],
		));
	}
	
	writeInLogs("Fin de la mise à jour des conditions de notification dans la table des personnes.", '1', NULL);
}

function notificationsConditionsMAJ()
{
	global $db;
	
	writeInLogs("Début de la mise à jour de la table des conditions de notification.", '1', NULL);
	
	$query = $db->query('UPDATE NOTIFICATIONS_CONDITIONS SET actifCeJour = 0;');
	$query = $db->query('UPDATE NOTIFICATIONS_CONDITIONS SET actifCeJour = 1 WHERE idCondition = 1;');
	
	$query = $db->prepare('
		UPDATE
			NOTIFICATIONS_CONDITIONS
		SET
			actifCeJour = 1
		WHERE
			codeCondition = :numJour
			OR
			UPPER(codeCondition) = UPPER(:nomJour)
	;');
	$query->execute(array(
		'numJour' => date('l'),
		'nomJour' => date('d'),
	));
	
	writeInLogs("Fin de la mise à jour de la table des conditions de notification.", '1', NULL);
}

function checkVehiculeMaintenance($idVehicule)
{
    global $db;
    $erreurs = 0;

    $alertes = $db->prepare('
        SELECT
            *
        FROM
            VEHICULES_HEALTH_ALERTES
        WHERE
            idVehicule = :idVehicule
        ;');
    $alertes->execute(array('idVehicule'=>$idVehicule));

    while($alerte = $alertes->fetch())
    {
        $last = $db->prepare('
            SELECT
                MAX(dateHealth) as dateHealth
            FROM
                VEHICULES_HEALTH_CHECKS c
                LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth
            WHERE
                idVehicule = :idVehicule
                AND
                idHealthType = :idHealthType
        ;');
        $last->execute(array(
            'idVehicule' => $idVehicule,
            'idHealthType' => $alerte['idHealthType'],
        ));
        $last = $last->fetch();

        if($last['dateHealth'] == Null)
        {
            $erreurs += 1;
        }
        else
        {
            if(date('Y-m-d', strtotime($last['dateHealth']. ' + '.$alerte['frequenceHealth'].' days')) <= date('Y-m-d'))
            {
                $erreurs += 1;
            }
        }
    }

    if($erreurs == 0)
    {
        return 0;
    }
    else
    {
        return 1;
    }
}

function checkAllMaintenance()
{
    global $db;
    writeInLogs("Lancement de la vérification des maintenance de tous les véhicules.", '1', NULL);
    $vehicules = $db->query('SELECT * FROM VEHICULES;');

    while ($vehicule = $vehicules->fetch())
    {
        checkOneMaintenance($vehicule['idVehicule']);
    }
    writeInLogs("Fin de la vérification des maintenance de tous les véhicules.", '1', NULL);
}

function checkOneMaintenance($idVehicule)
{
    global $db;

    writeInLogs("Lancement de la vérification des maintenance du véhicule ".$idVehicule, '1', NULL);
    
    $alertes = $db->prepare('SELECT COUNT(*) as nb FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule;');
    $alertes->execute(array('idVehicule'=>$idVehicule));
    $alertes = $alertes->fetch();
    $alertes = $alertes['nb'];
    
    if ($alertes > 0)
    {
        if (checkVehiculeMaintenance($idVehicule) == 1)
        {
            writeInLogs("Véhicule ".$idVehicule.' contrôlé en erreur de maintenance.', '1', NULL);
            $query2 = $db->prepare('UPDATE VEHICULES SET alerteMaintenance = True WHERE idVehicule = :idVehicule;');
            $query2->execute(array(
                'idVehicule' => $idVehicule
            ));
        }
        else
        {
            writeInLogs("Véhicule ".$idVehicule.' contrôlé en maintenance ok.', '1', NULL);
            $query2 = $db->prepare('UPDATE VEHICULES SET alerteMaintenance = False WHERE idVehicule = :idVehicule;');
            $query2->execute(array(
                'idVehicule' => $idVehicule
            ));
        }
    }
    else
    {
        writeInLogs("Vérification des maintenance du vehicule ".$idVehicule." impossible car aucune alerte paramétrée.", '2', NULL);
        $query2 = $db->prepare('UPDATE VEHICULES SET alerteMaintenance = Null WHERE idVehicule = :idVehicule;');
        $query2->execute(array(
            'idVehicule' => $idVehicule
        ));
    }

    writeInLogs("Fin de la vérification des maintenance du véhicule ".$idVehicule, '1', NULL);
}

function checkVehiculeDesinfection($idVehicule)
{
    global $db;
    $erreurs = 0;

    $alertes = $db->prepare('
        SELECT
            *
        FROM
            VEHICULES_DESINFECTIONS_ALERTES
        WHERE
            idVehicule = :idVehicule
        ;');
    $alertes->execute(array('idVehicule'=>$idVehicule));

    while($alerte = $alertes->fetch())
    {
        $last = $db->prepare('
            SELECT
                MAX(dateDesinfection) as dateDesinfection
            FROM
                VEHICULES_DESINFECTIONS
            WHERE
                idVehicule = :idVehicule
                AND
                idVehiculesDesinfectionsType = :idVehiculesDesinfectionsType
        ;');
        $last->execute(array(
            'idVehicule' => $idVehicule,
            'idVehiculesDesinfectionsType' => $alerte['idVehiculesDesinfectionsType'],
        ));
        $last = $last->fetch();

        if($last['dateDesinfection'] == Null)
        {
            $erreurs += 1;
        }
        else
        {
            if(date('Y-m-d', strtotime($last['dateDesinfection']. ' + '.$alerte['frequenceDesinfection'].' days')) <= date('Y-m-d'))
            {
                $erreurs += 1;
            }
        }
    }

    if($erreurs == 0)
    {
        return 0;
    }
    else
    {
        return 1;
    }
}

function checkAllDesinfection()
{
    global $db;
    writeInLogs("Lancement de la vérification des désinfections de tous les véhicules.", '1', NULL);
    $vehicules = $db->query('SELECT * FROM VEHICULES;');

    while ($vehicule = $vehicules->fetch())
    {
        checkOneDesinfection($vehicule['idVehicule']);
    }
    writeInLogs("Fin de la vérification des désinfections de tous les véhicules.", '1', NULL);
}

function checkOneDesinfection($idVehicule)
{
    global $db;

    writeInLogs("Lancement de la vérification des désinfections du véhicule ".$idVehicule, '1', NULL);
    
    $alertes = $db->prepare('SELECT COUNT(*) as nb FROM VEHICULES_DESINFECTIONS_ALERTES WHERE idVehicule = :idVehicule;');
    $alertes->execute(array('idVehicule'=>$idVehicule));
    $alertes = $alertes->fetch();
    $alertes = $alertes['nb'];
    
    if ($alertes > 0)
    {
        if (checkVehiculeDesinfection($idVehicule) == 1)
        {
            writeInLogs("Véhicule ".$idVehicule.' contrôlé en erreur de désinfection.', '1', NULL);
            $query2 = $db->prepare('UPDATE VEHICULES SET alerteDesinfection = True WHERE idVehicule = :idVehicule;');
            $query2->execute(array(
                'idVehicule' => $idVehicule
            ));
        }
        else
        {
            writeInLogs("Véhicule ".$idVehicule.' contrôlé en désinfection ok.', '1', NULL);
            $query2 = $db->prepare('UPDATE VEHICULES SET alerteDesinfection = False WHERE idVehicule = :idVehicule;');
            $query2->execute(array(
                'idVehicule' => $idVehicule
            ));
        }
    }
    else
    {
        writeInLogs("Vérification des désinfections du vehicule ".$idVehicule." impossible car aucune alerte paramétrée.", '2', NULL);
        $query2 = $db->prepare('UPDATE VEHICULES SET alerteDesinfection = Null WHERE idVehicule = :idVehicule;');
        $query2->execute(array(
            'idVehicule' => $idVehicule
        ));
    }

    writeInLogs("Fin de la vérification des désinfections du véhicule ".$idVehicule, '1', NULL);
}

function checkLotsConf($idLot)
{
    global $db;
    writeInLogs("Vérification de conformité du lot " . $idLot, '1', NULL);
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
        writeInLogs("Lot " . $idLot." vérifié non-conforme", '1', NULL);
        return 1;
    }
    else
    {
        writeInLogs("Lot " . $idLot." vérifié conforme", '1', NULL);
        return 0;
    }
}

function checkAllConf()
{
    global $db;
    writeInLogs("Lancement de la vérification de conformité de tous les lots.", '1', NULL);
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
    writeInLogs("Fin de la vérification de conformité de tous les lots.", '1', NULL);
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
        writeInLogs("Vérification de conformité du lot ".$idLot." impossible car non-rattaché à un référentiel", '2', NULL);
        $query2 = $db->prepare('UPDATE LOTS_LOTS SET alerteConfRef = Null WHERE idLot = :idLot;');
        $query2->execute(array(
            'idLot' => $data['idLot']
        ));
    }
}

function majIndicateursPersonne($idPersonne, $enableLog)
{
    global $db;

    $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;');
    $query -> execute(array('idPersonne' => $idPersonne));
    $data = $query->fetch();

    $conf_indicateur1Accueil = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['conf_indicateur1Accueil']);

    $conf_indicateur2Accueil = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['conf_indicateur2Accueil']);

    $conf_indicateur3Accueil = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['conf_indicateur3Accueil']);

    $conf_indicateur4Accueil = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['conf_indicateur4Accueil']);

    $conf_indicateur5Accueil = ($data['reserve_lecture']) && ($data['conf_indicateur5Accueil']);

    $conf_indicateur6Accueil = ($data['reserve_lecture']) && ($data['conf_indicateur6Accueil']);

    $conf_indicateur7Accueil = ($data['vehicules_lecture']) && ($data['conf_indicateur7Accueil']);

    $conf_indicateur8Accueil = ($data['vehicules_lecture']) && ($data['conf_indicateur8Accueil']);
    
    $conf_indicateur9Accueil = ($data['tenues_lecture'] OR $data['tenuesCatalogue_lecture']) && ($data['conf_indicateur9Accueil']);
    
    $conf_indicateur10Accueil = ($data['tenues_lecture'] OR $data['tenuesCatalogue_lecture']) && ($data['conf_indicateur10Accueil']);

    $conf_indicateur11Accueil = ($data['desinfections_lecture']) && ($data['conf_indicateur11Accueil']);

    $conf_indicateur12Accueil = ($data['vehiculeHealth_lecture']) && ($data['conf_indicateur12Accueil']);

    $query = $db->prepare('
        UPDATE
            PERSONNE_REFERENTE
        SET
            conf_indicateur1Accueil  = :conf_indicateur1Accueil,
            conf_indicateur2Accueil  = :conf_indicateur2Accueil,
            conf_indicateur3Accueil  = :conf_indicateur3Accueil,
            conf_indicateur4Accueil  = :conf_indicateur4Accueil,
            conf_indicateur5Accueil  = :conf_indicateur5Accueil,
            conf_indicateur6Accueil  = :conf_indicateur6Accueil,
            conf_indicateur7Accueil  = :conf_indicateur7Accueil,
            conf_indicateur8Accueil  = :conf_indicateur8Accueil,
            conf_indicateur9Accueil  = :conf_indicateur9Accueil,
            conf_indicateur10Accueil = :conf_indicateur10Accueil,
            conf_indicateur11Accueil = :conf_indicateur11Accueil,
            conf_indicateur12Accueil = :conf_indicateur12Accueil
        WHERE
            idPersonne = :idPersonne
    ;');
    $query->execute(array(
        'idPersonne'               => $idPersonne,
        'conf_indicateur1Accueil'  => (int)$conf_indicateur1Accueil,
        'conf_indicateur2Accueil'  => (int)$conf_indicateur2Accueil,
        'conf_indicateur3Accueil'  => (int)$conf_indicateur3Accueil,
        'conf_indicateur4Accueil'  => (int)$conf_indicateur4Accueil,
        'conf_indicateur5Accueil'  => (int)$conf_indicateur5Accueil,
        'conf_indicateur6Accueil'  => (int)$conf_indicateur6Accueil,
        'conf_indicateur7Accueil'  => (int)$conf_indicateur7Accueil,
        'conf_indicateur8Accueil'  => (int)$conf_indicateur8Accueil,
        'conf_indicateur9Accueil'  => (int)$conf_indicateur9Accueil,
        'conf_indicateur10Accueil' => (int)$conf_indicateur10Accueil,
        'conf_indicateur11Accueil' => (int)$conf_indicateur11Accueil,
        'conf_indicateur12Accueil' => (int)$conf_indicateur12Accueil,
    ));

    if ($enableLog == 1)
    {
        writeInLogs("Revue des indicateurs d'accueil pour la personne " . $idPersonne, '1', NULL);
    }
}

function majIndicateursProfil($idProfil)
{
    global $db;
    $query = $db->prepare('SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;');
    $query -> execute(array('idProfil' => $idProfil));

    writeInLogs("Revue des indicateurs d'accueil pour le profil " . $idProfil, '1', NULL);

    while ($data = $query->fetch())
    {
        majIndicateursPersonne($data['idPersonne'], 0);
    }
}

function majNotificationsPersonne($idPersonne, $enableLog)
{
    global $db;

    $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;');
    $query -> execute(array('idPersonne' => $idPersonne));
    $data = $query->fetch();
    
    $notif_lots_manquants = $data['notifications'] && ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_manquants']);
    
    $notif_lots_peremptions = $data['notifications'] && ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_peremptions']);
    
    $notif_lots_inventaires = $data['notifications'] && ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_inventaires']);
    
    $notif_lots_conformites = $data['notifications'] && ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_conformites']);
    
    $notif_reserves_manquants = $data['notifications'] && ($data['reserve_lecture']) && ($data['notif_reserves_manquants']);
    
    $notif_reserves_peremptions = $data['notifications'] && ($data['reserve_lecture']) && ($data['notif_reserves_peremptions']);
    
    $notif_reserves_inventaires = $data['notifications'] && ($data['reserve_lecture']) && ($data['notif_reserves_inventaires']);
    
    $notif_vehicules_assurances = $data['notifications'] && ($data['vehicules_lecture']) && ($data['notif_vehicules_assurances']);
    
    $notif_vehicules_revisions = $data['notifications'] && ($data['vehicules_lecture']) && ($data['notif_vehicules_revisions']);

    $notif_vehicules_desinfections = $data['notifications'] && ($data['desinfections_lecture']) && ($data['notif_vehicules_desinfections']);
    
    $notif_vehicules_ct = $data['notifications'] && ($data['vehicules_lecture']) && ($data['notif_vehicules_ct']);

    $notif_vehicules_health = $data['notifications'] && ($data['vehiculeHealth_lecture']) && ($data['notif_vehicules_health']);
    
    $notif_tenues_stock = $data['notifications'] && ($data['tenuesCatalogue_lecture']) && ($data['notif_tenues_stock']);
    
    $notif_tenues_retours = $data['notifications'] && ($data['tenues_lecture']) && ($data['notif_tenues_retours']);
    
    $query = $db->prepare('
        UPDATE
            PERSONNE_REFERENTE
        SET
            notif_lots_manquants          = :notif_lots_manquants,
            notif_lots_peremptions        = :notif_lots_peremptions,
            notif_lots_inventaires        = :notif_lots_inventaires,
            notif_lots_conformites        = :notif_lots_conformites,
            notif_reserves_manquants      = :notif_reserves_manquants,
            notif_reserves_peremptions    = :notif_reserves_peremptions,
            notif_reserves_inventaires    = :notif_reserves_inventaires,
            notif_vehicules_assurances    = :notif_vehicules_assurances,
            notif_vehicules_revisions     = :notif_vehicules_revisions,
            notif_vehicules_ct            = :notif_vehicules_ct,
            notif_vehicules_health        = :notif_vehicules_health,
            notif_vehicules_desinfections = :notif_vehicules_desinfections,
            notif_tenues_stock            = :notif_tenues_stock,
            notif_tenues_retours          = :notif_tenues_retours
        WHERE
            idPersonne                    = :idPersonne
    ;');
    $query->execute(array(
        'idPersonne'                    => $idPersonne,
        'notif_lots_manquants'          => (int)$notif_lots_manquants,
        'notif_lots_peremptions'        => (int)$notif_lots_peremptions,
        'notif_lots_inventaires'        => (int)$notif_lots_inventaires,
        'notif_lots_conformites'        => (int)$notif_lots_conformites,
        'notif_reserves_manquants'      => (int)$notif_reserves_manquants,
        'notif_reserves_peremptions'    => (int)$notif_reserves_peremptions,
        'notif_reserves_inventaires'    => (int)$notif_reserves_inventaires,
        'notif_vehicules_assurances'    => (int)$notif_vehicules_assurances,
        'notif_vehicules_revisions'     => (int)$notif_vehicules_revisions,
        'notif_vehicules_desinfections' => (int)$notif_vehicules_desinfections,
        'notif_vehicules_ct'            => (int)$notif_vehicules_ct,
        'notif_vehicules_health'        => (int)$notif_vehicules_health,
        'notif_tenues_stock'            => (int)$notif_tenues_stock,
        'notif_tenues_retours'          => (int)$notif_tenues_retours
    ));
    
    if ($enableLog == 1)
    {
        writeInLogs("Revue des notifications pour la personne " . $idPersonne, '1', NULL);
    }
}

function majNotificationsProfil($idProfil)
{
    global $db;
    $query = $db->prepare('SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;');
    $query -> execute(array('idProfil' => $idProfil));

    writeInLogs("Revue des notifications pour le profil " . $idProfil, '1', NULL);

    while ($data = $query->fetch())
    {
        majNotificationsPersonne($data['idPersonne'], 0);
    }
}

function majValideursPersonne($idPersonne, $enableLog)
{
    global $db;

    $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;');
    $query -> execute(array('idPersonne' => $idPersonne));
    $data = $query->fetch();
    
    if($data['commande_valider'] != 1)
    {
    	$query = $db->prepare('DELETE FROM COMMANDES_VALIDEURS_DEFAULT WHERE idPersonne = :idPersonne;');
    	$query -> execute(array('idPersonne' => $idPersonne));
    }
    
    if ($enableLog == 1)
    {
        writeInLogs("Nettoyage de la personne " . $idPersonne." des valideurs par défaut des commandes", '1', NULL);
    }
}

function majValideursProfil($idProfil)
{
    global $db;
    $query = $db->prepare('SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;');
    $query -> execute(array('idProfil' => $idProfil));

    writeInLogs("Nettoyage des valideurs par défaut des commandes pour le profil " . $idProfil, '1', NULL);

    while ($data = $query->fetch())
    {
        majValideursPersonne($data['idPersonne'], 0);
    }
}

function cmdTotal ($idCommande)
{
    global $db;

    $query2 = $db->prepare('
        SELECT
            IFNULL(SUM(prixProduitTTC*quantiteCommande),0) AS total
        FROM
            COMMANDES_MATERIEL c
            LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue 
        WHERE
            idCommande = :idCommande
    ;');
    $query2->execute(array('idCommande' => $idCommande));
    $total = $query2->fetch();

    return floor($total['total']*100)/100;
}
function cmdEstAffectee ($idPersonne, $idCommande)
{
    global $db;
    $query = $db->prepare('SELECT idAffectee FROM COMMANDES_AFFECTEES WHERE idCommande = :idCommande;');
    $query -> execute(array('idCommande' => $idCommande));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idAffectee')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function cmdEstValideur ($idPersonne, $idCommande)
{
    global $db;
    $query = $db->prepare('SELECT idValideur FROM COMMANDES_VALIDEURS WHERE idCommande = :idCommande;');
    $query -> execute(array('idCommande' => $idCommande));
    $data = $query->fetchAll();

    if(in_array($idPersonne, array_column($data, 'idValideur')))
    {
        $personne = $db->prepare('SELECT commande_valider, commande_valider_seuil FROM VIEW_HABILITATIONS WHERE idPersonne = :idPersonne;');
        $personne->execute(array('idPersonne'=>$idPersonne));
        $personne = $personne->fetch();

        if($personne['commande_valider'] == 0)
        {
            return 0;
        }
        else
        {
            if(is_null($personne['commande_valider_seuil']))
            {
                return 1;
            }
            else
            {
                if($personne['commande_valider_seuil']>=cmdTotal($idCommande))
                {
                    return 1;
                }
                else
                {
                    return -1;
                }
            }
        }

        return 1;
    }
    else
    {
        return 0;
    }
}
function cmdEstObservateur ($idPersonne, $idCommande)
{
    global $db;
    $query = $db->prepare('SELECT idObservateur FROM COMMANDES_OBSERVATEURS WHERE idCommande = :idCommande;');
    $query -> execute(array('idCommande' => $idCommande));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idObservateur')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function cmdEstDemandeur ($idPersonne, $idCommande)
{
    global $db;
    $query = $db->prepare('SELECT idDemandeur FROM COMMANDES_DEMANDEURS WHERE idCommande = :idCommande;');
    $query -> execute(array('idCommande' => $idCommande));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idDemandeur')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function centreCoutsEstCharge ($idPersonne, $idCentreDeCout)
{
    global $db;
    $query = $db->prepare('SELECT c.idPersonne FROM CENTRE_COUTS_PERSONNES c LEFT OUTER JOIN VIEW_HABILITATIONS v ON c.idPersonne = v.idPersonne WHERE c.idCentreDeCout = :idCentreDeCout AND v.cout_etreEnCharge=1;');
    $query -> execute(array('idCentreDeCout' => $idCentreDeCout));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idPersonne')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function tdlEstExecutant ($idPersonne, $idTache)
{
    global $db;
    $query = $db->prepare('SELECT idExecutant FROM TODOLIST_PERSONNES WHERE idTache = :idTache;');
    $query -> execute(array('idTache' => $idTache));
    $data = $query->fetchAll();
    
    if(in_array($idPersonne, array_column($data, 'idExecutant')))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}
function cmdEtatCentreCouts ($idCommande)
{
	global $db;
	$query = $db->prepare('SELECT * FROM COMMANDES WHERE idCommande = :idCommande;');
	$query -> execute(array('idCommande' => $idCommande));
	$commande = $query->fetch();
	
	if($commande['idEtat']==1 OR $commande['idEtat']==8 OR $commande['idCentreDeCout']==Null OR $commande['idCentreDeCout']=='')
	{
		return '<span class="badge bg-grey">N/A</span>';
	}
	
	if($commande['idEtat']<4)
	{
		return '<span class="badge bg-grey">Intégration non-disponible</span>';
	}
	
	if($commande['integreCentreCouts']==0)
	{
		return '<span class="badge bg-orange">En attente d\'intégration</span>';
	}
	
	$query = $db->prepare('SELECT COUNT(*) as nb FROM CENTRE_COUTS_OPERATIONS WHERE idCommande = :idCommande;');
	$query -> execute(array('idCommande' => $idCommande));
	$operations = $query->fetch();
	$operations = $operations['nb'];
	
	if($operations>0)
	{
		return '<span class="badge bg-green">Intégrée</span>';
	}
	else
	{
		return '<span class="badge bg-red">Intégration refusée</span>';
	}
}

function getTDLdateColor ($idTache)
{
	global $db;
	
	$tache = $db->prepare('SELECT * FROM TODOLIST WHERE idTache = :idTache;');
	$tache->execute(array('idTache'=>$idTache));
	$tache = $tache->fetch();
	
	if(date("Y-m-d", strtotime($tache['dateExecution'])) == date("Y-m-d"))
	return 'warning';
	
	if(date("Y-m-d", strtotime($tache['dateExecution'])) < date("Y-m-d"))
	return 'danger';
	
	return 'success';
}

?>