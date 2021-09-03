<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['idCentreDeCout'])==1)
{ ?>
    
    <?php
    if (isset($_GET['id']))
	{
	    $query = $db->prepare('SELECT * FROM CENTRE_COUTS_OPERATIONS WHERE idOperations=:idOperations;');
	    $query->execute(array('idOperations' => $_GET['id']));
	    $data = $query->fetch();
	    $query->closeCursor();
	}
    ?>
    
    <div class="modal fade" id="modalAnnuaireAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'une opération dans le centre de cout</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'centreCoutsOperationsUpdate.php?id='.$_GET['id'].'&idCentreDeCout='.$_GET['idCentreDeCout'] : 'centreCoutsOperationsAdd.php?idCentreDeCout='.$_GET['idCentreDeCout']?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Date/Heure: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="input-datetimepicker form-control" name="dateOperation" value="<?php echo $data['dateOperation']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleOperation']) ? $data['libelleOperation'] : ''?>" name="libelleOperation" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Montant Entrant:</label>
                                    <input type="number" step="0.01" min="0" class="form-control" value="<?= isset($data['montantEntrant']) ? $data['montantEntrant'] : ''?>" name="montantEntrant">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Montant Sortant:</label>
                                    <input type="number" step="0.01" min="0" class="form-control" value="<?= isset($data['montantSortant']) ? $data['montantSortant'] : ''?>" name="montantSortant">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Responsable de la transaction: </label>
                            <select class="form-control select2" style="width: 100%;" name="idPersonne">
                                <?php
                                $query2 = $db->query('SELECT * FROM VIEW_PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE cout_etreEnCharge = 1;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if($data2['idPersonne']==$data['idPersonne']){echo "selected";} ?>><?php echo $data2['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Détails</label>
                            <textarea class="form-control" rows="3" name="detailsMoyenTransaction"><?= isset($data['detailsMoyenTransaction']) ? $data['detailsMoyenTransaction'] : '' ?></textarea>
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