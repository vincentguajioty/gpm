<?php
session_start();
require_once('logCheck.php');
require_once('config/config.php');
require_once('config/bdd.php');
?>


<?php if(($_SESSION['commande_lecture']==1 OR $_SESSION['commande_ajout']==1 OR $_SESSION['commande_valider']==1 OR $_SESSION['commande_etreEnCharge']==1 OR $_SESSION['commande_abandonner']==1) AND in_array(1, $_SESSION['modals'])){?>
    <div class="modal fade" id="modalCommandesAddItem">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= (isset($_GET['idElement'])) ? 'Modifier' : 'Ajouter' ?> un élément à la commande</h4>
                </div>
                <form role="form" action="<?= (isset($_GET['idElement'])) ? 'commandeItemUpdate.php?idCommande='.$_GET['idCommande'].'&idElement='.$_GET['idElement'] : 'commandeItemAdd.php?idCommande='.$_GET['idCommande'] ?>" method="POST">
                    <div class="modal-body">
                    <div class="form-group">
                        <label>Matériel:<small style="color:grey;"> Requis</small></label>
                        <select <?= (isset($_GET['idElement'])) ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idMaterielCatalogue">
                            <option value="-1">Frais de port</option>
                            <?php
                            $query2 = $db->prepare('SELECT c.idMaterielCatalogue, c.libelleMateriel FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN (SELECT idMaterielCatalogue FROM COMMANDES_MATERIEL WHERE idCommande= :idCommande) o ON c.idMaterielCatalogue = o.idMaterielCatalogue WHERE o.idMaterielCatalogue IS NULL ORDER BY libelleMateriel;');
                            $query2->execute(array('idCommande' => $_GET['idCommande']));
                            while ($data2 = $query2->fetch())
                            {
                                ?>
                                <option <?php if (isset($data['idMaterielCatalogue']) AND ($data['idMaterielCatalogue'] == $data2['idMaterielCatalogue'])){ echo 'selected'; }?> value="<?php echo $data2['idMaterielCatalogue']; ?>"><?php echo $data2['libelleMateriel']; ?></option>
                                <?php
                            }
                            $query2->closeCursor(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantité:<small style="color:grey;"> Requis</small></label>
                        <input type="number" class="form-control" name="quantiteCommande" value="<?= isset($data['quantiteCommande']) ? $data['quantiteCommande'] : '' ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prix HT:</label>
                                <input type="number" step="0.01" class="form-control" name="prixProduitHT" value="<?= isset($data['prixProduitHT']) ? $data['prixProduitHT'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Taxe:</label>
                                <input type="number" step="0.01" class="form-control" name="taxeProduit" value="<?= isset($data['taxeProduit']) ? $data['taxeProduit'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prix TTC:<small style="color:grey;"> Requis</small></label>
                                <input type="number" step="0.01" class="form-control" name="prixProduitTTC" required value="<?= isset($data['prixProduitTTC']) ? $data['prixProduitTTC'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Remise:</label>
                                <input type="number" step="0.01" class="form-control" name="remiseProduit" value="<?= isset($data['remiseProduit']) ? $data['remiseProduit'] : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Référence produit chez le fournisseur:</label>
                        <input type="text" class="form-control" name="referenceProduitFournisseur" value="<?= isset($data['referenceProduitFournisseur']) ? $data['referenceProduitFournisseur'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Remarques:</label>
                        <textarea class="form-control" rows="3" name="remarqueArticle"><?= isset($data['remarqueArticle']) ? $data['remarqueArticle'] : '' ?></textarea>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right"><?= isset($_GET['idElement']) ? 'Modifier' : 'Ajouter' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<?php if(($_SESSION['commande_lecture']==1 OR $_SESSION['commande_ajout']==1 OR $_SESSION['commande_valider']==1 OR $_SESSION['commande_etreEnCharge']==1 OR $_SESSION['commande_abandonner']==1) AND in_array(100, $_SESSION['modals'])){?>
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




<?php if(($_SESSION['lots_lecture']==1 OR $_SESSION['lots_ajout']==1 OR $_SESSION['lots_modification']==1 OR $_SESSION['lots_suppression']==1) AND in_array(2, $_SESSION['modals'])) {?>
    <div class="modal fade" id="modalLotsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un lot</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'lotsUpdate.php?id='.$_GET['id'] : 'lotsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:</label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleLot']) ? $data['libelleLot']: '' ?>"
                                   name="libelleLot" required>
                        </div>
                        <div class="form-group">
                            <label>Référentiel à respecter: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleTypeLot">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LOTS_TYPES ORDER BY libelleTypeLot;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idTypeLot']; ?>" <?php if (isset($data['idTypeLot']) AND ($data2['idTypeLot'] == $data['idTypeLot'])) { echo 'selected'; } ?> ><?php echo $data2['libelleTypeLot']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Etat: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleEtat">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM ETATS ORDER BY libelleEtat;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idEtat']; ?>" <?php if (isset($data['idEtat']) AND ($data2['idEtat'] == $data['idEtat'])) { echo 'selected'; } ?> ><?php echo $data2['libelleEtat']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lieu de stockage:</label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLieu">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LIEUX ORDER BY libelleLieu;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idLieu']; ?>" <?php if (isset($data['idLieu']) AND ($data2['idLieu'] == $data['idLieu'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLieu']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Personne référente</label>
                            <select class="form-control select2" style="width: 100%;" name="identifiant">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY identifiant;');
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
                            <label>Date du dernier inventaire:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateDernierInventaire" value="<?= isset($data['dateDernierInventaire']) ? $data['dateDernierInventaire'] : '' ?>">
                            </div>
                            <!-- /.input group -->
                        </div>
                        <div class="form-group">
                            <label>Fréquence inventaire (jours):</label>
                            <input type="number" class="form-control" value="<?= isset($data['frequenceInventaire']) ? $data['frequenceInventaire'] : '' ?>"
                                   name="frequenceInventaire">
                        </div>

                        <!-- textarea -->
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3"
                                      name="commentairesLots"><?= isset($data['commentairesLots']) ? $data['commentairesLots'] : '' ?></textarea>
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




<?php if(($_SESSION['sac_lecture']==1 OR $_SESSION['sac_ajout']==1 OR $_SESSION['sac_modification']==1 OR $_SESSION['sac_suppression']==1) AND in_array(3, $_SESSION['modals'])) {?>
    <div class="modal fade" id="modalSacsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un sac</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'sacsUpdate.php?id='.$_GET['id'] : 'sacsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:</label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleSac']) ? $data['libelleSac']: ''?>"
                                   name="libelleSac" required>
                        </div>
                        <div class="form-group">
                            <label>Lot d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLot">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idLot']; ?>" <?php if (isset($data['idLot']) AND ($data2['idLot'] == $data['idLot'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLot']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" value="<?= isset($data['taille']) ? $data['taille']: ''?>"
                                   name="taille">
                        </div>
                        <div class="form-group">
                            <label>Couleur:</label>
                            <input type="text" class="form-control" value="<?= isset($data['couleur']) ? $data['couleur']: ''?>"
                                   name="couleur">
                        </div>
                        <div class="form-group">
                            <label>Fournisseur: </label>
                            <select class="form-control select2" style="width: 100%;" name="nomFournisseur">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idFournisseur']; ?>" <?php if (isset($data['idFournisseur']) AND ($data2['idFournisseur'] == $data['idFournisseur'])) { echo 'selected'; } ?> ><?php echo $data2['nomFournisseur']; ?></option>
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



<?php if(($_SESSION['sac2_lecture']==1 OR $_SESSION['sac2_ajout']==1 OR $_SESSION['sac2_modification']==1 OR $_SESSION['sac2_suppression']==1) AND in_array(4, $_SESSION['modals'])) {?>
    <div class="modal fade" id="modalEmplacementAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un emplacement</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'emplacementsUpdate.php?id='.$_GET['id'] : 'emplacementsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:</label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleEmplacement']) ? $data['libelleEmplacement'] : ''?>"
                                   name="libelleEmplacement" required>
                        </div>
                        <div class="form-group">
                            <label>Lot d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLot">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idLot']; ?>" <?php if (isset($data['idLot']) AND ($data2['idLot'] == $data['idLot'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLot']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sac d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleSac">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM MATERIEL_SAC ORDER BY libelleSac;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idSac']; ?>" data-id="<?php echo $data2['idLot']; ?>" <?php if (isset($data['idSac']) AND ($data2['idSac'] == $data['idSac'])) { echo 'selected'; } ?> ><?php echo $data2['libelleSac']; ?></option>
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




<?php if(($_SESSION['materiel_lecture']==1 OR $_SESSION['materiel_ajout']==1 OR $_SESSION['materiel_modification']==1 OR $_SESSION['materiel_suppression']==1) AND in_array(5, $_SESSION['modals'])) {?>
    <div class="modal fade" id="modalMaterielAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un Materiel</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'materielsUpdate.php?id='.$_GET['id'] : 'materielsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Référence du catalogue:</label>
                            <select class="form-control select2" style="width: 100%;" name="libelleMateriel">
                                <?php
                                $query2 = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idMaterielCatalogue']; ?>"<?php if (isset($data['idMaterielCatalogue']) AND ($data2['idMaterielCatalogue'] == $data['idMaterielCatalogue'])) { echo 'selected'; } ?> ><?php echo $data2['libelleMateriel']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lot: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLot">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idLot']; ?>"<?php if (isset($data['idLot']) AND ($data2['idLot'] == $data['idLot'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLot']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sac: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleSac">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleSac;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idSac']; ?>" data-id="<?php echo $data2['idLot']; ?>" <?php if (isset($data['idSac']) AND ($data2['idSac'] == $data['idSac'])) { echo 'selected'; } ?> data-id="<?php echo $data['libelleLot']; ?>"><?php echo $data2['libelleSac']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Emplacement: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleEmplacement">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleEmplacement;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idEmplacement']; ?>" data-id="<?php echo $data2['idSac']; ?>" <?php if (isset($data['idEmplacement']) AND ($data2['idEmplacement'] == $data['idEmplacement'])) { echo 'selected'; } ?> data-id="<?php echo $data['libelleSac']; ?>"><?php echo $data2['libelleEmplacement']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fournisseur:</label>
                            <select class="form-control select2" style="width: 100%;" name="nomFournisseur">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idFournisseur']; ?>" <?php if (isset($data['idFournisseur']) AND ($data2['idFournisseur'] == $data['idFournisseur'])) { echo 'selected'; } ?> ><?php echo $data2['nomFournisseur']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantité:</label>
                            <input type="text" class="form-control"  value="<?= isset($data['quantite']) ? $data['quantite'] : '' ?>"
                                   name="quantite">
                        </div>
                        <div class="form-group">
                            <label>Quantité d'Alerte:</label>
                            <input type="text" class="form-control"  value="<?= isset($data['quantiteAlerte']) ? $data['quantiteAlerte'] : '' ?>"
                                   name="quantiteAlerte">
                        </div>

                        <div class="checkbox">
                            <label>
                                <input
                                    <?php
                                    if (isset($data['peremption']) AND ($data['peremption'] != Null))
                                    {
                                        echo 'checked';
                                    }
                                    ?>
                                        type="checkbox" value="1" name="boolPeremption" onClick="montrer_cacher(this,'perem')"> Le matériel a une date de péremption
                            </label>
                        </div>

                        <div class="form-group" id="perem">
                            <label>Date de péremption:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="peremption" value="<?= isset($data['peremption']) ? $data['peremption'] : '' ?>">
                            </div>
                            <br/>
                            <label>Jours d'anticipation de l'alerte de péremption:</label>
                            <input type="text" class="form-control" value="<?= isset($data['peremption']) ? ((strtotime($data['peremption']) - strtotime($data['peremptionNotification']))/86400) : '' ?>"
                                   name="delaisPeremption">
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="commentairesElement"><?= isset($data['commentairesElement']) ? $data['commentairesElement'] : '' ?></textarea>
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




<?php if(($_SESSION['catalogue_lecture']==1 OR $_SESSION['catalogue_ajout']==1 OR $_SESSION['catalogue_modification']==1 OR $_SESSION['catalogue_suppression']==1) AND in_array(6, $_SESSION['modals'])) {?>
    <div class="modal fade" id="modalCatalogueAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un élément dans le catalogue</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'catalogueUpdate.php?id='.$_GET['id'] :  'catalogueAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Libellé de l'item à ajouter" value="<?= isset($data['libelleMateriel']) ? $data['libelleMateriel'] : '' ?>" name="libelleMateriel" required>
                        </div>
                        <div class="form-group">
                            <label>Catégorie: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleCategorie">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM MATERIEL_CATEGORIES ORDER BY libelleCategorie;');
                                while ($data2 = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idCategorie']; ?>" <?php if (isset ($data['libelleCategorie']) AND $data2['libelleCategorie'] == $data['libelleCategorie']) { echo 'selected'; } ?> ><?php echo $data2['libelleCategorie']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" placeholder="5cmx5cm, L, Adulte, Pédiatrique" value="<?= isset($data['taille']) ? $data['taille'] : '' ?>" name="taille">
                        </div>
                        <div class="form-group">
                            <label>Stérilité:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="sterilite" id="optionsRadios1" value="option1" <?php if (isset($data['sterilite']) AND $data['sterilite']==0) echo 'checked' ?>>
                                    Matériel non-stérile
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="sterilite" id="optionsRadios2" value="option2" <?php if (isset($data['sterilite']) AND $data['sterilite']==1) echo 'checked' ?>>
                                    Matériel stérile
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Conditionnement:</label>
                            <input type="text" class="form-control" placeholder="3 compresses par sachet ..." value="<?= isset($data['conditionnementMultiple']) ? $data['conditionnementMultiple'] : '' ?>"
                                   name="conditionnementMultiple">
                        </div>
                        <div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="commentairesMateriel"><?= isset($data['commentairesMateriel']) ? $data['commentairesMateriel'] : '' ?></textarea>
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





<?php if(($_SESSION['categories_lecture']==1 OR $_SESSION['categories_ajout']==1 OR $_SESSION['categories_modification']==1 OR $_SESSION['categories_suppression']==1) AND in_array(7, $_SESSION['modals'])) { ?>
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




<?php if(($_SESSION['lieux_lecture']==1 OR $_SESSION['lieux_ajout']==1 OR $_SESSION['lieux_modification']==1 OR $_SESSION['lieux_suppression']==1) AND in_array(8, $_SESSION['modals'])) { ?>
    <div class="modal fade" id="modalLieuxAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un lieu</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'lieuxUpdate.php?id='.$_GET['id'] : 'lieuxAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé du lieu de stockage:</label>
                            <input type="text" class="form-control" value="<?= isset($data['libelleLieu']) ? $data['libelleLieu'] : '' ?>" name="libelleLieu" required>
                        </div>
                        <!-- textarea -->
                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea class="form-control" rows="3" name="adresseLieu"><?= isset($data['adresseLieu']) ? $data['adresseLieu'] : '' ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Contrôle d'accès:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="accesReserve" id="optionsRadios1" value="option1"
                                        <?php if (isset($data['accesReserve']) AND $data['accesReserve']==0)
                                            echo 'checked'?>
                                    >
                                    Accès libre
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="accesReserve" id="optionsRadios2" value="option2"
                                        <?php if (isset($data['accesReserve']) AND $data['accesReserve']==1)
                                            echo 'checked'?>
                                    >
                                    Accès règlementé
                                </label>
                            </div>
                        </div>

                        <!-- textarea -->
                        <div class="form-group">
                            <label>Détails</label>
                            <textarea class="form-control" rows="3" name="detailsLieu"><?= isset($data['detailsLieu']) ? $data['detailsLieu'] : '' ?></textarea>
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






<?php if(($_SESSION['fournisseurs_lecture']==1 OR $_SESSION['fournisseurs_ajout']==1 OR $_SESSION['fournisseurs_modification']==1 OR $_SESSION['fournisseurs_suppression']==1) AND in_array(9, $_SESSION['modals'])) { ?>
    <div class="modal fade" id="modalFournisseursAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un Fournisseur</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'fournisseursUpdate.php?id='.$_GET['id'] : 'fournisseursAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom:<small style="color:grey;"> Requis</small></label>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <!-- TODO : modifier -->
                        <button type="submit" class="btn btn-primary pull-right"><?= isset($_GET['id']) ? 'Modifier' : 'Ajouter'?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>





<?php if(($_SESSION['annuaire_lecture']==1 OR $_SESSION['annuaire_ajout']==1 OR $_SESSION['annuaire_modification']==1 OR $_SESSION['annuaire_suppression']==1 OR $_SESSION['annuaire_mdp']==1) AND in_array(10, $_SESSION['modals'])) { ?>
    <div class="modal fade" id="modalAnnuaireAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un compte utilisateur</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'annuaireUpdate.php?id='.$_GET['id'] : 'annuaireAdd.php'?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Identifiant de connexion:</label>
                            <input type="text" class="form-control" value="<?= isset($data['identifiant']) ? $data['identifiant'] : ''?>"
                                   name="identifiant" required>
                        </div>
                        <div class="form-group">
                            <label>Nom:</label>
                            <input type="text" class="form-control" value="<?= isset($data['nomPersonne']) ? $data['nomPersonne'] : ''?>"
                                   name="nomPersonne">
                        </div>
                        <div class="form-group">
                            <label>Prénom:</label>
                            <input type="text" class="form-control" value="<?= isset($data['prenomPersonne']) ? $data['prenomPersonne'] : ''?>"
                                   name="prenomPersonne">
                        </div>
                        <div class="form-group">
                            <label>Adresse mail:</label>
                            <input type="text" class="form-control" value="<?= isset($data['mailPersonne']) ? $data['mailPersonne'] : ''?>"
                                   name="mailPersonne">
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" class="form-control" value="<?= isset($data['telPersonne']) ? $data['telPersonne'] : ''?>"
                                   name="telPersonne">
                        </div>
                        <div class="form-group">
                            <label>Fonction:</label>
                            <input type="text" class="form-control" value="<?= isset($data['fonction']) ? $data['fonction'] : ''?>"
                                   name="fonction">
                        </div>
                        <div class="form-group">
                            <label>Profil d'habilitation: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleProfil">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM PROFILS;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idProfil']; ?>" <?php if (isset($data['idProfil']) AND ($data2['idProfil'] == $data['idProfil'])) { echo 'selected'; } ?> ><?php echo $data2['libelleProfil']; ?></option>
                                    <?php
                                }
                                $query->closeCursor();
                                $query2->closeCursor();?>
                            </select>
                        </div>
                        <?php
                            if(!isset($_GET['id']))
                            { ?>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" value="1" name="notificationMailCreation" checked> Envoyer un email à l'utilisateur avec son identifiant et son mot de passe
                                        </label>
                                    </div>
                                </div>
                            <?php }
                        ?>
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






<?php if(($_SESSION['cout_lecture']==1 OR $_SESSION['cout_ajout']==1 OR $_SESSION['cout_etreEnCharge']==1 OR $_SESSION['cout_supprimer']==1) AND in_array(11, $_SESSION['modals'])) { ?>
    <div class="modal fade" id="modalCoutAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création' ?> d'un centre de cout</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'centreCoutsUpdate.php?id='.$_GET['id'] : 'centreCoutsAdd.php' ?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:</label>
                            <input type="text" class="form-control" name="libelleCentreDecout" required value="<?= isset($data['libelleCentreDecout']) ? $data['libelleCentreDecout'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Personne référente</label>
                            <select class="form-control select2" style="width: 100%;" name="idResponsable">
                                <option></option>
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




<?php if(($_SESSION['messages_ajout']==1) AND in_array(12, $_SESSION['modals'])) { ?>
    <div class="modal fade" id="modalMessageGeneralAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ajout d'un message général</h4>
                </div>
                <form role="form" action="messagesAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                                    <label>Titre:</label>
                                    <input type="text" class="form-control" placeholder="Titre du message"
                                           name="titreMessage" required>
                                </div>
                                <!-- textarea -->
                                <div class="form-group">
                                    <label>Message:</label>
                                    <textarea class="form-control" rows="3" placeholder="Corps du message"
                                              name="corpsMessage"></textarea>
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




<?php if(($_SESSION['typesLots_modification']==1) AND in_array(13, $_SESSION['modals'])) { ?>
    <div class="modal fade" id="modalReferentielContenu">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ajout d'un matériel au référentiel</h4>
                </div>
                <form role="form" action="referentielsAddItem.php?idLot=<?=$_GET['id']?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Matériel: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleMateriel">
                                <?php
                                $query = $db->prepare('SELECT c.idMaterielCatalogue, c.libelleMateriel FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN (SELECT idMaterielCatalogue FROM REFERENTIELS WHERE idTypeLot= :idTypeLot) r ON c.idMaterielCatalogue = r.idMaterielCatalogue WHERE r.idMaterielCatalogue IS NULL ORDER BY libelleMateriel;');
                                $query->execute(array('idTypeLot' => $_GET['id']));
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idMaterielCatalogue']; ?>"><?php echo $data['libelleMateriel']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantité:</label>
                            <input type="text" class="form-control" placeholder="1-2-3 ..."
                                   name="quantiteReferentiel">
                        </div>
                        <div class="form-group">
                            <label>Obligation:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="obligatoire" id="optionsRadios1" value="option1" checked>
                                    Ce matériel est obligatoire.
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="obligatoire" id="optionsRadios2" value="option2">
                                    Ce matériel est facultatif.
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Commentaires:</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="commentairesReferentiel"></textarea>
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




<?php if(($_SESSION['profils_ajout']==1) AND in_array(14, $_SESSION['modals'])) { ?>
    <div class="modal fade" id="modalProfilsDupliquer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Dupliquer un profil</h4>
                </div>
                <form role="form" action="profilsDupliquer.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Profil à dupliquer: </label>
                            <select class="form-control select2" style="width: 100%;" name="idProfil">
                                <?php
                                $query = $db->prepare('SELECT * FROM PROFILS ORDER BY libelleProfil;');
                                $query->execute(array('idTypeLot' => $_GET['id']));
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idProfil']; ?>"><?php echo $data['libelleProfil']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Dupliquer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
