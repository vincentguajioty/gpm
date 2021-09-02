<!DOCTYPE html>
<html>
<?php
session_start();
require_once 'verrouIPcheck.php';
?>
<?php include('headerCaptcha.php'); require_once('config/config.php'); ?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini sidebar-collapse">
<div class="wrapper">
    <?php require_once('config/config.php'); ?>

    <?php
    	if (checkIP($_SERVER['REMOTE_ADDR'])==1)
		{
			echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
			exit;
		}
    	if(!$ALERTES_BENEVOLES_LOTS AND !$ALERTES_BENEVOLES_VEHICULES)
    	{
    		echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    		exit;
    	}
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
	        <!-- Content Header (Page header) -->
	        
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
	        	<div class="row">
	        		<form role="form" class="spinnerAttenteSubmit" action="alerteBenevoleAdd.php" method="POST">
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
					                            <input type="text" class="form-control" name="nomDeclarant" required>
					                        </div>
					                    </div>
					                    <div class="col-md-4">
					                        <div class="form-group">
					                            <label>Adresse eMail:</label>
					                            <input type="email" class="form-control" name="mailDeclarant" required>
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
				            </div>
				        </div>
				        
				        <?php
				        	if ($ALERTES_BENEVOLES_LOTS)
				        	{
				        ?>
					        <div class="col-md-6 col-sm-12 col-xs-12">
					        	<div class="box box-warning">
					                <div class="box-header with-border">
					                	<i class="fa fa-medkit"></i>
					                    <h3 class="box-title">J'ai rencontré un soucis avec un lot opérationnel</h3>
					                </div>
					                <div class="box-body">
					                	<div class="form-group">
				                            <label>Selectionner le lot qui a posé problème:</label>
				                            <select class="form-control select2" style="width: 100%;" name="idLot">
				                                <option value="">--- Aucun lot sélectionné ---</option>
				                                <?php
				                                $query2 = $db->query('SELECT * FROM LOTS_LOTS WHERE idEtat = 1 ORDER BY libelleLot;');
				                                while ($data2 = $query2->fetch())
				                                {
				                                    ?>
				                                    <option value="<?php echo $data2['idLot']; ?>" ><?php echo $data2['libelleLot']; ?></option>
				                                    <?php
				                                }
				                                $query2->closeCursor(); ?>
				                            </select>
				                        </div>
				                        <div class="form-group">
				                            <label>Description du problème</label>
				                            <textarea class="form-control" rows="5" name="messageAlerteLot"></textarea>
				                        </div>
					                </div>
					            </div>
					        </div>
				    	<?php } ?>
	
				    	<?php
				        	if ($ALERTES_BENEVOLES_VEHICULES)
				        	{
				        ?>
					        <div class="col-md-6 col-sm-12 col-xs-12">
					        	<div class="box box-warning">
					                <div class="box-header with-border">
					                    <i class="fa fa-ambulance"></i>
					                    <h3 class="box-title">J'ai rencontré un soucis avec un véhicule</h3>
					                </div>
					                <div class="box-body">
					                	<div class="form-group">
		                                    <label>Selectionner le véhicule qui a posé problème:</label>
		                                    <select class="form-control select2" style="width: 100%;" name="idVehicule">
		                                        <option value="">--- Aucun véhicule sélectionné ---</option>
		                                        <?php
		                                        $query2 = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 ORDER BY libelleVehicule;');
		                                        while ($data2 = $query2->fetch())
		                                        {
		                                            ?>
		                                            <option value ="<?php echo $data2['idVehicule']; ?>"><?php echo $data2['libelleVehicule']; ?></option>
		                                            <?php
		                                        }
		                                        $query2->closeCursor(); ?>
		                                    </select>
		                                </div>
		                                <div class="form-group">
				                            <label>Description du problème</label>
				                            <textarea class="form-control" rows="5" name="messageAlerteVehicule"></textarea>
				                        </div>
					                </div>
					            </div>
					        </div>
				        <?php } ?>
	
	
				        <div class="col-md-12">
					        <center><button type="submit" class="btn btn-success">ENVOYER !</button></center>
					    </div>
	        		</form>	        
		        </div>
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
