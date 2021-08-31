<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['categories_lecture']==1 OR $_SESSION['categories_ajout']==1 OR $_SESSION['categories_modification']==1 OR $_SESSION['categories_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM MATERIEL_CATEGORIES WHERE idCategorie=:idCategorie;');
		    $query->execute(array('idCategorie' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalCategorieAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'une catégorie</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'categoriesUpdate.php?id='.$_GET['id'] : 'categoriesAdd.php'?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé</label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleCategorie']) ? $data['libelleCategorie'] : ''?>" name="libelleCategorie" required>
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