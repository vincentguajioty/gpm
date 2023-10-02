<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['sac2_lecture']==1 OR $_SESSION['sac2_ajout']==1 OR $_SESSION['sac2_modification']==1 OR $_SESSION['sac2_suppression']==1)
{?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE idEmplacement = :idEmplacement;');
		    $query->execute(array('idEmplacement' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    <div class="modal fade" id="modalEmplacementAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un emplacement</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="<?= isset($_GET['id']) ? 'emplacementsUpdate.php?id='.$_GET['id'] : 'emplacementsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleEmplacement']) ? $data['libelleEmplacement'] : ''?>" name="libelleEmplacement" required>
                        </div>
                        <div class="form-group">
                            <label>Sac d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleSac">
                                <option value="">--- Aucun rattachement ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleLot, libelleSac;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idSac']; ?>" <?php if (isset($data['idSac']) AND ($data2['idSac'] == $data['idSac'])) { echo 'selected'; } ?> <?php if (isset($_GET['idParent']) AND ($data2['idSac'] == $_GET['idParent'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLot']; ?> > <?php echo $data2['libelleSac']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
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