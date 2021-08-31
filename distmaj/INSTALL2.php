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
        echo "<script type='text/javascript'>document.location.replace('INSTALLFINISH.php');</script>";
        break;

    default:
        echo "<script type='text/javascript'>document.location.replace('INSTALLERROR.php');</script>";
        break;
}

?>