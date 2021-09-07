<!DOCTYPE html>
<html>
<?php
session_start();
require_once 'verrouIPcheck.php';
?>
<?php include('headerCaptcha.php'); require_once('config/config.php'); ?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini fixed">
<div class="wrapper">
    <?php require_once('config/config.php'); ?>

    <?php
    	if (checkIP($_SERVER['REMOTE_ADDR'])==1)
		{
			echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
			exit;
		}
    	if(!$CONSOMMATION_BENEVOLES)
    	{
    		echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    		exit;
    	}
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
	        <!-- Content Header (Page header) -->

	    <?php include('bandeausupPublic.php'); ?>
	        
        <?php
			if ($MAINTENANCE)
			{
				echo '<div class="alert alert-warning alert-dismissible">';
		        echo '<i class="icon fa fa-wrench"></i> Ce site est actuellement en maintenance et ne peut pas être utilisé. Merci de revenir plus tard.';
		        echo '</div>';
			}
			else
			{
		?>
	        <section class="content-header">
	            <h1>
	                <?= $APPNAME ?> - Communiquez avec votre équipe logistique !
	            </h1>
	        </section>
			
	        <!-- Main content -->
	        <section class="content">
	        	<?php include('confirmationBox.php'); ?>
	        	<?php
					if(is_null($_SESSION['nomDeclarantConsommation']))
					{ ?>
						<form role="form" class="spinnerAttenteSubmit" action="consommationBenevoleInit.php" method="POST">
		        			<div class="col-md-12">
					            <div class="box box-info">
					                <div class="box-header with-border">
					                	<i class="fa fa-user"></i>
					                    <h3 class="box-title">Mes informations</h3>
					                </div>
					                <div class="box-body">
					                	<div class="row">
					                		<div class="col-md-4">
							                	<div class="form-group">
						                            <label>Nom et Prenom:</label>
						                            <input type="text" class="form-control" name="nomDeclarantConsommation" required>
						                        </div>
						                    </div>
						                    <div class="col-md-4">
						                        <div class="form-group">
						                            <label>Evenement:</label>
						                            <input type="text" class="form-control" name="evenementConsommation" required>
						                        </div>
						                    </div>
						                    <div class="col-md-4">
						                        <div class="form-group">
						                            <label>IP:</label>
						                            <input type="email" class="form-control" name="ipDeclarant" value="<?=$_SERVER['REMOTE_ADDR']?>" disabled>
						                            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" >
						                        </div>
						                    </div>
				                    	</div>
					                </div>
					                <div class="box-footer">
					                	<button type="submit" class="btn btn-primary pull-right">Suivant</button>
					                </div>
					            </div>
					        </div>
					    </form>
					<?php
					}else{
					?>
						<?= $_SESSION['nomDeclarantConsommation'] ?>
						<br/>
						<?= $_SESSION['dateConsommation']         ?>
						<br/>
						<?= $_SESSION['evenementConsommation']    ?>
						<br/>
						<?= $_SESSION['ipDeclarantConsommation']  ?>
					<?php }
				?>
	        </section>
	    <?php } ?>
        
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php include('scripts.php'); ?>

<?php
	if($RECAPTCHA_ENABLE)
	{ ?>
		<script>
			grecaptcha.ready(function() {
	    		grecaptcha.execute('<?= $RECAPTCHA_SITEKEY ?>', {action: 'alerteBenevole'}).then(function(token) {
	    			document.getElementById('g-recaptcha-response').value=token;
	    		});
			});
		</script>
	<?php }
?>
</body>
</html>
