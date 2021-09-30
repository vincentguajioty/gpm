<?php
require_once '../config/config.php';
require_once '../config/bdd.php';

$query = $db->query('SELECT * FROM CONFIG;');
$data = $query->fetch();

switch($data['version'])
{
    case '2.2':
        writeInLogs("Début de l'installation de la mise à jour 2.3", '1', NULL);
        $query = $db->query(file_get_contents ("update2.3.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 2.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '2.3':
        writeInLogs("Début de l'installation de la mise à jour 2.4", '1', NULL);
        $query = $db->query(file_get_contents ("update2.4.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 2.4", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '2.4':
        writeInLogs("Début de l'installation de la mise à jour 2.5", '1', NULL);
        $query = $db->query(file_get_contents ("update2.5.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 2.5", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
		
	case '2.5':
        writeInLogs("Début de l'installation de la mise à jour 2.6", '1', NULL);
        $query = $db->query(file_get_contents ("update2.6.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 2.6", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '2.6':
        writeInLogs("Début de l'installation de la mise à jour 3.0", '1', NULL);
        $query = $db->query(file_get_contents ("update3.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 3.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '3.0':
        writeInLogs("Début de l'installation de la mise à jour 3.1", '1', NULL);
        $query = $db->query(file_get_contents ("update3.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 3.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '3.1':
        writeInLogs("Début de l'installation de la mise à jour 4.0", '1', NULL);
        $query = $db->query(file_get_contents ("update4.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 4.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '4.0':
        writeInLogs("Début de l'installation de la mise à jour 4.1", '1', NULL);
        $query = $db->query(file_get_contents ("update4.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 4.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
	
	case '4.1':
        writeInLogs("Début de l'installation de la mise à jour 4.2", '1', NULL);
        $query = $db->query(file_get_contents ("update4.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 4.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
	
	case '4.2':
        writeInLogs("Début de l'installation de la mise à jour 4.3", '1', NULL);
        $query = $db->query(file_get_contents ("update4.3.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 4.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '4.3':
        writeInLogs("Début de l'installation de la mise à jour 5.0", '1', NULL);
        $query = $db->query(file_get_contents ("update5.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 5.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.0':
        writeInLogs("Début de l'installation de la mise à jour 5.1", '1', NULL);
        $query = $db->query(file_get_contents ("update5.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 5.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.1':
        writeInLogs("Début de l'installation de la mise à jour 5.2", '1', NULL);
        $query = $db->query(file_get_contents ("update5.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 5.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.2':
        writeInLogs("Début de l'installation de la mise à jour 5.3", '1', NULL);
        $query = $db->query(file_get_contents ("update5.3.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 5.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
	
	case '5.3':
        writeInLogs("Début de l'installation de la mise à jour 5.4", '1', NULL);
        $query = $db->query(file_get_contents ("update5.4.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 5.4", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.4':
        writeInLogs("Début de l'installation de la mise à jour 5.5", '1', NULL);
        $query = $db->query(file_get_contents ("update5.5.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 5.5", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.5':
        writeInLogs("Début de l'installation de la mise à jour 5.6", '1', NULL);
        $query = $db->query(file_get_contents ("update5.6.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 5.6", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
		
    case '5.6':
        writeInLogs("Début de l'installation de la mise à jour 5.7", '1', NULL);
        $query = $db->query(file_get_contents ("update5.7.sql"));
        $query = $db->query('SELECT idPersonne FROM PERSONNE_REFERENTE;');
        while($data = $query->fetch())
        {
            majIndicateursPersonne($data['idPersonne'],1);
            majNotificationsPersonne($data['idPersonne'],1);
        }
        writeInLogs("Fin de l'installation de la mise à jour 5.7", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '5.7':
        writeInLogs("Début de l'installation de la mise à jour 6.0", '1', NULL);
        $query = $db->query(file_get_contents ("update6.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 6.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '6.0':
        writeInLogs("Début de l'installation de la mise à jour 6.1", '1', NULL);
        $query = $db->query(file_get_contents ("update6.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 6.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '6.1':
        writeInLogs("Début de l'installation de la mise à jour 6.2", '1', NULL);
        $query = $db->query(file_get_contents ("update6.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 6.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '6.2':
        writeInLogs("Début de l'installation de la mise à jour 7.0", '1', NULL);
        $query = $db->query(file_get_contents ("update7.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 7.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.0':
        writeInLogs("Début de l'installation de la mise à jour 7.1", '1', NULL);
        $query = $db->query(file_get_contents ("update7.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 7.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.1':
        writeInLogs("Début de l'installation de la mise à jour 7.2", '1', NULL);
        $query = $db->query(file_get_contents ("update7.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 7.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.2':
        writeInLogs("Début de l'installation de la mise à jour 7.3", '1', NULL);
        $query = $db->query(file_get_contents ("update7.3.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 7.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.3':
        writeInLogs("Début de l'installation de la mise à jour 7.4", '1', NULL);
        $query = $db->query(file_get_contents ("update7.4.1.sql"));
        $vehicules = $db->query('SELECT * FROM VEHICULES WHERE kilometrage > 0;');
        while($vehicule = $vehicules->fetch())
        {
        	$addSQL = $db->prepare('INSERT INTO VEHICULES_RELEVES SET idVehicule = :idVehicule, releveKilometrique = :releveKilometrique, dateReleve = :dateReleve, idPersonne = Null;');
        	$addSQL->execute(array(
        		'idVehicule' => $vehicule['idVehicule'],
        		'releveKilometrique' => $vehicule['kilometrage'],
        		'dateReleve' => date('Y-m-d')
        	));
        }
        $query = $db->query(file_get_contents ("update7.4.2.sql"));
        $query = $db->query('SELECT idPersonne FROM PERSONNE_REFERENTE;');
        while($data = $query->fetch())
        {
            majIndicateursPersonne($data['idPersonne'],1);
            majNotificationsPersonne($data['idPersonne'],1);
        }
        writeInLogs("Fin de l'installation de la mise à jour 7.4", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.4':
        writeInLogs("Début de l'installation de la mise à jour 7.5", '1', NULL);
        $query = $db->query(file_get_contents ("update7.5.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 7.5", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.5':
        writeInLogs("Début de l'installation de la mise à jour 7.6", '1', NULL);
        $query = $db->query(file_get_contents ("update7.6.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 7.6", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.6':
        writeInLogs("Début de l'installation de la mise à jour 8.0", '1', NULL);
        $query = $db->query(file_get_contents ("update8.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 8.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '8.0':
        writeInLogs("Début de l'installation de la mise à jour 8.1", '1', NULL);
        $query = $db->query(file_get_contents ("update8.1.1.sql"));
        $centres = $db->query('SELECT * FROM CENTRE_COUTS WHERE idResponsable IS NOT NULL;');
        while($centre = $centres->fetch())
        {
        	$addSQL = $db->prepare('INSERT INTO CENTRE_COUTS_PERSONNES SET idCentreDeCout = :idCentreDeCout, idPersonne = :idPersonne;');
        	$addSQL->execute(array(
        		'idCentreDeCout' => $centre['idCentreDeCout'],
        		'idPersonne' => $centre['idResponsable']
        	));
        }
        $query = $db->query(file_get_contents ("update8.1.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 8.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '8.1':
        writeInLogs("Début de l'installation de la mise à jour 8.2", '1', NULL);
        $query = $db->query(file_get_contents ("update8.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 8.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
    
    case '8.2':
        writeInLogs("Début de l'installation de la mise à jour 8.3", '1', NULL);
        $query = $db->query(file_get_contents ("update8.3.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 8.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '8.3':
        writeInLogs("Début de l'installation de la mise à jour 8.4", '1', NULL);
        $query = $db->query(file_get_contents ("update8.4.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 8.4", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '8.4':
        writeInLogs("Début de l'installation de la mise à jour 8.5", '1', NULL);
        $query = $db->query(file_get_contents ("update8.5.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 8.5", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '8.5':
        writeInLogs("Début de l'installation de la mise à jour 8.6", '1', NULL);
        $query = $db->query(file_get_contents ("update8.6.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 8.6", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
    
    case '8.6':
        writeInLogs("Début de l'installation de la mise à jour 9.0", '1', NULL);
        writeInLogs("Sauvegarde des anciennes logs en base vers le fichier logs/_OLD_LOGS_TABLE.log", '1', NULL);
        $logs = $db->query('SELECT * FROM LOGS l LEFT OUTER JOIN LOGS_LEVEL ll ON l.idLogLevel = ll.idLogLevel;');
        while($log = $logs->fetch())
        {
            $content = '['.$log['dateEvt'].']'."\t".'['.$log['adresseIP'].']'."\t".'['.$log['utilisateurEvt'].']'."\t".'['.$log['libelleLevel'].'] '.$log['detailEvt'].PHP_EOL;
            chdir(dirname(__FILE__));
            file_put_contents('../logs/_OLD_LOGS_TABLE.log', $content, FILE_APPEND);
        }
        writeInLogs("Fin de la sauvegarde des anciennes logs en base", '1', NULL);
        writeInLogs("Début du script de base de données", '1', NULL);
        $query = $db->query(file_get_contents ("update9.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 9.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '9.0':
        writeInLogs("Début de l'installation de la mise à jour 9.1", '1', NULL);
        $query = $db->query(file_get_contents ("update9.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 9.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
    
    case '9.1':
        writeInLogs("Début de l'installation de la mise à jour 9.2", '1', NULL);
        $query = $db->query(file_get_contents ("update9.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 9.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '9.2':
        writeInLogs("Début de l'installation de la mise à jour 9.3", '1', NULL);
        $query = $db->query(file_get_contents ("update9.3.sql"));
        $query = $db->query('SELECT idPersonne FROM PERSONNE_REFERENTE;');
        while($data = $query->fetch())
        {
            majIndicateursPersonne($data['idPersonne'],1);
            majNotificationsPersonne($data['idPersonne'],1);
        }
        writeInLogs("Fin de l'installation de la mise à jour 9.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '9.3':
        writeInLogs("Début de l'installation de la mise à jour 9.4", '1', NULL);
        $query = $db->query(file_get_contents ("update9.4.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 9.4", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '9.4':
        writeInLogs("Début de l'installation de la mise à jour 9.5", '1', NULL);
        $query = $db->query(file_get_contents ("update9.5.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 9.5", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '9.5':
        writeInLogs("Début de l'installation de la mise à jour 9.6", '1', NULL);
        $query = $db->query(file_get_contents ("update9.6.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 9.6", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '9.6':
        writeInLogs("Début de l'installation de la mise à jour 9.7", '1', NULL);
        $query = $db->query(file_get_contents ("update9.7.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 9.7", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '9.7':
        writeInLogs("Début de l'installation de la mise à jour 9.8", '1', NULL);
        $query = $db->query(file_get_contents ("update9.8.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 9.8", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '9.8':
        writeInLogs("Début de l'installation de la mise à jour 10.0", '1', NULL);
        $query = $db->query(file_get_contents ("update10.0.sql"));
        notificationsConditionsMAJ();
        notificationsMAJpersonne();
        $query = $db->query('SELECT idPersonne FROM PERSONNE_REFERENTE;');
        while($data = $query->fetch())
        {
            majIndicateursPersonne($data['idPersonne'],1);
            majNotificationsPersonne($data['idPersonne'],1);
        }
        writeInLogs("Fin de l'installation de la mise à jour 10.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '10.0':
        writeInLogs("Début de l'installation de la mise à jour 10.1", '1', NULL);
        $query = $db->query(file_get_contents ("update10.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 10.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '10.1':
        writeInLogs("Début de l'installation de la mise à jour 10.2", '1', NULL);
        $query = $db->query(file_get_contents ("update10.2.sql"));
        $vehicules = $db->query('SELECT idVehicule FROM VEHICULES;');
        while($vehicule = $vehicules->fetch())
        {
        	$updateVehicule = $db->prepare('UPDATE VEHICULES SET couleurGraph = :couleurGraph WHERE idVehicule = :idVehicule;');
        	$updateVehicule->execute(array('couleurGraph'=>randomColor(),'idVehicule'=>$vehicule['idVehicule']));
        }
        writeInLogs("Fin de l'installation de la mise à jour 10.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '10.2':
        writeInLogs("Début de l'installation de la mise à jour 10.3", '1', NULL);
        $query = $db->query(file_get_contents ("update10.3.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 10.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '10.3':
        writeInLogs("Début de l'installation de la mise à jour 11.0", '1', NULL);
        $query = $db->query(file_get_contents ("update11.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 11.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '11.0':
        writeInLogs("Début de l'installation de la mise à jour 11.1", '1', NULL);
        $query = $db->query(file_get_contents ("update11.1.sql"));
        $query = $db->query('SELECT idPersonne FROM PERSONNE_REFERENTE;');
        while($data = $query->fetch())
        {
            majIndicateursPersonne($data['idPersonne'],1);
            majNotificationsPersonne($data['idPersonne'],1);
        }
        writeInLogs("Fin de l'installation de la mise à jour 11.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '11.1':
        writeInLogs("Début de l'installation de la mise à jour 12.0", '1', NULL);
        $query = $db->query(file_get_contents ("update12.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 12.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '12.0':
        writeInLogs("Début de l'installation de la mise à jour 12.1", '1', NULL);
        $query = $db->query(file_get_contents ("update12.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 12.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '12.1':
        writeInLogs("Début de l'installation de la mise à jour 12.2", '1', NULL);
        $query = $db->query(file_get_contents ("update12.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 12.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '12.2':
        writeInLogs("Début de l'installation de la mise à jour 12.3", '1', NULL);
        $query = $db->query(file_get_contents ("update12.3.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 12.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '12.3':
        writeInLogs("Début de l'installation de la mise à jour 13.0", '1', NULL);
        $query = $db->query(file_get_contents ("update13.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 13.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '13.0':
        writeInLogs("Début de l'installation de la mise à jour 13.1", '1', NULL);
        $query = $db->query(file_get_contents ("update13.1.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 13.1", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '13.1':
        writeInLogs("Début de l'installation de la mise à jour 13.2", '1', NULL);
        $query = $db->query(file_get_contents ("update13.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 13.2", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '13.2':
        writeInLogs("Début de l'installation de la mise à jour 13.3", '1', NULL);
        $query = $db->query(file_get_contents ("update13.3.1.sql"));

        $revision  = $db->query('SELECT idHealthType FROM VEHICULES_HEALTH_TYPES WHERE libelleHealthType = "Révision" ORDER BY idHealthType DESC');
        $revision  = $revision->fetch();
        $revision  = $revision['idHealthType'];
        $ct        = $db->query('SELECT idHealthType FROM VEHICULES_HEALTH_TYPES WHERE libelleHealthType = "CT" ORDER BY idHealthType DESC');
        $ct        = $ct->fetch();
        $ct        = $ct['idHealthType'];
        $assurance = $db->query('SELECT idHealthType FROM VEHICULES_HEALTH_TYPES WHERE libelleHealthType = "Renouvellement Assurance" ORDER BY idHealthType DESC');
        $assurance = $assurance->fetch();
        $assurance = $assurance['idHealthType'];

        $vehicules = $db->query('SELECT * FROM VEHICULES;');
        while($vehicule = $vehicules->fetch())
        {
            $query = $db->prepare('INSERT INTO VEHICULES_HEALTH_ALERTES SET idHealthType = :idHealthType, idVehicule = :idVehicule, frequenceHealth = 365');
            $query->execute(array('idHealthType'=>$revision, 'idVehicule'=>$vehicule['idVehicule']));
            $query = $db->prepare('INSERT INTO VEHICULES_HEALTH_ALERTES SET idHealthType = :idHealthType, idVehicule = :idVehicule, frequenceHealth = 365');
            $query->execute(array('idHealthType'=>$ct, 'idVehicule'=>$vehicule['idVehicule']));
            $query = $db->prepare('INSERT INTO VEHICULES_HEALTH_ALERTES SET idHealthType = :idHealthType, idVehicule = :idVehicule, frequenceHealth = 365');
            $query->execute(array('idHealthType'=>$assurance, 'idVehicule'=>$vehicule['idVehicule']));

            if($vehicule['dateNextCT'] <> Null)
            {
                $query = $db->prepare('
                    INSERT INTO
                        VEHICULES_HEALTH
                    SET
                        idVehicule = :idVehicule,
                        dateHealth = :dateHealth
                    ;');
                $query->execute(array(
                    'idVehicule' => $vehicule['idVehicule'],
                    'dateHealth' => date('Y-m-d', strtotime($vehicule['dateNextCT'].' -1 year')),
                ));
                switch($query->errorCode())
                {
                    case '00000':
                        $health = $db->query('SELECT idVehiculeHealth FROM VEHICULES_HEALTH ORDER BY idVehiculeHealth DESC;');
                        $health = $health->fetch();
                        $insert = $db->prepare('
                            INSERT INTO
                                VEHICULES_HEALTH_CHECKS
                            SET
                                idVehiculeHealth = :idVehiculeHealth,
                                idHealthType     = :idHealthType,
                                remarquesCheck   = "Ajouté par le script de version"
                            ;');
                        $insert->execute(array(
                            'idVehiculeHealth' => $health['idVehiculeHealth'],
                            'idHealthType'     => $ct,
                        ));
                    break;
                }
            }

            if($vehicule['dateNextRevision'] <> Null)
            {
                $query = $db->prepare('
                    INSERT INTO
                        VEHICULES_HEALTH
                    SET
                        idVehicule = :idVehicule,
                        dateHealth = :dateHealth
                    ;');
                $query->execute(array(
                    'idVehicule' => $vehicule['idVehicule'],
                    'dateHealth' => date('Y-m-d', strtotime($vehicule['dateNextRevision'].' -1 year')),
                ));
                switch($query->errorCode())
                {
                    case '00000':
                        $health = $db->query('SELECT idVehiculeHealth FROM VEHICULES_HEALTH ORDER BY idVehiculeHealth DESC;');
                        $health = $health->fetch();
                        $insert = $db->prepare('
                            INSERT INTO
                                VEHICULES_HEALTH_CHECKS
                            SET
                                idVehiculeHealth = :idVehiculeHealth,
                                idHealthType     = :idHealthType,
                                remarquesCheck   = "Ajouté par le script de version"
                            ;');
                        $insert->execute(array(
                            'idVehiculeHealth' => $health['idVehiculeHealth'],
                            'idHealthType'     => $revision,
                        ));
                    break;
                }
            }

            if($vehicule['assuranceExpiration'] <> Null)
            {
                $query = $db->prepare('
                    INSERT INTO
                        VEHICULES_HEALTH
                    SET
                        idVehicule = :idVehicule,
                        dateHealth = :dateHealth
                    ;');
                $query->execute(array(
                    'idVehicule' => $vehicule['idVehicule'],
                    'dateHealth' => date('Y-m-d', strtotime($vehicule['assuranceExpiration'].' -1 year')),
                ));
                switch($query->errorCode())
                {
                    case '00000':
                        $health = $db->query('SELECT idVehiculeHealth FROM VEHICULES_HEALTH ORDER BY idVehiculeHealth DESC;');
                        $health = $health->fetch();
                        $insert = $db->prepare('
                            INSERT INTO
                                VEHICULES_HEALTH_CHECKS
                            SET
                                idVehiculeHealth = :idVehiculeHealth,
                                idHealthType     = :idHealthType,
                                remarquesCheck   = "Ajouté par le script de version"
                            ;');
                        $insert->execute(array(
                            'idVehiculeHealth' => $health['idVehiculeHealth'],
                            'idHealthType'     => $assurance,
                        ));
                    break;
                }
            }
        }
        checkAllMaintenance();
        $query = $db->query(file_get_contents ("update13.3.2.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 13.3", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '13.3':
        writeInLogs("Début de l'installation de la mise à jour 13.4", '1', NULL);
        $query = $db->query(file_get_contents ("update13.4.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 13.4", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '13.4':
        writeInLogs("Début de l'installation de la mise à jour 13.5", '1', NULL);
        $query = $db->query(file_get_contents ("update13.5.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 13.5", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '13.5':
        writeInLogs("Début de l'installation de la mise à jour 13.6", '1', NULL);
        $query = $db->query(file_get_contents ("update13.6.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 13.6", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '13.6':
        writeInLogs("Début de l'installation de la mise à jour 14.0", '1', NULL);
        $query = $db->query(file_get_contents ("update14.0.sql"));
        writeInLogs("Fin de l'installation de la mise à jour 14.0", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '14.0':
        writeInLogs("Fin des mises à jour", '1', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALLFINISH.php');</script>";
        break;

    default:
        writeInLogs("Erreur dans le module de mises à jour", '3', NULL);
        echo "<script type='text/javascript'>document.location.replace('INSTALLERROR.php');</script>";
        break;
}

?>