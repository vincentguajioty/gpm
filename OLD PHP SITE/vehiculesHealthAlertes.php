<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['vehiculeHealth_ajout']==1 AND $_SESSION['vehiculeHealth_modification']==1)
{ ?>
    
    <?php
	    $query = $db->prepare('SELECT * FROM VEHICULES WHERE idVehicule = :idVehicule;');
	    $query->execute(array('idVehicule' => $_GET['idVehicule']));
	    $data = $query->fetch();
	    $query->closeCursor();
    ?>
    
    <div class="modal fade" id="modalAnnuaireAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Gestion des notifications de maintenance pour <?=$data['libelleVehicule']?></h4>
                </div>
                <form role="form" action="vehiculesHealthAlertesUpdate.php?idVehicule=<?=$_GET['idVehicule']?>" method="POST">
                    <div class="modal-body">
                        <?php
                            if($data['idEtat'] == 2)
                            {
                        ?>
                            <div class="alert alert-warning">
                                <i class="icon fa fa-warning"></i> Attention les notifications sont désactivées pour ce véhicule
                            </div>
                        <?php } ?>
                        <table class="table table-bordered">
                            <tr>
                                <th>Type</th>
                                <th>Alerte active</th>
                                <th>Fréquence maintenance (jours)</th>
                            </tr>
                            <?php
                                $typesMaintenances = $db->prepare('
                                    SELECT
                                        t.libelleHealthType,
                                        a.idHealthAlerte,
                                        a.frequenceHealth,
                                        t.idHealthType
                                    FROM
                                        VEHICULES_HEALTH_TYPES t
                                        LEFT OUTER JOIN 
                                            (SELECT * FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule) a
                                        ON t.idHealthType = a.idHealthType
                                    ORDER BY
                                        t.libelleHealthType
                                    ;');
                                $typesMaintenances->execute(array('idVehicule'=>$_GET['idVehicule']));
                                while($typesMaintenance = $typesMaintenances->fetch())
                                {?>
                                    <tr>
                                        <td><?=$typesMaintenance['libelleHealthType']?></td>
                                        <td><?php if($typesMaintenance['idHealthAlerte'] <> Null) { echo '<i class="fa fa-check"></i>'; } ?></td>
                                        <td><input type="number" min="0" class="form-control" value="<?php echo $typesMaintenance['frequenceHealth']; ?>"name="formArray[<?=$_GET['idVehicule']?>][<?= $typesMaintenance['idHealthType'] ?>][frequenceHealth]"></td>
                                    </tr>
                                <?php }
                            ?>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>