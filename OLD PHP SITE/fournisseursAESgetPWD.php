<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['fournisseurs_lecture']==1 OR $_SESSION['appli_conf']==1)
{?>
    
    
    <div class="modal fade" id="modalLotsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Déverrouiller les informations chiffrées</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="fournisseursAESunlock.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Clef de déchiffrement: <small style="color:grey;">Requis</small></label>
                            <input type="password" class="form-control" name="aesKey" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Déverouiller</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

