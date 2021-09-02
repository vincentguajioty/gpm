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
        <b><?php echo $APPNAME;?></b>
    </div>
    <div class="alert alert-danger">
        <center>
            <h4>ATTENTION</h4>
            Notre système de sécurité a assimilé votre comportement à celui d'un robot. Votre demande n'a pas été enregistrée.
            <br/><br/>
            Merci de contacter un administrateur <?php echo $APPNAME;?>.
        </center>
    </div>
    <!-- /.login-box-body -->
</div>
<center>GPM - Gestionnaire de Parc Matériel</center>
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
