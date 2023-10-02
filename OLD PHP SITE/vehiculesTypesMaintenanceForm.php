<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['vehiculeHealthType_lecture']==1 OR $_SESSION['vehiculeHealthType_ajout']==1 OR $_SESSION['vehiculeHealthType_modification']==1 OR $_SESSION['vehiculeHealthType_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM VEHICULES_HEALTH_TYPES WHERE idHealthType=:idHealthType;');
		    $query->execute(array('idHealthType' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalVehiculesMaintenanceTypesAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un type de maintenance de vehicule</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vehiculesTypesMaintenanceUpdate.php?id='.$_GET['id'] : 'vehiculesTypesMaintenanceAdd.php'?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleHealthType']) ? $data['libelleHealthType'] : ''?>" name="libelleHealthType" required>
                        </div>
                        <div class="form-group">
				            <label>Synthèse:</label>
				            </br>
				            <div class="checkbox">
				                <label>
				                    <input type="checkbox" value="1" name="affichageSynthese" <?php if (isset($_GET['id']) AND ($data['affichageSynthese']==1)) {echo 'checked';} ?>> Affichage sur l'écran de synthèse
								</label>
							</div>
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