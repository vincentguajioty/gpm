<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['lieux_lecture']==1 OR $_SESSION['lieux_ajout']==1 OR $_SESSION['lieux_modification']==1 OR $_SESSION['lieux_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM LIEUX WHERE idLieu=:idLieu;');
		    $query->execute(array('idLieu' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    <div class="modal fade" id="modalLieuxAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un lieu</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'lieuxUpdate.php?id='.$_GET['id'] : 'lieuxAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé du lieu de stockage: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleLieu']) ? $data['libelleLieu'] : '' ?>" name="libelleLieu" required>
                        </div>
                        <!-- textarea -->
                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea class="form-control" rows="3" name="adresseLieu"><?= isset($data['adresseLieu']) ? $data['adresseLieu'] : '' ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Contrôle d'accès:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="accesReserve" id="optionsRadios1" value="option1"
                                        <?php if (isset($data['accesReserve']) AND $data['accesReserve']==0)
                                            echo 'checked'?>
                                    >
                                    Accès libre
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="accesReserve" id="optionsRadios2" value="option2"
                                        <?php if (isset($data['accesReserve']) AND $data['accesReserve']==1)
                                            echo 'checked'?>
                                    >
                                    Accès règlementé
                                </label>
                            </div>
                        </div>

                        <!-- textarea -->
                        <div class="form-group">
                            <label>Détails</label>
                            <textarea class="form-control" rows="3" name="detailsLieu"><?= isset($data['detailsLieu']) ? $data['detailsLieu'] : '' ?></textarea>
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
