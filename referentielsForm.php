<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['typesLots_ajout']==1 OR $_SESSION['typesLots_modification']==1)
{?>
    
    <?php
        if (isset($_GET['id']))
        {
            $query = $db->prepare('SELECT * FROM LOTS_TYPES WHERE idTypeLot=:idTypeLot;');
            $query->execute(array('idTypeLot' => $_GET['id']));
            $data = $query->fetch();
            $query->closeCursor();
        }
    ?>
    
    <div class="modal fade" id="modalReferentielAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un référentiel</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="<?= isset($_GET['id']) ? 'referentielsUpdate.php?id='.$_GET['id'] : 'referentielsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé du référentiel: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleTypeLot']) ? $data['libelleTypeLot']: '' ?>" name="libelleTypeLot" required>
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
