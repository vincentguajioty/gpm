<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['fournisseurs_lecture']==1 OR $_SESSION['fournisseurs_modification']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
    	{
		    $query = $db->prepare('SELECT *, AES_DECRYPT(aesFournisseur, :aesKey) as aesFournisseurDecode FROM FOURNISSEURS WHERE idFournisseur=:idFournisseur;');
		    $query->execute(array('idFournisseur' => $_GET['id'], 'aesKey'=>$_SESSION['aesFour']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalFournisseursAESUpdate">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Modification des informations chiffrées d'un fournisseur</h4>
                </div>
                <form role="form" action="fournisseursAESUpdate.php?id=<?= $_GET['id'] ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <textarea class="form-control" rows="3" name="aesFournisseur"><?= isset($data['aesFournisseurDecode']) ? $data['aesFournisseurDecode'] : '' ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>