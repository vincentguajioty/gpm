<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['lots_ajout']==1)
{?>
        
    <div class="modal fade" id="modalLotsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Duplication d'un lot</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="lotsDuplicateSQL.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Lot à dupliquer: </label>
                            <select class="form-control select2" style="width: 100%;" name="idLot" required>
                                <?php
                                $query2 = $db->query('SELECT * FROM LOTS_LOTS WHERE inventaireEnCours IS NULL ORDER BY libelleLot;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idLot']; ?>" ><?php echo $data2['libelleLot']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Dupliquer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

