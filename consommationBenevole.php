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
	        	<?php
	        		if(isset($_SESSION['evenementConsommation']))
	        		{ ?>
	        			<h1>
			                Matériel utilisé sur l'évènement "<?=$_SESSION['evenementConsommation']?>"
			            </h1>
	        		<?php }
	        		else
	        		{ ?>
	        			<h1>
			                <?= $APPNAME ?> - Tracez le consommable utilisé !
			            </h1>
	        		<?php }
	        	?>
	            
	        </section>
			
	        <!-- Main content -->
	        <section class="content">
	        	<div class="row">
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
							                            <label>Evènement:</label>
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
							<div class="col-md-12">
					            <div class="box box-info">
					                <div class="box-header with-border">
					                	<i class="fa fa-medkit"></i>
					                    <h3 class="box-title">Matériel consommé</h3>
					                </div>
					                <div class="box-body">
					                	<table id="tri1" class="table table-bordered table-hover">
					                        <thead>
					                            <tr>
					                                <th class="all">Matériel</th>
					                                <th class="all">Lot</th>
					                                <th class="all">Quantité</th>
					                                <th class="all">Reconditionné</th>
					                                <th class="all">Actions</th>
					                            </tr>
					                        </thead>
					                        <tbody>
					                        <?php
					                        foreach($_SESSION['consoArray'] as $item)
					                        {
					                            ?>
					                            <tr>
					                                <td><?php
					                                	$query = $db->prepare('SELECT libelleMateriel FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue;');
					                                	$query->execute(array('idMaterielCatalogue' => $item[0]));
					                                	$data = $query->fetch();
					                                	echo $data['libelleMateriel'];
					                                ?></td>
					                                <td><?php
					                                	$query = $db->prepare('SELECT libelleLot FROM LOTS_LOTS WHERE idLot = :idLot;');
					                                	$query->execute(array('idLot' => $item[1]));
					                                	$data = $query->fetch();
					                                	echo $data['libelleLot'];
					                                ?></td>
					                                <td><?= $item[2] ?></td>
					                                <td><?php
					                                	if($item[3]>0)
					                                	{
					                                		$query = $db->prepare('SELECT libelleConteneur FROM RESERVES_CONTENEUR WHERE idConteneur = :idConteneur;');
						                                	$query->execute(array('idConteneur' => $item[3]));
						                                	$data = $query->fetch();
						                                	echo $data['libelleConteneur'];
					                                	}
					                                	else
					                                	{
					                                		echo "Non-reconditionné";
					                                	}
					                            	?></td>
					                                <td>
					                                    BOUTONS
					                                </td>
					                            </tr>
					                            <?php
					                        }
					                        $query->closeCursor(); ?>
					                        <tr>
					                        	<td></td>
					                        	<td></td>
					                        	<td></td>
					                        	<td></td>
					                        	<td><a href="consommationBenevoleForm.php" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a></td>
					                        </tr>
					                        </tbody>
					                    </table>
					                </div>
					            </div>
					        </div>
							<form role="form" class="spinnerAttenteSubmit" action="consommationBenevoleComments.php" method="POST">
			        			<div class="col-md-12">
						            <div class="box box-info">
						                <div class="box-header with-border">
						                	<i class="fa fa-comments"></i>
						                    <h3 class="box-title">Commentaires</h3>
						                </div>
						                <div class="box-body">
						                	<div class="form-group">
					                            <textarea class="form-control" rows="3" name="commentairesConsommation"><?= $_SESSION['commentairesConsommation'] ?></textarea>
					                        </div>
						                </div>
						                <div class="box-footer">
						                	<button type="submit" class="btn btn-primary pull-right">Enregistrer le commentaire</button>
						                </div>
						            </div>
						        </div>
						    </form>
						<?php }
					?>
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
