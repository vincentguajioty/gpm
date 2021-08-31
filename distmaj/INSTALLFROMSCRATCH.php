<!DOCTYPE html>
<html>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
</header>

<body class="hold-transition login-page">
<div class="login-box">
    <!-- /.login-logo -->
    <div class="row">
        <div class="alert alert-warning">
            <center>
                <h4>INSTALLATION</h4>
                Votre base de données a été analysée et elle semble vite. GPM va donc s'installer dessus.
                <br/><br/>
                <form role="form" action="INSTALLFROMSCRATCH2.php" method="POST">

                    <div class="form-group">
                        <input type="checkbox" value="1" name="bkup" required> Je confirme que ma base de données est vide.
                    </div>
                    <div class="form-group">
                        <input type="checkbox" value="1" name="rollback" required> Je confirme que GPM peut créer ses table dessus.
                    </div>
                    <div class="form-group">
                        <input type="checkbox" value="1" name="temps" required> J'ai compris qu'une fois que j'aurais appuyé sur "Installer", les opérations vont prendre un certain temps à être menées sur la base et qu'il ne faut en aucun cas que je ne touche à quoi que ce soit.
                    </div>
                    <div class="form-group">
                        <input type="checkbox" value="1" name="suite" required> J'ai compris qu'à la fin du process d'installation, je vais être automatiquement redirigé vers le gestionnaire de mise à jour pour mettre à jour GPM.
                    </div>

                    <button type="submit" class="btn btn-success">Installer</button>
                </form>
            </center>
        </div>
    </div>

    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

</body>
</html>
