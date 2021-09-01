<!DOCTYPE html>
<html>
<?php include('header.php');?>
<?php
session_start();
$_SESSION['page'] = 401;
require_once('logCheck.php');
require_once('config/config.php');
?>
<?php
if ($_SESSION['annuaire_ajout']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>

<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>

    <?php
        if(!isset($_SESSION['importStade']) OR $_SESSION['importStade']==0)
        {
			$_SESSION['importStade'] = 1;
			$query = $db->query('TRUNCATE PERSONNE_REFERENTE_TEMP;');
        }
    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Import d'utilisateurs dans <?= $APPNAME ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="annuaire.php"><i class="fa fa-home"></i>Annuaire</a></li>
                <li class="active">Import d'utilisateurs</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
			<div class="row">
	            <div class="col-md-8">
	                <div class="nav-tabs-custom">
	                    <ul class="nav nav-tabs">
                            <?php if($_SESSION['importStade']>0){?><li <?= $_SESSION['importStade']==1 ? 'class="active"' : ''?>><a href="#1" data-toggle="tab">Récupérer le modèle</a></li><?php } ?>
                            <?php if($_SESSION['importStade']>1){?><li <?= $_SESSION['importStade']==2 ? 'class="active"' : ''?>><a href="#2" data-toggle="tab">Charger le modèle</a></li><?php } ?>
	                        <?php if($_SESSION['importStade']>2){?><li <?= $_SESSION['importStade']==3 ? 'class="active"' : ''?>><a href="#3" data-toggle="tab">Selectionner les profils</a></li><?php } ?>
	                    </ul>
	                    <div class="tab-content">
							<?php if($_SESSION['importStade']>0){?>
		                        <div class="<?= $_SESSION['importStade']==1 ? 'active' : ''?> tab-pane" id="1">
                                	<div class="box-body">
                                		<center><a href="annuaireImportDLtemplate.php" class="btn btn-success">Telecharger le modèle</a></center>
                                	</div>
	                                <div class="box-footer">
	                                    <a href="annuaireImportReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sur de vouloir annuler l\'import ?');">Abandon de l'import</a>
	                                    <?php if ($_SESSION['importStade']==1) { ?><a href="annuaireImportGo2.php" class="btn btn-info pull-right">Suivant</a><?php } ?>
	                                </div>
		                        </div>
		                    <?php } ?>
							<?php if($_SESSION['importStade']>1){?>
								<div class="<?= $_SESSION['importStade']==2 ? 'active' : ''?> tab-pane" id="2">
		                            <form role="form" class="spinnerAttenteSubmit" action="annuaireImportGo3.php" method="POST" enctype="multipart/form-data">
	                                    <div class="form-group">
				                            <label>Modèle rempli: <small style="color:grey;">Requis</small></label>
				                            <input style="width: 100%;" type="file" name="urlTemplate" id="urlTemplate" required>
				                        </div>
	                                
		                                <div class="box-footer">
		                                    <a href="annuaireImportReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sur de vouloir annuler l\'import ?');">Abandon de l'import</a>
		                                    <?php if ($_SESSION['importStade']==2) { ?><button type="submit" class="btn btn-info pull-right">Suivant</button> <?php } ?>
		                                </div>
		                            </form>
		                        </div>
							<?php } ?>	
							<?php if($_SESSION['importStade']>2){?>
								<div class="<?= $_SESSION['importStade']==3 ? 'active' : ''?> tab-pane" id="3">
		                            <form role="form" class="spinnerAttenteSubmit" action="annuaireImportGoOK.php" method="POST">
			                            <div class="box-body">
				                            <div class="form-group">
					                            <label>Profils d'habilitation: </label>
					                            <select class="form-control select2" style="width: 100%;" name="idProfil[]" multiple <?php if($_SESSION['profils_modification']==0){ echo 'disabled'; } ?>>
					                                <?php
										            if (isset($_GET['id']))
										            {
										                $query2 = $db->prepare('SELECT ao.*, aop.idPersonne FROM PROFILS ao LEFT JOIN PROFILS_PERSONNES aop ON (ao.idProfil = aop.idProfil AND aop.idPersonne = :idPersonne) ORDER BY libelleProfil;');
										                $query2->execute(array('idPersonne' => $_GET['id']));
										            }
										            else
										            {
										                $query2 = $db->query('SELECT * FROM PROFILS ORDER BY libelleProfil;');
										            }
					
					                                while ($data2 = $query2->fetch())
					                                {
					                                    
					                                    echo '<option value=' . $data2['idProfil'];
					
										                if (isset($data2['idPersonne']) AND $data2['idPersonne'])
										                {
										                    echo " selected ";
										                }
										                echo '>' . $data2['libelleProfil'] . '</option>';
					                                }
					                                $query2->closeCursor();?>
					                            </select>
					                        </div>
				                        	
				                        	<div class="form-group">
					                            <label>Vérification des données:</label>
		                                		<table class="table table-bordered">
			                                		<tr>
			                                			<th>Identifiant</th>
			                                			<th>Nom</th>
			                                			<th>Prenom</th>
			                                			<th>Mail</th>
			                                			<th>Téléphone</th>
			                                			<th>Fonction</th>
			                                			<th>Mail de création</th>
			                                		</tr>
		                                		
		                                			<?php
		                                				$query = $db->query('SELECT * FROM PERSONNE_REFERENTE_TEMP;');
								                        while ($data = $query->fetch())
								                        {?>
								                        	<tr>
									                        	<td><?php echo $data['identifiant']; ?></td>
								                                <td><?php echo $data['nomPersonne']; ?></td>
								                                <td><?php echo $data['prenomPersonne']; ?></td>
								                                <td><?php echo $data['mailPersonne']; ?></td>
								                                <td><?php echo $data['telPersonne']; ?></td>
								                                <td><?php echo $data['fonction']; ?></td>
								                                <td><?php
								                                	if ($data['mailCreation']==1)
							                                        {
							                                            ?><span class="badge bg-green">Oui</span><?php
							                                        }
							                                        else
							                                        {
							                                            ?><span class="badge bg-orange">Non</span><?php
							                                        }
		                                        					?>
		                                        				</td>
								                            </tr>
								                        <?php }
		                                			?>
		                                		</table>
	                                		</div>
	                                	</div>
		                                <div class="box-footer">
		                                    <a href="annuaireImportReset.php" class="btn btn-danger" onclick="return confirm('Etes-vous sur de vouloir annuler l\'import ?');">Abandon de l'import</a>
		                                    <?php if ($_SESSION['importStade']==3) { ?><button type="submit" class="btn btn-info pull-right">Valider l'import</button><?php } ?>
		                                </div>
		                            </form>
		                        </div>
	                    	<?php } ?>   
	                    </div>
	                </div>
	            </div>
	            
	            <div class="col-md-4">
	            	<ul class="timeline">
			            <li class="time-label">
							<span class="bg-blue">Import d'utilisateurs</span>
			            </li>
			            <li>
							<?php
								if ($_SESSION['importStade'] == 1)
								{
									echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
								}
								else if ($_SESSION['importStade'] > 1)
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
								else
								{
									echo '<i class="fa fa-spinner bg-grey"></i>';
								}
							?>
							<div class="timeline-item">
								<h3 class="timeline-header no-border">Télécharger le modèle à remplir</h3>
							</div>
			            </li>
			            <li>
							<?php
								if ($_SESSION['importStade'] == 2)
								{
									echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
								}
								else if ($_SESSION['importStade'] > 2)
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
								else
								{
									echo '<i class="fa fa-spinner bg-grey"></i>';
								}
							?>
							<div class="timeline-item">
								<h3 class="timeline-header no-border">Charger le fichier rempli</h3>
							</div>
			            </li>
			            <li>
							<?php
								if ($_SESSION['importStade'] == 3)
								{
									echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
								}
								else if ($_SESSION['importStade'] > 3)
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
								else
								{
									echo '<i class="fa fa-spinner bg-grey"></i>';
								}
							?>
							<div class="timeline-item">
								<h3 class="timeline-header no-border">Selectionner les profils</h3>
							</div>
			            </li>
			            <li>
			              <i class="fa fa-download bg-gray"></i>
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
