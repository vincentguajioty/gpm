<!DOCTYPE html>
<html>
<?php
	include('header.php');
	require_once 'config/version.php';
?>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>APOLLON</b><br/><small>Gestion du parc matériel</small>
    </div>
    <!-- /.login-logo -->
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
    <!-- /.login-box-body -->
</div>
<center>Version <?php echo $VERSION; ?></center>
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
