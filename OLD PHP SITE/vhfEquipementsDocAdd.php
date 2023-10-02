<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_equipement_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $_POST['idTypeDocument'] = ($_POST['idTypeDocument'] == Null) ? Null : $_POST['idTypeDocument'];

    $ext = strtolower(substr(strrchr($_FILES['urlFichierDocVHF']['name'], '.'), 1));

    $query = $db->prepare('INSERT INTO DOCUMENTS_VHF(idVhfEquipement, nomDocVHF, formatDocVHF, dateDocVHF, urlFichierDocVHF, idTypeDocument) VALUES(:idVhfEquipement, :nomDocVHF, :formatDocVHF, CURRENT_TIMESTAMP, :urlFichierDocVHF, :idTypeDocument);');
    $query->execute(array(
        'idVhfEquipement' => $_GET['idVhfEquipement'],
        'urlFichierDocVHF' => Null,
        'nomDocVHF' => $_POST['nomDocVHF'],
        'formatDocVHF' => $ext,
        'idTypeDocument' => $_POST['idTypeDocument']
    ));


    $query = $db->query('SELECT MAX(idDocVHF) as idDocVHF FROM DOCUMENTS_VHF;');
    $data = $query->fetch();

    $nom = "documents/vhfEquipements/{$data['idDocVHF']}.{$ext}";

    $resultat = move_uploaded_file($_FILES['urlFichierDocVHF']['tmp_name'], $nom);
    if ($resultat) {
        $query = $db->prepare('UPDATE DOCUMENTS_VHF SET urlFichierDocVHF = :urlFichierDocVHF WHERE idDocVHF = :idDocVHF;');
        $query->execute(array(
            'idDocVHF' => $data['idDocVHF'],
            'urlFichierDocVHF' => $nom
        ));
        writeInLogs("Ajout d'une pièce jointe référence " . $_POST['nomDocVHF'] . " à la radio " . $_GET['idVhfEquipement'], '1', NULL);
    } else {
        $query = $db->prepare('DELETE FROM DOCUMENTS_VHF WHERE idDocVHF = :idDocVHF;');
        $query->execute(array(
            'idDocVHF' => $data['idDocVHF']
        ));
        writeInLogs("Erreur inconnue lors de l'ajout d'une pièce jointe à la radio " . $_GET['idVhfEquipement'], '3', NULL);
        $_SESSION['returnMessage'] = "Erreur inconnue lors du chargement du document.";
        $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>