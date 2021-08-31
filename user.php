<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 000;
require_once('logCheck.php');

?>
<?php include('header.php'); require_once('config/config.php'); ?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once('config/config.php'); ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Mon compte
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Mon compte</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Modification de mon compte</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="userUpdate.php" method="POST">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Mon identifiant:</label>
                            <input type="text" class="form-control" value="<?=$_SESSION['identifiant']?>"
                                   name="identifiant" disabled>
                        </div>
                        <div class="form-group">
                            <label>Nom:</label>
                            <input type="text" class="form-control" value="<?=$_SESSION['nomPersonne']?>"
                                   name="nomPersonne">
                        </div>
                        <div class="form-group">
                            <label>Prénom:</label>
                            <input type="text" class="form-control" value="<?=$_SESSION['prenomPersonne']?>"
                                   name="prenomPersonne">
                        </div>
                        <div class="form-group">
                            <label>Adresse eMail:</label>
                            <input type="email" class="form-control" value="<?=$_SESSION['mailPersonne']?>"
                                   name="mailPersonne">
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="tel" class="form-control" value="<?=$_SESSION['telPersonne']?>"
                                   name="telPersonne">
                        </div>
                        <div class="form-group">
                            <label>Fonction</label>
                            <input type="tel" class="form-control" value="<?=$_SESSION['fonction']?>"
                                   name="fonction">
                        </div>
                        <div class="form-group">
                            <label>Mon profil <?php echo $APPNAME;?>:</label>
                            <input type="text" class="form-control" value="<?=$_SESSION['libelleProfil']?>"
                                   name="libelleProfil" disabled>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php include('confirmationBox.php'); ?>
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Modification de mon mot de passe</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="userPWD.php" method="POST">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Mon mot de passe Actuel:</label>
                            <input type="password" class="form-control" placeholder="*****"
                                   name="old" required>
                        </div>
                        <div class="form-group">
                            <label>Nouveau mot de passe:</label>
                            <input type="password" class="form-control" placeholder="*****"
                                   name="new1" required>
                        </div>
                        <div class="form-group">
                            <label>Saisir à nouveau le nouveau mot de passe:</label>
                            <input type="password" class="form-control" placeholder="*****"
                                   name="new2" required>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
                        </div>
                    </form>
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
