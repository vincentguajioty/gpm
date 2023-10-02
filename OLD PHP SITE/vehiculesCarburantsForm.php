<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['carburants_lecture']==1 OR $_SESSION['carburants_ajout']==1 OR $_SESSION['carburants_modification']==1 OR $_SESSION['carburants_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM CARBURANTS WHERE idCarburant=:idCarburant;');
		    $query->execute(array('idCarburant' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalVehiculesCarburantsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un carburant</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vehiculesCarburantsUpdate.php?id='.$_GET['id'] : 'vehiculesCarburantsAdd.php'?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleCarburant']) ? $data['libelleCarburant'] : ''?>" name="libelleCarburant" required>
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