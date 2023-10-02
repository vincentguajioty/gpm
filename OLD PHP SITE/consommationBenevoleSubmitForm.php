<?php

require_once('config/config.php');
session_start();

?>
    
<div class="modal fade" id="modalConsoAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Envoi du rapport de matériel utilisés</h4>
            </div>
            <form role="form" action="consommationBenevoleSubmit.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Un dernier commentaire ?<small style="color:grey;"> Facultatif</small></label>
                        <textarea class="form-control" rows="3" name="commentairesConsommation"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Je veux modifier ma déclaration</button>
                    <button type="submit" class="btn btn-primary pull-right">ENVOYER !</button>
                </div>
            </form>
        </div>
    </div>
</div>
