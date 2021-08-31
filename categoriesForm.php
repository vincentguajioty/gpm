<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 302;
require_once('logCheck.php');
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
                Etats des lots opérationnels
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="etats.php">Catégories de matériel</a></li>
                <li class="active">Ajouter/Modifier une catégorie</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php
            if ($_GET['id'] == 0) {
                ?>
                <!-- general form elements disabled -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ajout d'une catégorie</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="categoriesAdd.php" method="POST">
                            <!-- text input -->
                            <div class="form-group">
                                <label>Libellé:</label>
                                <input type="text" class="form-control" placeholder="Libellé de la catégorie à ajouter"
                                       name="libelleCategorie" required>
                            </div>
                            <div class="box-footer">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->

                </div>

                <?php
            }
            else {
                ?>

                <!-- general form elements disabled -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Modification d'une catégorie</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="categoriesUpdate.php?id=<?=$_GET['id']?>" method="POST">
                            <?php
                            $query = $db->prepare('SELECT * FROM MATERIEL_CATEGORIES WHERE idCategorie=:idCategorie;');
                            $query->execute(array('idCategorie' => $_GET['id']));
                            $data = $query->fetch();
                            ?>

                            <div class="form-group">
                                <label>Libellé</label>
                                <input type="text" class="form-control" value="<?=$data['libelleCategorie']?>"
                                       name="libelleCategorie" required>
                            </div>

                            <div class="box-footer">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <button type="submit" class="btn btn-info pull-right">Modifier</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->

                </div>

                <?php
            }
            ?>

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
