<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 505;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['appli_conf']==0)
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
                Configuration générale de <?php echo $APPNAME; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">IP</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- general form elements disabled -->
            <div class="box box-info">
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="appliConfUpdate.php" method="POST">
                        <?php
                        $query = $db->query('SELECT * FROM CONFIG;');
                        $data = $query->fetch();
                        ?>

                        <div class="form-group">
                            <label>Nom de l'application:</label>
                            <input type="text" class="form-control" value="<?=$data['appname']?>" name="appname" required>
                        </div>
                        <div class="form-group">
                            <label>Couleur du site:</label>
                            <select class="form-control select2" style="width: 100%;" name="sitecolor">
                                <option value="blue" <?php if($data['sitecolor']=='blue'){echo 'selected';}?>>Bleu</option>
                                <option value="black" <?php if($data['sitecolor']=='black'){echo 'selected';}?>>Noir</option>
                                <option value="purple" <?php if($data['sitecolor']=='purple'){echo 'selected';}?>>Violet</option>
                                <option value="green" <?php if($data['sitecolor']=='green'){echo 'selected';}?>>Vert</option>
                                <option value="red" <?php if($data['sitecolor']=='red'){echo 'selected';}?>>Rouge</option>
                                <option value="yellow" <?php if($data['sitecolor']=='yellow'){echo 'selected';}?>>Orange</option>
                                <option value="blue-light" <?php if($data['sitecolor']=='blue-light'){echo 'selected';}?>>Bleu clair</option>
                                <option value="black-light" <?php if($data['sitecolor']=='black-light'){echo 'selected';}?>>Noir clair</option>
                                <option value="purple-light" <?php if($data['sitecolor']=='purple-light'){echo 'selected';}?>>Violet clair</option>
                                <option value="green-light" <?php if($data['sitecolor']=='green-light'){echo 'selected';}?>>Vert clair</option>
                                <option value="red-light" <?php if($data['sitecolor']=='red-light'){echo 'selected';}?>>Rouge clair</option>
                                <option value="yellow-light" <?php if($data['sitecolor']=='yellow-light'){echo 'selected';}?>>Orange clair</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>URL du site:</label>
                            <input type="text" class="form-control" value="<?=$data['urlsite']?>" name="urlsite" required>
                        </div>
                        <div class="form-group">
                            <label>Adresse mail expéditrice des notifications:</label>
                            <input type="text" class="form-control" value="<?=$data['mailserver']?>" name="mailserver" required>
                        </div>
                        <div class="form-group">
                            <label>Temps avant déconnexion d'un utilisateur inactif (minutes):</label>
                            <input type="number" class="form-control" value="<?=$data['logouttemp']?>" name="logouttemp" required>
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
