<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['etats_lecture']==1 OR $_SESSION['etats_ajout']==1 OR $_SESSION['etats_modification']==1 OR $_SESSION['etats_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM VEHICULES_ETATS WHERE idVehiculesEtat=:idVehiculesEtat;');
		    $query->execute(array('idVehiculesEtat' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalCategorieAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un état de véhicule</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'etatsVehiculesUpdate.php?id='.$_GET['id'] : 'etatsVehiculesAdd.php'?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleVehiculesEtat']) ? $data['libelleVehiculesEtat'] : ''?>" name="libelleVehiculesEtat" required>
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