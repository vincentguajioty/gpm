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

    $query = $db->prepare('INSERT INTO INVENTAIRES(idLot, dateInventaire, idPersonne, commentairesInventaire) VALUES(:idLot, :dateInventaire, :idPersonne, :commentairesInventaire);');
    $query->execute(array(
        'idLot' => $_GET['id'],
        'dateInventaire' => $_POST['dateInventaire'],
        'idPersonne' => $_POST['identifiant'],
        'commentairesInventaire' => $_POST['commentairesInventaire']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'inventaire pour le lot", '2');
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

            foreach ($_POST['formArray'] as $idLot => $sacs) {
                foreach ($sacs as $idSac => $emplacements) {
                    foreach ($emplacements as $idEmplacement => $materiel) {
                        foreach ($materiel as $idElement => $matos){
                            if ($matos['per'] != Null)
                            {
                                $query = $db->prepare('UPDATE MATERIEL_ELEMENT SET peremption = :peremption, peremptionNotification = :peremptionNotification, quantite = :quantite WHERE idElement = :idElement;');
                                $query->execute(array(
                                    'peremption' => $matos['per'],
                                    'peremptionNotification' => $matos['perNot'],
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

                            $query2 = $db->prepare('SELECT idMaterielCatalogue FROM MATERIEL_ELEMENT WHERE idElement = :idElement;');
                            $query2->execute(array(
                                'idElement' => $idElement
                            ));
                            $data2 = $query2->fetch();

                            if (!isset($invArray[$data2['idMaterielCatalogue']]))
                            {
                                if ($matos['per'] != Null)
                                {
                                    $invArray[$data2['idMaterielCatalogue']] = [
                                        'qtt' => $matos['qtt'],
                                        'per' => $matos['per']
                                    ];
                                }
                                else
                                {
                                    $invArray[$data2['idMaterielCatalogue']] = [
                                        'qtt' => $matos['qtt'],
                                        'per' => Null
                                    ];
                                }
                            }
                            else
                            {
                                $invArray[$data2['idMaterielCatalogue']]['qtt'] += $matos['qtt'];

                                if ($matos['per'] != Null)
                                {
                                    if ($invArray[$data2['idMaterielCatalogue']]['per'] != Null)
                                    {
                                        if ($invArray[$data2['idMaterielCatalogue']]['per'] > $matos['per'])
                                        {
                                            $invArray[$data2['idMaterielCatalogue']]['per'] = $matos['per'];
                                        }
                                    }
                                    else
                                    {
                                        $invArray[$data2['idMaterielCatalogue']]['per'] = $matos['per'];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach ($invArray as $idMaterielCatalogue => $elements)
            {
                $query = $db->prepare('INSERT INTO INVENTAIRES_CONTENUS(idInventaire, idMaterielCatalogue, quantiteInventaire, peremptionInventaire) VALUES(:idInventaire, :idMaterielCatalogue, :quantiteInventaire, :peremptionInventaire);');
                $query->execute(array(
                    'idInventaire' => $data['idInventaire'],
                    'idMaterielCatalogue' => $idMaterielCatalogue,
                    'quantiteInventaire' => $elements['qtt'],
                    'peremptionInventaire' => $elements['per']
                ));
            }

        break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'inventaire", '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout de l'inventaire.";
            $_SESSION['returnType'] = '2';
    }

	checkOneConf($_GET['id']);

    echo "<script>javascript:history.go(-2);</script>";
}
?>