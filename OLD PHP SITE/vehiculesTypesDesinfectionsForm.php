<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['typesDesinfections_lecture']==1 OR $_SESSION['typesDesinfections_ajout']==1 OR $_SESSION['typesDesinfections_modification']==1 OR $_SESSION['typesDesinfections_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM VEHICULES_DESINFECTIONS_TYPES WHERE idVehiculesDesinfectionsType=:idVehiculesDesinfectionsType;');
		    $query->execute(array('idVehiculesDesinfectionsType' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalVehiculesDesinfectionsTypesAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un type de désinfection de vehicule</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vehiculesTypesDesinfectionsUpdate.php?id='.$_GET['id'] : 'vehiculesTypesDesinfectionsAdd.php'?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleVehiculesDesinfectionsType']) ? $data['libelleVehiculesDesinfectionsType'] : ''?>" name="libelleVehiculesDesinfectionsType" required>
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