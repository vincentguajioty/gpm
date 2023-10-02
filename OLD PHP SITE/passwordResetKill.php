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
        $delete = $db->prepare('DELETE FROM RESETPASSWORD WHERE idReset = :idReset;');
        $delete->execute(array('idReset'=>$_GET['idReset']));
        writeInLogs("Suppression à la demande de l'utilisateur du tocken de reset de mot de passe.", '2', NULL);
    ?>


    <div class="alert alert-warning">
        <center>
            <h4>ANNULATION</h4>
            A votre demande, la réinitialisation de votre mot de passe a été annulée. Le lien de réinitialisation n'est donc plus valide
        </center>
    </div>
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
