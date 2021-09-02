<!DOCTYPE html>
<html>
<?php
include('headerCaptcha.php');
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
        <div class="login-box-body">
            <p class="login-box-msg">Saisissez identifiants et mail pour réinitialiser votre mot de passe</p>

            <form action="passwordResetSQL.php" method="post">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="Identifiant" name="identifiant" required>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Adresse mail" name="mailPersonne" required>
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" >
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="row">
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-warning">Recevoir le mail de réinitialisation</button>
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
<?php
	if($RECAPTCHA_ENABLE)
	{ ?>
		<script>
			grecaptcha.ready(function() {
	    		grecaptcha.execute('<?= $RECAPTCHA_SITEKEY ?>', {action: 'pwdReset'}).then(function(token) {
	    			document.getElementById('g-recaptcha-response').value=token;
	    		});
			});
		</script>
	<?php }
?>
</body>
</html>
