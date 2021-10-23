<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['reserve_ajout']==1 OR $_SESSION['reserve_modification']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM RESERVES_CONTENEUR WHERE idConteneur=:idConteneur;');
		    $query->execute(array('idConteneur' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalReserveConteneur">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un conteneur</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'reserveConteneurUpdate.php?id='.$_GET['id'] : 'reserveConteneurAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleConteneur']) ? $data['libelleConteneur'] : ''?>" name="libelleConteneur" required>
                        </div>
                        <div class="form-group">
                            <label>Lieu de stockage:</label>
                            <select class="form-control select2" style="width: 100%;" name="idLieu">
                                <option value="">--- Pas de Lieux ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LIEUX ORDER BY libelleLieu;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idLieu']; ?>" <?php if (isset($data['idLieu']) AND ($data2['idLieu'] == $data['idLieu'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLieu']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date du dernier inventaire: <small style="color:grey;">Requis</small></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateDernierInventaire" value="<?= isset($data['dateDernierInventaire']) ? $data['dateDernierInventaire'] : '' ?>" required>
                            </div>
                            <!-- /.input group -->
                        </div>
                        <div class="form-group">
                            <label>Fréquence inventaire (jours): <small style="color:grey;">Requis</small></label>
                            <input type="number" class="form-control" value="<?= isset($data['frequenceInventaire']) ? $data['frequenceInventaire'] : '' ?>" name="frequenceInventaire" required>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="dispoBenevoles" <?php if($data['dispoBenevoles']){echo "checked";} ?>> Cette réserve est accessible pour le reconditionnement des bénévoles
                                </label>
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