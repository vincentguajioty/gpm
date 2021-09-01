<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['tenues_lecture']==1 OR $_SESSION['tenues_ajout']==1 OR $_SESSION['tenues_modification']==1 OR $_SESSION['tenues_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM TENUES_AFFECTATION WHERE idTenue=:idTenue;');
		    $query->execute(array('idTenue' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}

    ?>
    
    <div class="modal fade" id="modalTenueAffectation">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'une affectation de tenue</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'tenuesAffectationsUpdate.php?id='.$_GET['id'] : 'tenuesAffectationsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Element de tenue: </label>
                            <select class="form-control select2" style="width: 100%;" name="idCatalogueTenue" required>
                                <?php
                                $query2 = $db->query('SELECT * FROM TENUES_CATALOGUE ORDER BY libelleCatalogueTenue;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idCatalogueTenue']; ?>" <?php if (isset($data['idCatalogueTenue']) AND ($data2['idCatalogueTenue'] == $data['idCatalogueTenue'])) { echo 'selected'; } ?> ><?php echo $data2['libelleCatalogueTenue'] . ' (' . $data2['tailleCatalogueTenue'] . ') (Stock: ' . $data2['stockCatalogueTenue'] . ')'; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Affectation pour: </label>
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
                            <label>Affectée à un externe:</label>
                            <input list="externesConnus" type="text" class="form-control" name="personneNonGPM" placeholder="Selectionner 'Affectée à un externe' au-dessus" value="<?= isset($data['personneNonGPM']) ? $data['personneNonGPM'] : '' ?>">
                            <datalist id="externesConnus">
                            	<?php
                            		$connus = $db->query('SELECT DISTINCT personneNonGPM FROM TENUES_AFFECTATION ORDER BY personneNonGPM;');
                            		while($connu = $connus->fetch())
                            		{
                            			echo '<option value="'.$connu['personneNonGPM'].'">';
                            		}
                            	?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label>Date d'affectation:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateAffectation" value="<?= isset($data['dateAffectation']) ? $data['dateAffectation'] : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Retour de la tenue prévue le:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateRetour" value="<?= isset($data['dateRetour']) ? $data['dateRetour'] : '' ?>">
                            </div>
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
