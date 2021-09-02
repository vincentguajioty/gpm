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
                        <div class="form-group">
                            <label>Personnes responsables: </label>
                            <select class="form-control select2" style="width: 100%;" name="idPersonne[]" multiple>
                                <?php
                                $query2 = $db->prepare('
                                    SELECT
                                        ao.*,
                                        (SELECT idCentreDeCout FROM CENTRE_COUTS_PERSONNES aop WHERE ao.idPersonne = aop.idPersonne AND aop.idCentreDeCout = :idCentreDeCout) as idCentreDeCout
                                    FROM
                                        PERSONNE_REFERENTE ao
                                        JOIN VIEW_HABILITATIONS h ON ao.idPersonne = h.idPersonne
                                    WHERE
                                        cout_etreEnCharge = 1
                                    ORDER BY
                                        nomPersonne,
                                        prenomPersonne;');
				                $query2->execute(array('idCentreDeCout' => $_GET['id']));

                                while ($data2 = $query2->fetch())
                                {
                                    
                                    echo '<option value=' . $data2['idPersonne'];

					                if (isset($data2['idCentreDeCout']) AND $data2['idCentreDeCout'])
					                {
					                    echo " selected ";
					                }
					                echo '>' . $data2['identifiant'] . '</option>';
                                }
                                
                                $query2->closeCursor(); ?>
                            </select>
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
