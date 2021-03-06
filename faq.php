<!DOCTYPE html>
<html>
<?php include('header.php'); ?>
<?php
session_start();
$_SESSION['page'] = 502;
require_once('logCheck.php');
require_once 'config/config.php';
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
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
                <div class="col-md-4">
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
                            <br/>
                            <a href="https://github.com/vincentguajioty/gpm/issues/new/choose">Les tickets GitHub sont également un bon moyen de communiquer !</a>
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
                            <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" src="https://i.creativecommons.org/l/by-nc-sa/4.0/80x15.png" /></a> Elements graphiques du site: <a href="https://adminlte.io/">AdminLTE</a> ; Application: Vincent Guajioty
                            <br/><br/>
                            Version <?php echo $VERSION; ?> <a href="https://github.com/vincentguajioty/gpm"><b style="color:red;"><?php echo file_get_contents("https://maj.guajioty.fr/gpmMAJ.php?versionClient=".$VERSION); ?></b></a>
                            <br/><br/>
                            Adresse mail de l'administrateur de cette instance de GPM: <?php echo $MAILSERVER; ?>
                            <br/><br/>
                            Adresse mail du référent CNIL de cette instance de GPM: <?php echo $MAILCNIL; ?>
                            <br/><br/>
                            <a href="contact.php">Formulaire de contact du developpeur</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="box box-info">

                        <div class="box-header with-border">
                            <i class="fa fa-balance-scale"></i>

                            <h3 class="box-title">Conditions générales</h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <?php
                            	$disclaimer = $db->query('SELECT cnilDisclaimer FROM CONFIG;');
                            	$disclaimer = $disclaimer->fetch();
                            	$disclaimer = $disclaimer['cnilDisclaimer'];
                            	echo nl2br($disclaimer);
                            ?>
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
