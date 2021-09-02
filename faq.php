<!DOCTYPE html>
<html>
<?php include('header.php'); ?>
<?php
session_start();
$_SESSION['page'] = 502;
require_once('logCheck.php');
require_once 'config/config.php';
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                A propos
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">A propos</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-8">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <i class="fa fa-clock-o"></i>

                            <h3 class="box-title">Développements et mises à jour de GPM</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <?php echo file_get_contents("https://maj.guajioty.fr/gpmTimeLine.php?versionClient=".$VERSION); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <i class="fa fa-paper-plane-o"></i>

                            <h3 class="box-title">Vos suggestions</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <?php echo file_get_contents("https://maj.guajioty.fr/gpmDevStack.php"); ?>
                            <a href="contact.php">N'hésitez pas à me faire parvenir vos idées !</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-info">

                        <div class="box-header with-border">
                            <i class="fa fa-bank"></i>

                            <h3 class="box-title">Copyright et Version</h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" src="https://i.creativecommons.org/l/by-nc-sa/4.0/80x15.png" /></a> Copyright &copy; 2017 Vincent Guajioty. All rights reserved.
                            <br/><br/>
                            Version <?php echo $VERSION; ?> <a href="http://maj.guajioty.fr/gpm.zip"><b style="color:red;"><?php echo file_get_contents("https://maj.guajioty.fr/gpmMAJ.php?versionClient=".$VERSION); ?></b></a>
                            <br/><br/>
                            Adresse mail de l'administrateur de cette instance de GPM: <?php echo $MAILSERVER; ?>
                            <br/><br/>
                            <a href="contact.php">Formulaire de contact du developpeur</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
