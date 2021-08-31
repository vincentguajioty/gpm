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
	                                <form role="form" action="transfertResLotsGo2.php" method="POST">
	                                    <div class="form-group">
	                                        <label>Catalogue: </label>
	                                        <select <?= $_SESSION['transfertStade']!=1 ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idMaterielCatalogue">
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
		                            <form role="form" action="transfertResLotsGo3.php" method="POST">
										<div class="form-group">
	                                        <label>Conteneur source: </label>
	                                        <select <?= $_SESSION['transfertStade']!=2 ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idReserveElement">
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
		                            <form role="form" action="transfertResLotsGo4.php" method="POST">
	                                    <div class="form-group">
	                                        <label>Lot de destination: </label>
	                                        <select <?= $_SESSION['transfertStade']!=3 ? 'disabled' : '' ?> class="form-control select2" style="width: 100%;" name="idElement">
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
				                            <label>Quantité à transférer (1 - <?= $_SESSION['transfertQttMax'] ?>):</label>
				                            <input <?php if (isset($_SESSION['transfertqttTrans'])){ echo 'value="'.$_SESSION['transfertqttTrans'].'"'; }else{ echo 'value="1"'; } ?> <?= $_SESSION['transfertStade']!=3 ? 'disabled' : '' ?> type="number" class="form-control" name="qttTrans" min="1" max="<?= $_SESSION['transfertQttMax'] ?>" required>
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
		                            RECAP A FAIRE
		                            <div class="box-footer">
	                                    <a href="transfertReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir abandonner la transfert ?');">Abandon du transfert</a>
	                                    <?php if ($_SESSION['transfertStade']==4) { ?><a href="transfertResLotsGoOK.php" class="btn btn-success pull-right">Effectuer</a> <?php } ?>
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
