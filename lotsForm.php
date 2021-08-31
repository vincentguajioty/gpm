<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['lots_lecture']==1 OR $_SESSION['lots_ajout']==1 OR $_SESSION['lots_modification']==1 OR $_SESSION['lots_suppression']==1)
{?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN ETATS s on l.idEtat = s.idEtat LEFT OUTER JOIN LIEUX e ON l.idLieu = e.idLieu LEFT OUTER JOIN PERSONNE_REFERENTE p on l.idPersonne = p.idPersonne WHERE idLot =:idLot;');
		    $query->execute(array('idLot' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    <div class="modal fade" id="modalLotsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un lot</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="<?= isset($_GET['id']) ? 'lotsUpdate.php?id='.$_GET['id'] : 'lotsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleLot']) ? $data['libelleLot']: '' ?>" name="libelleLot" required>
                        </div>
                        <div class="form-group">
                            <label>Référentiel à respecter: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleTypeLot">
                                <option value="">--- Pas d'analyse de Référentiel ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LOTS_TYPES ORDER BY libelleTypeLot;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idTypeLot']; ?>" <?php if (isset($data['idTypeLot']) AND ($data2['idTypeLot'] == $data['idTypeLot'])) { echo 'selected'; } ?> ><?php echo $data2['libelleTypeLot']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Etat: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="libelleEtat">
                                <?php
                                $query2 = $db->query('SELECT * FROM ETATS ORDER BY libelleEtat;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idEtat']; ?>" <?php if (isset($data['idEtat']) AND ($data2['idEtat'] == $data['idEtat'])) { echo 'selected'; } ?> ><?php echo $data2['libelleEtat']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lieu de stockage:</label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLieu">
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
                            <label>Affecté à un véhicule:</label>
                            <select class="form-control select2" style="width: 100%;" name="idVehicule">
                                <option value="">--- Pas affecté ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM VEHICULES ORDER BY libelleVehicule;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idVehicule']; ?>" <?php if (isset($data['idVehicule']) AND ($data2['idVehicule'] == $data['idVehicule'])) { echo 'selected'; } ?> ><?php echo $data2['libelleVehicule']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Personne référente</label>
                            <select class="form-control select2" style="width: 100%;" name="identifiant">
                                <option value="">--- Pas de Référent ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY identifiant;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idPersonne']) AND ($data2['idPersonne'] == $data['idPersonne'])) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
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

                        <!-- textarea -->
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3"
                                      name="commentairesLots"><?= isset($data['commentairesLots']) ? $data['commentairesLots'] : '' ?></textarea>
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

