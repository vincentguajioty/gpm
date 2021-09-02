<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['verrouIP']==1)
{

    $query = $db->prepare('SELECT * FROM VERROUILLAGE_IP WHERE idIP =:idIP;');
            $query->execute(array('idIP' => $_GET['id']));
            $data = $query->fetch();
            $query->closeCursor();
    ?>
    
    <div class="modal fade" id="IPVerr">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Commenter un verrouillage IP</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="verrouIPUpdate.php?id=<?= $_GET['id'] ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>IP:</label>
                            <input type="text" class="form-control" value="<?= $data['adresseIPverr'] ?>" name="adresseIPverr" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label>Date du verrouillage:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateVerr" value="<?= $data['dateVerr'] ?>" disabled>
                            </div>
                            <!-- /.input group -->
                        </div>
                        
                        <div class="form-group">
                            <label>Commentaires:</label>
                            <textarea class="form-control" rows="3"
                                      name="commentaire"><?= isset($data['commentaire']) ? $data['commentaire'] : '' ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

