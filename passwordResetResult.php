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
    {
        if($_GET['pwd']=='ko')
        {
            echo "
                <div class='alert alert-warning'>
                    <center>
                        <h4>Erreur de vérification</h4>
                        Les deux mots de passe saisis ne sont pas identiques. Merci de recommencer la procédure décrite dans l'email de réinitialisation.
                    </center>
                </div>
            ";
        }
        else
        {
            $reset = $db->prepare('SELECT * FROM RESETPASSWORD WHERE idReset = :idReset;');
            $reset->execute(array('idReset'=>$_GET['idReset']));
            $reset = $reset->fetch();

            if (empty($reset['idReset']) OR $reset['idReset'] == "" OR  !(password_verify($_GET['token'], $reset['tokenReset'])) OR $reset['dateValidite']<date('Y-m-d H:i:s'))
            {
                //pas bon
                writeInLogs("Accès non-autorisé sur la page de réinitialisation de mot de passe avec l\'idReset ".$_GET['idReset']." et le token ".$_GET['token'], '2', NULL);

                $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE dateEchec < :dateEchec;');
                $query->execute(array(
                    'dateEchec' => date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' - '.$VERROUILLAGE_IP_TEMPS.' days'))
                ));
                
                $query = $db->prepare('SELECT COUNT(*) as nb FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
                $query->execute(array(
                    'adresseIP' => $_SERVER['REMOTE_ADDR']
                ));
                $data = $query->fetch();
                
                if ($data['nb'] > $VERROUILLAGE_IP_OCCURANCES-2)
                {
                    $query = $db->prepare('INSERT INTO VERROUILLAGE_IP(adresseIPverr, dateVerr, commentaire)VALUES(:adresseIPverr, :dateVerr, :commentaire);');
                    $query->execute(array(
                        'dateVerr' => date('Y-m-d H:i:s'),
                        'adresseIPverr' => $_SERVER['REMOTE_ADDR'],
                        'commentaire' => 'Erreur de reset de mot de passe',
                    ));
                    
                    writeInLogs("Verouillage définitif de l\'adresse IP suite à la tentative de reset de mot de passe", '2', NULL);

                    $query = $db->prepare('DELETE FROM VERROUILLAGE_IP_TEMP WHERE adresseIP= :adresseIP;');
                    $query->execute(array(
                        'adresseIP' => $_SERVER['REMOTE_ADDR']
                    ));
                }
                else
                {
                    $query = $db->prepare('INSERT INTO VERROUILLAGE_IP_TEMP(adresseIP, dateEchec, commentaire)VALUES(:adresseIP, :dateEchec, commentaire);');
                    $query->execute(array(
                        'dateEchec' => date('Y-m-d H:i:s'),
                        'adresseIP' => $_SERVER['REMOTE_ADDR'],
                        'commentaire' => 'Erreur de reset de mot de passe',
                    ));

                    writeInLogs("Verouillage temporaire de l\'adresse IP suite à la tentative d'authentification avec ".$_POST['identifiant'], '2', NULL);
                }
                
                echo "
                    <div class='alert alert-danger'>
                        <center>
                            <h4>LIEN INVALIDE</h4>
                            Le lien de réinitialisation n'est pas pas valide.
                            <br/><br/>
                            Merci de contacter un administrateur <?php echo $APPNAME;?>.
                        </center>
                    </div>
                ";
            }
            else
            {?>
                <div class="login-box-body">

                    <form action="passwordResetResultSQL.php?idReset=<?=$_GET['idReset']?>" method="post">
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Nouveau mot de passe" name="new1">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Validation du nouveau mot de passe" name="new2">
                            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" >
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
            <?php }
        }
    }
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
	    		grecaptcha.execute('<?= $RECAPTCHA_SITEKEY ?>', {action: 'pwdResetNew'}).then(function(token) {
	    			document.getElementById('g-recaptcha-response').value=token;
	    		});
			});
		</script>
	<?php }
?>
</body>
</html>
