<!DOCTYPE html>
<html>
<?php
include('header.php');
session_start();
if ($_SESSION['identifiant'] == Null)
    echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
   
require_once 'verrouIPcheck.php';
if (checkIP($_SERVER['REMOTE_ADDR'])==1)
{
	echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
	exit;
}
?>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Double Authentification</b>
    </div>
    <?php include('confirmationBox.php'); ?>
    <!-- /.login-logo -->
    <div class="login-box-body">

        <form action="loginMFASQL.php" method="post">
            <div class="form-group has-feedback">
                <input type="authenticator" class="form-control" placeholder="Double authentification" name="authenticator" autofocus required>
                <span class="glyphicon glyphicon-qrcode form-control-feedback"></span>
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
