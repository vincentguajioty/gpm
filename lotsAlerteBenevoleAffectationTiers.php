<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

?>
    
<div class="modal fade" id="modalLotsAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Affectation d'une alerte de lots</h4>
            </div>
            <form role="form" class="spinnerAttenteSubmit" action="lotsAlerteBenevoleAffectationTiersUpdate.php?id=<?=$_GET['id']?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Affectation</label>
                        <select class="form-control select2" style="width: 100%;" name="idPersonne" required>
                            <option value="">--- Pas d'affectation ---</option>
                            <?php
                            $query2 = $db->query('SELECT identifiant, idPersonne FROM VIEW_HABILITATIONS WHERE alertesBenevolesLots_affectation = 1 ORDER BY identifiant;');
                            while ($data2 = $query2->fetch())
                            {
                                ?>
                                <option value ="<?php echo $data2['idPersonne']; ?>"><?php echo $data2['identifiant']; ?></option>
                                <?php
                            }
                            $query2->closeCursor(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary pull-right">Affecter</button>
                </div>
            </form>
        </div>
    </div>
</div>


