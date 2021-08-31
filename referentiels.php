<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/version.php'); ?>
<?php
session_start();
$_SESSION['page'] = 201;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['typesLots_lecture']==0)
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
                Référentiels des lots opérationnels
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Référentiels</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="box-header">
                        <?php if ($_SESSION['typesLots_ajout']==1) {?>
                            <h3 class="box-title"><a href="referentielsForm.php?id=0" class="btn btn-sm btn-success">Ajouter un référentiel</a></h3>
                        <?php } else {?>
                            </br>
                        <?php } ?>
                    </div>
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Libelle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM LOTS_TYPES ORDER BY libelleTypeLot;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idTypeLot']; ?></td>
                                <td><?php echo $data['libelleTypeLot']; ?></td>
                                <td>
                                    <?php if ($_SESSION['typesLots_lecture']==1) {?>
                                        <a href="referentielsContenu.php?id=<?=$data['idTypeLot']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['typesLots_modification']==1) {?>
                                        <a href="referentielsForm.php?id=<?=$data['idTypeLot']?>" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['typesLots_suppression']==1) {?>
                                        <a href="referentielsDelete.php?id=<?=$data['idTypeLot']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
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
