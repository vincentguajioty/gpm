<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['codeBarre_lecture']==1 OR $_SESSION['codeBarre_ajout']==1 OR $_SESSION['codeBarre_modification']==1 OR $_SESSION['codeBarre_suppression']==1)
{?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM CODES_BARRE WHERE idCode = :idCode;');
		    $query->execute(array('idCode' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    <div class="modal fade" id="modalLotsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Génération' ?> d'un code barre interne</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="<?= isset($_GET['id']) ? 'codesBarreInterneUpdate.php?id='.$_GET['id'] : 'codesBarreInterneAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Référence du catalogue: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idMaterielCatalogue" required>
                                <?php
                                if(isset($_GET['idCommande']))
                                {
                                    $query2 = $db->prepare('SELECT c.* FROM COMMANDES_MATERIEL m LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE idCommande = :idCommande ORDER BY libelleMateriel;');
                                    $query2->execute(array('idCommande'=>$_GET['idCommande']));
                                }
                                else
                                {
                                    $query2 = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
                                }
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
                            <label>Date de péremption:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="peremptionConsommable" value="<?= isset($data['peremptionConsommable']) ? $data['peremptionConsommable'] : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3" name="commentairesCode"><?= isset($data['commentairesCode']) ? $data['commentairesCode'] : '' ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right"><?= isset($_GET['id']) ? 'Modifier' : 'Générer' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

