<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 307;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['typesDesinfections_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Types de désinfections pour les véhicules
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Types de désinfection</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
            	<?php if ($_SESSION['typesDesinfections_ajout']==1) {?>
            		<div class="box-header">
                        <h3 class="box-title"><a href="vehiculesTypesDesinfectionsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un type</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Libellé</th>
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
