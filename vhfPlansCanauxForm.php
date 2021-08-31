<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['vhf_plan_modification']==1)
{?>

    <div class="modal fade" id="modalPlansCanalAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ajout d'un canal au plan</h4>
                </div>
                <form role="form" action="vhfPlansCanauxAdd.php?idVhfPlan=<?= $_GET['idVhfPlan']?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Numéro du canal: <small style="color:grey;">Requis</small></label>
                            <input type="number" class="form-control" name="numeroCanal" required>
                        </div>
                        <div class="form-group">
                            <label>Canal: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idVhfCanal">
                                <?php
                                $query2 = $db->query('SELECT * FROM VHF_CANAL ORDER BY chName;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idVhfCanal']; ?>" ><?php echo $data2['chName']; ?></option>
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
<?php } ?>

