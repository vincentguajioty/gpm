<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if ($_SESSION['vhf_canal_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $_POST['idTypeDocument'] = ($_POST['idTypeDocument'] == Null) ? Null : $_POST['idTypeDocument'];

    $ext = strtolower(substr(strrchr($_FILES['urlFichierDocCanalVHF']['name'], '.'), 1));

    $query = $db->prepare('INSERT INTO DOCUMENTS_CANAL_VHF(idVhfCanal, nomDocCanalVHF, formatDocCanalVHF, dateDocCanalVHF, urlFichierDocCanalVHF, idTypeDocument) VALUES(:idVhfCanal, :nomDocCanalVHF, :formatDocCanalVHF, CURRENT_TIMESTAMP, :urlFichierDocCanalVHF, :idTypeDocument);');
    $query->execute(array(
        'idVhfCanal' => $_GET['idVhfCanal'],
        'urlFichierDocCanalVHF' => Null,
        'nomDocCanalVHF' => $_POST['nomDocCanalVHF'],
        'formatDocCanalVHF' => $ext,
        'idTypeDocument' => $_POST['idTypeDocument']
    ));


    $query = $db->query('SELECT MAX(idDocCanalVHF) as idDocCanalVHF FROM DOCUMENTS_CANAL_VHF;');
    $data = $query->fetch();

    $nom = "documents/vhfCanaux/{$data['idDocCanalVHF']}.{$ext}";

    $resultat = move_uploaded_file($_FILES['urlFichierDocCanalVHF']['tmp_name'], $nom);
    if ($resultat) {
        $query = $db->prepare('UPDATE DOCUMENTS_CANAL_VHF SET urlFichierDocCanalVHF = :urlFichierDocCanalVHF WHERE idDocCanalVHF = :idDocCanalVHF;');
        $query->execute(array(
            'idDocCanalVHF' => $data['idDocCanalVHF'],
            'urlFichierDocCanalVHF' => $nom
        ));
        writeInLogs("Ajout d'une pièce jointe référence " . $_POST['nomDocCanalVHF'] . " au canal radio " . $_GET['idVhfCanal'], '2');
    } else {
        $query = $db->prepare('DELETE FROM DOCUMENTS_CANAL_VHF WHERE idDocCanalVHF = :idDocCanalVHF;');
        $query->execute(array(
            'idDocCanalVHF' => $data['idDocCanalVHF']
        ));
        writeInLogs("Erreur inconnue lors de l'ajout d'une pièce jointe au canal radio " . $_GET['idVhfCanal'], '5');
        $_SESSION['returnMessage'] = "Erreur inconnue lors du chargement du document.";
        $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>