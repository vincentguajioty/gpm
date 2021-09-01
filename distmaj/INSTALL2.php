<?php
require_once '../config/config.php';
require_once '../config/bdd.php';

$query = $db->query('SELECT * FROM CONFIG;');
$data = $query->fetch();

switch($data['version'])
{
    case '2.2':
        $query = $db->query(file_get_contents ("update2.3.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '2.3':
        $query = $db->query(file_get_contents ("update2.4.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '2.4':
        $query = $db->query(file_get_contents ("update2.5.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
		
	case '2.5':
        $query = $db->query(file_get_contents ("update2.6.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '2.6':
        $query = $db->query(file_get_contents ("update3.0.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '3.0':
        $query = $db->query(file_get_contents ("update3.1.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '3.1':
        $query = $db->query(file_get_contents ("update4.0.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '4.0':
        $query = $db->query(file_get_contents ("update4.1.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
	
	case '4.1':
        $query = $db->query(file_get_contents ("update4.2.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
	
	case '4.2':
        $query = $db->query(file_get_contents ("update4.3.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '4.3':
        $query = $db->query(file_get_contents ("update5.0.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.0':
        $query = $db->query(file_get_contents ("update5.1.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.1':
        $query = $db->query(file_get_contents ("update5.2.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.2':
        $query = $db->query(file_get_contents ("update5.3.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
	
	case '5.3':
        $query = $db->query(file_get_contents ("update5.4.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.4':
        $query = $db->query(file_get_contents ("update5.5.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '5.5':
        $query = $db->query(file_get_contents ("update5.6.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
		
    case '5.6':
        $query = $db->query(file_get_contents ("update5.7.sql"));
        $query = $db->query('SELECT idPersonne FROM PERSONNE_REFERENTE;');
        while($data = $query->fetch())
        {
            majIndicateursPersonne($data['idPersonne'],1);
            majNotificationsPersonne($data['idPersonne'],1);
        }
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '5.7':
        $query = $db->query(file_get_contents ("update6.0.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '6.0':
        $query = $db->query(file_get_contents ("update6.1.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '6.1':
        $query = $db->query(file_get_contents ("update6.2.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '6.2':
        $query = $db->query(file_get_contents ("update7.0.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.0':
        $query = $db->query(file_get_contents ("update7.1.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.1':
        $query = $db->query(file_get_contents ("update7.2.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.2':
        $query = $db->query(file_get_contents ("update7.3.sql"));
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;
        
    case '7.3':
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
        echo "<script type='text/javascript'>document.location.replace('INSTALL2.php');</script>";
        break;

    case '7.4':
        echo "<script type='text/javascript'>document.location.replace('INSTALLFINISH.php');</script>";
        break;


    default:
        echo "<script type='text/javascript'>document.location.replace('INSTALLERROR.php');</script>";
        break;
}

?>