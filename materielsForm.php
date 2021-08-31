<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';




if($_SESSION['materiel_lecture']==1 OR $_SESSION['materiel_ajout']==1 OR $_SESSION['materiel_modification']==1 OR $_SESSION['materiel_suppression']==1)
{?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN FOURNISSEURS f ON m.idFournisseur = f.idFournisseur WHERE idElement = :idElement;');
		    $query->execute(array('idElement' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    <div class="modal fade" id="modalMaterielAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un Materiel</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="<?= isset($_GET['id']) ? 'materielsUpdate.php?id='.$_GET['id'] : 'materielsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Référence du catalogue: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="libelleMateriel">
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
                            <label>Emplacement d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleEmplacement">
                                <option value="">--- Aucun rattachement ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleLot, libelleSac, libelleEmplacement;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idEmplacement']; ?>" <?php if (isset($data['idEmplacement']) AND ($data2['idEmplacement'] == $data['idEmplacement'])) { echo 'selected'; } ?> <?php if (isset($_GET['idParent']) AND ($data2['idEmplacement'] == $_GET['idParent'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLot']; ?> > <?php echo $data2['libelleSac']; ?> > <?php echo $data2['libelleEmplacement']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fournisseur:</label>
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

                        <div class="form-group">
                            <label>Quantité: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control"  value="<?= isset($data['quantite']) ? $data['quantite'] : '' ?>" name="quantite" required>
                        </div>
                        <div class="form-group">
                            <label>Quantité d'Alerte: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control"  value="<?= isset($data['quantiteAlerte']) ? $data['quantiteAlerte'] : '' ?>" name="quantiteAlerte" required>
                        </div>

                        <div class="form-group">
                            <label>Date de péremption:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="peremption" value="<?= isset($data['peremption']) ? $data['peremption'] : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Anticipation de la notification:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="peremptionNotification" value="<?= isset($data['peremptionNotification']) ? $data['peremptionNotification'] : '' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="commentairesElement"><?= isset($data['commentairesElement']) ? $data['commentairesElement'] : '' ?></textarea>
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