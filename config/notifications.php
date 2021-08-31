<?php

require_once 'bdd.php';
require_once 'config.php';
require_once 'mailFunction.php';

checkAllConf();

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
$query = $db->query('SELECT COUNT(*) as nb FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE notifications=1 OR notifications=2;');
$data = $query->fetch();
$nbDest = $data['nb'];
$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND (assuranceExpiration IS NOT NULL) AND ((assuranceExpiration < CURRENT_DATE) OR (assuranceExpiration = CURRENT_DATE));');
$data = $query->fetch();
$nbAssurance = $data['nb'];
$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND ((dateNextRevision IS NOT NULL) AND ((dateNextRevision < CURRENT_DATE) OR (dateNextRevision = CURRENT_DATE))OR((dateNextCT IS NOT NULL) AND ((dateNextCT < CURRENT_DATE) OR (dateNextCT = CURRENT_DATE))));');
$data = $query->fetch();
$nbRevisions = $data['nb'];

if ($nbDest > 0)
{

    $nbAlertes = $nbManquant + $nbPerime + $nbLotsNOK + $nbManquantReserve + $nbPerimeReserve + $nbAssurance + $nbRevisions;

    if ($nbAlertes == 0)
    {
        $sujet = "[" . $APPNAME . "] Bilan journalier - Aucune alerte";
    }
    else
    {
        if ($nbAlertes == 1)
        {
            $sujet = "[" . $APPNAME . "] Bilan journalier - 1 alerte en cours sur votre parc materiel";
        }
        else
        {
            $sujet = "[" . $APPNAME . "] Bilan journalier - " . $nbAlertes  . " alertes en cours sur votre parc materiel";
        }
    }

    //=====Déclaration des messages au format texte et au format HTML.
    $message_html = "Bonjour, <br/><br/>Ceci est une notification journalière d'alerte sur " . $APPNAME . "<br/><br/>Alertes de péremption des consommables dans les lots:<br/><ul>";
    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE) AND idEtat = 1;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleLot'] . " > " . $data['libelleSac'] . " > " . $data['libelleEmplacement'] . " > " . $data['libelleMateriel']."</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";

    $message_html = $message_html . "Alertes de quantité des lots:<br/><ul>";
    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleLot'] . " > " . $data['libelleSac'] . " > " . $data['libelleEmplacement'] . " > " . $data['libelleMateriel']."</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";

    $message_html = $message_html . "Alertes de conformité des lots:<br/><ul>";
    $query = $db->query('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot WHERE alerteConfRef = 1 AND idEtat = 1;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleLot'] . "</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";
    
    $message_html = $message_html . "Alertes de péremption de la réserve:<br/><ul>";
    $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE peremptionReserve < CURRENT_DATE OR peremptionReserve = CURRENT_DATE;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleConteneur'] . " > " . $data['libelleMateriel']."</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";
    
    $message_html = $message_html . "Alertes de quantité de la réserve:<br/><ul>";
    $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleConteneur'] . " > " . $data['libelleMateriel']."</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";
    
    $message_html = $message_html . "Véhicules dont l'assurance est arrivée à échéance:<br/><ul>";
    $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND (assuranceExpiration IS NOT NULL) AND ((assuranceExpiration < CURRENT_DATE) OR (assuranceExpiration = CURRENT_DATE));');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleVehicule'] . "</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";
    
    $message_html = $message_html . "Véhicules à faire passer à la révision ou au contrôle technique:<br/><ul>";
    $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND ((dateNextRevision IS NOT NULL) AND ((dateNextRevision < CURRENT_DATE) OR (dateNextRevision = CURRENT_DATE))OR((dateNextCT IS NOT NULL) AND ((dateNextCT < CURRENT_DATE) OR (dateNextCT = CURRENT_DATE))));');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleVehicule'] . "</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";
    
    $message_html = $message_html . "Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;


    if ($nbAlertes > 0)
    {
        $prio = 3;
    }
    else
    {
        $prio = 2;
    }

    //==========

    //=====Création du message.
    $message.= $RETOURLIGNE.$message_html.$RETOURLIGNE;
    //==========
    //=====Envoi de l'e-mail.
    if ($nbAlertes == 0)
    {
        $query = $db->query('SELECT mailPersonne FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE notifications=2;');
    }
    else
    {
        $query = $db->query('SELECT mailPersonne FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE notifications=1 OR notifications=2;');
    }

    while($data = $query->fetch())
    {
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
    }

    //==========
}

?>
