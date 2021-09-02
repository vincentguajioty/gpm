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
		    $query = $db->prepare('SELECT * FROM CENTRE_COUTS_PERSONNES WHERE idGerant=:idGerant;');
		    $query->execute(array('idGerant' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}

    ?>
    
    <div class="modal fade" id="modalCoutAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un responsable de centre de cout</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'centreCoutsGerantUpdate.php?id='.$_GET['id'] : 'centreCoutsGerantAdd.php?idCentreDeCout='.$_GET['idCentreDeCout'] ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Personne:</label>
                            <select class="form-control select2" style="width: 100%;" name="idPersonne" required>
                                <option value=""></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM VIEW_HABILITATIONS WHERE cout_etreEnCharge=1 ORDER BY identifiant;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idPersonne']) AND ($data2['idPersonne'] == $data['idPersonne'])) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Montant maximal de validation de commandes:</label>
                            <input type="number" min="0" step="1" class="form-control" value="<?= isset($data['montantMaxValidation']) ? $data['montantMaxValidation'] : '' ?>" name="montantMaxValidation">
                        </div>
                        <center><i>Validation illimitée: laisser le champ vide. Bloquer toute validation: mettre "0"</i></center>
                        </br>
                        <div class="form-group">
                            <label>Droits étendus:</label>
                            </br>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="depasseBudget" <?php if (isset($_GET['id']) AND ($data['depasseBudget']==1)) {echo 'checked';} ?>> Solde insuffisant: Cet utilisateur peut valider des commandes même si le centre de couts n'a pas le solde suffisant pour supporter cette commande.
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="validerClos" <?php if (isset($_GET['id']) AND ($data['validerClos']==1)) {echo 'checked';} ?>> Centre clos: Cet utilisateur peut valider des commandes même si le centre de couts est clos (date d'ouverture pas atteinte ou date de fermeture passée)
                                </label>
                            </div>
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
