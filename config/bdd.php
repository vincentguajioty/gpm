<?php
session_start();

//---------------------- BASE DE DONNEES ---------------------- 
$SERVEURDB = 'x.x.x.x:xxxx'; //adresse IP du serveur entre simple cote, ex: $SERVEUR = '192.169.1.5';
$DBNAME = 'GPM'; //nom de la base de données, ex: $DB = 'GPM';
$CHARSET = 'utf8'; //type d'interclassement, utf8 étant recommandé, ex: $CHARSET = 'utf8';
$USER = 'xxxx'; //nom d'utilisateur d'accès à la base de données, ex: $USER = 'utilisateur';
$PASSWORD = 'xxxx'; //mot de passe d'accès à la base de données, ex: $PASSWORD = 'motDePasse';
//-------------------- FIN BASE DE DONNEES ---------------------

try{
    $db = new PDO('mysql:host=' . $SERVEURDB . ';dbname=' . $DBNAME . ';charset=' . $CHARSET, $USER , $PASSWORD);
    $query = $db->prepare('SELECT COUNT(*) as nb FROM information_schema.tables WHERE table_schema = :table_schema;');
    $query->execute(array(
        'table_schema' => $DBNAME));
    $data = $query->fetch();
    if ($data['nb']==0)
    {
        echo "<script type='text/javascript'>document.location.replace('distmaj/INSTALLFROMSCRATCH.php');</script>";
    }

}
catch(PDOException $ex){
    echo "<script type='text/javascript'>document.location.replace('bdderror.php');</script>";
}

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

?>