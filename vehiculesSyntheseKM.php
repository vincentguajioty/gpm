<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1004;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['vehicules_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Evolution kilométrique
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Relevés kilométriques</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="row">
            
	            <div class="col-lg-12">
	    			<form role="form" action="vehiculesSyntheseKMgo.php" method="POST">
						<div class="box box-success">
							<div class="box-header with-border">
								<h3 class="box-title">Selection de la plage</h3>
							</div>
							<div class="box-body chart-responsive">
								<div class="col-lg-6">
									<div class="form-group">
				                        <label>Date inférieure:</label>
				                        <div class="input-group">
				                            <div class="input-group-addon">
				                                <i class="fa fa-calendar"></i>
				                            </div>
				                            <input class="input-datepicker form-control" name="dateInf" value="<?=$_GET['dateInf']?>" required>
				                        </div>
				                    </div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
				                        <label>Date suppérieure:</label>
				                        <div class="input-group">
				                            <div class="input-group-addon">
				                                <i class="fa fa-calendar"></i>
				                            </div>
				                            <input class="input-datepicker form-control" name="dateSup" value="<?=$_GET['dateSup']?>" required>
				                        </div>
				                    </div>
								</div>
								<button type="submit" class="btn btn-primary pull-right">Actualiser</button>
							</div>
						</div>
					</form>
				</div>
	            	
				<div class="col-md-12">
	                <div class="box box-success">
	                    <div class="box-header with-border">
	                        <h3 class="box-title">Relevés kilométriques</h3>
	                    </div>
	                    <div class="box-body chart-responsive">
	                        <div class="chart" id="line-chart"></div>
	                    </div>
	                </div>
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

<script>
    $(function () {
        "use strict";
        
        <?php
            $vehicules = $db->query("SELECT idVehicule, libelleVehicule, couleurGraph FROM VEHICULES;");
            $vehicules = $vehicules->fetchAll();

            $releves = $db->prepare("SELECT * FROM VIEW_VEHICULES_KM WHERE dateReleve IS NOT Null AND dateReleve > :dateInf AND dateReleve < :dateSup ORDER BY dateReleve DESC;");
            $releves->execute(array(
            	'dateInf' => $_GET['dateInf'],
            	'dateSup' => $_GET['dateSup'],
            ));
        ?>
        
        var line = new Morris.Line({
          element: 'line-chart',
          resize: true,
          data: [
            <?php
                while($releve = $releves->fetch())
                {
                    echo "{x: '".$releve['dateReleve']."', ".$releve['idVehicule'].": ".$releve['releveKilometrique']."},";
                }
            ?>
          ],
          xkey: 'x',
          ykeys: [<?php foreach($vehicules as $vehicule){echo "'".$vehicule['idVehicule']."',";} ?>],
          labels: [<?php foreach($vehicules as $vehicule){echo "'".$vehicule['libelleVehicule']."',";} ?>],
          lineColors: [<?php foreach($vehicules as $vehicule){echo "'".$vehicule['couleurGraph']."',";} ?>],
          hideHover: 'auto'
        });
  });
</script>


</body>
</html>
