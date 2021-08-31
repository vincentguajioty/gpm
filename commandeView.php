<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/version.php'); ?>
<?php
session_start();
$_SESSION['page'] = 601;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['commande_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat WHERE idCommande=:idCommande;');
    $query->execute(array('idCommande' => $_GET['id']));
    $data = $query->fetch();
    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                COMMANDE <?php echo $_GET['id']; ?> - <?php echo $data['libelleEtat']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="commandesToutes.php">Commandes</a></li>
                <li class="active">Commande <?php echo $_GET['id']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
			<div class="row">
	            <div class="col-md-8">
	                <div class="nav-tabs-custom">
	                    <ul class="nav nav-tabs">
	                        <li class="active"><a href="#general" data-toggle="tab">Informations générales</a></li>
	                        <li><a href="#contenu" data-toggle="tab">Contenu</a></li>
	                        <?php if ($data['idEtat']>1) { ?><li><a href="#validation" data-toggle="tab">Validation</a></li> <?php } ?>
	                        <?php if ($data['idEtat']>2) { ?><li><a href="#fournisseur" data-toggle="tab">Passage de la commande</a></li> <?php } ?>
	                        <?php if ($data['idEtat']>3) { ?><li><a href="#livraison" data-toggle="tab">Livraison</a></li> <?php } ?>
	                    </ul>
	                    <div class="tab-content">
	
	                        <div class="active tab-pane" id="general">
	                            <form role="form" action="commandesUpdate.php?id=<?php echo $_GET['id'];?>" method="POST">
	                                <div class="row">
	                                    <div class="col-md-6">
	                                        <div class="form-group">
	                                            <label>Fournisseur: </label>
	                                            <select <?php if(($data['idEtat']>1) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" name="idFournisseur">
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM FOURNISSEURS;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idFournisseur']; ?>" <?php if ($data['idFournisseur'] == $data2['idFournisseur']) { echo 'selected'; } ?>><?php echo $data2['nomFournisseur']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group">
	                                            <label>Demandeur: </label>
	                                            <select <?php if(($data['idEtat']>1) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" name="idDemandeur">
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if ($data['idDemandeur'] == $data2['idPersonne']) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group">
	                                            <label>Observateur: </label>
	                                            <select <?php if(($data['idEtat']>1) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" name="idObservateur">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if ($data['idObservateur'] == $data2['idPersonne']) { echo 'selected'; } ?>><?php echo $data2['identifiant']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group">
	                                            <label>Centre de cout: </label>
	                                            <select <?php if(($data['idEtat']>1) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" name="idCentreDeCout">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM CENTRE_COUTS;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idCentreDeCout']; ?>" <?php if ($data['idCentreDeCout'] == $data2['idCentreDeCout']) { echo 'selected'; } ?>><?php echo $data2['libelleCentreDecout']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group" id="dateCreation">
	                                            <label>Date de création:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateCreation" value="<?php echo $data['dateCreation']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	
	                                    <div class="col-md-6">
	                                        <div class="form-group">
	                                            <label>Etat: </label>
	                                            <select disabled class="form-control" name="idEtat">
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM COMMANDES_ETATS;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idEtat']; ?>" <?php if ($data['idEtat'] == $data2['idEtat']) { echo 'selected'; } ?> ><?php echo $data2['libelleEtat']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group">
	                                            <label>Affectation: </label>
	                                            <select <?php if(($data['idEtat']>1) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" name="idAffectee">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1 AND commande_etreEnCharge = 1;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if ($data['idAffectee'] == $data2['idPersonne']) { echo 'selected'; } ?>><?php echo $data2['identifiant']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group">
	                                            <label>Validation de la demande de commande par: </label>
	                                            <select <?php if(($data['idEtat']>1) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" name="idValideur">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1 AND commande_valider = 1;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if ($data['idValideur'] == $data2['idPersonne']) { echo 'selected'; } ?>><?php echo $data2['identifiant']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group">
	                                            <label>Lieu de livraison: </label>
	                                            <select <?php if(($data['idEtat']>1) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" name="idLieuLivraison">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM LIEUX;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idLieu']; ?>" <?php if ($data['idLieuLivraison'] == $data2['idLieu']) { echo 'selected'; } ?>><?php echo $data2['libelleLieu']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group" id="dateCloture">
	                                            <label>Date de clôture:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateCreation" value="<?php echo $data['dateCloture']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="form-group">
	                                    <label>Remarques:</label>
	                                    <textarea <?php if(($data['idEtat']>1) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" rows="3" name="remarquesGenerales"><?php echo $data['remarquesGenerales']; ?></textarea>
	                                </div>
	                                <div class="box-footer">
	                                    <?php if (($data['idEtat']<7) AND ($_SESSION['commande_abandonner']==1)){ ?><a href="commandesGo8.php?id=<?=$_GET['id']?>" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir abandonner la commande (action irreversible) ?');">Abandon de la commande</a> <?php } ?>
	                                    <?php if (($data['idEtat']==1) AND (($_SESSION['commande_ajout']==1) OR ($_SESSION['commande_etreEnCharge']==1))) { ?><button type="submit" class="btn btn-warning pull-right">Modifier</button> <?php } ?>
	                                </div>
	                            </form>
	                        </div>
	
	
	                        <div class="tab-pane" id="contenu">
	                            <div class="box-body table-responsive no-padding">
					              <table class="table table-hover">
					                <tr>
					                  <th>Matériel</th>
					                  <th>Référence</th>
					                  <th>Prix unitaire TTC</th>
					                  <th>Quantité</th>
					                  <th>Sous-Total</th>
					                  <th></th>
					                </tr>
					                <?php
                                        $query2 = $db->prepare('SELECT * FROM COMMANDES_MATERIEL c LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue WHERE idCommande = :idCommande ORDER BY libelleMateriel ASC ;');
                                        $query2->execute(array('idCommande' => $_GET['id']));
                                        $totalCMD = 0;
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            	<tr>
								                  <td><?php echo $data2['libelleMateriel'];?></td>
								                  <td><?php echo $data2['referenceProduitFournisseur'];?></td>
								                  <td><?php echo $data2['prixProduitTTC'];?> €</td>
								                  <td><?php echo $data2['quantiteCommande'];?></td>
								                  <td><?php echo $data2['prixProduitTTC']*$data2['quantiteCommande']; $totalCMD = $totalCMD + ($data2['prixProduitTTC']*$data2['quantiteCommande']);?> €</td>
								                  <td>
								                	<?php if(($data['idEtat']==1) AND (($_SESSION['commande_ajout']==1) OR ($_SESSION['commande_etreEnCharge']==1))){ ?>
								                		<a data-toggle="modal" data-target="#modalUpdateItem" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
								                		<a href="commandeItemDelete.php?idCommande=<?=$data2['idCommande']?>&idMaterielCatalogue=<?=$data2['idMaterielCatalogue']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-minus"></i></a>
								                	<?php }?>
								                	
								                	<?php if(($data['idEtat']>1) AND ($_SESSION['commande_lecture']==1)){ ?>
								                		<a data-toggle="modal" data-target="#modalViewItem" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
								                	<?php }?>
								                	
								                  </td>
								                </tr>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                        <tr>
								        	<td></td>
								        	<td></td>
								        	<td></td>
								        	<td><b>Total:</b></td>
								        	<td><b><?php echo $totalCMD;?> €</b></td>
								        	<td></td>
								        <tr>
                                        <?php if(($data['idEtat']==1) AND (($_SESSION['commande_ajout']==1) OR ($_SESSION['commande_etreEnCharge']==1))){ ?>
										        <tr>
										        	<td></td>
										        	<td></td>
										        	<td></td>
										        	<td></td>
										        	<td></td>
										        	<td><a data-toggle="modal" data-target="#modalAddItem" class="btn btn-xs btn-success"><i class="fa fa-plus"></i></a></td>
										        <tr>
										  <?php }?>
					              </table>
					              
					            </div>
	                        </div>
	
	
	                        <div class="tab-pane" id="validation">
	                            <form role="form" action="commandesGo13.php?id=<?=$_GET['id']?>" method="POST">
	                                <div class="row">
	                                    <div class="col-md-4">
	                                        <div class="form-group" id="dateDemandeValidation">
	                                            <label>Date de demande:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateDemandeValidation" value="<?php echo $data['dateDemandeValidation']; ?>">
	                                            </div>
	                                        </div>
	
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="form-group">
	                                            <label>Valideur: </label>
	                                            <select disabled class="form-control" name="idValideur">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1 AND commande_valider = 1;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if ($data['idValideur'] == $data2['idPersonne']) { echo 'selected'; } ?>><?php echo $data2['identifiant']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="form-group" id="dateValidation">
	                                            <label>Date de validation:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateValidation" value="<?php echo $data['dateValidation']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="form-group">
	                                    <label>Remarques (obligatoires si refus):</label>
	                                    <textarea <?php if(($data['idEtat']>2) OR ($_SESSION['commande_valider']==0) OR ($data['idValideur'] != $_SESSION['idPersonne'])){echo 'disabled';}?> class="form-control" rows="3" name="remarquesValidation"><?php echo $data['remarquesValidation']; ?></textarea>
	                                </div>
	                                <div class="box-footer">
	                                    <?php if (($data['idEtat']==2) AND ($_SESSION['commande_valider']==1) AND ($data['idValideur']== $_SESSION['idPersonne'])){ ?><button name="button" value="nok" type="submit" class="btn btn-danger pull-left">Refuser la commande</button> <?php } ?>
	                                    <?php if (($data['idEtat']==2) AND ($_SESSION['commande_valider']==1) AND ($data['idValideur']== $_SESSION['idPersonne'])) { ?><button name="button" value="ok" type="submit" class="btn btn-success pull-right">Valider la commande</button> <?php } ?>
	                                </div>
	                            </form>
	                        </div>
	                        
	                        
	                        <div class="tab-pane" id="fournisseur">
	                            <form role="form" action="commandesUpdateFour.php?id=<?=$_GET['id']?>" method="POST">
	                                <div class="row">
	                                    <div class="col-md-6">
	                                        <div class="form-group">
	                                            <label>Fournisseur: </label>
	                                            <select disabled class="form-control" name="idFournisseur">
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM FOURNISSEURS;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idFournisseur']; ?>" <?php if ($data['idFournisseur'] == $data2['idFournisseur']) { echo 'selected'; } ?>><?php echo $data2['nomFournisseur']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group" id="datePassage">
	                                            <label>Date de passage de la commande:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input <?php if(($data['idEtat']>3) OR ($_SESSION['commande_etreEnCharge']==0) OR ($_SESSION['idPersonne'] != $data['idAffectee'])){echo 'disabled';}?> type="text" class="input-datepicker form-control" name="datePassage" value="<?php echo $data['datePassage']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	
	                                    <div class="col-md-6">
	                                        <div class="form-group">
				                                <label>Référence de la commande fournisseur:</label>
				                                <input <?php if(($data['idEtat']>3) OR ($_SESSION['commande_etreEnCharge']==0) OR ($_SESSION['idPersonne'] != $data['idAffectee'])){echo 'disabled';}?> type="text" class="form-control" name="numCommandeFournisseur" value="<?php echo $data['numCommandeFournisseur']; ?>">
				                            </div>
	                                        <div class="form-group" id="dateLivraisonPrevue">
	                                            <label>Date prévue de livraison:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input <?php if(($data['idEtat']>3) OR ($_SESSION['commande_etreEnCharge']==0) OR ($_SESSION['idPersonne'] != $data['idAffectee'])){echo 'disabled';}?> type="text" class="input-datepicker form-control" name="dateLivraisonPrevue" value="<?php echo $data['dateLivraisonPrevue']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="box-footer">
	                                    <?php if (($data['idEtat']==3) AND ($_SESSION['commande_etreEnCharge']==1) AND ($_SESSION['idPersonne'] == $data['idAffectee'])) { ?><button type="submit" class="btn btn-warning pull-right">Modifier</button> <?php } ?>
	                                </div>
	                            </form>
	                        </div>
	                        
	                        
	                        <div class="tab-pane" id="livraison">
	                            <form role="form" action="commandesGo56.php?id=<?=$_GET['id']?>" method="POST">
	                                <div class="row">
	                                    <div class="col-md-4">
	                                        <div class="form-group" id="dateLivraisonPrevue">
	                                            <label>Date prévue de livraison:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateLivraisonPrevue" value="<?php echo $data['dateLivraisonPrevue']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="form-group">
	                                            <label>Lieu de livraison: </label>
	                                            <select disabled class="form-control" name="idLieuLivraison">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM LIEUX;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idLieu']; ?>" <?php if ($data['idLieuLivraison'] == $data2['idLieu']) { echo 'selected'; } ?>><?php echo $data2['libelleLieu']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="form-group" id="dateLivraisoneffective">
	                                            <label>Date de livraison effective:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input <?php if(($data['idEtat']>4) OR ($_SESSION['commande_etreEnCharge']==0) OR ($_SESSION['idPersonne'] != $data['idAffectee'])){echo 'disabled';}?> type="text" class="input-datepicker form-control" name="dateLivraisoneffective" value="<?php echo $data['dateLivraisoneffective']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="form-group">
	                                    <label>Remarques:</label>
	                                    <textarea <?php if(($data['idEtat']>4) OR ($_SESSION['commande_etreEnCharge']==0) OR ($_SESSION['idPersonne'] != $data['idAffectee'])){echo 'disabled';}?> class="form-control" rows="3" name="remarquesLivraison"><?php echo $data['remarquesLivraison']; ?></textarea>
	                                </div>
	                                <div class="box-footer">
	                                    <?php if (($data['idEtat']==4) AND ($_SESSION['commande_etreEnCharge']==1) AND ($_SESSION['idPersonne'] == $data['idAffectee'])){ ?><button name="button" value="nok" type="submit" class="btn btn-danger pull-left">Commande reçue > SAV</button> <?php } ?>
	                                    <?php if (($data['idEtat']==4) AND ($_SESSION['commande_etreEnCharge']==1) AND ($_SESSION['idPersonne'] == $data['idAffectee'])) { ?><button name="button" value="ok" type="submit" class="btn btn-success pull-right">Commande reçue > OK</button> <?php } ?>
	                                </div>
	                            </form>
	                        </div>
	
	
	                    </div>
	                </div>
	                
	                <?php if ((($_SESSION['idPersonne'] == $data['idDemandeur']) OR ($_SESSION['idPersonne'] == $data['idObservateur']) OR ($_SESSION['idPersonne'] == $data['idValideur']) OR ($_SESSION['idPersonne'] == $data['idAffectee'])) AND ($data['idEtat']<7)){ ?>
	                <div class="box box">
	                	<div class="box-body">
	                		<form role="form" action="commandesNotesAdd.php?id=<?=$_GET['id']?>" method="POST">
	                                <div class="form-group">
	                                    <label>Note:</label>
	                                    <textarea class="form-control" rows="3" name="notes"></textarea>
	                                </div>
	                                <div class="box-footer">
	                                    <button type="submit" class="btn btn-success pull-right">Ajouter</button>
	                                </div>
	                            </form>
	                	</div>
	                </div>
	                 <?php } ?>
	                 
	                <div class="box box-warning">
	                    <div class="box-body">
	                        <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
	                        <?php if (($data['idEtat']==1) AND (($_SESSION['commande_ajout']==1)OR($_SESSION['commande_etreEnCharge']==1))) { ?><a href="commandesGo2.php?id=<?=$_GET['id']?>" <?php if($data['idValideur'] == ''){echo 'disabled';}?> class="btn btn-info pull-right">Soumettre à validation</a> <?php } ?>
	                        <?php if (($data['idEtat']==3) AND ($_SESSION['idPersonne'] == $data['idAffectee'])) { ?><a href="commandesGo4.php?id=<?=$_GET['id']?>" class="btn btn-info pull-right">Commande passée > En attente de livraison</a> <?php } ?>
	                        <?php if (($data['idEtat']==5) AND ($_SESSION['idPersonne'] == $data['idAffectee'])) { ?><a href="commandesGo7.php?id=<?=$_GET['id']?>" class="btn btn-info pull-right">Cloturer la commande</a> <?php } ?>
	                        <?php if (($data['idEtat']==6) AND ($_SESSION['idPersonne'] == $data['idAffectee'])) { ?><a href="commandesGo5.php?id=<?=$_GET['id']?>" class="btn btn-info pull-right">SAV terminé > Commande OK</a> <?php } ?>
	                    </div>
	                </div>
	            </div>
	
	
	            <div class="col-md-4">
	                <ul class="timeline">
			            <li class="time-label">
			                  <span class="bg-blue">
			                    Historique
			                  </span>
			            </li>
			            <?php
			                    $query = $db->prepare('SELECT * FROM COMMANDES_TIMELINE c LEFT OUTER JOIN COMMANDES_TIMELINE_ICON i ON c.idComIcon = i.idComIcon WHERE idCommande=:idCommande ORDER BY dateEvtCommande DESC;');
			                    $query->execute(array('idCommande' => $_GET['id']));
			                    while($data = $query->fetch())
			                    { ?>
			                    	<li>
						              <i class="fa <?php echo $data['iconFontAsw'];?> bg-<?php echo $data['iconColor'];?>"></i>
						              <div class="timeline-item">
						                <span class="time"><i class="fa fa-clock-o"></i> <?php echo $data['dateEvtCommande'];?></span>
						                <h3 class="timeline-header no-border"><?php echo $data['detailsEvtCommande'];?></h3>
						              </div>
						            </li>
			                    <?php }
			             ?>
			            <li>
			              <i class="fa fa-clock-o bg-gray"></i>
			            </li>
			          </ul>
	            </div>
	            
	            </div>

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include('footer.php'); ?>


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php include('scripts.php'); ?>
</body>


<!-- Modal -->
<div class="modal fade" id="modalAddItem">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Ajouter un élément à la commande</h4>
      </div>
      <form role="form" action="commandeItemAdd.php?idCommande=<?=$_GET['id']?>" method="POST">
	      <div class="modal-body">
	        <div class="form-group">
				<label>Matériel: </label>
				<select class="form-control" name="idMaterielCatalogue">
					<?php
					$query2 = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
					$query2->execute(array('idTypeLot' => $_GET['id']));
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
				<label>Quantité:</label>
				<input type="number" class="form-control" name="quantiteCommande">
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
						<label>Prix TTC:</label>
						<input type="number" step="0.01" class="form-control" name="prixProduitTTC">
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
<div class="modal fade" id="modalUpdateItem">
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
<div class="modal fade" id="modalViewItem">
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


</html>
