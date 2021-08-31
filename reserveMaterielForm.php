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
		    $query = $db->prepare('SELECT * FROM RESERVES_MATERIEL WHERE idReserveElement=:idReserveElement;');
		    $query->execute(array('idReserveElement' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalReserveMateriel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un matériel dans la réserve</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'reserveMaterielUpdate.php?id='.$_GET['id'] : 'reserveMaterielAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Référence du catalogue: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idMaterielCatalogue">
                                <?php
                                $query2 = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idMaterielCatalogue']; ?>"<?php if (isset($data['idMaterielCatalogue']) AND ($data2['idMaterielCatalogue'] == $data['idMaterielCatalogue'])) { echo 'selected'; } ?> ><?php echo $data2['libelleMateriel']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Conteneur: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idConteneur">
                                <?php
                                $query2 = $db->query('SELECT * FROM RESERVES_CONTENEUR ORDER BY libelleConteneur;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idConteneur']; ?>" <?php if (isset($data['idConteneur']) AND ($data2['idConteneur'] == $data['idConteneur'])) { echo 'selected'; } ?> data-id="<?php echo $data['libelleSac']; ?>"><?php echo $data2['libelleConteneur']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fournisseur:</label>
                            <select class="form-control select2" style="width: 100%;" name="idFournisseur">
                                <option value="">--- Pas de Fournisseur ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idFournisseur']; ?>" <?php if (isset($data['idFournisseur']) AND ($data2['idFournisseur'] == $data['idFournisseur'])) { echo 'selected'; } ?> ><?php echo $data2['nomFournisseur']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantité: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control"  value="<?= isset($data['quantiteReserve']) ? $data['quantiteReserve'] : '' ?>" name="quantiteReserve" required>
                        </div>
                        <div class="form-group">
                            <label>Quantité d'Alerte: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control"  value="<?= isset($data['quantiteAlerteReserve']) ? $data['quantiteAlerteReserve'] : '' ?>" name="quantiteAlerteReserve" required>
                        </div>

                        <div class="form-group">
                            <label>Date de péremption:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="peremptionReserve" value="<?= isset($data['peremptionReserve']) ? $data['peremptionReserve'] : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Anticipation de la notification:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="peremptionNotificationReserve" value="<?= isset($data['peremptionNotificationReserve']) ? $data['peremptionNotificationReserve'] : '' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="commentairesReserveElement"><?= isset($data['commentairesReserveElement']) ? $data['commentairesReserveElement'] : '' ?></textarea>
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