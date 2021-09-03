<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['codeBarre_lecture']==1)
{?>
    
    <div class="modal fade" id="modalLotsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Imprimer des codes barre d'emplacement</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="emplacementsCBShow.php?id=<?=$_GET['id']?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre d'exemplaires: <small style="color:grey;">Requis</small></label>
                            <input type="number" min="1" step="1" class="form-control" value="1" name="qtt" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Imprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

