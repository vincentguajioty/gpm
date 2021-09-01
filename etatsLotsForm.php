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
		    $query = $db->prepare('SELECT * FROM LOTS_ETATS WHERE idLotsEtat=:idLotsEtat;');
		    $query->execute(array('idLotsEtat' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalCategorieAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un état de lots</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'etatsLotsUpdate.php?id='.$_GET['id'] : 'etatsLotsAdd.php'?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleLotsEtat']) ? $data['libelleLotsEtat'] : ''?>" name="libelleLotsEtat" required>
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