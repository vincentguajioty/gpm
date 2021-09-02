<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['vhf_equipement_lecture']==1 OR $_SESSION['vhf_equipement_ajout']==1 OR $_SESSION['vhf_equipement_modification']==1)
{?>

    <?php
    if (isset($_GET['id']))
    {
        $query = $db->prepare('SELECT * FROM VHF_ACCESSOIRES WHERE idVhfAccessoire =:idVhfAccessoire;');
        $query->execute(array('idVhfAccessoire' => $_GET['id']));
        $data = $query->fetch();
        $query->closeCursor();
    }
    ?>

    <div class="modal fade" id="modalAccessoiresAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un accessoire radio</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vhfEquipementsAccessoiresUpdate.php?id='.$_GET['id'] : 'vhfEquipementsAccessoiresAdd.php?idVhfEquipement='.$_GET['idVhfEquipement'] ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleVhfAccessoire']) ? $data['libelleVhfAccessoire']: '' ?>" name="libelleVhfAccessoire" required>
                        </div>
                        <div class="form-group">
                            <label>Marque/Modèle:</label>
                            <input type="text" class="form-control" value="<?= isset($data['marqueModeleVhfAccessoire']) ? $data['marqueModeleVhfAccessoire']: '' ?>" name="marqueModeleVhfAccessoire">
                        </div>
                        <div class="form-group">
                            <label>S/N:</label>
                            <input type="text" class="form-control" value="<?= isset($data['SnVhfAccessoire']) ? $data['SnVhfAccessoire']: '' ?>" name="SnVhfAccessoire">
                        </div>
                        <div class="form-group">
                            <label>Type: </label>
                            <select class="form-control select2" style="width: 100%;" name="idVhfAccessoireType">
                                <option value="">--- Pas Spécifié ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM VHF_ACCESSOIRES_TYPES ORDER BY libelleVhfAccessoireType;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idVhfAccessoireType']; ?>" <?php if (isset($data['idVhfAccessoireType']) AND ($data2['idVhfAccessoireType'] == $data['idVhfAccessoireType'])) { echo 'selected'; } ?> ><?php echo $data2['libelleVhfAccessoireType']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remarques:</label>
                            <textarea class="form-control" rows="3"
                                      name="remarquesVhfAccessoire"><?= isset($data['remarquesVhfAccessoire']) ? $data['remarquesVhfAccessoire'] : '' ?></textarea>
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

