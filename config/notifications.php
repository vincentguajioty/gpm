<?php

require_once 'bdd.php';
require_once 'version.php';

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
    $passage_ligne = "\r\n";

    $nbAlertes = $nbManquant + $nbPerime + $nbLotsNOK;

    if ($nbAlertes == 0)
    {
        $sujet = $APPNAME . " - Bilan journalier - Aucune alerte";
    }
    else
    {
        if ($nbAlertes == 1)
        {
            $sujet = $APPNAME . " - Bilan journalier - 1 alerte en cours sur votre parc materiel";
        }
        else
        {
            $sujet = $APPNAME . " - Bilan journalier - " . $nbAlertes  . " alertes en cours sur votre parc materiel";
        }
    }

    //=====Déclaration des messages au format texte et au format HTML.
    $message_html = file_get_contents('notificationsC1.html', FILE_USE_INCLUDE_PATH);
    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<tr>";
        $message_html = $message_html . "<th>".$data['idElement']."</th>";
        $message_html = $message_html . "<th>".$data['libelleMateriel']."</th>";
        $message_html = $message_html . "<th>".$data['libelleEmplacement']."</th>";
        $message_html = $message_html . "<th>".$data['libelleSac']."</th>";
        $message_html = $message_html . "<th>".$data['libelleLot']."</th>";
        $message_html = $message_html . "<th>".$data['identifiant']."</th>";
        $message_html = $message_html . "<th>".$data['peremption']."</th>";
        $message_html = $message_html . "</tr>";
    }

    $message_html = $message_html . file_get_contents('notificationsC2.html', FILE_USE_INCLUDE_PATH);
    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE quantite < quantiteAlerte OR quantite = quantiteAlerte;');
    while($data = $query->fetch())
    {
        $message_html = $message_html . "<tr>";
        $message_html = $message_html . "<th>".$data['idElement']."</th>";
        $message_html = $message_html . "<th>".$data['libelleMateriel']."</th>";
        $message_html = $message_html . "<th>".$data['libelleEmplacement']."</th>";
        $message_html = $message_html . "<th>".$data['libelleSac']."</th>";
        $message_html = $message_html . "<th>".$data['libelleLot']."</th>";
        $message_html = $message_html . "<th>".$data['identifiant']."</th>";
        $message_html = $message_html . "<th>".$data['quantite']."</th>";
        $message_html = $message_html . "</tr>";
    }

    $message_html = $message_html . file_get_contents('notificationsC3.html', FILE_USE_INCLUDE_PATH);
    $query = $db->query('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot WHERE l.idTypeLot IS NOT NULL;');
    while($data = $query->fetch())
    {
        if(checkLotsConf($data['idLot']) == 1)
        {
            $message_html = $message_html . "<tr>";
            $message_html = $message_html . "<th>".$data['idLot']."</th>";
            $message_html = $message_html . "<th>".$data['libelleLot']."</th>";
            $message_html = $message_html . "<th>".$data['libelleTypeLot']."</th>";
            $message_html = $message_html . "<th>".$data['identifiant']."</th>";
            $message_html = $message_html . "</tr>";
        }
    }

    $message_html = $message_html . file_get_contents('notificationsC4.html', FILE_USE_INCLUDE_PATH);
    //==========

    //=====Création de la boundary
    $boundary = "-----=".md5(rand());
    //==========

    //=====Création du header de l'e-mail.
    $header = "From: \"" . $APPNAME . "\"<" . $MAILSERVER . ">".$passage_ligne;
    $header.= "Reply-to: ".$MAILSERVER.$passage_ligne;
    $header.= "MIME-Version: 1.0".$passage_ligne;
    if ($nbAlertes > 0)
    {
        $header .= "X-Priority: 1".$passage_ligne;
    }
    else
    {
        $header .= "X-Priority: 5".$passage_ligne;
    }

    $header.= "Content-Type: text/html; charset=UTF-8;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
    //==========

    //=====Création du message.
    //$message = $passage_ligne."--".$boundary.$passage_ligne;
    //=====Ajout du message au format HTML.
    //$message.= "Content-Type: text/html".$passage_ligne;
    //$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
    $message.= $passage_ligne.$message_html.$passage_ligne;
    //==========

    //==========
    //$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
    //$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
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
        if(mail($data['mailPersonne'],$sujet,$message,$header))
        {
            $query2 = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurApollonEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurApollonEvt, :idLogLevel, :detailEvt);');
            $query2->execute(array(
                'dateEvt' => date('Y-m-d H:i:s'),
                'adresseIP' => 'Serveur Principal',
                'utilisateurApollonEvt' => 'APOLLON',
                'idLogLevel' => '2',
                'detailEvt' => 'Notification journalière envoyée avec succès à ' . $data['mailPersonne']
            ));
        }
        else
        {
            $query2 = $db->prepare('INSERT INTO LOGS(dateEvt, adresseIP, utilisateurApollonEvt, idLogLevel, detailEvt)VALUES(:dateEvt, :adresseIP, :utilisateurApollonEvt, :idLogLevel, :detailEvt);');
            $query2->execute(array(
                'dateEvt' => date('Y-m-d H:i:s'),
                'adresseIP' => 'Serveur Principal',
                'utilisateurApollonEvt' => 'APOLLON',
                'idLogLevel' => '5',
                'detailEvt' => 'Echec dans la l\'envoi de la notification journalière à ' . $data['mailPersonne']
            ));
        }
    }

    //==========
}

?>
