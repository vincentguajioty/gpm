<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if($_SESSION['vhf_equipement_modification']==1)
{?>
    <div class="modal fade" id="modalVHFDocAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Attacher un document à l'équipement radio</h4>
                </div>
                <form role="form" action="vhfEquipementsDocAdd.php?idVhfEquipement=<?=$_GET['idVhfEquipement']?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Document à charger: <small style="color:grey;">Requis</small></label>
                            <input style="width: 100%;" type="file" name="urlFichierDocVHF" id="urlFichierDocVHF" required>
                        </div>
                        <div class="form-group">
                            <label>Nom du document: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" name="nomDocVHF" required>
                        </div>
                        <div class="form-group">
                            <label>Type de document: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idTypeDocument">
                                <?php
                                $query2 = $db->query('SELECT * FROM DOCUMENTS_TYPES ORDER BY libelleTypeDocument;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idTypeDocument']; ?>"><?php echo $data2['libelleTypeDocument']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
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