<!DOCTYPE html>
<html>
<?php
include('header.php');
session_start();
if ($_SESSION['identifiant'] == Null)
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
?>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Modification du mot de passe</b>
    </div>
    <?php include('confirmationBox.php'); ?>
    <!-- /.login-logo -->
    <div class="login-box-body">

        <form action="loginChangePWDstartSQL.php" method="post">
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Nouveau mot de passe" name="new1">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Validation du nouveau mot de passe" name="new2">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Continuer</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../../bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="../../plugins/iCheck/icheck.min.js"></script>
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
