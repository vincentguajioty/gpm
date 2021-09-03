<?php

session_start();
if ($_SESSION['identifiant'] == Null)
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
require_once 'config/bdd.php';
require_once 'config/config.php';
require_once 'plugins/authenticator/authenticator.php';

$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS a ON p.idPersonne = a.idPersonne WHERE p.identifiant= :identifiant;');
$query->execute(array(
    'identifiant' => $_SESSION['identifiant']
));
$data = $query->fetch();

$Authenticator = new Authenticator();
$checkResult = $Authenticator->verifyCode($data['doubleAuthSecret'], $_POST['authenticator'], 1);

if (!$checkResult)
{
    writeInLogs("Authentification MFA rejetée", '2', NULL);

    $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
    $query->execute(array(
        'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - '.$VERROUILLAGE_IP_TEMPS.' days'))
    ));
    
    $query = $db->prepare('SELECT COUNT(*) as nb FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
    $query->execute(array(
        'adresseIP' => $_SERVER['REMOTE_ADDR']
    ));
    $data = $query->fetch();
    
    if ($data['nb'] > $VERROUILLAGE_IP_OCCURANCES-2)
    {
        $query = $db->prepare('INSERT INTO VERROUILLAGE_IP(adresseIPverr, dateVerr, commentaire)VALUES(:adresseIPverr, :dateVerr, :commentaire);');
        $query->execute(array(
            'dateVerr' => date('Y-m-d H:i:s'),
            'adresseIPverr' => $_SERVER['REMOTE_ADDR'],
            'commentaire' => 'Erreur d\'authentification MFA pour ' . $_SESSION['identifiant'],
        ));
        
        writeInLogs("Verouillage définitif de l\'adresse IP suite à la tentative d'authentification avec ".$_SESSION['identifiant'], '2', NULL);

        $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
        $query->execute(array(
            'adresseIP' => $_SERVER['REMOTE_ADDR']
        ));
    }
    else
    {
        $query = $db->prepare('INSERT INTO VERROUILLAGE_IP_TEMP(adresseIP, dateEchec, commentaire)VALUES(:adresseIP, :dateEchec, :commentaire);');
        $query->execute(array(
            'dateEchec' => date('Y-m-d H:i:s'),
            'adresseIP' => $_SERVER['REMOTE_ADDR'],
            'commentaire' => 'Erreur d\'authentification MFA pour ' . $_SESSION['identifiant'],
        ));

        writeInLogs("Verouillage temporaire de l\'adresse IP suite à la tentative d'authentification avec ".$_SESSION['identifiant'], '2', NULL);
    }

    echo "<script type='text/javascript'>document.location.replace('loginMFA.php');</script>";
}
else
{
    writeInLogs("Connexion réussie et contrôle MFA correct", '1', NULL);
    $_SESSION['connexion_connexion'] = $data['connexion_connexion'];
    echo "<script type='text/javascript'>document.location.replace('index.php');</script>";
}
?>