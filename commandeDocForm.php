<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if($_SESSION['commande_lecture']==1 OR $_SESSION['commande_ajout']==1 OR $_SESSION['commande_valider']==1 OR $_SESSION['commande_etreEnCharge']==1 OR $_SESSION['commande_abandonner']==1)
{?>
    <div class="modal fade" id="modalCommandesDocAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Attacher un document à la commande</h4>
                </div>
                <form role="form" action="commandeDocAdd.php?idCommande=<?=$_GET['idCommande']?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Document à charger:<small style="color:grey;"> Requis</small></label>
                            <input style="width: 100%;" type="file" name="urlFichierDocCommande" id="urlFichierDocCommande" required>
                        </div>
                        <div class="form-group">
                            <label>Nom du document:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" name="nomDocCommande" required>
                        </div>
                        <div class="form-group">
                            <label>Type de document:<small style="color:grey;"> Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idTypeDocument">
                                <?php
                                $query2 = $db->query('SELECT * FROM DOCUMENTS_TYPES ORDER BY libelleTypeDocument;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idTypeDocument']; ?>"><?php echo $data2['libelleTypeDocument']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>