<?php
	session_start();
	if($_SESSION['EXIT']) {header( "refresh:3;url=logout.php" );}
?>
<!DOCTYPE html>
<html>
<?php
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
		        		if($_SESSION['EXIT'])
		        		{
							
							echo '<div class="col-md-12">';
							echo '<div class="alert alert-success">';
					        echo '<i class="icon fa fa-check"></i> Déclaration enregistrée ! Vous serez redirigé vers l\'accueil dans 3 secondes ...';
					        echo '</div>';
					        echo '</div>';
		        			exit;
		        		}
		        	?>
		        	<?php
						if(is_null($_SESSION['nomDeclarantConsommation']))
						{ ?>
							<form role="form" class="spinnerAttenteSubmit" action="consommationBenevoleInit.php" method="POST">
			        			<div class="col-md-12">
						            <div class="box box-info">
						                <div class="box-header with-border">
						                	<i class="fa fa-user"></i>
						                    <h3 class="box-title">Informations générales</h3>
						                </div>
						                <div class="box-body">
						                	<div class="row">
						                		<div class="col-md-3">
								                	<div class="form-group">
							                            <input type="text" class="form-control" placeholder="Nom Prénom" name="nomDeclarantConsommation" required>
							                        </div>
							                    </div>
							                    <div class="col-md-3">
							                        <div class="form-group">
							                            <input type="text" class="form-control" placeholder="Libellé de l'évènement, poste de secours, manoeuvre, ..." name="evenementConsommation" required>
							                        </div>
							                    </div>
							                    <div class="col-md-3">
							                        <div class="input-group">
				                                        <div class="input-group-addon">
				                                            <i class="fa fa-calendar"></i>
				                                        </div>
				                                        <input class="input-datepicker form-control" placeholder="Date de l'évènement" name="dateConsommation" required>
				                                    </div>
							                    </div>
							                    <div class="col-md-3">
							                        <div class="form-group">
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
					                                <th class="all">Lot</th>
					                                <th class="all">Matériel utilisé</th>
					                                <th class="not-mobile">Quantité</th>
					                                <th class="not-mobile">Reconditionné</th>
					                                <th class="not-mobile"></th>
					                            </tr>
					                        </thead>
					                        <tbody>
					                        <?php
					                        foreach($_SESSION['consoArray'] as $line => $content)
					                        {
					                            ?>
					                            <tr>
					                                <td><?php
					                                	$query = $db->prepare('SELECT libelleLot FROM LOTS_LOTS WHERE idLot = :idLot;');
					                                	$query->execute(array('idLot' => $content[1]));
					                                	$data = $query->fetch();
					                                	echo $data['libelleLot'];
					                                ?></td>
					                                <td><?php
					                                	$query = $db->prepare('SELECT libelleMateriel FROM MATERIEL_CATALOGUE WHERE idMaterielCatalogue = :idMaterielCatalogue;');
					                                	$query->execute(array('idMaterielCatalogue' => $content[0]));
					                                	$data = $query->fetch();
					                                	echo $data['libelleMateriel'];
					                                ?></td>
					                                <td><?= $content[2] ?></td>
					                                <td><?php
					                                	if($content[3]>0)
					                                	{
					                                		$query = $db->prepare('SELECT libelleConteneur FROM RESERVES_CONTENEUR WHERE idConteneur = :idConteneur;');
						                                	$query->execute(array('idConteneur' => $content[3]));
						                                	$data = $query->fetch();
						                                	echo '<span class="badge bg-green">'.$data['libelleConteneur'].'</span>';
					                                	}
					                                	else
					                                	{
					                                		echo '<span class="badge bg-orange">Non-Reconditionné</span>';
					                                	}
					                            	?></td>
					                                <td>
					                                    <a href="consommationBenevoleDelete.php?id=<?=$line?>" class="btn btn-xs btn-danger" title="Supprimer"><i class="fa fa-trash"></i></a>
					                                </td>
					                            </tr>
					                            <?php
					                        }
					                        $query->closeCursor(); ?>
					                        <tr>
					                        	<td><a href="consommationBenevoleForm.php" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i> Ajouter</a></td>
					                        	<td></td>
					                        	<td></td>
					                        	<td></td>
					                        	<td></td>
					                        </tr>
					                        </tbody>
					                    </table>
					                </div>
					            </div>
					        </div>
					        <div class="col-md-12">
					        	<a href="consommationBenevoleSubmitForm.php" class="btn btn-xl btn-success pull-right modal-form" title="Envoyer"><i class="fa fa-envelope"></i> Envoyer à l'équipe logistique</a>
					        </div>
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
