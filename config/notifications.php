<?php

require_once 'bdd.php';
require_once 'config.php';
require_once 'mailFunction.php';

checkAllConf();

$query = $db->query('SELECT COUNT(*) as nb FROM PERSONNE_REFERENTE WHERE notif_lots_manquants = 1 OR notif_lots_peremptions = 1 OR notif_lots_inventaires = 1 OR notif_lots_conformites = 1 OR notif_reserves_manquants = 1 OR notif_reserves_peremptions = 1 OR notif_reserves_inventaires = 1 OR notif_vehicules_assurances = 1 OR notif_vehicules_revisions = 1 OR notif_vehicules_ct = 1;');
$data = $query->fetch();
$nbDest = $data['nb'];

if ($nbDest > 0)
{
    $query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE alerteConfRef = 1 AND idEtat = 1;');
	$data = $query->fetch();
	$nbLotsNOK = $data['nb'];

	$query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
	$data = $query->fetch();
	$nbManquant = $data['nb'];

	$query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE) AND idEtat = 1;');
	$data = $query->fetch();
	$nbPerime = $data['nb'];
	
	$query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve;');
	$data = $query->fetch();
	$nbManquantReserve = $data['nb'];
	
	$query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE peremptionReserve < CURRENT_DATE OR peremptionReserve = CURRENT_DATE;');
	$data = $query->fetch();
	$nbPerimeReserve = $data['nb'];
	
	$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND (assuranceExpiration IS NOT NULL) AND ((assuranceExpiration < CURRENT_DATE) OR (assuranceExpiration = CURRENT_DATE));');
	$data = $query->fetch();
	$nbAssurance = $data['nb'];
	
	$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND ((dateNextRevision IS NOT NULL) AND (dateNextRevision < CURRENT_DATE));');
	$data = $query->fetch();
	$nbRevisions = $data['nb'];

	$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND ((dateNextCT IS NOT NULL) AND ((dateNextCT < CURRENT_DATE) OR (dateNextCT = CURRENT_DATE)));');
	$data = $query->fetch();
	$nbCT = $data['nb'];
	
	$query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE idEtat = 1 AND (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
	$data = $query->fetch();
	$nbInventaires = $data['nb'];
	
	$query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_CONTENEUR WHERE (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
	$data = $query->fetch();
	$nbInventairesReserve = $data['nb'];
    
    $nbAlertes = $nbManquant + $nbPerime + $nbLotsNOK + $nbManquantReserve + $nbPerimeReserve + $nbAssurance + $nbRevisions + $nbInventaires + $nbInventairesReserve + $nbCT;

    if ($nbAlertes == 0)
    {
        $query2 = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
            $query2->execute(array(
                'dateEvt' => date('Y-m-d H:i:s'),
                'adresseIP' => 'Serveur Principal',
                'utilisateurEvt' => $APPNAME,
                'idLogLevel' => '2',
                'detailEvt' => 'Pas de notification journalière à envoyer.'
            ));
        exit;
    }


    if ($nbPerime > 0)
	{
	    $message_Perime = $message_Perime . "Alertes de péremption des consommables dans les lots:<br/><ul>";
	    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE) AND idEtat = 1;');
	    while($data = $query->fetch())
	    {
	        $message_Perime = $message_Perime . "<li>".$data['libelleLot'] . " > " . $data['libelleSac'] . " > " . $data['libelleEmplacement'] . " > " . $data['libelleMateriel']."</li>";
	    }
	    $message_Perime = $message_Perime."</ul><br/><br/>";
	}
	
	if ($nbManquant > 0)
	{
	    $message_Manquant = $message_Manquant . "Alertes de quantité des lots:<br/><ul>";
	    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
	    while($data = $query->fetch())
	    {
	        $message_Manquant = $message_Manquant . "<li>".$data['libelleLot'] . " > " . $data['libelleSac'] . " > " . $data['libelleEmplacement'] . " > " . $data['libelleMateriel']."</li>";
	    }
	    $message_Manquant = $message_Manquant."</ul><br/><br/>";
	}
	
	if ($nbLotsNOK > 0)
	{
	    $message_Conf = $message_Conf . "Alertes de conformité des lots:<br/><ul>";
	    $query = $db->query('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot WHERE alerteConfRef = 1 AND idEtat = 1;');
	    while($data = $query->fetch())
	    {
	        $message_Conf = $message_Conf . "<li>".$data['libelleLot'] . "</li>";
	    }
	    $message_Conf = $message_Conf."</ul><br/><br/>";
	}
	
	if ($nbInventaires > 0)
	{
	    $message_InventaireLots = $message_InventaireLots . "Alertes d'inventaires des lots:<br/><ul>";
	    $query = $db->query('SELECT * FROM LOTS_LOTS WHERE idEtat = 1 AND (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
	    while($data = $query->fetch())
	    {
	        $message_InventaireLots = $message_InventaireLots . "<li>".$data['libelleLot'] . "</li>";
	    }
	    $message_InventaireLots = $message_InventaireLots."</ul><br/><br/>";
	}
	
	if ($nbPerimeReserve > 0)
	{
	    $message_PerimeReserve = $message_PerimeReserve . "Alertes de péremption de la réserve:<br/><ul>";
	    $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE peremptionReserve < CURRENT_DATE OR peremptionReserve = CURRENT_DATE;');
	    while($data = $query->fetch())
	    {
	        $message_PerimeReserve = $message_PerimeReserve . "<li>".$data['libelleConteneur'] . " > " . $data['libelleMateriel']."</li>";
	    }
	    $message_PerimeReserve = $message_PerimeReserve."</ul><br/><br/>";
	}
    
    if ($nbManquantReserve > 0)
	{
	    $message_ManquantReserve = $message_ManquantReserve . "Alertes de quantité de la réserve:<br/><ul>";
	    $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve;');
	    while($data = $query->fetch())
	    {
	        $message_ManquantReserve = $message_ManquantReserve . "<li>".$data['libelleConteneur'] . " > " . $data['libelleMateriel']."</li>";
	    }
	    $message_ManquantReserve = $message_ManquantReserve."</ul><br/><br/>";
	}
	
	if ($nbInventairesReserve > 0)
	{
	    $message_InventaireReserve = $message_InventaireReserve . "Alertes d'inventaires des conteneurs de réserve:<br/><ul>";
	    $query = $db->query('SELECT * FROM RESERVES_CONTENEUR WHERE (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
	    while($data = $query->fetch())
	    {
	        $message_InventaireReserve = $message_InventaireReserve . "<li>".$data['libelleConteneur'] . "</li>";
	    }
	    $message_InventaireReserve = $message_InventaireReserve."</ul><br/><br/>";
	}
    
    if ($nbAssurance > 0)
	{
	    $message_Assurance = $message_Assurance . "Véhicules dont l'assurance est arrivée à échéance:<br/><ul>";
	    $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND (assuranceExpiration IS NOT NULL) AND ((assuranceExpiration < CURRENT_DATE) OR (assuranceExpiration = CURRENT_DATE));');
	    while($data = $query->fetch())
	    {
	        $message_Assurance = $message_Assurance . "<li>".$data['libelleVehicule'] . "</li>";
	    }
	    $message_Assurance = $message_Assurance."</ul><br/><br/>";
	}
    
    if ($nbRevisions > 0)
	{
	    $message_Revisions = $message_Revisions . "Véhicules à faire passer à la révision:<br/><ul>";
	    $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND ((dateNextRevision IS NOT NULL) AND (dateNextRevision < CURRENT_DATE));');
	    while($data = $query->fetch())
	    {
	        $message_Revisions = $message_Revisions . "<li>".$data['libelleVehicule'] . "</li>";
	    }
	    $message_Revisions = $message_Revisions."</ul><br/><br/>";
	}

	if ($nbCT > 0)
	{
	    $message_CT = $message_CT . "Véhicules à faire passer au CT:<br/><ul>";
	    $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND ((dateNextCT IS NOT NULL) AND ((dateNextCT < CURRENT_DATE) OR (dateNextCT = CURRENT_DATE)));');
	    while($data = $query->fetch())
	    {
	        $message_CT = $message_CT . "<li>".$data['libelleVehicule'] . "</li>";
	    }
	    $message_CT = $message_CT."</ul><br/><br/>";
	}





    $query = $db->query('SELECT * FROM PERSONNE_REFERENTE WHERE notif_lots_manquants = 1 OR notif_lots_peremptions = 1 OR notif_lots_inventaires = 1 OR notif_lots_conformites = 1 OR notif_reserves_manquants = 1 OR notif_reserves_peremptions = 1 OR notif_reserves_inventaires = 1 OR notif_vehicules_assurances = 1 OR notif_vehicules_revisions = 1 OR notif_vehicules_ct = 1;');
	while($data = $query->fetch())
	{
		$nbAlertes = 0;
		$nbAlertes += ($data['notif_lots_manquants'] == 1) ? $nbManquant : 0;
		$nbAlertes += ($data['notif_lots_peremptions'] == 1) ? $nbPerime : 0;
		$nbAlertes += ($data['notif_lots_inventaires'] == 1) ? $nbInventaires : 0;
		$nbAlertes += ($data['notif_lots_conformites'] == 1) ? $nbLotsNOK : 0;
		$nbAlertes += ($data['notif_reserves_manquants'] == 1) ? $nbManquantReserve : 0;
		$nbAlertes += ($data['notif_reserves_peremptions'] == 1) ? $nbPerimeReserve : 0;
		$nbAlertes += ($data['notif_vehicules_assurances'] == 1) ? $nbAssurance : 0;
		$nbAlertes += ($data['notif_vehicules_revisions'] == 1) ? $nbRevisions : 0;
		$nbAlertes += ($data['notif_vehicules_ct'] == 1) ? $nbCT : 0;
		$nbAlertes += ($data['notif_reserves_inventaires'] == 1) ? $nbInventairesReserve : 0;

		if($nbAlertes > 0)
		{
			if ($nbAlertes == 1)
		    {
		        $sujet = "[" . $APPNAME . "] Bilan journalier - 1 alerte en cours sur votre parc materiel";
		    }
		    else
		    {
		        $sujet = "[" . $APPNAME . "] Bilan journalier - " . $nbAlertes  . " alertes en cours sur votre parc materiel";
		    }


		    $message_html = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/>Ceci est une notification journalière d'alerte sur " . $APPNAME . ".<br/><br/>";

		    if(($data['notif_lots_manquants']) AND ($nbManquant>0))
		    {
		    	$message_html .= $message_Manquant;
		    }
		    if(($data['notif_lots_peremptions']) AND ($nbPerime>0))
		    {
		    	$message_html .= $message_Perime;
		    }
		    if(($data['notif_lots_inventaires']) AND ($nbInventaires>0))
		    {
		    	$message_html .= $message_InventaireLots;
		    }
		    if(($data['notif_lots_conformites']) AND ($nbLotsNOK>0))
		    {
		    	$message_html .= $message_Conf;
		    }
		    if(($data['notif_reserves_manquants']) AND ($nbManquantReserve>0))
		    {
		    	$message_html .= $message_ManquantReserve;
		    }
		    if(($data['notif_reserves_peremptions']) AND ($nbPerimeReserve>0))
		    {
		    	$message_html .= $message_PerimeReserve;
		    }
		    if(($data['notif_reserves_inventaires']) AND ($nbInventairesReserve>0))
		    {
		    	$message_html .= $message_InventaireReserve;
		    }
		    if(($data['notif_vehicules_assurances']) AND ($nbAssurance>0))
		    {
		    	$message_html .= $message_Assurance;
		    }
		    if(($data['notif_vehicules_revisions']) AND ($nbRevisions>0))
		    {
		    	$message_html .= $message_Revisions;
		    }
		    if(($data['notif_vehicules_ct']) AND ($nbCT>0))
		    {
		    	$message_html .= $message_CT;
		    }
		    
		    $message_html = $message_html . "Cordialement<br/><br/>L'équipe administrative de " . $APPNAME . "<br/><br/>";
		    $message.= $RETOURLIGNE.$message_html.$RETOURLIGNE;
		    $prio = 3;

		    if(sendmail($data['mailPersonne'], $sujet, $prio, $message))
	        {
	            $query2 = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
	            $query2->execute(array(
	                'dateEvt' => date('Y-m-d H:i:s'),
	                'adresseIP' => 'Serveur Principal',
	                'utilisateurEvt' => $APPNAME,
	                'idLogLevel' => '2',
	                'detailEvt' => 'Notification journalière envoyée avec succès à ' . $data['mailPersonne']
	            ));
	        }
	        else
	        {
	            $query2 = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurEvt, :idLogLevel, :detailEvt);');
	            $query2->execute(array(
	                'dateEvt' => date('Y-m-d H:i:s'),
	                'adresseIP' => 'Serveur Principal',
	                'utilisateurEvt' => $APPNAME,
	                'idLogLevel' => '5',
	                'detailEvt' => 'Echec dans la l\'envoi de la notification journalière à ' . $data['mailPersonne']
	            ));
	        }
	        
	        unset($message_html);
	        unset($message);
	        unset($sujet);
		}
		
	}
}

?>
