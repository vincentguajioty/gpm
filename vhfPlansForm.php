<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['vhf_plan_lecture']==1 OR $_SESSION['vhf_plan_ajout']==1 OR $_SESSION['vhf_plan_modification']==1)
{?>

    <?php
    if (isset($_GET['id']))
    {
        $query = $db->prepare('SELECT * FROM VHF_PLAN WHERE idVhfPlan =:idVhfPlan;');
        $query->execute(array('idVhfPlan' => $_GET['id']));
        $data = $query->fetch();
        $query->closeCursor();
    }
    ?>

    <div class="modal fade" id="modalPlansAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un plan</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vhfPlansUpdate.php?id='.$_GET['id'] : 'vhfPlansAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libellePlan']) ? $data['libellePlan']: '' ?>" name="libellePlan" required>
                        </div>
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3"
                                      name="remarquesPlan"><?= isset($data['remarquesPlan']) ? $data['remarquesPlan'] : '' ?></textarea>
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

