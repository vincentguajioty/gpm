<?php
session_start();

function createDB()
{
    //---------------------- BASE DE DONNEES ----------------------
    $SERVEURDB = 'val1'; //adresse IP du serveur entre simple cote, ex: $SERVEUR = '192.169.1.5';
    $DBNAME = 'val2'; //nom de la base de données, ex: $DB = 'GPM';
    $CHARSET = 'utf8'; //type d'interclassement, utf8 étant recommandé, ex: $CHARSET = 'utf8';
    $USER = 'val3'; //nom d'utilisateur d'accès à la base de données, ex: $USER = 'utilisateur';
    $PASSWORD = 'val4'; //mot de passe d'accès à la base de données, ex: $PASSWORD = 'motDePasse';
    //-------------------- FIN BASE DE DONNEES ---------------------

    try {
        $db = new PDO('mysql:host=' . $SERVEURDB . ';dbname=' . $DBNAME . ';charset=' . $CHARSET, $USER, $PASSWORD);
        $query = $db->prepare('SELECT COUNT(*) as nb FROM information_schema.tables WHERE table_schema = :table_schema;');
        $query->execute(array(
            'table_schema' => $DBNAME));
        $data = $query->fetch();
        if ($data['nb'] == 0 AND (strpos($_SERVER['HTTP_REFERER'], "INSTALLFROMSCRATCH.php") == false)) {
            echo "<script type='text/javascript'>document.location.replace('distmaj/INSTALLFROMSCRATCH.php');</script>";
        }

    } catch (PDOException $ex) {
        echo "<script type='text/javascript'>document.location.replace('bdderror.php');</script>";
    }

    return $db;

}

$db = createDB();

require_once('fonctions.php');

?>