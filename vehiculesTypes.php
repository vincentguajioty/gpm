<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 306;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['vehicules_types_lecture']==0 AND $_SESSION['typesDesinfections_lecture']==0 AND $_SESSION['vehiculeHealthType_lecture']==0)
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
                Types de véhicules
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Types de véhicules</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            
            <?php
            	if ($_SESSION['vehicules_types_lecture']==1)
            	{ ?>
		            <div class="box">
		        		<div class="box-header">
		        			<h3 class="box-title">Types de véhicules</h3>
		        			<?php if ($_SESSION['vehicules_types_ajout']==1) {?>
		                    	<h3 class="box-title pull-right"><a href="vehiculesTypesForm.php" class="btn btn-sm btn-success modal-form">Ajouter un type</a></h3>
		                    <?php } ?>
		            	</div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                    <table class="table table-hover">
		                        <thead>
		                            <tr>
		                                <th>#</th>
		                                <th>Libelle</th>
		                                <th>Actions</th>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        <?php
		                        $query = $db->query('SELECT * FROM VEHICULES_TYPES ORDER BY libelleType;');
		                        while ($data = $query->fetch())
		                        {?>
		                            <tr>
		                                <td><?php echo $data['idVehiculesType']; ?></td>
		                                <td><?php echo $data['libelleType']; ?></td>
		                                <td>
		                                    <?php if ($_SESSION['vehicules_types_modification']==1) {?>
		                                        <a href="vehiculesTypesForm.php?id=<?=$data['idVehiculesType']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
		                                    <?php }?>
		                                    <?php if ($_SESSION['vehicules_types_suppression']==1) {?>
		                                        <a href="modalDeleteConfirm.php?case=vehiculesTypesDelete&id=<?=$data['idVehiculesType']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
		                                    <?php }?>
		                                </td>
		                            </tr>
		                            <?php
		                        }
		                        $query->closeCursor(); ?>
		                        </tbody>
		
		
		                    </table>
		                </div>
		            </div>
		    <?php } ?>
            
            <?php
            	if ($_SESSION['typesDesinfections_lecture']==1)
            	{ ?>
		            <div class="box">
		        		<div class="box-header">
		        			<h3 class="box-title">Types de désinfections</h3>
		        			<?php if ($_SESSION['typesDesinfections_ajout']==1) {?>
		                    	<h3 class="box-title pull-right"><a href="vehiculesTypesDesinfectionsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un type</a></h3>
		                    <?php } ?>
		            	</div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                    <table class="table table-hover">
		                        <thead>
		                            <tr>
		                                <th>#</th>
		                                <th>Libellé</th>
		                                <th>Affiché sur l'écran de synthèse</th>
		                                <th>Actions</th>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        <?php
		                        $query = $db->query('SELECT * FROM VEHICULES_DESINFECTIONS_TYPES ORDER BY libelleVehiculesDesinfectionsType;');
		                        while ($data = $query->fetch())
		                        {?>
		                            <tr>
		                                <td><?php echo $data['idVehiculesDesinfectionsType']; ?></td>
		                                <td><?php echo $data['libelleVehiculesDesinfectionsType']; ?></td>
		                                <td><?php if($data['affichageSynthese'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
		                                <td>
		                                    <?php if ($_SESSION['typesDesinfections_modification']==1) {?>
		                                        <a href="vehiculesTypesDesinfectionsForm.php?id=<?=$data['idVehiculesDesinfectionsType']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
		                                    <?php }?>
		                                    <?php if ($_SESSION['typesDesinfections_suppression']==1) {?>
		                                        <a href="modalDeleteConfirm.php?case=vehiculesTypesDesinfectionsDelete&id=<?=$data['idVehiculesDesinfectionsType']?>" class="btn btn-xs btn-danger modal-form" title="Suppimer"><i class="fa fa-trash"></i></a>
		                                    <?php }?>
		                                </td>
		                            </tr>
		                            <?php
		                        }
		                        $query->closeCursor(); ?>
		                        </tbody>
		                    </table>
		                </div>
		            </div>
		    <?php } ?>
            
            <?php
            	if ($_SESSION['vehiculeHealthType_lecture']==1)
            	{ ?>
		            <div class="box">
		        		<div class="box-header">
		                    <h3 class="box-title">Types de maintenances régulières</h3>
		                    <?php if ($_SESSION['vehiculeHealthType_ajout']==1) {?>
		                    	<h3 class="box-title pull-right"><a href="vehiculesTypesMaintenanceForm.php" class="btn btn-sm btn-success modal-form">Ajouter un type</a></h3>
		                    <?php } ?>
		            	</div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                    <table class="table table-hover">
		                        <thead>
		                            <tr>
		                                <th>#</th>
		                                <th>Libellé</th>
		                                <th>Affiché sur l'écran de synthèse</th>
		                                <th>Actions</th>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        <?php
		                        $query = $db->query('SELECT * FROM VEHICULES_HEALTH_TYPES ORDER BY libelleHealthType;');
		                        while ($data = $query->fetch())
		                        {?>
		                            <tr>
		                                <td><?php echo $data['idHealthType']; ?></td>
		                                <td><?php echo $data['libelleHealthType']; ?></td>
		                                <td><?php if($data['affichageSynthese'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
		                                <td>
		                                    <?php if ($_SESSION['vehiculeHealthType_modification']==1) {?>
		                                        <a href="vehiculesTypesMaintenanceForm.php?id=<?=$data['idHealthType']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
		                                    <?php }?>
		                                    <?php if ($_SESSION['vehiculeHealthType_suppression']==1) {?>
		                                        <a href="modalDeleteConfirm.php?case=vehiculesTypesMaintenanceDelete&id=<?=$data['idHealthType']?>" class="btn btn-xs btn-danger modal-form" title="Suppimer"><i class="fa fa-trash"></i></a>
		                                    <?php }?>
		                                </td>
		                            </tr>
		                            <?php
		                        }
		                        $query->closeCursor(); ?>
		                        </tbody>
		                    </table>
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
