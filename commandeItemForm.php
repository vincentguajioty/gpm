<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if($_SESSION['commande_lecture']==1)
{?>
    
    <?php
    	if (isset($_GET['idElement']))
		{
		    $query = $db->prepare('SELECT * FROM COMMANDES_MATERIEL m LEFT OUTER JOIN COMMANDES c ON m.idCommande = c.idCommande WHERE m.idCommande=:idCommande AND idMaterielCatalogue = :idMaterielCatalogue;');
		    $query->execute(array('idCommande' => $_GET['idCommande'], 'idMaterielCatalogue' => $_GET['idElement']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalCommandesAddItem">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= (isset($_GET['idElement'])) ? 'Modifier' : 'Ajouter' ?> un élément à la commande</h4>
                </div>
                <form role="form" action="<?= (isset($_GET['idElement'])) ? 'commandeItemUpdate.php?idCommande='.$_GET['idCommande'].'&idElement='.$_GET['idElement'] : 'commandeItemAdd.php?idCommande='.$_GET['idCommande'] ?>" method="POST">
                    <div class="modal-body">
                    <div class="form-group">
                        <label>Matériel: <small style="color:grey;">Requis</small></label>
                        <select <?= (isset($_GET['idElement'])) ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idMaterielCatalogue">
                            <option value="-1">Frais de port</option>
                            <?php
                            if(isset($_GET['idElement']))
                            {
                            	$query2 = $db->prepare('SELECT idMaterielCatalogue, libelleMateriel FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue;');
                            	$query2->execute(array('idMaterielCatalogue' => $_GET['idElement']));
                            }
                            else
                            {
                            	$query2 = $db->prepare('SELECT c.idMaterielCatalogue, c.libelleMateriel FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN (SELECT idMaterielCatalogue FROM COMMANDES_MATERIEL WHERE idCommande= :idCommande) o ON c.idMaterielCatalogue = o.idMaterielCatalogue WHERE o.idMaterielCatalogue IS NULL ORDER BY libelleMateriel;');
                            	$query2->execute(array('idCommande' => $_GET['idCommande']));
                            }
                            while ($data2 = $query2->fetch())
                            {
                                ?>
                                <option <?php if (isset($_GET['idElement']) AND ($_GET['idElement'] == $data2['idMaterielCatalogue'])){ echo 'selected'; }?> value="<?php echo $data2['idMaterielCatalogue']; ?>"><?php echo $data2['libelleMateriel']; ?></option>
                                <?php
                            }
                            $query2->closeCursor(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantité: <small style="color:grey;">Requis</small></label>
                        <input type="number" class="form-control" name="quantiteCommande" value="<?= isset($data['quantiteCommande']) ? $data['quantiteCommande'] : '' ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prix HT:</label>
                                <input type="number" step="0.01" class="form-control" name="prixProduitHT" value="<?= isset($data['prixProduitHT']) ? $data['prixProduitHT'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Taxe:</label>
                                <input type="number" step="0.01" class="form-control" name="taxeProduit" value="<?= isset($data['taxeProduit']) ? $data['taxeProduit'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prix TTC: <small style="color:grey;">Requis</small></label>
                                <input type="number" step="0.01" class="form-control" name="prixProduitTTC" required value="<?= isset($data['prixProduitTTC']) ? $data['prixProduitTTC'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Remise:</label>
                                <input type="number" step="0.01" class="form-control" name="remiseProduit" value="<?= isset($data['remiseProduit']) ? $data['remiseProduit'] : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Référence produit chez le fournisseur:</label>
                        <input type="text" class="form-control" name="referenceProduitFournisseur" value="<?= isset($data['referenceProduitFournisseur']) ? $data['referenceProduitFournisseur'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Remarques:</label>
                        <textarea class="form-control" rows="3" name="remarqueArticle"><?= isset($data['remarqueArticle']) ? $data['remarqueArticle'] : '' ?></textarea>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <?php if($data['idEtat']<2){?><button type="submit" class="btn btn-primary pull-right"><?= isset($_GET['idElement']) ? 'Modifier' : 'Ajouter' ?></button><?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>