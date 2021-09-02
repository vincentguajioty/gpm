<!DOCTYPE html>
<html>
<?php include('header.php');?>
<?php
session_start();
$_SESSION['page'] = 801;
require_once('logCheck.php');
require_once('config/config.php');
?>
<?php
if ($_SESSION['reserve_cmdVersReserve']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>

<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>

    <?php
        (isset($_SESSION['transfertStade']) AND ($_SESSION['transfertStade']!=0)) ? '' : $_SESSION['transfertStade'] = 1 ;
    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Transfert de matériel COMMANDES <i class="fa fa-arrow-right"></i> RESERVE
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Transfert de matériel</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
			<div class="row">
	            <div class="col-md-8">
	                <div class="nav-tabs-custom">
	                    <ul class="nav nav-tabs">
                            <?php if($_SESSION['transfertStade']>0){?><li <?= $_SESSION['transfertStade']==1 ? 'class="active"' : ''?>><a href="#1" data-toggle="tab">Selection de la commande</a></li><?php } ?>
                            <?php if($_SESSION['transfertStade']>1){?><li <?= $_SESSION['transfertStade']==2 ? 'class="active"' : ''?>><a href="#2" data-toggle="tab">Selection du matériel</a></li><?php } ?>
	                        <?php if($_SESSION['transfertStade']>2){?><li <?= $_SESSION['transfertStade']==3 ? 'class="active"' : ''?>><a href="#3" data-toggle="tab">Selection de la destination</a></li><?php } ?>
							<?php if($_SESSION['transfertStade']>3){?><li <?= $_SESSION['transfertStade']==4 ? 'class="active"' : ''?>><a href="#4" data-toggle="tab">Récapitulatif</a></li><?php } ?>
	                    </ul>
	                    <div class="tab-content">
							<?php if($_SESSION['transfertStade']>0){?>
		                        <div class="<?= $_SESSION['transfertStade']==1 ? 'active' : ''?> tab-pane" id="1">
	                                <form role="form" class="spinnerAttenteSubmit" action="transfertCmdResGo2.php" method="POST">
	                                    <div class="form-group">
	                                        <label>Commande: <small style="color:grey;">Requis</small></label>
	                                        <select <?= $_SESSION['transfertStade']!=1 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertCmd']) ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idCommande" required>
	                                            <?php
	                                            $query = $db->query('SELECT * FROM COMMANDES c LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur WHERE idEtat=5;');
	                                            while ($data = $query->fetch())
	                                            {
	                                                ?>
	                                                <option <?php if (isset($_SESSION['transfertCmd']) AND $_SESSION['transfertCmd']==$data['idCommande']){ echo 'selected'; } ?> value="<?php echo $data['idCommande']; ?>"><?php echo '['.$data['idCommande'].'] '.$data['dateCreation'].' - '.$data['nomFournisseur']; ?></option>
	                                                <?php
	                                            }
	                                            $query->closeCursor(); ?>
	                                        </select>
	                                    </div>
	                                
		                                <div class="box-footer">
		                                    <a href="transfertReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir abandonner la transfert ?');">Abandon du transfert</a>
		                                    <?php if ($_SESSION['transfertStade']==1) { ?><button type="submit" class="btn btn-info pull-right">Suivant</button> <?php } ?>
		                                </div>
		                            </form>
		                        </div>
		                    <?php } ?>
							<?php if($_SESSION['transfertStade']>1){?>
								<div class="<?= $_SESSION['transfertStade']==2 ? 'active' : ''?> tab-pane" id="2">
		                            <form role="form" class="spinnerAttenteSubmit" action="transfertCmdResGo3.php" method="POST">
	                                    <div class="form-group">
	                                        <label>Item de la commande: <small style="color:grey;">Requis</small></label>
	                                        <select <?= $_SESSION['transfertStade']!=2 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertIdMaterielCatalogue']) ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idMaterielCatalogue" required>
	                                            <?php
	                                            $query = $db->prepare('SELECT * FROM COMMANDES_MATERIEL m LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE idCommande = :idCommande AND quantiteAtransferer > 0;');
	                                            $query->execute(array(
        											'idCommande' => $_SESSION['transfertCmd']
        										));
	                                            while ($data = $query->fetch())
	                                            {
	                                                ?>
	                                                <option <?php if (isset($_SESSION['transfertIdMaterielCatalogue']) AND $_SESSION['transfertIdMaterielCatalogue']==$data['idMaterielCatalogue']){ echo 'selected'; } ?> value="<?php echo $data['idMaterielCatalogue']; ?>"><?php echo $data['libelleMateriel'].' [quantité commandée: '.$data['quantiteCommande'].']'; ?></option>
	                                                <?php
	                                            }
	                                            $query->closeCursor(); ?>
	                                        </select>
	                                    </div>
	                                    <div class="form-group" id="perem">
				                            <label>Le matériel a une date de péremption (laisser vide sinon):</label>
				                            <div class="input-group">
				                                <div class="input-group-addon">
				                                    <i class="fa fa-calendar"></i>
				                                </div>
				                                <input <?php if (isset($_SESSION['transfertPeremption'])){ echo 'value="'.$_SESSION['transfertPeremption'].'"'; } ?> <?= $_SESSION['transfertStade']!=2 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertPeremption']) ? 'disabled' : '' ?> class="input-datepicker form-control" name="peremption">
				                            </div>
				                        </div>
	                                
		                                <div class="box-footer">
		                                    <a href="transfertReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir abandonner la transfert ?');">Abandon du transfert</a>
		                                    <?php if ($_SESSION['transfertStade']==2) { ?><button type="submit" class="btn btn-info pull-right">Suivant</button> <?php } ?>
		                                </div>
		                            </form>
		                        </div>
							<?php } ?>	
							<?php if($_SESSION['transfertStade']>2){?>
								<div class="<?= $_SESSION['transfertStade']==3 ? 'active' : ''?> tab-pane" id="3">
		                            <form role="form" class="spinnerAttenteSubmit" action="transfertCmdResGo4.php" method="POST">
	                                    <div class="form-group">
	                                        <label>Conteneur de destination: <small style="color:grey;">Requis</small></label>
	                                        <select <?= $_SESSION['transfertStade']!=3 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertIdReserveElement']) ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idReserveElement" required>
	                                            <?php
	                                            $query = $db->prepare('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur = c.idConteneur LEFT OUTER JOIN LIEUX l ON c.idLieu = l.idLieu LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE m.idMaterielCatalogue = :idMaterielCatalogue;');
	                                            $query->execute(array(
        											'idMaterielCatalogue' => $_SESSION['transfertIdMaterielCatalogue']
        										));
	                                            while ($data = $query->fetch())
	                                            {
	                                                ?>
	                                                <option <?php if (isset($_SESSION['transfertIdReserveElement']) AND $_SESSION['transfertIdReserveElement']==$data['idReserveElement']){ echo 'selected'; } ?> value="<?php echo $data['idReserveElement']; ?>"><?php echo '['.$data['libelleLieu'].'] '.$data['libelleConteneur'].' - Actuellement '.$data['quantiteReserve'].' '.$data['libelleMateriel']; ?></option>
	                                                <?php
	                                            }
	                                            $query->closeCursor(); ?>
	                                        </select>
	                                    </div>
	                                    <div class="form-group">
				                            <label>Quantité à transférer (1 - <?= $_SESSION['transfertQttMax'] ?>): <small style="color:grey;">Requis</small></label>
				                            <input <?php if (isset($_SESSION['transfertqttTrans'])){ echo 'value="'.$_SESSION['transfertqttTrans'].'"'; }else{ echo 'value="'.$_SESSION['transfertQttMax'].'"'; } ?> <?= $_SESSION['transfertStade']!=3 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertqttTrans']) ? 'disabled' : '' ?> type="number" class="form-control" name="qttTrans" min="1" max="<?= $_SESSION['transfertQttMax'] ?>" required>
				                        </div>
	                                    

		                                <div class="box-footer">
		                                    <a href="transfertReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir abandonner la transfert ?');">Abandon du transfert</a>
		                                    <?php if ($_SESSION['transfertStade']==3) { ?><button type="submit" class="btn btn-info pull-right">Suivant</button> <?php } ?>
		                                </div>
		                            </form>
		                        </div>
	                    	<?php } ?>   
							<?php if($_SESSION['transfertStade']>3){?>
								<div class="<?= $_SESSION['transfertStade']==4 ? 'active' : ''?> tab-pane" id="4">
	                            	<table class="table table-bordered">
			                            <tr>
			                                <th></th>
			                                <th>Commande source</th>
			                                <th>Conteneur destination</th>
			                            </tr>
			                            <tr>
			                            	<th>Commande</th>
			                            	<td><?= $_SESSION['transfertCmd'] ?></td>
			                            	<td></td>
			                            </tr>
			                            <tr>
			                            	<th>Fournisseur</th>
			                            	<?php
			                            		$query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur WHERE idCommande = :idCommande;');
	                                            $query->execute(array(
        											'idCommande' => $_SESSION['transfertCmd']
        										));
        										$data = $query->fetch();
			                            	?>
			                            	<td><?= $data['nomFournisseur'] ?></td>
			                            	<td></td>
			                            </tr>
			                            <tr>
			                            	<th>Matériel</th>
			                            	<?php
			                            		$query = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue;');
	                                            $query->execute(array(
        											'idMaterielCatalogue' => $_SESSION['transfertIdMaterielCatalogue']
        										));
        										$data = $query->fetch();
			                            	?>
			                            	<td><?= $data['libelleMateriel'] ?></td>
			                            	<td></td>
			                            </tr>
			                            <tr>
			                            	<th>Quantité à transférer</th>
			                            	<td><?= $_SESSION['transfertqttTrans'] ?></td>
			                            	<td></td>
			                            </tr>
			                            <tr>
			                            	<th>Restera à transférer</th>
			                            	<td><?= $_SESSION['transfertQttMax'] - $_SESSION['transfertqttTrans'] ?></td>
			                            	<td></td>
			                            </tr>
			                            <tr>
			                            	<th>Conteneur</th>
			                            	<?php
			                            		$query = $db->prepare('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur = c.idConteneur WHERE m.idReserveElement = :idReserveElement;');
	                                            $query->execute(array(
        											'idReserveElement' => $_SESSION['transfertIdReserveElement']
        										));
        										$data = $query->fetch();
			                            	?>
			                            	<td></td>
			                            	<td><?= $data['libelleConteneur'] ?></td>
			                            </tr>
			                            <tr>
			                            	<th>Quantité avant transfert</th>
			                            	<td></td>
			                            	<td><?= $data['quantiteReserve'] ?></td>
			                            </tr>
			                            <tr>
			                            	<th>Péremption avant transfert</th>
			                            	<td></td>
			                            	<td><?= $data['peremptionReserve'] ?></td>
			                            </tr>
			                            <tr>
			                            	<th>Quantité après transfert</th>
			                            	<td></td>
			                            	<td><?= $data['quantiteReserve'] + $_SESSION['transfertqttTrans'] ?></td>
			                            </tr>
			                            <tr>
			                            	<th>Péremption après transfert</th>
			                            	<td></td>
			                            	<td><?php
				                            	if (isset($_SESSION['transfertPeremption']))
												{
													if ($data['peremptionReserve'] != Null AND $data['quantiteReserve'] > 0)
													{
														if ($data['peremptionReserve'] > $_SESSION['transfertPeremption'])
														{
															echo $_SESSION['transfertPeremption'];
														}
														else
														{
															echo $data['peremptionReserve'];
														}
													}
													else
													{
														echo $_SESSION['transfertPeremption'];
													}
												}
												else
												{
													echo $data['peremptionReserve'];
												}
											?></td>
			                            </tr>
			                        </table>
		                            <div class="box-footer">
	                                    <a href="transfertReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir abandonner la transfert ?');">Abandon du transfert</a>
	                                    <?php if ($_SESSION['transfertStade']==4) { ?><a href="transfertCmdResGoOK.php" class="btn btn-success pull-right spinnerAttenteClick">Effectuer</a> <?php } ?>
	                                </div>
		                        </div>
		                    <?php } ?>
	                    </div>
	                </div>
	            </div>
	            
	            <div class="col-md-4">
	            	<ul class="timeline">
			            <li class="time-label">
							<span class="bg-blue">Transfert</span>
			            </li>
			            <li>
							<?php
								if ($_SESSION['transfertStade'] == 1)
								{
									echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
								}
								else if ($_SESSION['transfertStade'] > 1)
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
								else
								{
									echo '<i class="fa fa-spinner bg-grey"></i>';
								}
							?>
							<div class="timeline-item">
								<h3 class="timeline-header no-border">Selection de la commande</h3>
							</div>
			            </li>
			            <li>
							<?php
								if ($_SESSION['transfertStade'] == 2)
								{
									echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
								}
								else if ($_SESSION['transfertStade'] > 2)
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
								else
								{
									echo '<i class="fa fa-spinner bg-grey"></i>';
								}
							?>
							<div class="timeline-item">
								<h3 class="timeline-header no-border">Selection du matériel</h3>
							</div>
			            </li>
			            <li>
							<?php
								if ($_SESSION['transfertStade'] == 3)
								{
									echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
								}
								else if ($_SESSION['transfertStade'] > 3)
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
								else
								{
									echo '<i class="fa fa-spinner bg-grey"></i>';
								}
							?>
							<div class="timeline-item">
								<h3 class="timeline-header no-border">Selection de la destination</h3>
							</div>
			            </li>
			            <li>
							<?php
								if ($_SESSION['transfertStade'] == 4)
								{
									echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
								}
								else if ($_SESSION['transfertStade'] > 4)
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
								else
								{
									echo '<i class="fa fa-spinner bg-grey"></i>';
								}
							?>
							<div class="timeline-item">
								<h3 class="timeline-header no-border">Validation</h3>
							</div>
			            </li>
			            <li>
			              <i class="fa fa-exchange bg-gray"></i>
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

</html>
