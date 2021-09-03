<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['lots_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    
    $query = $db->prepare('
        INSERT INTO
            INVENTAIRES
        SET
            idLot                  = :idLot,
            dateInventaire         = :dateInventaire,
            idPersonne             = :idPersonne,
            commentairesInventaire = :commentairesInventaire
        ;');
    $query->execute(array(
        'idLot'                  => $_GET['id'],
        'dateInventaire'         => $_POST['dateInventaire'],
        'idPersonne'             => $_SESSION['idPersonne'],
        'commentairesInventaire' => $_POST['commentairesInventaire']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'inventaire pour le lot ".$_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Inventaire ajouté avec succès.';
            $_SESSION['returnType'] = '1';

            if ($_POST['boolDernier'] != Null)
            {
                $query = $db->prepare('UPDATE LOTS_LOTS SET dateDernierInventaire = :dateDernierInventaire WHERE idLot = :idLot;');
                $query->execute(array(
                    'idLot' => $_GET['id'],
                    'dateDernierInventaire' => $_POST['dateInventaire']
                ));
            }

            $query = $db->prepare('SELECT MAX(idInventaire) as idInventaire FROM INVENTAIRES WHERE idLot = :idLot;');
            $query->execute(array(
                'idLot' => $_GET['id']
            ));
            $data = $query->fetch();
            $idInventaire = $data['idInventaire'];

            foreach ($_POST['formArray'] as $idLot => $sacs) {
                foreach ($sacs as $idSac => $emplacements) {
                    foreach ($emplacements as $idEmplacement => $materiel) {
                        foreach ($materiel as $idElement => $matos){
                            if ($matos['per'] != Null)
                            {
                                $query = $db->prepare('UPDATE MATERIEL_ELEMENT SET peremption = :peremption, quantite = :quantite WHERE idElement = :idElement;');
                                $query->execute(array(
                                    'peremption' => $matos['per'],
                                    'quantite' => $matos['qtt'],
                                    'idElement' => $idElement
                                ));
                            }
                            else
                            {
                                $query = $db->prepare('UPDATE MATERIEL_ELEMENT SET quantite = :quantite WHERE idElement = :idElement;');
                                $query->execute(array(
                                    'quantite' => $matos['qtt'],
                                    'idElement' => $idElement
                                ));
                            }
                        }
                    }
                }
            }

            figeInventaireLot($_GET['id'], $idInventaire);

        break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'inventaire ".$_GET['id'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout de l'inventaire.";
            $_SESSION['returnType'] = '2';
    }

	checkOneConf($_GET['id']);

	$lock = $db->prepare('UPDATE LOTS_LOTS SET inventaireEnCours = Null WHERE idLot = :idLot;');
    $lock->execute(array(
        'idLot' => $_GET['id']
    ));

    echo "<script type='text/javascript'>document.location.replace('lotsContenu.php?id=".$_GET['id']."');</script>";
}
?>