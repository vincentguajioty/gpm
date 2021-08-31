<!DOCTYPE html>
<html>
<?php
session_start();
require_once('logCheck.php');
require_once 'config/config.php';
?>
<?php include('header.php'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">


        <!-- Main content -->
        <section class="content">
            <div class="error-page">

                <div class="error-content">
                    <h3><i class="fa fa-warning text-red"></i> Oups! Erreur d'habilitation.</h3>

                    <p>
                        <?php echo $APPNAME; ?> est équipé d'un dispositif de controle d'accès. Les administrateurs sont en mesure de définir pour chaque utilisateur s'il a le droit de consulter certaines pages ou pas.
                        <br/><br/>
                        Visiblement, vous n'êtes pas habilité à accéder à cette page.
                        <br/><br/>
                        Vous pouvez <a href="index.php">retourner à l'accueil</a>.
                    </p>

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
