<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['vehicules_lecture']==1 OR $_SESSION['vehicules_modification']==1)
{ ?>
    
    <?php
    if (isset($_GET['id']))
	{
	    $query = $db->prepare('SELECT * FROM VEHICULES_MAINTENANCE WHERE idMaintenance = :idMaintenance;');
	    $query->execute(array('idMaintenance' => $_GET['id']));
	    $data = $query->fetch();
	    $query->closeCursor();
	}
    ?>
    
    <div class="modal fade" id="modalAnnuaireAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'une maintenance véhicule</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vehiculesMaintenanceUpdate.php?idVehicule='.$_GET['idVehicule'].'&id='.$_GET['id'] : 'vehiculesMaintenanceAdd.php?idVehicule='.$_GET['idVehicule']?>" method="POST">
                    <div class="modal-body">                        
                        <div class="form-group">
                            <label>Executant <small style="color:grey;"> Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idExecutant">
                                <?php
                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE vehicules_modification = 1;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idExecutant']) AND ($data2['idPersonne'] == $data['idExecutant'])) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor();
                                $query2->closeCursor();?>
                            </select>
                        </div>
						<div class="form-group">
                            <label>Tache <small style="color:grey;"> Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idTypeMaintenance">
                                <?php
                                $query2 = $db->query('SELECT * FROM VEHICULES_MAINTENANCE_TYPES ORDER BY libelleTypeMaintenance ASC;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idTypeMaintenance']; ?>" <?php if (isset($data['idTypeMaintenance']) AND ($data2['idTypeMaintenance'] == $data['idTypeMaintenance'])) { echo 'selected'; } ?> ><?php echo $data2['libelleTypeMaintenance']; ?></option>
                                    <?php
                                }
                                $query->closeCursor();
                                $query2->closeCursor();?>
                            </select>
                        </div>
                        <div class="form-group">
							<label>Date <small style="color:grey;"> Requis</small></label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="input-datetimepicker form-control" name="dateMaintenance" value="<?php echo $data['dateMaintenance']; ?>" required>
							</div>
						</div>
                        <div class="form-group">
                            <label>Détails:</label>
                            <textarea class="form-control" rows="3" name="detailsMaintenance"><?php echo $data['detailsMaintenance']; ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right"><?= isset($_GET['id']) ? 'Modifier' : 'Ajouter'?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>