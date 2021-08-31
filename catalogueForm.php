<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['catalogue_lecture']==1 OR $_SESSION['catalogue_ajout']==1 OR $_SESSION['catalogue_modification']==1 OR $_SESSION['catalogue_suppression']==1)
{?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN MATERIEL_CATEGORIES b ON c.idCategorie = b.idCategorie WHERE idMaterielCatalogue=:idMaterielCatalogue;');
		    $query->execute(array('idMaterielCatalogue' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    <div class="modal fade" id="modalCatalogueAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un élément dans le catalogue</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'catalogueUpdate.php?id='.$_GET['id'] :  'catalogueAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" placeholder="Libellé de l'item à ajouter" value="<?= isset($data['libelleMateriel']) ? $data['libelleMateriel'] : '' ?>" name="libelleMateriel" required>
                        </div>
                        <div class="form-group">
                            <label>Catégorie: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleCategorie">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM MATERIEL_CATEGORIES ORDER BY libelleCategorie;');
                                while ($data2 = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idCategorie']; ?>" <?php if (isset ($data['libelleCategorie']) AND $data2['libelleCategorie'] == $data['libelleCategorie']) { echo 'selected'; } ?> ><?php echo $data2['libelleCategorie']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" placeholder="5cmx5cm, L, Adulte, Pédiatrique" value="<?= isset($data['taille']) ? $data['taille'] : '' ?>" name="taille">
                        </div>
                        <div class="form-group">
                            <label>Stérilité:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="sterilite" id="optionsRadios1" value="option1" <?php if (isset($data['sterilite']) AND $data['sterilite']==0) echo 'checked' ?>>
                                    Matériel non-stérile
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="sterilite" id="optionsRadios2" value="option2" <?php if (isset($data['sterilite']) AND $data['sterilite']==1) echo 'checked' ?>>
                                    Matériel stérile
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Conditionnement:</label>
                            <input type="text" class="form-control" placeholder="3 compresses par sachet ..." value="<?= isset($data['conditionnementMultiple']) ? $data['conditionnementMultiple'] : '' ?>" name="conditionnementMultiple">
                        </div>
                        <div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails" name="commentairesMateriel"><?= isset($data['commentairesMateriel']) ? $data['commentairesMateriel'] : '' ?></textarea>
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
