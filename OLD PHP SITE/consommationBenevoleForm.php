<?php

require_once('config/config.php');
session_start();

?>
    
<div class="modal fade" id="modalConsoAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ajout d'une consommation</h4>
            </div>
            <form role="form" action="consommationBenevoleAdd.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Lot utilisé: <small style="color:grey;"> Requis</small></label>
                        <select class="form-control select2" style="width: 100%;" name="idLot" required>
                            <?php
                            $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                            while ($data2 = $query2->fetch())
                            {
                                ?>
                                <option value="<?=$data2['idLot']?>"><?=$data2['libelleLot']?></option>
                                <?php
                            }
                            $query2->closeCursor(); ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Consommable/Matériel utilisé: <small style="color:grey;"> Requis</small></label>
                                <select class="form-control select2" style="width: 100%;" name="idMaterielCatalogue" required>
                                    <?php
                                    $query2 = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?=$data2['idMaterielCatalogue']?>"><?=$data2['libelleMateriel']?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quantité utilisée: <small style="color:grey;"> Requis</small></label>
                                <input type="number" class="form-control" name="quantiteConsommation" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Reconditionnement: <small style="color:grey;"> Requis</small></label>
                        <select class="form-control select2" style="width: 100%;" name="idConteneur">
                            <option value="">--- Ce matériel n'a pas pu être reconditionné ---</option>
                            <?php
                            $query2 = $db->query('SELECT * FROM RESERVES_CONTENEUR WHERE dispoBenevoles=1 ORDER BY libelleConteneur;');
                            while ($data2 = $query2->fetch())
                            {
                                ?>
                                <option value="<?=$data2['idConteneur']?>">Reconditionné avec <?=$data2['libelleConteneur']?></option>
                                <?php
                            }
                            $query2->closeCursor(); ?>
                        </select>
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
