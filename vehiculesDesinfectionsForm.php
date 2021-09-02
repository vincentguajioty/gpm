<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['desinfections_lecture']==1 OR $_SESSION['desinfections_ajout']==1 OR $_SESSION['desinfections_modification']==1 OR $_SESSION['desinfections_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM VEHICULES_DESINFECTIONS WHERE idVehiculesDesinfection=:idVehiculesDesinfection;');
		    $query->execute(array('idVehiculesDesinfection' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalVehiculesDesinfectionsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'une désinfection de vehicule</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vehiculesDesinfectionsUpdate.php?id='.$_GET['id'] : 'vehiculesDesinfectionsAdd.php?idVehicule='.$_GET['idVehicule']?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Date <small style="color:grey;"> Requis</small></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="input-datepicker form-control" name="dateDesinfection" value="<?php echo $data['dateDesinfection']; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Type de désinfection <small style="color:grey;"> Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idVehiculesDesinfectionsType" required>
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM VEHICULES_DESINFECTIONS_TYPES ORDER BY libelleVehiculesDesinfectionsType;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idVehiculesDesinfectionsType']; ?>" <?php if (isset($data['idVehiculesDesinfectionsType']) AND ($data2['idVehiculesDesinfectionsType'] == $data['idVehiculesDesinfectionsType'])) { echo 'selected'; } ?> ><?php echo $data2['libelleVehiculesDesinfectionsType']; ?></option>
                                    <?php
                                }
                                $query->closeCursor();
                                $query2->closeCursor();?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Executant <small style="color:grey;"> Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idExecutant" required>
                                <?php
                                if(isset($_GET['id']))
                                {
                                    $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE;');
                                }
                                else
                                {
                                    $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE desinfections_modification = 1;');
                                }
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idExecutant']) AND ($data2['idPersonne'] == $data['idExecutant'])) { echo 'selected'; } if(!(isset($_GET['id']))AND$data2['idPersonne']==$_SESSION['idPersonne']){echo 'selected';} ?> ><?php echo $data2['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor();
                                $query2->closeCursor();?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remarques:</label>
                            <textarea class="form-control" rows="3" name="remarquesDesinfection"><?php echo $data['remarquesDesinfection']; ?></textarea>
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