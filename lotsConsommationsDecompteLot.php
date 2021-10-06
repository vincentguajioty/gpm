<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['consommationLots_affectation']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $conso = $db->prepare('
        SELECT
            *
        FROM
            LOTS_CONSOMMATION_MATERIEL
        WHERE
            idConsommationMateriel = :idConsommationMateriel
    ');
    $conso->execute(array('idConsommationMateriel' => $_GET['id']));
    $conso = $conso->fetch();

    $reserve = $db->prepare('
        UPDATE
            MATERIEL_ELEMENT e
            LEFT OUTER JOIN MATERIEL_EMPLACEMENT em ON e.idEmplacement = em.idEmplacement
            LEFT OUTER JOIN MATERIEL_SAC s ON em.idSac = s.idSac
        SET
            e.quantite = e.quantite - :quantiteConsommation
        WHERE
            s.idLot = :idLot
            AND idMaterielCatalogue = :idMaterielCatalogue
    ');
    $reserve->execute(array(
        'quantiteConsommation' => $conso['quantiteConsommation'],
        'idLot'                => $conso['idLot'],
        'idMaterielCatalogue'  => $conso['idMaterielCatalogue'],
    ));
    switch($reserve->errorCode())
    {
        case '00000':
            writeInLogs("Décompte lot pour la consommation " . $_GET['id'], '1', NULL);

            $consoUpdate = $db->prepare('
                UPDATE
                    LOTS_CONSOMMATION_MATERIEL
                SET
                    traiteOperateur = true
                WHERE
                    idConsommationMateriel = :idConsommationMateriel
            ');
            $consoUpdate->execute(array('idConsommationMateriel' => $_GET['id']));

            break;

        default:
            writeInLogs("Erreur lors du décompte lot pour la consommation " . $_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur lors la mise à jour de la lot. Assurez vous que ce matériel y est bien inventorié.";
            $_SESSION['returnType'] = '2';
    }

    checkOneConf($conso['idLot']);

    echo "<script>window.location = document.referrer;</script>";
}
?>