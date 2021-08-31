<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['vhf_canal_lecture']==1 OR $_SESSION['vhf_canal_ajout']==1 OR $_SESSION['vhf_canal_modification']==1)
{?>

    <?php
    if (isset($_GET['id']))
    {
        $query = $db->prepare('SELECT * FROM VHF_CANAL WHERE idVhfCanal =:idVhfCanal;');
        $query->execute(array('idVhfCanal' => $_GET['id']));
        $data = $query->fetch();
        $query->closeCursor();
    }
    ?>

    <div class="modal fade" id="modalCannauxAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un canal</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vhfCanauxUpdate.php?id='.$_GET['id'] : 'vhfCanauxAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['chName']) ? $data['chName']: '' ?>" name="chName" required>
                        </div>
                        <div class="form-group">
                            <label>Technologie: </label>
                            <select class="form-control select2" style="width: 100%;" name="idVhfTechno">
                                <option value="">--- Pas Spécifiée ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM VHF_TECHNOLOGIES ORDER BY libelleTechno;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idVhfTechno']; ?>" <?php if (isset($data['idVhfTechno']) AND ($data2['idVhfTechno'] == $data['idVhfTechno'])) { echo 'selected'; } ?> ><?php echo $data2['libelleTechno']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fréquence Rx: <small style="color:grey;">Requis</small></label>
                            <input type="number" step="0.00001" class="form-control" value="<?= isset($data['rxFreq']) ? $data['rxFreq']: '' ?>" name="rxFreq" required>
                        </div>
                        <div class="form-group">
                            <label>Fréquence Tx: <small style="color:grey;">Requis</small></label>
                            <input type="number" step="0.00001" class="form-control" value="<?= isset($data['txFreq']) ? $data['txFreq']: '' ?>" name="txFreq" required>
                        </div>
                        <div class="form-group">
                            <label>CTCSS Rx:</label>
                            <input type="number" step="0.00001" class="form-control" value="<?= isset($data['rxCtcss']) ? $data['rxCtcss']: '' ?>" name="rxCtcss">
                        </div>
                        <div class="form-group">
                            <label>CTCSS Tx:</label>
                            <input type="number" step="0.00001" class="form-control" value="<?= isset($data['txCtcss']) ? $data['txCtcss']: '' ?>" name="txCtcss">
                        </div>
                        <div class="form-group">
                            <label>CTCSS Porteuse:</label>
                            <input type="number" step="0.00001" class="form-control" value="<?= isset($data['niveauCtcss']) ? $data['niveauCtcss']: '' ?>" name="niveauCtcss">
                        </div>
                        <div class="form-group">
                            <label>Puissance d'émission:</label>
                            <input type="number" step="0.01" class="form-control" value="<?= isset($data['txPower']) ? $data['txPower']: '' ?>" name="txPower">
                        </div>
                        <div class="form-group">
                            <label>Code d'appel selectif:</label>
                            <input type="text" class="form-control" value="<?= isset($data['appelSelectifCode']) ? $data['appelSelectifCode']: '' ?>" name="appelSelectifCode">
                        </div>
                        <div class="form-group">
                            <label>Porteuse d'appel selectif:</label>
                            <input type="number" step="0.01" class="form-control" value="<?= isset($data['appelSelectifPorteuse']) ? $data['appelSelectifPorteuse']: '' ?>" name="appelSelectifPorteuse">
                        </div>
                        <div class="form-group">
                            <label>Let:</label>
                            <input type="number" class="form-control" value="<?= isset($data['let']) ? $data['let']: '' ?>" name="let">
                        </div>
                        <div class="form-group">
                            <label>NoTone:</label>
                            <input type="number" class="form-control" value="<?= isset($data['notone']) ? $data['notone']: '' ?>" name="notone">
                        </div>
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3"
                                      name="remarquesCanal"><?= isset($data['remarquesCanal']) ? $data['remarquesCanal'] : '' ?></textarea>
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

