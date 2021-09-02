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
        'dateInventaire'         => $_POST['dateInventaire'],
        'idPersonne'             => $_POST['identifiant'],
        'commentairesInventaire' => $_POST['commentairesInventaire']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'inventaire pour le conteneur ".$_GET['id'], '1', NULL);
            $_SESSION['returnMessage'] = 'Inventaire ajouté avec succès.';
            $_SESSION['returnType'] = '1';

            if ($_POST['boolDernier'] != Null)
            {
                $query = $db->prepare('UPDATE RESERVES_CONTENEUR SET dateDernierInventaire = :dateDernierInventaire WHERE idConteneur = :idConteneur;');
                $query->execute(array(
                    'idConteneur' => $_GET['id'],
                    'dateDernierInventaire' => $_POST['dateInventaire']
                ));
            }

            $query = $db->prepare('SELECT MAX(idReserveInventaire) as idReserveInventaire FROM RESERVES_INVENTAIRES WHERE idConteneur = :idConteneur;');
            $query->execute(array(
                'idConteneur' => $_GET['id']
            ));
            $data = $query->fetch();

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

					$query2 = $db->prepare('SELECT idMaterielCatalogue FROM RESERVES_MATERIEL WHERE idReserveElement = :idReserveElement;');
					$query2->execute(array(
						'idReserveElement' => $idReserveElement
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

            foreach ($invArray as $idMaterielCatalogue => $elements)
            {
                $query = $db->prepare('
                	INSERT INTO
                		RESERVES_INVENTAIRES_CONTENUS
                	SET
                		idReserveInventaire  = :idReserveInventaire,
                		idMaterielCatalogue  = :idMaterielCatalogue,
                		quantiteInventaire   = :quantiteInventaire,
                		peremptionInventaire = :peremptionInventaire
                	;');
                $query->execute(array(
                    'idReserveInventaire'  => $data['idReserveInventaire'],
                    'idMaterielCatalogue'  => $idMaterielCatalogue,
                    'quantiteInventaire'   => $elements['qtt'],
                    'peremptionInventaire' => $elements['per']
                ));
            }

        break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'inventaire", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout de l'inventaire.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script type='text/javascript'>document.location.replace('reserveConteneurContenu.php?id=".$_GET['id']."');</script>";
}
?>