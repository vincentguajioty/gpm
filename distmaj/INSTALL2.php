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
        echo "<script type='text/javascript'>document.location.replace('INSTALLFINISH.php');</script>";
        break;

    default:
        echo "<script type='text/javascript'>document.location.replace('INSTALLERROR.php');</script>";
        break;
}

?>