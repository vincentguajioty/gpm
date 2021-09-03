<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if($_SESSION['codeBarre_lecture']==1 OR $_SESSION['codeBarre_ajout']==1 OR $_SESSION['codeBarre_modification']==1 OR $_SESSION['codeBarre_suppression']==1)
{?>
    
    <div class="modal fade" id="modalCodeFournisseurAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Enregistrer un nouveau code barre fournisseur</h4>
                </div>
                <form role="form" class="spinnerAttenteSubmit" action="codesBarreFournisseurAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="form-group">
                                <label>Référence du catalogue: <small style="color:grey;">Requis</small></label>
                                <select class="form-control select2" style="width: 100%;" name="idMaterielCatalogue" required>
                                    <?php
                                    if(isset($_GET['idCommande']))
                                    {
                                        $query2 = $db->prepare('SELECT c.* FROM COMMANDES_MATERIEL m LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE idCommande = :idCommande ORDER BY libelleMateriel;');
                                        $query2->execute(array('idCommande'=>$_GET['idCommande']));
                                    }
                                    else
                                    {
                                        $query2 = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
                                    }

                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value ="<?php echo $data2['idMaterielCatalogue']; ?>" ><?php echo $data2['libelleMateriel']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Code Barre: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" name="codeBarre" required>
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

