<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['cout_lecture']==1 OR $_SESSION['cout_ajout']==1 OR $_SESSION['cout_etreEnCharge']==1 OR $_SESSION['cout_supprimer']==1)
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
                        <div class="form-group">
                            <label>Personne référente</label>
                            <select class="form-control select2" style="width: 100%;" name="idResponsable">
                                <option value="">--- Aucun Référent ---</option>
                                <?php
                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE cout_etreEnCharge=1 ORDER BY identifiant;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idResponsable']) AND ($data2['idPersonne'] == $data['idResponsable'])){echo 'selected'; }?>><?php echo $data2['identifiant']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
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
