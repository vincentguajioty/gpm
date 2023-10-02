<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

if (centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['idCentreDeCout'])==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    exit;
}
else {

    $_POST['idTypeDocument'] = ($_POST['idTypeDocument'] == Null) ? Null : $_POST['idTypeDocument'];

    $ext = strtolower(substr(strrchr($_FILES['urlFichierDocCouts']['name'], '.'), 1));

    $query = $db->prepare('
        INSERT INTO
            DOCUMENTS_CENTRE_COUTS
        SET
            idCentreDeCout     = :idCentreDeCout,
            nomDocCouts        = :nomDocCouts,
            formatDocCouts     = :formatDocCouts,
            dateDocCouts       = CURRENT_TIMESTAMP,
            urlFichierDocCouts = :urlFichierDocCouts,
            idTypeDocument     = :idTypeDocument
        ;');
    $query->execute(array(
        'idCentreDeCout'     => $_GET['idCentreDeCout'],
        'urlFichierDocCouts' => Null,
        'nomDocCouts'        => $_POST['nomDocCouts'],
        'formatDocCouts'     => $ext,
        'idTypeDocument'     => $_POST['idTypeDocument']
    ));


    $query = $db->query('SELECT MAX(idDocCouts) as idDocCouts FROM DOCUMENTS_CENTRE_COUTS;');
    $data = $query->fetch();

    $nom = "documents/centresCouts/{$data['idDocCouts']}.{$ext}";

    $resultat = move_uploaded_file($_FILES['urlFichierDocCouts']['tmp_name'], $nom);
    if ($resultat) {
        $query = $db->prepare('UPDATE DOCUMENTS_CENTRE_COUTS SET urlFichierDocCouts = :urlFichierDocCouts WHERE idDocCouts = :idDocCouts;');
        $query->execute(array(
            'idDocCouts' => $data['idDocCouts'],
            'urlFichierDocCouts' => $nom
        ));
        writeInLogs("Ajout d'une pièce jointe référence " . $_POST['nomDocCouts'] . " au centre de cout " . $_GET['idCentreDeCout'], '1', NULL);
    } else {
        $query = $db->prepare('DELETE FROM DOCUMENTS_CENTRE_COUTS WHERE idDocCouts = :idDocCouts;');
        $query->execute(array(
            'idDocCouts' => $data['idDocCouts']
        ));
        writeInLogs("Erreur inconnue lors de l'ajout d'une pièce jointe au centre de couts " . $_GET['idCentreDeCout'], '3', NULL);
        $_SESSION['returnMessage'] = "Erreur inconnue lors du chargement du document.";
        $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>