<!DOCTYPE html>
<html>
<?php
include('header.php');
require_once 'config/config.php';
require_once 'verrouIPcheck.php';
?>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b><?php echo $APPNAME;?></b><br/><small>Gestion du Parc Matériel</small>
    </div>
    <!-- /.login-logo -->
    <?php
    if (($VERSION != $VERSIONCHECK) AND (file_exists('distmaj/INSTALL.php')))
    {
        echo "<script type='text/javascript'>document.location.replace('distmaj/INSTALL.php');</script>";
    }else if ($VERSION != $VERSIONCHECK)
    { ?>
        <div class="alert alert-danger">
            <center>
                <h4>ATTENTION</h4>
                La version de la base de données ne semble pas correspondre à la version du site web installé.
                <br/><br/>
                Vous ne devriez pas continuer et contacter votre administrateur système (<?php echo $MAILSERVER;?>).
            </center>
        </div>
    <?php }
    ?>


    <?php
    if (checkIP($_SERVER['REMOTE_ADDR'])==0)
    { ?>
        <div class="login-box-body">
            <p class="login-box-msg">Saisissez vos identifiants pour accéder à l'outil</p>

            <form action="loginSQL.php" method="post">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="Identifiant" name="identifiant" autofocus>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Mot de passe" name="motDePasse">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">GO !</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
    <?php }
    else
    { ?>
        <div class="alert alert-danger">
            <center>
                <h4>ATTENTION</h4>
                Suite à un nombre trop important d'erreurs de connexion depuis votre adresse IP (<?php echo $_SERVER['REMOTE_ADDR'];?>), tous les accès depuis cette adresse ont été bloqués.
                <br/><br/>
                Merci de contacter un administrateur <?php echo $APPNAME;?> pour envisager un déblocage de votre adresse.
            </center>
        </div>
    <?php }
    ?>
    <!-- /.login-box-body -->
</div>
<center>Gestionnaire de Parc Matériel</center>
<center>Version <?php echo $VERSION; ?></center>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
