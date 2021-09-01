<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['tenuesCatalogue_lecture']==1 OR $_SESSION['tenuesCatalogue_ajout']==1 OR $_SESSION['tenuesCatalogue_modification']==1 OR $_SESSION['tenuesCatalogue_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM TENUES_CATALOGUE WHERE idCatalogueTenue=:idCatalogueTenue;');
		    $query->execute(array('idCatalogueTenue' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}

    ?>
    
    <div class="modal fade" id="modalTenueCatalogue">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un item dans le catalogue des tenues</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'tenuesCatalogueUpdate.php?id='.$_GET['id'] : 'tenuesCatalogueAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" name="libelleCatalogueTenue" required value="<?= isset($data['libelleCatalogueTenue']) ? $data['libelleCatalogueTenue'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" name="tailleCatalogueTenue" value="<?= isset($data['tailleCatalogueTenue']) ? $data['tailleCatalogueTenue'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Sérigraphie:</label>
                            <input type="text" class="form-control" name="serigraphieCatalogueTenue" value="<?= isset($data['serigraphieCatalogueTenue']) ? $data['serigraphieCatalogueTenue'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock:</label>
                            <input type="number" class="form-control" name="stockCatalogueTenue" value="<?= isset($data['stockCatalogueTenue']) ? $data['stockCatalogueTenue'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock d'alerte:</label>
                            <input type="number" min="0" class="form-control" name="stockAlerteCatalogueTenue" value="<?= isset($data['stockAlerteCatalogueTenue']) ? $data['stockAlerteCatalogueTenue'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Fournisseur: </label>
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
