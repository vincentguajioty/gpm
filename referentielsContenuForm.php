<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if($_SESSION['typesLots_modification']==1)
{ ?>
    <div class="modal fade" id="modalReferentielContenu">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ajout d'un matériel au référentiel</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="referentielsAddItem.php?idLot=<?=$_GET['id']?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Matériel: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="libelleMateriel">
                                <?php
                                $query = $db->prepare('SELECT c.idMaterielCatalogue, c.libelleMateriel FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN (SELECT idMaterielCatalogue FROM REFERENTIELS WHERE idTypeLot= :idTypeLot) r ON c.idMaterielCatalogue = r.idMaterielCatalogue WHERE r.idMaterielCatalogue IS NULL ORDER BY libelleMateriel;');
                                $query->execute(array('idTypeLot' => $_GET['id']));
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idMaterielCatalogue']; ?>"><?php echo $data['libelleMateriel']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantité: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" placeholder="1-2-3 ..." name="quantiteReferentiel" required>
                        </div>
                        <div class="form-group">
                            <label>Obligation:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="obligatoire" id="optionsRadios1" value="option1" checked>
                                    Ce matériel est obligatoire.
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="obligatoire" id="optionsRadios2" value="option2">
                                    Ce matériel est facultatif.
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Commentaires:</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails" name="commentairesReferentiel"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>