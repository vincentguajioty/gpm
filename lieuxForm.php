<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 304;
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
                Lieux de stockage des lots
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="lieux.php">Lieux</a></li>
                <li class="active">Ajouter/Modifier un lieu</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Modification d'un lieu</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="lieuxUpdate.php?id=<?=$_GET['id']?>" method="POST">
                        <?php
                        $query = $db->prepare('SELECT * FROM LIEUX WHERE idLieu=:idLieu;');
                        $query->execute(array('idLieu' => $_GET['id']));
                        $data = $query->fetch();
                        ?>
                        <!-- text input -->
                        <div class="form-group">
                            <label>Libellé du lieu de stockage:</label>
                            <input type="text" class="form-control" value="<?=$data['libelleLieu']?>" name="libelleLieu" required>
                        </div>
                        <!-- textarea -->
                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea class="form-control" rows="3" name="adresseLieu"><?=$data['adresseLieu']?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Contrôle d'accès:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="accesReserve" id="optionsRadios1" value="option1"
                                           <?php if ($data['accesReserve']==0)
                                               echo 'checked'?>
                                           >
                                    Accès libre
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="accesReserve" id="optionsRadios2" value="option2"
                                        <?php if ($data['accesReserve']==1)
                                            echo 'checked'?>
                                            >
                                    Accès règlementé
                                </label>
                            </div>
                        </div>

                        <!-- textarea -->
                        <div class="form-group">
                            <label>Détails</label>
                            <textarea class="form-control" rows="3" name="detailsLieu"><?=$data['detailsLieu']?></textarea>
                        </div>
                        <div class="box-footer">
                            <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->

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
