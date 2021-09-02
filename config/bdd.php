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

function writeInLogs($contentEVT, $levelEVT, $userSpecifique)
{
    require_once('config.php');
    global $DEBUG;

    if($levelEVT == 4 AND $DEBUG == 0)
    {
        exit;
    }

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

    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET conf_indicateur1Accueil = :conf_indicateur1Accueil, conf_indicateur2Accueil = :conf_indicateur2Accueil, conf_indicateur3Accueil = :conf_indicateur3Accueil, conf_indicateur4Accueil = :conf_indicateur4Accueil, conf_indicateur5Accueil = :conf_indicateur5Accueil, conf_indicateur6Accueil = :conf_indicateur6Accueil, conf_indicateur7Accueil = :conf_indicateur7Accueil, conf_indicateur8Accueil = :conf_indicateur8Accueil, conf_indicateur9Accueil = :conf_indicateur9Accueil, conf_indicateur10Accueil = :conf_indicateur10Accueil, conf_indicateur11Accueil = :conf_indicateur11Accueil WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $idPersonne,
        'conf_indicateur1Accueil' => (int)$conf_indicateur1Accueil,
        'conf_indicateur2Accueil' => (int)$conf_indicateur2Accueil,
        'conf_indicateur3Accueil' => (int)$conf_indicateur3Accueil,
        'conf_indicateur4Accueil' => (int)$conf_indicateur4Accueil,
        'conf_indicateur5Accueil' => (int)$conf_indicateur5Accueil,
        'conf_indicateur6Accueil' => (int)$conf_indicateur6Accueil,
        'conf_indicateur7Accueil' => (int)$conf_indicateur7Accueil,
        'conf_indicateur8Accueil' => (int)$conf_indicateur8Accueil,
        'conf_indicateur9Accueil' => (int)$conf_indicateur9Accueil,
        'conf_indicateur10Accueil' => (int)$conf_indicateur10Accueil,
        'conf_indicateur11Accueil' => (int)$conf_indicateur11Accueil
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
    
    $notif_tenues_stock = $data['notifications'] && ($data['tenuesCatalogue_lecture']) && ($data['notif_tenues_stock']);
    
    $notif_tenues_retours = $data['notifications'] && ($data['tenues_lecture']) && ($data['notif_tenues_retours']);
    
    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET notif_lots_manquants = :notif_lots_manquants, notif_lots_peremptions = :notif_lots_peremptions, notif_lots_inventaires = :notif_lots_inventaires, notif_lots_conformites = :notif_lots_conformites, notif_reserves_manquants = :notif_reserves_manquants, notif_reserves_peremptions = :notif_reserves_peremptions, notif_reserves_inventaires = :notif_reserves_inventaires, notif_vehicules_assurances = :notif_vehicules_assurances, notif_vehicules_revisions = :notif_vehicules_revisions, notif_vehicules_ct = :notif_vehicules_ct, notif_vehicules_desinfections = :notif_vehicules_desinfections, notif_tenues_stock = :notif_tenues_stock, notif_tenues_retours = :notif_tenues_retours WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $idPersonne,
        'notif_lots_manquants' => (int)$notif_lots_manquants,
        'notif_lots_peremptions' => (int)$notif_lots_peremptions,
        'notif_lots_inventaires' => (int)$notif_lots_inventaires,
        'notif_lots_conformites' => (int)$notif_lots_conformites,
        'notif_reserves_manquants' => (int)$notif_reserves_manquants,
        'notif_reserves_peremptions' => (int)$notif_reserves_peremptions,
        'notif_reserves_inventaires' => (int)$notif_reserves_inventaires,
        'notif_vehicules_assurances' => (int)$notif_vehicules_assurances,
        'notif_vehicules_revisions' => (int)$notif_vehicules_revisions,
        'notif_vehicules_desinfections' => (int)$notif_vehicules_desinfections,
        'notif_vehicules_ct' => (int)$notif_vehicules_ct,
        'notif_tenues_stock' => (int)$notif_tenues_stock,
        'notif_tenues_retours' => (int)$notif_tenues_retours
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