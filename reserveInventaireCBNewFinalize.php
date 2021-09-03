<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['reserve_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query = $db->prepare('
        INSERT INTO
            RESERVES_INVENTAIRES
        SET
            idConteneur            = :idConteneur,
            dateInventaire         = :dateInventaire,
            idPersonne             = :idPersonne,
            commentairesInventaire = :commentairesInventaire
        ;');
    $query->execute(array(
        'idConteneur'            => $_GET['id'],
        'dateInventaire'         => date('Y-m-d'),
        'idPersonne'             => $_SESSION['idPersonne'],
        'commentairesInventaire' => $_POST['commentairesInventaire']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'inventaire pour le conteneur ".$_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Inventaire ajouté avec succès.';
            $_SESSION['returnType'] = '1';

            $query = $db->prepare('UPDATE RESERVES_CONTENEUR SET dateDernierInventaire = :dateDernierInventaire WHERE idConteneur = :idConteneur;');
            $query->execute(array(
                'idConteneur' => $_GET['id'],
                'dateDernierInventaire' => date('Y-m-d')
            ));

            $query = $db->prepare('SELECT MAX(idReserveInventaire) as idReserveInventaire FROM RESERVES_INVENTAIRES WHERE idConteneur = :idConteneur;');
            $query->execute(array(
                'idConteneur' => $_GET['id']
            ));
            $data = $query->fetch();
            $idReserveInventaire = $data['idReserveInventaire'];

            if(isset($_POST['formArray']))
            {
                foreach ($_POST['formArray'] as $idConteneur => $materiel) {
                    foreach ($materiel as $idReserveElement => $matos){
                        if ($matos['per'] != Null)
                        {
                            $query = $db->prepare('
                                UPDATE
                                    RESERVES_MATERIEL
                                SET
                                    peremptionReserve = :peremptionReserve,
                                    quantiteReserve   = :quantiteReserve
                                WHERE
                                    idReserveElement  = :idReserveElement
                                ;');
                            $query->execute(array(
                                'peremptionReserve' => $matos['per'],
                                'quantiteReserve'   => $matos['qtt'],
                                'idReserveElement'  => $idReserveElement
                            ));
                        }
                        else
                        {
                            $query = $db->prepare('UPDATE RESERVES_MATERIEL SET quantiteReserve = :quantiteReserve WHERE idReserveElement = :idReserveElement;');
                            $query->execute(array(
                                'quantiteReserve' => $matos['qtt'],
                                'idReserveElement' => $idReserveElement
                            ));
                        }
                    }
                }
            }

            $scansSuccess = $db->prepare('
                SELECT
                    *
                FROM 
                    VIEW_SCAN_RESULTS_RESERVES
                WHERE
                    idConteneur = :idConteneur
                    AND
                    idMaterielCatalogue IS NOT NULL
                ;
            ');
            $scansSuccess->execute(array(
                'idConteneur' => $_GET['id'],
            ));
            while($element = $scansSuccess->fetch())
            {
                $update = $db->prepare('
                    UPDATE
                        RESERVES_MATERIEL
                    SET
                        peremptionReserve = :peremptionReserve,
                        quantiteReserve = :quantiteReserve
                    WHERE
                        idConteneur = :idConteneur
                        AND
                        idMaterielCatalogue = :idMaterielCatalogue
                ;');
                $update->execute(array(
                    'peremptionReserve'   => $element['peremption'],
                    'quantiteReserve'     => $element['quantite'],
                    'idConteneur'         => $element['idConteneur'],
                    'idMaterielCatalogue' => $element['idMaterielCatalogue'],
                ));
            }

            $clean = $db->prepare('DELETE FROM RESERVES_INVENTAIRES_TEMP WHERE idConteneur = :idConteneur;');
            $clean->execute(array(
                'idConteneur' => $_GET['id']
            ));

            figeInventaireReserve($_GET['id'], $idReserveInventaire);

        break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'inventaire pour le conteneur ".$_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout de l'inventaire.";
            $_SESSION['returnType'] = '2';
    }


    $lock = $db->prepare('UPDATE RESERVES_CONTENEUR SET inventaireEnCours = Null WHERE idConteneur = :idConteneur;');
    $lock->execute(array(
        'idConteneur' => $_GET['id']
    ));

    echo "<script type='text/javascript'>document.location.replace('reserveConteneurContenu.php?id=".$_GET['id']."');</script>";
}
?>