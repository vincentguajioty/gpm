<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vehicules_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $_POST['idTypeDocument'] = ($_POST['idTypeDocument'] == Null) ? Null : $_POST['idTypeDocument'];

    $ext = strtolower(substr(strrchr($_FILES['urlFichierDocVehicule']['name'], '.'), 1));

    $query = $db->prepare('INSERT INTO DOCUMENTS_VEHICULES(idVehicule, nomDocVehicule, formatDocVehicule, dateDocVehicule, urlFichierDocVehicule, idTypeDocument) VALUES(:idVehicule, :nomDocVehicule, :formatDocVehicule, CURRENT_TIMESTAMP, :urlFichierDocVehicule, :idTypeDocument);');
    $query->execute(array(
        'idVehicule' => $_GET['idVehicule'],
        'urlFichierDocVehicule' => Null,
        'nomDocVehicule' => $_POST['nomDocVehicule'],
        'formatDocVehicule' => $ext,
        'idTypeDocument' => $_POST['idTypeDocument']
    ));


    $query = $db->query('SELECT MAX(idDocVehicules) as idDocVehicules FROM DOCUMENTS_VEHICULES;');
    $data = $query->fetch();

    $nom = "documents/vehicules/{$data['idDocVehicules']}.{$ext}";

    $resultat = move_uploaded_file($_FILES['urlFichierDocVehicule']['tmp_name'], $nom);
    if ($resultat) {
        $query = $db->prepare('UPDATE DOCUMENTS_VEHICULES SET urlFichierDocVehicule = :urlFichierDocVehicule WHERE idDocVehicules = :idDocVehicules;');
        $query->execute(array(
            'idDocVehicules' => $data['idDocVehicules'],
            'urlFichierDocVehicule' => $nom
        ));
        writeInLogs("Ajout d'une pièce jointe référence " . $_POST['nomDocVehicule'] . " au véhicule " . $_GET['idVehicule'], '2');
    } else {
        $query = $db->prepare('DELETE FROM DOCUMENTS_VEHICULES WHERE idDocVehicules = :idDocVehicules;');
        $query->execute(array(
            'idDocVehicules' => $data['idDocVehicules']
        ));
        writeInLogs("Erreur inconnue lors de l'ajout d'une pièce jointe au véhicule " . $_GET['idVehicule'], '5');
        $_SESSION['returnMessage'] = "Erreur inconnue lors du chargement du document.";
        $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>