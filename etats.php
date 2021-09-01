<!DOCTYPE html>
<html>
<?php include('header.php'); require_once 'config/config.php'; ?>
<?php
session_start();
$_SESSION['page'] = 303;
require_once('logCheck.php');
?>
<?php
    if ($_SESSION['etats_lecture']==0)
        echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Configuration des libellés des états (lots, matériels, véhicules)
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Etats</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['etats_ajout']==1) {?>
                	<div class="box-header">
                        <h3 class="box-title">Etats des lots</h3>
                        <h3 class="box-title pull-right"><a href="etatsLotsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un état de lots</a></h3>
                	</div>
                <?php } ?>
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
                        $query = $db->query('SELECT * FROM LOTS_ETATS ORDER BY libelleLotsEtat;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idLotsEtat']; ?></td>
                                <td><?php echo $data['libelleLotsEtat']; ?></td>
                                <td>
                                    <?php if ($_SESSION['etats_modification']==1) {?>
                                        <a href="etatsLotsForm.php?id=<?=$data['idLotsEtat']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['etats_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=etatsLotsDelete&id=<?=$data['idLotsEtat']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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

            <div class="box">
                <?php if ($_SESSION['etats_ajout']==1) {?>
                    <div class="box-header">
                        <h3 class="box-title">Etats des matériels</h3>
                        <h3 class="box-title pull-right"><a href="etatsMaterielsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un état de matériels</a></h3>
                    </div>
                <?php } ?>
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
                        $query = $db->query('SELECT * FROM MATERIEL_ETATS ORDER BY libelleMaterielsEtat;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idMaterielsEtat']; ?></td>
                                <td><?php echo $data['libelleMaterielsEtat']; ?></td>
                                <td>
                                    <?php if ($_SESSION['etats_modification']==1) {?>
                                        <a href="etatsMaterielsForm.php?id=<?=$data['idMaterielsEtat']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['etats_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=etatsMaterielsDelete&id=<?=$data['idMaterielsEtat']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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

            <div class="box">
                <?php if ($_SESSION['etats_ajout']==1) {?>
                    <div class="box-header">
                        <h3 class="box-title">Etats des véhicules</h3>
                        <h3 class="box-title pull-right"><a href="etatsVehiculesForm.php" class="btn btn-sm btn-success modal-form">Ajouter un état de véhicules</a></h3>
                    </div>
                <?php } ?>
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
                        $query = $db->query('SELECT * FROM VEHICULES_ETATS ORDER BY libelleVehiculesEtat;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idVehiculesEtat']; ?></td>
                                <td><?php echo $data['libelleVehiculesEtat']; ?></td>
                                <td>
                                    <?php if ($_SESSION['etats_modification']==1) {?>
                                        <a href="etatsVehiculesForm.php?id=<?=$data['idVehiculesEtat']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['etats_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=etatsVehiculesDelete&id=<?=$data['idVehiculesEtat']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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
