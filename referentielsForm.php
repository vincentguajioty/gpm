<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 201;
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
                Référentiels des lots opérationnels
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="referentiels.php">Référentiels</a></li>
                <li class="active">Ajouter/Modifier un référentiel</li>
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
                        <h3 class="box-title">Ajout d'un référentiel</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="referentielsAdd.php" method="POST">
                            <!-- text input -->
                            <div class="form-group">
                                <label>Libellé du référentiel:</label>
                                <input type="text" class="form-control" placeholder="Exemple: Lot A"
                                       name="libelleTypeLot" required>
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
                        <h3 class="box-title">Modification d'un référentiel</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="referentielsUpdate.php?id=<?=$_GET['id']?>" method="POST">
                            <?php
                            $query = $db->prepare('SELECT * FROM LOTS_TYPES WHERE idTypeLot=:idTypeLot;');
                            $query->execute(array('idTypeLot' => $_GET['id']));
                            $data = $query->fetch();
                            ?>
                            <!-- text input -->
                            <div class="form-group">
                                <label>Libellé du référentiel:</label>
                                <input type="text" class="form-control" value="<?=$data['libelleTypeLot']?>" name="libelleTypeLot" required>
                            </div>
                            <div class="box-footer">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <?php if ($_SESSION['typesLots_modification']==1) { ?>
                                    <button type="submit" class="btn btn-info pull-right">Modifier</button>
                                <?php } ?>
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
