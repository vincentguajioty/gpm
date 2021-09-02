<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['cautions_lecture']==1 OR $_SESSION['cautions_ajout']==1 OR $_SESSION['cautions_modification']==1 OR $_SESSION['cautions_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM CAUTIONS WHERE idCaution=:idCaution;');
		    $query->execute(array('idCaution' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}

    ?>
    
    <div class="modal fade" id="modalCaution">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'une caution</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'cautionsUpdate.php?id='.$_GET['id'] : 'cautionsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Caution de: </label>
                            <select class="form-control select2" style="width: 100%;" name="idPersonne">
                                <option value="">--- Affectée à un externe ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY nomPersonne, prenomPersonne;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idPersonne']) AND ($data2['idPersonne'] == $data['idPersonne'])) { echo 'selected'; } ?> ><?php echo $data2['nomPersonne'] . ' ' . $data2['prenomPersonne']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>De la part d'un externe:</label>
                            <input list="externesConnus" type="text" class="form-control" name="personneNonGPM" placeholder="Selectionner 'Affectée à un externe' au-dessus" value="<?= isset($data['personneNonGPM']) ? $data['personneNonGPM'] : '' ?>">
                            <datalist id="externesConnus">
                            	<?php
                            		$connus = $db->query('
                            			SELECT personneNonGPM FROM
										(SELECT DISTINCT personneNonGPM FROM TENUES_AFFECTATION WHERE personneNonGPM IS NOT NULL) aff
										UNION
										(SELECT DISTINCT personneNonGPM FROM CAUTIONS WHERE personneNonGPM IS NOT NULL)
										ORDER BY personneNonGPM;
                            		');
                            		while($connu = $connus->fetch())
                            		{
                            			echo '<option value="'.$connu['personneNonGPM'].'">';
                            		}
                            	?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label>Montant:</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="montantCaution" value="<?= isset($data['montantCaution']) ? $data['montantCaution'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Date d'emission:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateEmissionCaution" value="<?= isset($data['dateEmissionCaution']) ? $data['dateEmissionCaution'] : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Date d'expiration:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateExpirationCaution" value="<?= isset($data['dateExpirationCaution']) ? $data['dateExpirationCaution'] : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Détails sur la caution:</label>
                            <input type="text" class="form-control" name="detailsMoyenPaiement" value="<?= isset($data['detailsMoyenPaiement']) ? $data['detailsMoyenPaiement'] : '' ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right"><?= isset($_GET['id']) ? 'Modifier' : 'Ajouter' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
