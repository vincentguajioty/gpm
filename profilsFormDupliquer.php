<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if($_SESSION['profils_ajout']==1)
{ ?>
    <div class="modal fade" id="modalProfilsDupliquer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Dupliquer un profil</h4>
                </div>
                <form role="form" action="profilsDupliquer.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Profil à dupliquer: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idProfil">
                                <?php
                                $query = $db->prepare('SELECT * FROM PROFILS ORDER BY libelleProfil;');
                                $query->execute(array('idTypeLot' => $_GET['id']));
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idProfil']; ?>"><?php echo $data['libelleProfil']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
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