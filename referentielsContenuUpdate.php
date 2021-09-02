<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['typesLots_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $delete = $db->prepare('DELETE FROM REFERENTIELS WHERE idTypeLot = :idTypeLot');
    $delete->execute(array('idTypeLot'=>$_GET['id']));

    switch($delete->errorCode())
    {
        case '00000':
            writeInLogs("Nettoyage du référentiel ".$_GET['id']." effectué avec succès", '1', NULL);
            foreach ($_POST['formArray'] as $materiel => $content) {
                if($content['qtt'] > 0)
                {
                    $insert = $db->prepare('
                        INSERT INTO
                            REFERENTIELS
                        SET
                            idMaterielCatalogue     = :idMaterielCatalogue,
                            idTypeLot               = :idTypeLot,
                            quantiteReferentiel     = :quantiteReferentiel,
                            obligatoire             = :obligatoire,
                            commentairesReferentiel = :commentairesReferentiel
                        ;');
                    $insert->execute(array(
                        'idMaterielCatalogue'     => $materiel,
                        'idTypeLot'               => $_GET['id'],
                        'quantiteReferentiel'     => $content['qtt'],
                        'obligatoire'             => $content['obligatoire'],
                        'commentairesReferentiel' => $content['commentairesReferentiel'],
                    ));
                    switch($insert->errorCode())
                    {
                        case '00000':
                            writeInLogs("Insertion du matériel ".$materiel." en quantité ".$content['qtt']." au référentiel ".$_GET['id'], '1', NULL);
                            break;

                        default:
                            writeInLogs("Erreur inconnue lors de l'insertion du matériel ".$materiel." en quantité ".$content['qtt']." au référentiel ".$_GET['id'], '3', NULL);
                    }
                }
            }
        break;

        default:
            writeInLogs("Erreur inconnue lors du nettoyage du référentiel ".$_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'enregistrement. Aucune modification n'a été apportée.";
            $_SESSION['returnType'] = '2';
    }

    checkAllConf();

    echo "<script type='text/javascript'>document.location.replace('referentielsContenu.php?id=" . $_GET['id'] . "');</script>";
  
}
?>