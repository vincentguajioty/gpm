<!DOCTYPE html>
<html>
<?php include('header.php');?>
<?php
session_start();
$_SESSION['page'] = 802;
require_once('logCheck.php');
require_once('config/config.php');
?>
<?php
if ($_SESSION['reserve_ReserveVersLot']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>

<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
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
                Transfert de matériel RESERVE <i class="fa fa-arrow-right"></i> LOTS
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Transfert de matériel</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <?php
                if($LOTSLOCK)
                {
                    echo '<div class="alert alert-warning alert-dismissible">';
                    echo '<i class="icon fa fa-warning"></i> Des inventaires de lots sont en cours, cette section est donc verrouillée.';
                    echo '</div>';
                }
                else
                { ?>
					<div class="row">
			            <div class="col-md-8">
			                <div class="nav-tabs-custom">
			                    <ul class="nav nav-tabs">
		                            <?php if($_SESSION['transfertStade']>0){?><li <?= $_SESSION['transfertStade']==1 ? 'class="active"' : ''?>><a href="#1" data-toggle="tab">Selection du matériel</a></li><?php } ?>
		                            <?php if($_SESSION['transfertStade']>1){?><li <?= $_SESSION['transfertStade']==2 ? 'class="active"' : ''?>><a href="#2" data-toggle="tab">Selection du conteneur source</a></li><?php } ?>
			                        <?php if($_SESSION['transfertStade']>2){?><li <?= $_SESSION['transfertStade']==3 ? 'class="active"' : ''?>><a href="#3" data-toggle="tab">Selection de l'emplacement de destination</a></li><?php } ?>
									<?php if($_SESSION['transfertStade']>3){?><li <?= $_SESSION['transfertStade']==4 ? 'class="active"' : ''?>><a href="#4" data-toggle="tab">Récapitulatif</a></li><?php } ?>
			                    </ul>
			                    <div class="tab-content">
									<?php if($_SESSION['transfertStade']>0){?>
				                        <div class="<?= $_SESSION['transfertStade']==1 ? 'active' : ''?> tab-pane" id="1">
			                                <form role="form" class="spinnerAttenteSubmit" action="transfertResLotsGo2.php" method="POST">
			                                    <div class="form-group">
			                                        <label>Catalogue: <small style="color:grey;">Requis</small></label>
			                                        <select <?= $_SESSION['transfertStade']!=1 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertIdMaterielCatalogue']) ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idMaterielCatalogue" required>
			                                            <?php
			                                            $query = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
			                                            while ($data = $query->fetch())
			                                            {
			                                                ?>
			                                                <option <?php if (isset($_SESSION['transfertIdMaterielCatalogue']) AND $_SESSION['transfertIdMaterielCatalogue']==$data['idMaterielCatalogue']){ echo 'selected'; } ?> value="<?php echo $data['idMaterielCatalogue']; ?>"><?php echo $data['libelleMateriel']; ?></option>
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
				                            <form role="form" class="spinnerAttenteSubmit" action="transfertResLotsGo3.php" method="POST">
												<div class="form-group">
			                                        <label>Conteneur source: <small style="color:grey;">Requis</small></label>
			                                        <select <?= $_SESSION['transfertStade']!=2 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertIdReserveElement']) ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idReserveElement" required>
			                                            <?php
			                                            $query = $db->prepare('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur = c.idConteneur LEFT OUTER JOIN LIEUX l on c.idLieu = l.idLieu LEFT OUTER JOIN MATERIEL_CATALOGUE h ON m.idMaterielCatalogue = h.idMaterielCatalogue WHERE m.idMaterielCatalogue = :idMaterielCatalogue and quantiteReserve > 0;');
			                                            $query->execute(array(
		        											'idMaterielCatalogue' => $_SESSION['transfertIdMaterielCatalogue']
		        										));
			                                            while ($data = $query->fetch())
			                                            {
			                                                ?>
			                                                <option <?php if (isset($_SESSION['transfertIdReserveElement']) AND $_SESSION['transfertIdReserveElement']==$data['idReserveElement']){ echo 'selected'; } ?> value="<?php echo $data['idReserveElement']; ?>"><?php echo '['.$data['libelleLieu'].'] '.$data['libelleConteneur'].' - '.$data['quantiteReserve'].' '.$data['libelleMateriel'].' disponible(s)'; ?></option>
			                                                <?php
			                                            }
			                                            $query->closeCursor(); ?>
			                                        </select>
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
				                            <form role="form" class="spinnerAttenteSubmit" action="transfertResLotsGo4.php" method="POST">
			                                    <div class="form-group">
			                                        <label>Lot de destination: <small style="color:grey;">Requis</small></label>
			                                        <select <?= $_SESSION['transfertStade']!=3 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertIdMaterielLot']) ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idElement" required>
			                                            <?php
			                                            $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT k ON e.idEmplacement = k.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON k.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE idMaterielCatalogue = :idMaterielCatalogue ;');
			                                            $query->execute(array(
		        											'idMaterielCatalogue' => $_SESSION['transfertIdMaterielCatalogue']
		        										));
			                                            while ($data = $query->fetch())
			                                            {
			                                                ?>
			                                                <option <?php if (isset($_SESSION['transfertIdMaterielLot']) AND $_SESSION['transfertIdMaterielLot']==$data['idElement']){ echo 'selected'; } ?> value="<?php echo $data['idElement']; ?>"><?php echo $data['libelleLot'].' > '.$data['libelleSac'].' > '.$data['libelleEmplacement'].' - Actuellement '.$data['quantite'].' affectés.'; ?></option>
			                                                <?php
			                                            }
			                                            $query->closeCursor(); ?>
			                                        </select>
			                                    </div>
			                                    <div class="form-group">
						                            <label>Quantité à transférer (1 - <?= $_SESSION['transfertQttMax'] ?>): <small style="color:grey;">Requis</small></label>
						                            <input <?php if (isset($_SESSION['transfertqttTrans'])){ echo 'value="'.$_SESSION['transfertqttTrans'].'"'; }else{ echo 'value="1"'; } ?> <?= $_SESSION['transfertStade']!=3 ? 'disabled' : '' ?> <?= isset($_SESSION['transfertqttTrans']) ? 'disabled' : '' ?> type="number" class="form-control" name="qttTrans" min="1" max="<?= $_SESSION['transfertQttMax'] ?>" required>
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
					                                <th>Conteneur source</th>
					                                <th>Lot destination</th>
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
					                            	<th>Conteneur</th>
					                            	<?php
														$query = $db->prepare('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur = c.idConteneur WHERE m.idReserveElement = :idReserveElement;');
														$query->execute(array(
															'idReserveElement' => $_SESSION['transfertIdReserveElement']
														));
														$data = $query->fetch();
													?>
					                            	<td><?= $data['libelleConteneur'] ?></td>
					                            	<td></td>
					                            </tr>
					                            <tr>
					                            	<th>Quantité avant transfert</th>
					                            	<td><?= $data['quantiteReserve'] ?></td>
					                            	<td></td>
					                            </tr>
					                            <tr>
					                            	<th>Quantité à transferer</th>
					                            	<td><?= $_SESSION['transfertqttTrans'] ?></td>
					                            	<td></td>
					                            </tr>
					                            <tr>
					                            	<th>Quantité après transfert</th>
					                            	<td><?= $data['quantiteReserve'] - $_SESSION['transfertqttTrans'] ?></td>
					                            	<td></td>
					                            </tr>
					                            <tr>
					                            	<th>Lot</th>
					                            	<?php
														$query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE idElement = :idElement;');
														$query->execute(array(
															'idElement' => $_SESSION['transfertIdMaterielLot']
														));
														$data = $query->fetch();
													?>
					                            	<td></td>
					                            	<td><?= $data['libelleLot'] ?></td>
					                            </tr>
					                            <tr>
					                            	<th>Sac</th>
					                            	<td></td>
					                            	<td><?= $data['libelleSac'] ?></td>
					                            </tr>
					                            <tr>
					                            	<th>Emplacement</th>
					                            	<td></td>
					                            	<td><?= $data['libelleEmplacement'] ?></td>
					                            </tr>
					                            <tr>
					                            	<th>Quantité avant transfert</th>
					                            	<td></td>
					                            	<td><?= $data['quantite'] ?></td>
					                            </tr>
					                            <tr>
					                            	<th>Péremption avant transfert</th>
					                            	<td></td>
					                            	<td><?= $data['peremption'] ?></td>
					                            </tr>
					                            <tr>
					                            	<th>Quantité après transfert</th>
					                            	<td></td>
					                            	<td><?= $data['quantite'] + $_SESSION['transfertqttTrans'] ?></td>
					                            </tr>
					                            <tr>
					                            	<th>Péremption après transfert</th>
					                            	<td></td>
					                            	<td><?php
					                            	$query = $db->prepare('SELECT * FROM RESERVES_MATERIEL WHERE idReserveElement = :idReserveElement;');
													$query->execute(array(
												        'idReserveElement' => $_SESSION['transfertIdReserveElement']
												    ));
												    $reserve = $query->fetch();
					                            	if (isset($reserve['peremptionReserve']))
														{
															if ($data['peremption'] != Null AND $data['quantite'])
															{
																if ($data['peremption'] > $reserve['peremptionReserve'])
																{
																	echo $reserve['peremptionReserve'];
																}
																else
																{
																	echo $data['peremption'];
																}
															}
															else
															{
																echo $reserve['peremptionReserve'];
															}
														}
														else
														{
															echo $data['peremption'];
														}
					                            	?></td>
					                            </tr>
					                        </table>
				                            <div class="box-footer">
			                                    <a href="transfertReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir abandonner la transfert ?');">Abandon du transfert</a>
			                                    <?php if ($_SESSION['transfertStade']==4) { ?><a href="transfertResLotsGoOK.php" class="btn btn-success pull-right spinnerAttenteClick">Effectuer</a> <?php } ?>
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
										<h3 class="timeline-header no-border">Selection du matériel</h3>
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
										<h3 class="timeline-header no-border">Selection du conteneur source</h3>
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
										<h3 class="timeline-header no-border">Selection du lot de destination</h3>
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
			    <?php } ?>
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
