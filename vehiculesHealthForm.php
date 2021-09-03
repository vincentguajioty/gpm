<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['vehiculeHealth_lecture']==1 OR $_SESSION['vehiculeHealth_ajout']==1 OR $_SESSION['vehiculeHealth_modification']==1 OR $_SESSION['vehiculeHealth_suppression']==1)
{
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM VEHICULES_HEALTH WHERE idVehiculeHealth=:idVehiculeHealth;');
		    $query->execute(array('idVehiculeHealth' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();

            $typesMaintenances = $db->prepare('
                SELECT
                    t.idHealthType,
                    t.libelleHealthType,
                    MAX(cal.dateHealth) as dateHealth,
                    veh.frequenceHealth,
                    h.remarquesCheck,
                    h.idVehiculeHealth
                FROM
                    VEHICULES_HEALTH_TYPES t
                    LEFT OUTER JOIN (SELECT * FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule)veh ON t.idHealthType = veh.idHealthType
                    LEFT OUTER JOIN (SELECT c.*, h.dateHealth FROM VEHICULES_HEALTH_CHECKS c LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth WHERE h.idVehicule = :idVehicule)cal ON t.idHealthType = cal.idHealthType
                    LEFT OUTER JOIN (SELECT * FROM VEHICULES_HEALTH_CHECKS WHERE idVehiculeHealth = :idVehiculeHealth) h ON t.idHealthType = h.idHealthType
                GROUP BY
                    t.idHealthType
                ORDER BY
                    libelleHealthType
                ;');
            $typesMaintenances->execute(array('idVehicule'=>$data['idVehicule'], 'idVehiculeHealth'=>$_GET['id']));
            $typesMaintenances = $typesMaintenances->fetchAll();
		}
        else
        {
            $typesMaintenances = $db->prepare('
                SELECT
                    t.idHealthType,
                    t.libelleHealthType,
                    MAX(cal.dateHealth) as dateHealth,
                    veh.frequenceHealth
                FROM
                    VEHICULES_HEALTH_TYPES t
                    LEFT OUTER JOIN (SELECT * FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule) veh ON t.idHealthType = veh.idHealthType
                    LEFT OUTER JOIN (SELECT c.*, h.dateHealth FROM VEHICULES_HEALTH_CHECKS c LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth WHERE h.idVehicule = :idVehicule)cal ON t.idHealthType = cal.idHealthType
                GROUP BY
                    t.idHealthType
                ORDER BY
                    t.libelleHealthType
                ;');
            $typesMaintenances->execute(array('idVehicule'=>$_GET['idVehicule']));
            $typesMaintenances = $typesMaintenances->fetchAll();
        }
    ?>
    
    
    <div class="modal fade" id="modalVehiculesHealthAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'une maintenance régulière de vehicule</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vehiculesHealthUpdate.php?id='.$_GET['id'] : 'vehiculesHealthAdd.php?idVehicule='.$_GET['idVehicule']?>" method="POST">
                    <div class="modal-body">
                    	<div class=row>
                    		<div class="col-md-6">
		                        <div class="form-group">
		                            <label>Date <small style="color:grey;"> Requis</small></label>
		                            <div class="input-group">
		                                <div class="input-group-addon">
		                                    <i class="fa fa-calendar"></i>
		                                </div>
		                                <input type="text" class="input-datepicker form-control" name="dateHealth" value="<?= (isset($_GET['id']) ? $data['dateHealth'] : date('Y-m-d')) ?>" required>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="col-md-6">
		                    	<div class="form-group">
		                            <label>Relevé kilométrique</label>
		                            <input type="number" min="0" class="form-control" value="<?php echo $data['releveKilometrique']; ?>" name="releveKilometrique">
		                        </div>
		                    </div>
		                </div>
                        <div class="form-group">
                            <label>Executant <small style="color:grey;"> Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idPersonne" required>
                                <option value=""></option>
                                <?php
                                if(isset($_GET['id']))
                                {
                                    $query2 = $db->query('SELECT * FROM VIEW_PERSONNE_REFERENTE;');
                                }
                                else
                                {
                                    $query2 = $db->query('SELECT * FROM VIEW_PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE vehiculeHealth_modification = 1;');
                                }
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idPersonne']) AND ($data2['idPersonne'] == $data['idPersonne'])) { echo 'selected'; } if(!(isset($_GET['id']))AND$data2['idPersonne']==$_SESSION['idPersonne']){echo 'selected';} ?> ><?php echo $data2['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor();
                                $query2->closeCursor();?>
                            </select>
                        </div>
                        
                        <table class="table table-bordered">
                            <tr>
                                <td colspan=3 style="background-color:#ff5f5f;color:#ffffff;"><b><center>Taches à faire pour ce véhicule</center></b></td>
                            </tr>
                            <?php
                                foreach($typesMaintenances as $typesMaintenance)
                                {
                                    if ($typesMaintenance['frequenceHealth'] != Null AND (date('Y-m-d', strtotime($typesMaintenance['dateHealth']. ' + '.$typesMaintenance['frequenceHealth'].' days'))<=date('Y-m-d')))
                                    { ?>
                                        <tr style="background-color:#ff5f5f;color:#ffffff;">
                                            <td><?=$typesMaintenance['libelleHealthType']?></td>
                                            <td><input
                                                    type="checkbox"
                                                    value="1"
                                                    name="formArray[<?=$_GET['idVehicule'].$data['idVehicule']?>][<?= $typesMaintenance['idHealthType'] ?>][done]"
                                                    <?php if (isset($typesMaintenance['idVehiculeHealth'])) {echo 'checked';} ?>
                                                > Fait
                                            </td>
                                            <td><input
                                                    type="text"
                                                    placeholder="Remarques"
                                                    class="form-control"
                                                    value="<?php echo $typesMaintenance['remarquesCheck']; ?>"
                                                    name="formArray[<?=$_GET['idVehicule'].$data['idVehicule']?>][<?= $typesMaintenance['idHealthType'] ?>][remarquesCheck]"
                                                >
                                            </td>
                                        </tr>
                                    <?php }
                                }
                            ?>

                            <tr>
                                <td colspan=3 style="background-color:#1b8700;color:#ffffff;"><b><center>Taches à venir pour ce véhicule</center></b></td>
                            </tr>
                            <?php
                                foreach($typesMaintenances as $typesMaintenance)
                                {
                                    if ($typesMaintenance['frequenceHealth'] != Null AND (date('Y-m-d', strtotime($typesMaintenance['dateHealth']. ' + '.$typesMaintenance['frequenceHealth'].' days'))>date('Y-m-d')))
                                    { ?>
                                        <tr style="background-color:#1b8700;color:#ffffff;">
                                            <td><?=$typesMaintenance['libelleHealthType']?></td>
                                            <td><input
                                                    type="checkbox"
                                                    value="1"
                                                    name="formArray[<?=$_GET['idVehicule'].$data['idVehicule']?>][<?= $typesMaintenance['idHealthType'] ?>][done]"
                                                    <?php if (isset($typesMaintenance['idVehiculeHealth'])) {echo 'checked';} ?>
                                                > Fait
                                            </td>
                                            <td><input
                                                    type="text"
                                                    placeholder="Remarques"
                                                    class="form-control"
                                                    value="<?php echo $typesMaintenance['remarquesCheck']; ?>"
                                                    name="formArray[<?=$_GET['idVehicule'].$data['idVehicule']?>][<?= $typesMaintenance['idHealthType'] ?>][remarquesCheck]"
                                                >
                                            </td>
                                        </tr>
                                    <?php }
                                }
                            ?>


                            <tr>
                                <td colspan=3 style="background-color:#969696;color:#ffffff;"><b><center>Taches non-attendues pour ce véhicule</center></b></td>
                            </tr>
                            <?php
                                foreach($typesMaintenances as $typesMaintenance)
                                {
                                    if ($typesMaintenance['frequenceHealth'] == Null)
                                    { ?>
                                        <tr style="background-color:#969696;color:#ffffff;">
                                            <td><?=$typesMaintenance['libelleHealthType']?></td>
                                            <td><input
                                                    type="checkbox"
                                                    value="1"
                                                    name="formArray[<?=$_GET['idVehicule'].$data['idVehicule']?>][<?= $typesMaintenance['idHealthType'] ?>][done]"
                                                    <?php if (isset($typesMaintenance['idVehiculeHealth'])) {echo 'checked';} ?>
                                                > Fait
                                            </td>
                                            <td><input
                                                    type="text"
                                                    placeholder="Remarques"
                                                    class="form-control"
                                                    value="<?php echo $typesMaintenance['remarquesCheck']; ?>"
                                                    name="formArray[<?=$_GET['idVehicule'].$data['idVehicule']?>][<?= $typesMaintenance['idHealthType'] ?>][remarquesCheck]"
                                                >
                                            </td>
                                        </tr>
                                    <?php }
                                }
                            ?>
                        </table>
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