<?php
session_start();
require_once('logCheck.php');
require_once('config/config.php');
require_once('config/bdd.php');

?>

<!-- COMMANDES -->
<?php if($_SESSION['commande_lecture']==1 OR $_SESSION['commande_ajout']==1 OR $_SESSION['commande_valider']==1 OR $_SESSION['commande_etreEnCharge']==1 OR $_SESSION['commande_abandonner']==1){?>
    <div class="modal fade" id="modalCommandesAddItem">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ajouter un élément à la commande</h4>
                </div>
                <form role="form" action="commandeItemAdd.php?idCommande=<?=$_GET['id']?>" method="POST">
                    <div class="modal-body">
                    <div class="form-group">
                        <label>Matériel:<small style="color:grey;"> Requis</small></label>
                        <select class="form-control select2" style="width: 100%;" name="idMaterielCatalogue">
                            <option value="-1">Frais de port</option>
                            <?php
                            $query2 = $db->prepare('SELECT c.idMaterielCatalogue, c.libelleMateriel FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN (SELECT idMaterielCatalogue FROM COMMANDES_MATERIEL WHERE idCommande= :idCommande) o ON c.idMaterielCatalogue = o.idMaterielCatalogue WHERE o.idMaterielCatalogue IS NULL ORDER BY libelleMateriel;');
                            $query2->execute(array('idCommande' => $_GET['id']));
                            while ($data2 = $query2->fetch())
                            {
                                ?>
                                <option value="<?php echo $data2['idMaterielCatalogue']; ?>"><?php echo $data2['libelleMateriel']; ?></option>
                                <?php
                            }
                            $query->closeCursor(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantité:<small style="color:grey;"> Requis</small></label>
                        <input type="number" class="form-control" name="quantiteCommande" required>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prix HT:</label>
                                <input type="number" step="0.01" class="form-control" name="prixProduitHT">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Taxe:</label>
                                <input type="number" step="0.01" class="form-control" name="taxeProduit">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prix TTC:<small style="color:grey;"> Requis</small></label>
                                <input type="number" step="0.01" class="form-control" name="prixProduitTTC" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Remise:</label>
                                <input type="number" step="0.01" class="form-control" name="remiseProduit">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Référence produit chez le fournisseur:</label>
                        <input type="text" class="form-control" name="referenceProduitFournisseur">
                    </div>
                    <div class="form-group">
                        <label>Remarques:</label>
                        <textarea class="form-control" rows="3" name="remarqueArticle"></textarea>
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
    <div class="modal fade" id="modalCommandesDocAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Attacher un document à la commande</h4>
                </div>
                <form role="form" action="commandeDocAdd.php?idCommande=<?=$_GET['id']?>" method="POST" enctype="multipart/form-data">
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
    <div class="modal fade" id="modalCommandesUpdateItem">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Modifier un élément de la commande</h4>
                </div>
                <div class="modal-body">
                    EN COURS DE DEV
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalCommandesViewItem">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">ELEMENT</h4>
                </div>
                <div class="modal-body">
                    EN COURS DE DEV
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>



<!-- LOTS -->
<?php if($_SESSION['lots_lecture']==1 OR $_SESSION['lots_ajout']==1 OR $_SESSION['lots_modification']==1 OR $_SESSION['lots_suppression']==1){?>
    <div class="modal fade" id="modalLotsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un lot</h4>
                </div>
                <form role="form" action="lotsAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Entrez un nom pour ce lot" name="libelleLot" required>
                        </div>
                        <div class="form-group">
                            <label>Référentiel à respecter: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleTypeLot">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM LOTS_TYPES ORDER BY libelleTypeLot;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idTypeLot']; ?>"><?php echo $data['libelleTypeLot']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Etat: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleEtat">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM ETATS ORDER BY libelleEtat;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idEtat']; ?>"><?php echo $data['libelleEtat']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lieu de stockage:</label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLieu">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM LIEUX ORDER BY libelleLieu;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idLieu']; ?>"><?php echo $data['libelleLieu']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Personne référente</label>
                            <select class="form-control select2" style="width: 100%;" name="identifiant">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY identifiant;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idPersonne']; ?>"><?php echo $data['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date du dernier inventaire:<small style="color:grey;"> Requis</small></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateDernierInventaire" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Fréquence inventaire (jours):<small style="color:grey;"> Requis</small></label>
                            <input type="number" class="form-control" placeholder="Entrez un nombre de jours" name="frequenceInventaire" required>
                        </div>
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails" name="commentairesLots"></textarea>
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



<!-- SACS -->
<?php if($_SESSION['sac_lecture']==1 OR $_SESSION['sac_ajout']==1 OR $_SESSION['sac_modification']==1 OR $_SESSION['sac_suppression']==1){?>
    <div class="modal fade" id="modalSacsAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un sac</h4>
                </div>
                <form role="form" action="sacsAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Entrez un nom pour ce sac" name="libelleSac" required>
                        </div>
                        <div class="form-group">
                            <label>Lot d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLot">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idLot']; ?>"><?php echo $data['libelleLot']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" placeholder="100L, 50L, 50cmx50cm"
                                   name="taille">
                        </div>
                        <div class="form-group">
                            <label>Couleur:</label>
                            <input type="text" class="form-control" placeholder="Rouge, Bleue"
                                   name="couleur">
                        </div>
                        <div class="form-group">
                            <label>Fournisseur: </label>
                            <select class="form-control select2" style="width: 100%;" name="nomFournisseur">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idFournisseur']; ?>"><?php echo $data['nomFournisseur']; ?></option>
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


<!-- EMPLACEMENTS -->
<?php if($_SESSION['sac2_lecture']==1 OR $_SESSION['sac2_ajout']==1 OR $_SESSION['sac2_modification']==1 OR $_SESSION['sac2_suppression']==1){?>
    <div class="modal fade" id="modalEmplacementAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un emplacement</h4>
                </div>
                <form role="form" action="emplacementsAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Entrez un nom pour cet emplacement" name="libelleEmplacement" required>
                        </div>
                        <div class="form-group">
                            <label>Lot d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLot">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idLot']; ?>"><?php echo $data['libelleLot']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sac d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleSac">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleSac;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idSac']; ?>" data-id="<?php echo $data['idLot']; ?>"><?php echo $data['libelleSac']; ?></option>
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



<!-- MATERIEL -->
<?php if($_SESSION['materiel_lecture']==1 OR $_SESSION['materiel_ajout']==1 OR $_SESSION['materiel_modification']==1 OR $_SESSION['materiel_suppression']==1){?>
    <div class="modal fade" id="modalMaterielAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un Materiel</h4>
                </div>
                <form role="form" action="materielsAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Référence du catalogue:<small style="color:grey;"> Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="libelleMateriel">
                                <?php
                                $query = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
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
                            <label>Lot: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLot">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idLot']; ?>"><?php echo $data['libelleLot']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sac: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleSac">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleSac;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idSac']; ?>" data-id="<?php echo $data['idLot']; ?>"><?php echo $data['libelleSac']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Emplacement: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleEmplacement">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleEmplacement;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idEmplacement']; ?>" data-id="<?php echo $data['idSac']; ?>"><?php echo $data['libelleEmplacement']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fournisseur:</label>
                            <select class="form-control select2" style="width: 100%;" name="nomFournisseur">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idFournisseur']; ?>"><?php echo $data['nomFournisseur']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantité:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Quantité présente" name="quantite" required>
                        </div>
                        <div class="form-group">
                            <label>Quantité d'Alerte: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Permet de déclencher une alerte une fois ce seuil atteint" name="quantiteAlerte" required>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input checked type="checkbox" value="1" name="boolPeremption" onClick="montrer_cacher(this,'perem')"> Le matériel a une date de péremption
                            </label>
                        </div>

                        <div class="form-group" id="perem">
                            <label>Date de péremption:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="input-datepicker form-control" name="peremption">
                            </div>
                            <br/>
                            <label>Jours d'anticipation de l'alerte de péremption:</label>
                            <input type="text" class="form-control" placeholder="ex: Saisissez 5 pour recevoir une alerte à J-5 de la péremption"
                                   name="delaisPeremption">
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="commentairesElement"></textarea>
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



<!-- CATALOGUE -->
<?php if($_SESSION['catalogue_lecture']==1 OR $_SESSION['catalogue_ajout']==1 OR $_SESSION['catalogue_modification']==1 OR $_SESSION['catalogue_suppression']==1){?>
    <div class="modal fade" id="modalCatalogueAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un élément dans le catalogue</h4>
                </div>
                <form role="form" action="catalogueAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Libellé le l'item à ajouter" name="libelleMateriel" required>
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
                                    <option value="<?php echo $data2['idCategorie']; ?>"><?php echo $data2['libelleCategorie']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" placeholder="5cmx5cm, L, Adulte, Pédiatrique" name="taille">
                        </div>
                        <div class="form-group">
                            <label>Stérilité:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="sterilite" id="optionsRadios1" value="option1" checked>
                                    Matériel non-stérile
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="sterilite" id="optionsRadios2" value="option2">
                                    Matériel stérile
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Conditionnement:</label>
                            <input type="text" class="form-control" placeholder="3 compresses par sachet ..."
                                   name="conditionnementMultiple">
                        </div>
                        <div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="commentairesMateriel"></textarea>
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




<!-- CATEGORIES -->
<?php if($_SESSION['categories_lecture']==1 OR $_SESSION['categories_ajout']==1 OR $_SESSION['categories_modification']==1 OR $_SESSION['categories_suppression']==1) { ?>
    <div class="modal fade" id="modalCategorieAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'une catégorie</h4>
                </div>
                <form role="form" action="categoriesAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Libellé de la catégorie à ajouter" name="libelleCategorie" required>
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



<!-- LIEUX -->
<?php if($_SESSION['lieux_lecture']==1 OR $_SESSION['lieux_ajout']==1 OR $_SESSION['lieux_modification']==1 OR $_SESSION['lieux_suppression']==1) { ?>
    <div class="modal fade" id="modalLieuxAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un lieu</h4>
                </div>
                <form role="form" action="lieuxAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé du lieu de stockage:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Entrez un nom pour ce lieu" name="libelleLieu" required>
                        </div>
                        <!-- textarea -->
                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea class="form-control" rows="3" placeholder="Entez l'adresse du lieu" name="adresseLieu"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Contrôle d'accès:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="accesReserve" id="optionsRadios1" value="option1" checked>
                                    Accès libre
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="accesReserve" id="optionsRadios2" value="option2">
                                    Accès règlementé
                                </label>
                            </div>
                        </div>

                        <!-- textarea -->
                        <div class="form-group">
                            <label>Détails</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="detailsLieu"></textarea>
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





<!-- FOURNISSEURS -->
<?php if($_SESSION['fournisseurs_lecture']==1 OR $_SESSION['fournisseurs_ajout']==1 OR $_SESSION['fournisseurs_modification']==1 OR $_SESSION['fournisseurs_suppression']==1) { ?>
    <div class="modal fade" id="modalFournisseursAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un Fournisseur</h4>
                </div>
                <form role="form" action="fournisseursAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="Entrez le nom du fournisseur" name="nomFournisseur" required>
                        </div>
                        <!-- textarea -->
                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea class="form-control" rows="3" placeholder="Entez l'adresse du fournisseur" name="adresseFournisseur"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Téléphone:</label>
                            <input type="tel" class="form-control" placeholder="Entrez le numéro de téléphone du fournisseur" name="telephoneFournisseur">
                        </div>
                        <div class="form-group">
                            <label>eMail:</label>
                            <input type="email" class="form-control" placeholder="Entrez l'eMail du fournisseur" name="mailFournisseur">
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




<!-- ANNUAIRE -->
<?php if($_SESSION['annuaire_lecture']==1 OR $_SESSION['annuaire_ajout']==1 OR $_SESSION['annuaire_modification']==1 OR $_SESSION['annuaire_suppression']==1 OR $_SESSION['annuaire_mdp']==1) { ?>
    <div class="modal fade" id="modalAnnuaireAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un compte utilisateur</h4>
                </div>
                <form role="form" action="annuaireAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Identifiant de connexion:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" placeholder="prenom.nom" name="identifiant" required>
                        </div>
                        <div class="form-group">
                            <label>Nom:</label>
                            <input type="text" class="form-control" placeholder="Nom de famille" name="nomPersonne">
                        </div>
                        <div class="form-group">
                            <label>Prénom:</label>
                            <input type="text" class="form-control" placeholder="Prénom" name="prenomPersonne">
                        </div>
                        <div class="form-group">
                            <label>Adresse mail:</label>
                            <input type="text" class="form-control" placeholder="xx.xx@xx.xx" name="mailPersonne">
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" class="form-control" placeholder="Téléhpone fixe ou mobile" name="telPersonne">
                        </div>
                        <div class="form-group">
                            <label>Fonction:</label>
                            <input type="text" class="form-control" placeholder="Fonction au sein de l'organisme" name="fonction">
                        </div>
                        <div class="form-group">
                            <label>Profil d'habilitation: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleProfil">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM PROFILS;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idProfil']; ?>"><?php echo $data['libelleProfil']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="notificationMailCreation" checked> Envoyer un email à l'utilisateur avec son identifiant et son mot de passe
                                </label>
                            </div>
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





<!-- CENTRES DE COUTS -->
<?php if($_SESSION['cout_lecture']==1 OR $_SESSION['cout_ajout']==1 OR $_SESSION['cout_etreEnCharge']==1 OR $_SESSION['cout_supprimer']==1) { ?>
    <div class="modal fade" id="modalCoutAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Création d'un centre de cout</h4>
                </div>
                <form role="form" action="centreCoutsAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé:<small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" name="libelleCentreDecout" required>
                        </div>
                        <div class="form-group">
                            <label>Personne référente</label>
                            <select class="form-control select2" style="width: 100%;" name="idResponsable">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE cout_etreEnCharge=1 ORDER BY identifiant;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idPersonne']; ?>"><?php echo $data['identifiant']; ?></option>
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



<!-- MESSAGES GENERAUX -->
<?php if($_SESSION['messages_ajout']==1) { ?>
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





