<!DOCTYPE html>
<html>
<?php require_once '../config/config.php'; ?>
<?php require_once '../config/bdd.php'; ?>
<header>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Mise à jour</title>
    <link rel="icon" type="image/png" href="../img/favicon.png" />
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/select2.min.css">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
</header>



<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
            <div class="alert alert-warning">
                <center>
                    <h4>MISE A JOUR</h4>
                    Vous êtes sur le point de migrer votre base de données à la version <?php echo $VERSIONCHECK?> de GPM !
                    <br/><br/>
                    <form role="form" action="INSTALL2.php" method="POST">

                        <div class="form-group">
                            <input type="checkbox" value="1" name="bkup" required> J'ai fait une sauvegarde de ma base de données et suis en mesure de la restaurer si cette mise à jour venait à corrompre la base.
                        </div>
                        <div class="form-group">
                            <input type="checkbox" value="1" name="rollback" required> J'ai compris qu'aucune procédure de retour en arrière n'est prévue dans cette mise à jour. Tout rollback devra être fait à la main grâce à la sauvegarde des données.
                        </div>
                        <div class="form-group">
                            <input type="checkbox" value="1" name="temps" required> J'ai compris qu'une fois que j'aurais appuyé sur "Lancer la mise à jour", les opérations vont prendre un certain temps à être menées sur la base et qu'il ne faut en aucun cas que je ne touche à quoi que ce soit.
                        </div>

                        <button type="submit" class="btn btn-success">Lancer la mise à jour</button>
                    </form>
                </center>
            </div>

        <!-- /.login-box-body -->
    </div>
<!-- /.login-box -->

</body>
</html>