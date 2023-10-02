<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

?>
    
<div class="modal fade" id="modalFournisseursAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ajout d'une note</h4>
            </div>
            <form role="form" action="commandesNotesAdd.php?id=<?=$_GET['id']?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Note: <small style="color:grey;">Requis</small></label>
                        <textarea class="form-control" rows="3" name="notes" required=""></textarea>
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