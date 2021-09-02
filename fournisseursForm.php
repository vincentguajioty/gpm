<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['fournisseurs_lecture']==1 OR $_SESSION['fournisseurs_ajout']==1 OR $_SESSION['fournisseurs_modification']==1 OR $_SESSION['fournisseurs_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT * FROM FOURNISSEURS WHERE idFournisseur=:idFournisseur;');
		    $query->execute(array('idFournisseur' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalFournisseursAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un Fournisseur</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'fournisseursUpdate.php?id='.$_GET['id'] : 'fournisseursAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" placeholder="Entrez le nom du fournisseur" value="<?= isset($data['nomFournisseur']) ? $data['nomFournisseur'] : '' ?>" name="nomFournisseur" required>
                        </div>
                        <!-- textarea -->
                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea class="form-control" rows="3" placeholder="Entez l'adresse du fournisseur" name="adresseFournisseur"><?= isset($data['adresseFournisseur']) ? $data['adresseFournisseur'] : '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Téléphone:</label>
                            <input type="tel" class="form-control" placeholder="Entrez le numéro de téléphone du fournisseur" value="<?= isset($data['telephoneFournisseur']) ? $data['telephoneFournisseur'] : '' ?>" name="telephoneFournisseur">
                        </div>
                        <div class="form-group">
                            <label>eMail:</label>
                            <input type="email" class="form-control" placeholder="Entrez l'eMail du fournisseur" value="<?= isset($data['mailFournisseur']) ? $data['mailFournisseur'] : '' ?>" name="mailFournisseur">
                        </div>
                        <div class="form-group">
                            <label>Site Web:</label>
                            <input type="text" class="form-control" placeholder="Entrez l'adresse internet du site du fournisseur" value="<?= isset($data['siteWebFournisseur']) ? $data['siteWebFournisseur'] : '' ?>" name="siteWebFournisseur">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right"><?= isset($_GET['id']) ? 'Modifier' : 'Ajouter'?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>