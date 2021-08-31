<?php

require_once 'bdd.php';
require_once 'config.php';
require_once 'mailFunction.php';

//======================================================== RECOPIE DE LA FONCTION ========================================================
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

//======================================================== FIN DE LA RECOPIE ========================================================

$nbLotsNOK = 0;
$query = $db->query('SELECT * FROM LOTS_LOTS WHERE idTypeLot IS NOT NULL;');
while ($data = $query->fetch())
{
    $nbLotsNOK = $nbLotsNOK + checkLotsConf($data['idLot']);
}
$query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE quantite < quantiteAlerte OR quantite = quantiteAlerte;');
$data = $query->fetch();
$nbManquant = $data['nb'];
$query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE;');
$data = $query->fetch();
$nbPerime = $data['nb'];
$query = $db->query('SELECT COUNT(*) as nb FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE notifications=1 OR notifications=2;');
$data = $query->fetch();
$nbDest = $data['nb'];

if ($nbDest > 0)
{

    $nbAlertes = $nbManquant + $nbPerime + $nbLotsNOK;

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
    $message_html = "Bonjour, <br/><br/>Ceci est une notification journalière d'alerte sur " . $APPNAME . "<br/><br/>Alertes de péremption des consommables:<br/><ul>";
    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleLot'] . " > " . $data['libelleSac'] . " > " . $data['libelleEmplacement'] . " > " . $data['libelleMateriel']."</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";

    $message_html = $message_html . "Alertes de quantité:<br/><ul>";
    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE quantite < quantiteAlerte OR quantite = quantiteAlerte;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<li>".$data['libelleLot'] . " > " . $data['libelleSac'] . " > " . $data['libelleEmplacement'] . " > " . $data['libelleMateriel']."</li>";
    }
    $message_html = $message_html."</ul><br/><br/>";

    $message_html = $message_html . "Alertes de conformité des lots:<br/><ul>";
    $query = $db->query('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot WHERE l.idTypeLot IS NOT NULL;');
    while($data = $query->fetch())
    {
        if(checkLotsConf($data['idLot']) == 1)
        {
            $message_html = $message_html . "<li>".$data['libelleLot'] . "</li>";
        }
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
