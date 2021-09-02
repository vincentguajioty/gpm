<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['lots_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	$OKsacs = 0;
	$NOKsacs = 0;
	$OKemplacements = 0;
	$NOKemplacements = 0;
	$OKmateriels = 0;
	$NOKmateriels = 0;

    $query = $db->prepare('SELECT * FROM LOTS_LOTS WHERE idLot = :idLot;');
    $query->execute(array('idLot' => $_POST['idLot']));
    $lotSource = $query->fetch();

    $query = $db->prepare('
        INSERT INTO
            LOTS_LOTS
        SET
            libelleLot            = :libelleLot,
            idTypeLot             = :idTypeLot,
            idEtat                = :idEtat,
            idLieu                = :idLieu,
            idPersonne            = :idPersonne,
            dateDernierInventaire = :dateDernierInventaire,
            frequenceInventaire   = :frequenceInventaire,
            commentairesLots      = :commentairesLots,
            idVehicule            = :idVehicule,
            idLotsEtat            = :idLotsEtat
        ;');
    $query->execute(array(
        'libelleLot'            => $lotSource['libelleLot'].' - Copie',
        'idTypeLot'             => $lotSource['idTypeLot'],
        'idEtat'                => $lotSource['idEtat'],
        'idLieu'                => $lotSource['idLieu'],
        'idPersonne'            => $lotSource['idPersonne'],
        'dateDernierInventaire' => $lotSource['dateDernierInventaire'],
        'frequenceInventaire'   => $lotSource['frequenceInventaire'],
        'commentairesLots'      => $lotSource['commentairesLots'],
        'idVehicule'            => $lotSource['idVehicule'],
        'idLotsEtat'            => $lotSource['idLotsEtat']
    ));

    switch($query->errorCode())
    {
        case '00000':            
            $query = $db->query('SELECT MAX(idLot) as idLot FROM LOTS_LOTS;');
            $idLotDuplicate = $query->fetch();
            $idLotDuplicate = $idLotDuplicate['idLot'];

            $sacs = $db->prepare('SELECT * FROM MATERIEL_SAC WHERE idLot = :idLot;');
            $sacs->execute(array('idLot' => $lotSource['idLot']));
            while($sac = $sacs->fetch())
            {
                $query = $db->prepare('INSERT INTO MATERIEL_SAC SET
                    libelleSac    = :libelleSac,
                    idLot         = :idLot,
                    taille        = :taille,
                    couleur       = :couleur,
                    idFournisseur = :idFournisseur
                    ;');
                $query->execute(array(
                    'libelleSac'    => $sac['libelleSac'],
                    'idLot'         => $idLotDuplicate,
                    'taille'        => $sac['taille'],
                    'couleur'       => $sac['couleur'],
                    'idFournisseur' => $sac['idFournisseur']
                ));

                switch($query->errorCode())
                {
                    case '00000':
                        $OKsacs = $OKsacs + 1;

                        $query = $db->query('SELECT MAX(idSac) as idSac FROM MATERIEL_SAC;');
			            $idSacDuplicate = $query->fetch();
			            $idSacDuplicate = $idSacDuplicate['idSac'];

			            $emplacements = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT WHERE idSac = :idSac;');
			            $emplacements->execute(array('idSac' => $sac['idSac']));
			            while($emplacement = $emplacements->fetch())
			            {
			            	$query = $db->prepare('INSERT INTO MATERIEL_EMPLACEMENT SET
			            		libelleEmplacement = :libelleEmplacement,
			            		idSac = :idSac;');
						    $query->execute(array(
						        'libelleEmplacement' => $emplacement['libelleEmplacement'],
						        'idSac' => $idSacDuplicate
						    ));


						    switch($query->errorCode())
						    {
						        case '00000':
						            $OKemplacements = $OKemplacements + 1;
									
									$query = $db->query('SELECT MAX(idEmplacement) as idEmplacement FROM MATERIEL_EMPLACEMENT;');
									$idEmplacementDuplicate = $query->fetch();
									$idEmplacementDuplicate = $idEmplacementDuplicate['idEmplacement'];

									$materiels = $db->prepare('SELECT * FROM MATERIEL_ELEMENT WHERE idEmplacement = :idEmplacement;');
									$materiels->execute(array('idEmplacement' => $emplacement['idEmplacement']));
									while($materiel = $materiels->fetch())
									{

										$query = $db->prepare('
                                            INSERT INTO
                                                MATERIEL_ELEMENT
                                            SET 
                                                idMaterielCatalogue    = :idMaterielCatalogue,
                                                idEmplacement          = :idEmplacement,
                                                idFournisseur          = :idFournisseur,
                                                quantite               = :quantite,
                                                quantiteAlerte         = :quantiteAlerte,
                                                peremption             = :peremption,
                                                peremptionNotification = :peremptionNotification,
                                                commentairesElement    = :commentairesElement,
                                                idMaterielsEtat        = :idMaterielsEtat
                                            ;');
									    $query->execute(array(
                                            'idMaterielCatalogue'    => $materiel['idMaterielCatalogue'],
                                            'idEmplacement'          => $idEmplacementDuplicate,
                                            'idFournisseur'          => $materiel['idFournisseur'],
                                            'quantite'               => $materiel['quantite'],
                                            'quantiteAlerte'         => $materiel['quantiteAlerte'],
                                            'peremption'             => $materiel['peremption'],
                                            'peremptionNotification' => $materiel['peremptionNotification'],
                                            'commentairesElement'    => $materiel['commentairesElement'],
                                            'idMaterielsEtat'        => $materiel['idMaterielsEtat']
									    ));

									    switch($query->errorCode())
									    {
									        case '00000':
									            $OKmateriels = $OKmateriels + 1;
									            break;

									        default:
									            $NOKmateriels = $NOKmateriels + 1;
									    }

									}

						            break;

						        default:
						            $NOKemplacements = $NOKemplacements + 1;
						    }

			            }

                        break;

                    default:
                        $NOKsacs = $NOKsacs + 1;
                }
            }

            writeInLogs("Lot ".$_POST['idLot']." dupliqué avec succès. Duplication en succès de ".$OKsacs." sacs, ".$OKemplacements." emplacements, ".$OKmateriels." matériels. Duplication en erreur de ".$NOKsacs." sacs, ".$NOKemplacements." emplacements, ".$NOKmateriels." matériels.", '1', NULL);
            $_SESSION['returnMessage'] = "1 lot dupliqué avec succès. Duplication en succès de ".$OKsacs." sacs, ".$OKemplacements." emplacements, ".$OKmateriels." matériels. Duplication en erreur de ".$NOKsacs." sacs, ".$NOKemplacements." emplacements, ".$NOKmateriels." matériels.";
            if($NOKsacs + $NOKemplacements + $NOKmateriels == 0){$_SESSION['returnType'] = '1';}else{$_SESSION['returnType'] = '2';}

            break;

        default:
            writeInLogs("Erreur inconnue lors de la duplication du lot " . $_POST['idLot'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la duplication du lot.";
            $_SESSION['returnType'] = '2';
    }

	checkAllConf();
    echo "<script>window.location = document.referrer;</script>";
}
?>