<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if($_SESSION['sac_lecture']==1 OR $_SESSION['sac_ajout']==1 OR $_SESSION['sac_modification']==1 OR $_SESSION['sac_suppression']==1)
{?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN FOURNISSEURS f ON s.idFournisseur = f.idFournisseur WHERE idSac = :idSac;');
		    $query->execute(array('idSac' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    

    <div class="modal fade" id="modalSacsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un sac</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="<?= isset($_GET['id']) ? 'sacsUpdate.php?id='.$_GET['id'] : 'sacsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleSac']) ? $data['libelleSac']: ''?>" name="libelleSac" required>
                        </div>
                        <div class="form-group">
                            <label>Lot d'appartenance:</label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLot">
                                <option value="">--- Aucun rattachement ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idLot']; ?>" <?php if (isset($data['idLot']) AND ($data2['idLot'] == $data['idLot'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLot']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" value="<?= isset($data['taille']) ? $data['taille']: ''?>"
                                   name="taille">
                        </div>
                        <div class="form-group">
                            <label>Couleur:</label>
                            <input type="text" class="form-control" value="<?= isset($data['couleur']) ? $data['couleur']: ''?>"
                                   name="couleur">
                        </div>
                        <div class="form-group">
                            <label>Fournisseur: </label>
                            <select class="form-control select2" style="width: 100%;" name="nomFournisseur">
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