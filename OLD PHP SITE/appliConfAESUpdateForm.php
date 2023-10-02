<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['appli_conf']==1)
{?>
    
    
    <div class="modal fade" id="modalLotsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Changement de la clef</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="appliConfAESUpdateUpdate.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Clef actuelle: <small style="color:grey;">Requis</small></label>
                            <input type="password" class="form-control" name="aesKey" required>
                        </div>
                        <div class="form-group">
                            <label>Nouvelle Clef: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" name="key1" required>
                        </div>
                        <div class="form-group">
                            <label>Confirmation de la nouvelle Clef: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" name="key2" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

