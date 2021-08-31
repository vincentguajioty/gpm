<?php
session_start();

function createDB()
{
    //---------------------- BASE DE DONNEES ----------------------
    $SERVEURDB = 'x.x.x.'; //adresse IP du serveur entre simple cote, ex: $SERVEUR = '192.169.1.5';
    $DBNAME = 'GPM'; //nom de la base de données, ex: $DB = 'GPM';
    $CHARSET = 'utf8'; //type d'interclassement, utf8 étant recommandé, ex: $CHARSET = 'utf8';
    $USER = 'xxx'; //nom d'utilisateur d'accès à la base de données, ex: $USER = 'utilisateur';
    $PASSWORD = 'xxxx'; //mot de passe d'accès à la base de données, ex: $PASSWORD = 'motDePasse';
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

function majIndicateursPersonne($idPersonne)
{
    global $db;

    $query = $db->prepare('
        SELECT 
            pe.idPersonne,
            pe.identifiant,
            pe.nomPersonne,
            pe.prenomPersonne,
            pe.mailPersonne,
            pe.telPersonne,
            pe.fonction,
            pe.motDePasse,
            pe.conf_accueilRefresh,
            pe.conf_indicateur1Accueil,
            pe.conf_indicateur2Accueil,
            pe.conf_indicateur3Accueil,
            pe.conf_indicateur4Accueil,
            pe.conf_indicateur5Accueil,
            pe.conf_indicateur6Accueil,
            pe.conf_indicateur7Accueil,
            pe.conf_indicateur8Accueil,
            pe.derniereConnexion,
            MAX(po.connexion_connexion) as connexion_connexion,
            MAX(po.logs_lecture) as logs_lecture,
            MAX(po.annuaire_lecture) as annuaire_lecture,
            MAX(po.annuaire_ajout) as annuaire_ajout,
            MAX(po.annuaire_modification) as annuaire_modification,
            MAX(po.annuaire_mdp) as annuaire_mdp,
            MAX(po.annuaire_suppression) as annuaire_suppression,
            MAX(po.profils_lecture) as profils_lecture,
            MAX(po.profils_ajout) as profils_ajout,
            MAX(po.profils_modification) as profils_modification,
            MAX(po.profils_suppression) as profils_suppression,
            MAX(po.categories_lecture) as categories_lecture,
            MAX(po.categories_ajout) as categories_ajout,
            MAX(po.categories_modification) as categories_modification,
            MAX(po.categories_suppression) as categories_suppression,
            MAX(po.fournisseurs_lecture) as fournisseurs_lecture,
            MAX(po.fournisseurs_ajout) as fournisseurs_ajout,
            MAX(po.fournisseurs_modification) as fournisseurs_modification,
            MAX(po.fournisseurs_suppression) as fournisseurs_suppression,
            MAX(po.typesLots_lecture) as typesLots_lecture,
            MAX(po.typesLots_ajout) as typesLots_ajout,
            MAX(po.typesLots_modification) as typesLots_modification,
            MAX(po.typesLots_suppression) as typesLots_suppression,
            MAX(po.lieux_lecture) as lieux_lecture,
            MAX(po.lieux_ajout) as lieux_ajout,
            MAX(po.lieux_modification) as lieux_modification,
            MAX(po.lieux_suppression) as lieux_suppression,
            MAX(po.lots_lecture) as lots_lecture,
            MAX(po.lots_ajout) as lots_ajout,
            MAX(po.lots_modification) as lots_modification,
            MAX(po.lots_suppression) as lots_suppression,
            MAX(po.sac_lecture) as sac_lecture,
            MAX(po.sac_ajout) as sac_ajout,
            MAX(po.sac_modification) as sac_modification,
            MAX(po.sac_suppression) as sac_suppression,
            MAX(po.sac2_lecture) as sac2_lecture,
            MAX(po.sac2_ajout) as sac2_ajout,
            MAX(po.sac2_modification) as sac2_modification,
            MAX(po.sac2_suppression) as sac2_suppression,
            MAX(po.catalogue_lecture) as catalogue_lecture,
            MAX(po.catalogue_ajout) as catalogue_ajout,
            MAX(po.catalogue_modification) as catalogue_modification,
            MAX(po.catalogue_suppression) as catalogue_suppression,
            MAX(po.materiel_lecture) as materiel_lecture,
            MAX(po.materiel_ajout) as materiel_ajout,
            MAX(po.materiel_modification) as materiel_modification,
            MAX(po.materiel_suppression) as materiel_suppression,
            MAX(po.messages_ajout) as messages_ajout,
            MAX(po.messages_suppression) as messages_suppression,
            MAX(po.verrouIP) as verrouIP,
            MAX(po.commande_lecture) as commande_lecture,
            MAX(po.commande_ajout) as commande_ajout,
            MAX(po.commande_valider) as commande_valider,
            MAX(po.commande_etreEnCharge) as commande_etreEnCharge,
            MAX(po.commande_abandonner) as commande_abandonner,
            MAX(po.cout_lecture) as cout_lecture,
            MAX(po.cout_ajout) as cout_ajout,
            MAX(po.cout_etreEnCharge) as cout_etreEnCharge,
            MAX(po.cout_supprimer) as cout_supprimer,
            MAX(po.appli_conf) as appli_conf,
            MAX(po.reserve_lecture) as reserve_lecture,
            MAX(po.reserve_ajout) as reserve_ajout,
            MAX(po.reserve_modification) as reserve_modification,
            MAX(po.reserve_suppression) as reserve_suppression,
            MAX(po.reserve_cmdVersReserve) as reserve_cmdVersReserve,
            MAX(po.reserve_ReserveVersLot) as reserve_ReserveVersLot,
            MAX(po.vhf_canal_lecture) as vhf_canal_lecture,
            MAX(po.vhf_canal_ajout) as vhf_canal_ajout,
            MAX(po.vhf_canal_modification) as vhf_canal_modification,
            MAX(po.vhf_canal_suppression) as vhf_canal_suppression,
            MAX(po.vhf_plan_lecture) as vhf_plan_lecture,
            MAX(po.vhf_plan_ajout) as vhf_plan_ajout,
            MAX(po.vhf_plan_modification) as vhf_plan_modification,
            MAX(po.vhf_plan_suppression) as vhf_plan_suppression,
            MAX(po.vhf_equipement_lecture) as vhf_equipement_lecture,
            MAX(po.vhf_equipement_ajout) as vhf_equipement_ajout,
            MAX(po.vhf_equipement_modification) as vhf_equipement_modification,
            MAX(po.vhf_equipement_suppression) as vhf_equipement_suppression,
            MAX(po.vehicules_lecture) as vehicules_lecture,
            MAX(po.vehicules_ajout) as vehicules_ajout,
            MAX(po.vehicules_modification) as vehicules_modification,
            MAX(po.vehicules_suppression) as vehicules_suppression,
            MAX(po.vehicules_types_lecture) as vehicules_types_lecture,
            MAX(po.vehicules_types_ajout) as vehicules_types_ajout,
            MAX(po.vehicules_types_modification) as vehicules_types_modification,
            MAX(po.vehicules_types_suppression) as vehicules_types_suppression,
            MAX(po.maintenance) as maintenance,
            MAX(po.todolist_perso) as todolist_perso,
			MAX(po.todolist_lecture) as todolist_lecture,
			MAX(po.todolist_modification) as todolist_modification,
			MAX(po.contactMailGroupe) as contactMailGroupe
        FROM
            PROFILS_PERSONNES j
                LEFT OUTER JOIN PERSONNE_REFERENTE pe ON j.idPersonne = pe.idPersonne
                LEFT OUTER JOIN PROFILS po ON j.idProfil = po.idProfil
        WHERE
            pe.idPersonne = :idPersonne;');
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

    $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET conf_indicateur1Accueil = :conf_indicateur1Accueil, conf_indicateur2Accueil = :conf_indicateur2Accueil, conf_indicateur3Accueil = :conf_indicateur3Accueil, conf_indicateur4Accueil = :conf_indicateur4Accueil, conf_indicateur5Accueil = :conf_indicateur5Accueil, conf_indicateur6Accueil = :conf_indicateur6Accueil, conf_indicateur7Accueil = :conf_indicateur7Accueil, conf_indicateur8Accueil = :conf_indicateur8Accueil WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $idPersonne,
        'conf_indicateur1Accueil' => $conf_indicateur1Accueil,
        'conf_indicateur2Accueil' => $conf_indicateur2Accueil,
        'conf_indicateur3Accueil' => $conf_indicateur3Accueil,
        'conf_indicateur4Accueil' => $conf_indicateur4Accueil,
        'conf_indicateur5Accueil' => $conf_indicateur5Accueil,
        'conf_indicateur6Accueil' => $conf_indicateur6Accueil,
        'conf_indicateur7Accueil' => $conf_indicateur7Accueil,
        'conf_indicateur8Accueil' => $conf_indicateur8Accueil
    ));

    writeInLogs("Revue des indicateurs d'accueil pour la personne " . $idPersonne, '3');
}

function majIndicateursProfil($idProfil)
{
    global $db;
    $query = $db->prepare('SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;');
    $query -> execute(array('idProfil' => $idProfil));

    writeInLogs("Revue des indicateurs d'accueil pour le profil " . $idProfil, '3');

    while ($data = $query->fetch())
    {
        majIndicateursPersonne($data['idPersonne']);
    }
}

function majNotificationsPersonne($idPersonne)
{
	global $db;

    $query = $db->prepare('
        SELECT 
            pe.idPersonne,
            pe.identifiant,
            pe.nomPersonne,
            pe.prenomPersonne,
            pe.mailPersonne,
            pe.telPersonne,
            pe.fonction,
            pe.motDePasse,
            pe.conf_accueilRefresh,
            pe.conf_indicateur1Accueil,
            pe.conf_indicateur2Accueil,
            pe.conf_indicateur3Accueil,
            pe.conf_indicateur4Accueil,
            pe.conf_indicateur5Accueil,
            pe.conf_indicateur6Accueil,
            pe.conf_indicateur7Accueil,
            pe.conf_indicateur8Accueil,
            pe.notif_lots_manquants,
            pe.notif_lots_peremptions,
            pe.notif_lots_inventaires,
            pe.notif_lots_conformites,
            pe.notif_reserves_manquants,
            pe.notif_reserves_peremptions,
            pe.notif_reserves_inventaires,
            pe.notif_vehicules_assurances,
            pe.notif_vehicules_revisions,
            pe.notif_vehicules_ct,
            pe.derniereConnexion,
            MAX(po.connexion_connexion) as connexion_connexion,
            MAX(po.logs_lecture) as logs_lecture,
            MAX(po.annuaire_lecture) as annuaire_lecture,
            MAX(po.annuaire_ajout) as annuaire_ajout,
            MAX(po.annuaire_modification) as annuaire_modification,
            MAX(po.annuaire_mdp) as annuaire_mdp,
            MAX(po.annuaire_suppression) as annuaire_suppression,
            MAX(po.profils_lecture) as profils_lecture,
            MAX(po.profils_ajout) as profils_ajout,
            MAX(po.profils_modification) as profils_modification,
            MAX(po.profils_suppression) as profils_suppression,
            MAX(po.categories_lecture) as categories_lecture,
            MAX(po.categories_ajout) as categories_ajout,
            MAX(po.categories_modification) as categories_modification,
            MAX(po.categories_suppression) as categories_suppression,
            MAX(po.fournisseurs_lecture) as fournisseurs_lecture,
            MAX(po.fournisseurs_ajout) as fournisseurs_ajout,
            MAX(po.fournisseurs_modification) as fournisseurs_modification,
            MAX(po.fournisseurs_suppression) as fournisseurs_suppression,
            MAX(po.typesLots_lecture) as typesLots_lecture,
            MAX(po.typesLots_ajout) as typesLots_ajout,
            MAX(po.typesLots_modification) as typesLots_modification,
            MAX(po.typesLots_suppression) as typesLots_suppression,
            MAX(po.lieux_lecture) as lieux_lecture,
            MAX(po.lieux_ajout) as lieux_ajout,
            MAX(po.lieux_modification) as lieux_modification,
            MAX(po.lieux_suppression) as lieux_suppression,
            MAX(po.lots_lecture) as lots_lecture,
            MAX(po.lots_ajout) as lots_ajout,
            MAX(po.lots_modification) as lots_modification,
            MAX(po.lots_suppression) as lots_suppression,
            MAX(po.sac_lecture) as sac_lecture,
            MAX(po.sac_ajout) as sac_ajout,
            MAX(po.sac_modification) as sac_modification,
            MAX(po.sac_suppression) as sac_suppression,
            MAX(po.sac2_lecture) as sac2_lecture,
            MAX(po.sac2_ajout) as sac2_ajout,
            MAX(po.sac2_modification) as sac2_modification,
            MAX(po.sac2_suppression) as sac2_suppression,
            MAX(po.catalogue_lecture) as catalogue_lecture,
            MAX(po.catalogue_ajout) as catalogue_ajout,
            MAX(po.catalogue_modification) as catalogue_modification,
            MAX(po.catalogue_suppression) as catalogue_suppression,
            MAX(po.materiel_lecture) as materiel_lecture,
            MAX(po.materiel_ajout) as materiel_ajout,
            MAX(po.materiel_modification) as materiel_modification,
            MAX(po.materiel_suppression) as materiel_suppression,
            MAX(po.messages_ajout) as messages_ajout,
            MAX(po.messages_suppression) as messages_suppression,
            MAX(po.verrouIP) as verrouIP,
            MAX(po.commande_lecture) as commande_lecture,
            MAX(po.commande_ajout) as commande_ajout,
            MAX(po.commande_valider) as commande_valider,
            MAX(po.commande_etreEnCharge) as commande_etreEnCharge,
            MAX(po.commande_abandonner) as commande_abandonner,
            MAX(po.cout_lecture) as cout_lecture,
            MAX(po.cout_ajout) as cout_ajout,
            MAX(po.cout_etreEnCharge) as cout_etreEnCharge,
            MAX(po.cout_supprimer) as cout_supprimer,
            MAX(po.appli_conf) as appli_conf,
            MAX(po.reserve_lecture) as reserve_lecture,
            MAX(po.reserve_ajout) as reserve_ajout,
            MAX(po.reserve_modification) as reserve_modification,
            MAX(po.reserve_suppression) as reserve_suppression,
            MAX(po.reserve_cmdVersReserve) as reserve_cmdVersReserve,
            MAX(po.reserve_ReserveVersLot) as reserve_ReserveVersLot,
            MAX(po.vhf_canal_lecture) as vhf_canal_lecture,
            MAX(po.vhf_canal_ajout) as vhf_canal_ajout,
            MAX(po.vhf_canal_modification) as vhf_canal_modification,
            MAX(po.vhf_canal_suppression) as vhf_canal_suppression,
            MAX(po.vhf_plan_lecture) as vhf_plan_lecture,
            MAX(po.vhf_plan_ajout) as vhf_plan_ajout,
            MAX(po.vhf_plan_modification) as vhf_plan_modification,
            MAX(po.vhf_plan_suppression) as vhf_plan_suppression,
            MAX(po.vhf_equipement_lecture) as vhf_equipement_lecture,
            MAX(po.vhf_equipement_ajout) as vhf_equipement_ajout,
            MAX(po.vhf_equipement_modification) as vhf_equipement_modification,
            MAX(po.vhf_equipement_suppression) as vhf_equipement_suppression,
            MAX(po.vehicules_lecture) as vehicules_lecture,
            MAX(po.vehicules_ajout) as vehicules_ajout,
            MAX(po.vehicules_modification) as vehicules_modification,
            MAX(po.vehicules_suppression) as vehicules_suppression,
            MAX(po.vehicules_types_lecture) as vehicules_types_lecture,
            MAX(po.vehicules_types_ajout) as vehicules_types_ajout,
            MAX(po.vehicules_types_modification) as vehicules_types_modification,
            MAX(po.vehicules_types_suppression) as vehicules_types_suppression,
            MAX(po.maintenance) as maintenance,
            MAX(po.todolist_perso) as todolist_perso,
			MAX(po.todolist_lecture) as todolist_lecture,
			MAX(po.todolist_modification) as todolist_modification,
			MAX(po.contactMailGroupe) as contactMailGroupe
        FROM
            PROFILS_PERSONNES j
                LEFT OUTER JOIN PERSONNE_REFERENTE pe ON j.idPersonne = pe.idPersonne
                LEFT OUTER JOIN PROFILS po ON j.idProfil = po.idProfil
        WHERE
            pe.idPersonne = :idPersonne;');
    $query -> execute(array('idPersonne' => $idPersonne));
    $data = $query->fetch();
    
    $notif_lots_manquants = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_manquants']);
	$notif_lots_peremptions = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_peremptions']);
	$notif_lots_inventaires = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_inventaires']);
	$notif_lots_conformites = ($data['lots_lecture'] OR $data['sac_lecture'] OR $data['sac2_lecture'] OR $data['materiel_lecture']) && ($data['notif_lots_conformites']);
	$notif_reserves_manquants = ($data['reserve_lecture']) && ($data['notif_reserves_manquants']);
	$notif_reserves_peremptions = ($data['reserve_lecture']) && ($data['notif_reserves_peremptions']);
	$notif_reserves_inventaires = ($data['reserve_lecture']) && ($data['notif_reserves_inventaires']);
	$notif_vehicules_assurances = ($data['vehicules_lecture']) && ($data['notif_vehicules_assurances']);
	$notif_vehicules_revisions = ($data['vehicules_lecture']) && ($data['notif_vehicules_revisions']);
	$notif_vehicules_ct = ($data['vehicules_lecture']) && ($data['notif_vehicules_ct']);
	
	$query = $db->prepare('UPDATE PERSONNE_REFERENTE SET notif_lots_manquants = :notif_lots_manquants, notif_lots_peremptions = :notif_lots_peremptions, notif_lots_inventaires = :notif_lots_inventaires, notif_lots_conformites = :notif_lots_conformites, notif_reserves_manquants = :notif_reserves_manquants, notif_reserves_peremptions = :notif_reserves_peremptions, notif_reserves_inventaires = :notif_reserves_inventaires, notif_vehicules_assurances = :notif_vehicules_assurances, notif_vehicules_revisions = :notif_vehicules_revisions, notif_vehicules_ct = :notif_vehicules_ct WHERE idPersonne = :idPersonne ;');
    $query->execute(array(
        'idPersonne' => $idPersonne,
        'notif_lots_manquants' => $notif_lots_manquants,
		'notif_lots_peremptions' => $notif_lots_peremptions,
		'notif_lots_inventaires' => $notif_lots_inventaires,
		'notif_lots_conformites' => $notif_lots_conformites,
		'notif_reserves_manquants' => $notif_reserves_manquants,
		'notif_reserves_peremptions' => $notif_reserves_peremptions,
		'notif_reserves_inventaires' => $notif_reserves_inventaires,
		'notif_vehicules_assurances' => $notif_vehicules_assurances,
		'notif_vehicules_revisions' => $notif_vehicules_revisions,
		'notif_vehicules_ct' => $notif_vehicules_ct
    ));
    
    writeInLogs("Revue des notifications pour la personne " . $idPersonne, '3');
}

function majNotificationsProfil($idProfil)
{
    global $db;
    $query = $db->prepare('SELECT idPersonne FROM PROFILS_PERSONNES WHERE idProfil = :idProfil;');
    $query -> execute(array('idProfil' => $idProfil));

    writeInLogs("Revue des notifications pour le profil " . $idProfil, '3');

    while ($data = $query->fetch())
    {
        majNotificationsPersonne($data['idPersonne']);
    }
}

?>