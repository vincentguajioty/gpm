<?php
require_once 'config/bdd.php';

function checkIP($IP)
{
    global $db;

    $query = $db->prepare('SELECT * FROM VERROUILLAGE_IP WHERE adresseIPverr = :adresseIPverr;');
    $query->execute(array(
        'adresseIPverr' => $IP
    ));
    $data = $query -> fetch();

    if ($data['idIP'] > 0)
    {
        return 1;
    }
    else
    {
        return 0;
    }
}

?>