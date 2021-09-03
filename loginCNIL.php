<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 000;
?>
<?php include('header.php'); require_once('config/config.php'); ?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>

    <!-- Main content -->
    <section class="content">
    	<div class="box box-warning">
            <div class="box-header with-border">
                <i class="fa fa-balance-scale"></i>
                <h3 class="box-title">Conditions générales</h3>
            </div>

            <!-- /.box-header -->
            <div class="box-body">
            	<?php
		        	$disclaimer = $db->query('SELECT cnilDisclaimer FROM CONFIG;');
		        	$disclaimer = $disclaimer->fetch();
		        	$disclaimer = $disclaimer['cnilDisclaimer'];
		        	echo nl2br($disclaimer);
		        ?>
            </div>
            <div class="box-footer">
	            <form action="loginCNILSQL.php" method="post">
		        	<div class="checkbox">
		                <label>
		                    <input type="checkbox" value="1" name="disclaimerAccept" required> J'ai lu, j'ai compris, et j'adhère aux conditions citées ci-dessus.
		                </label>
		            </div>
		            <button type="submit" class="btn btn-success">Accepter et continuer</button>
		            <a href="logout.php" class="btn btn-danger spinnerAttenteClick pull-right">Refuser</a>
		        </form>
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
