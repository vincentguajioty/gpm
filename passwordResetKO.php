<!DOCTYPE html>
<html>
<?php
include('header.php');
require_once 'config/config.php';
require_once 'verrouIPcheck.php';
?>

<?php
if ($RESETPASSWORD == 0)
{ 
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
    exit;
}           
?>


<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Mot de passe oublié</b>
    </div>
    <!-- /.login-logo -->
    <?php
    if (checkIP($_SERVER['REMOTE_ADDR'])==0)
    { ?>
        <div class="alert alert-warning">
            <center>
                <h4>ERREUR</h4>
                Identifiant non-reconnu ou adresse mail invalide.
            </center>
        </div>
    <?php }
    else
    { ?>
        <div class="alert alert-danger">
            <center>
                <h4>ATTENTION</h4>
                Suite à un nombre trop important d'erreurs de connexion depuis votre adresse IP (<?php echo $_SERVER['REMOTE_ADDR'];?>), tous les accès depuis cette adresse ont été bloqués.
                <br/><br/>
                Merci de contacter un administrateur <?php echo $APPNAME;?>.
            </center>
        </div>
    <?php }
    ?>
    <!-- /.login-box-body -->
</div>
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
