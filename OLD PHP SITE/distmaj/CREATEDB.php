<!DOCTYPE html>
<html>
<header>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Installation</title>
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
        <div class="alert alert-warning">
            <center>
                <h4>INSTALLATION</h4>
                Merci de renseigner les informations de connexion à votre base de données
                <br/><br/>
                <form role="form" action="CREATEDB2.php" method="POST">

                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" placeholder="Adresse IP" name="ip" required>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" placeholder="Nom de la base" name="dbname" required>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" placeholder="Utilisateur" name="user" required>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" placeholder="Mot de passe" name="password">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>

                    <button type="submit" class="btn btn-success">ENREGISTRER</button>
                </form>
            </center>
        </div>
        <!-- /.login-box-body -->
    </div>
<!-- /.login-box -->

</body>
</html>
