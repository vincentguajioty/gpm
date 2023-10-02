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
        $query = $db->prepare('SELECT * FROM VHF_EQUIPEMENTS WHERE idVhfEquipement =:idVhfEquipement;');
        $query->execute(array('idVhfEquipement' => $_GET['id']));
        $data = $query->fetch();
        $query->closeCursor();
    }
    ?>

    <div class="modal fade" id="modalEquipementsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un équipement radio</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vhfEquipementsUpdate.php?id='.$_GET['id'] : 'vhfEquipementsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Indicatif: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['vhfIndicatif']) ? $data['vhfIndicatif']: '' ?>" name="vhfIndicatif" required>
                        </div>
                        <div class="form-group">
                            <label>Marque/Modèle:</label>
                            <input type="text" class="form-control" value="<?= isset($data['vhfMarqueModele']) ? $data['vhfMarqueModele']: '' ?>" name="vhfMarqueModele">
                        </div>
                        <div class="form-group">
                            <label>S/N:</label>
                            <input type="text" class="form-control" value="<?= isset($data['vhfSN']) ? $data['vhfSN']: '' ?>" name="vhfSN">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type: </label>
                                    <select class="form-control select2" style="width: 100%;" name="idVhfType">
                                        <option value="">--- Pas Spécifié ---</option>
                                        <?php
                                        $query2 = $db->query('SELECT * FROM VHF_TYPES_EQUIPEMENTS ORDER BY libelleType;');
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <option value ="<?php echo $data2['idVhfType']; ?>" <?php if (isset($data['idVhfType']) AND ($data2['idVhfType'] == $data['idVhfType'])) { echo 'selected'; } ?> ><?php echo $data2['libelleType']; ?></option>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Etat: </label>
                                    <select class="form-control select2" style="width: 100%;" name="idVhfEtat">
                                        <option value="">--- Pas Spécifié ---</option>
                                        <?php
                                        $query2 = $db->query('SELECT * FROM VHF_ETATS ORDER BY libelleVhfEtat;');
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <option value ="<?php echo $data2['idVhfEtat']; ?>" <?php if (isset($data['idVhfEtat']) AND ($data2['idVhfEtat'] == $data['idVhfEtat'])) { echo 'selected'; } ?> ><?php echo $data2['libelleVhfEtat']; ?></option>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Personne responsable de l'équipement: </label>
                                    <select class="form-control select2" style="width: 100%;" name="idResponsable">
                                        <option value="">--- Pas de Responsable ---</option>
                                        <?php
                                        $query2 = $db->query('SELECT * FROM VIEW_PERSONNE_REFERENTE ORDER BY identifiant;');
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <option value ="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idResponsable']) AND ($data2['idPersonne'] == $data['idResponsable'])) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Plan de fréquence appliqué: </label>
                                    <select class="form-control select2" style="width: 100%;" name="idVhfPlan">
                                        <option value="">--- Poste non-programmé ---</option>
                                        <?php
                                        $query2 = $db->query('SELECT * FROM VHF_PLAN ORDER BY libellePlan;');
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <option value ="<?php echo $data2['idVhfPlan']; ?>" <?php if (isset($data['idVhfPlan']) AND ($data2['idVhfPlan'] == $data['idVhfPlan'])) { echo 'selected'; } ?> ><?php echo $data2['libellePlan']; ?></option>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date de la dernière programmation:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input class="input-datepicker form-control" name="dateDerniereProg" value="<?= isset($data['dateDerniereProg']) ? $data['dateDerniereProg'] : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Remarques:</label>
                            <textarea class="form-control" rows="3"
                                      name="remarquesVhfEquipement"><?= isset($data['remarquesVhfEquipement']) ? $data['remarquesVhfEquipement'] : '' ?></textarea>
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

