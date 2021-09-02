<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['cout_ajout']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout=:idCentreDeCout;');
		    $query->execute(array('idCentreDeCout' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}

    ?>
    
    <div class="modal fade" id="modalCoutAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un centre de cout</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'centreCoutsUpdate.php?id='.$_GET['id'] : 'centreCoutsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" name="libelleCentreDecout" required value="<?= isset($data['libelleCentreDecout']) ? $data['libelleCentreDecout'] : '' ?>">
                        </div>
                        <div class="row">
                        	<div class="col-md-6">
                        		<div class="form-group">
                                    <label>Date d'ouverture:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input class="input-datepicker form-control" name="dateOuverture" value="<?= isset($data['dateOuverture']) ? $data['dateOuverture'] : '' ?>">
                                    </div>
                                </div>
                        	</div>
                        	<div class="col-md-6">
                        		<div class="form-group">
                                    <label>Date de fermeture:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input class="input-datepicker form-control" name="dateFermeture" value="<?= isset($data['dateFermeture']) ? $data['dateFermeture'] : '' ?>">
                                    </div>
                                </div>
                        	</div>
                        </div>
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3" name="commentairesCentreCout"><?= isset($data['commentairesCentreCout']) ? $data['commentairesCentreCout'] : '' ?></textarea>
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
