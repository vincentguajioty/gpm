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

<?php require_once '../config/config.php'; ?>
<?php require_once '../config/bdd.php'; ?>

<body class="hold-transition login-page">
<div class="login-box">
    <!-- /.login-logo -->
    <div class="row">
        <div class="alert alert-success">
            <center>
                <h4>MISE A JOUR TERMINEE AVEC SUCCES</h4>
                Rappel: les dossiers "DOCUMENTS" et "LOGS" qui contiennent les pièces jointes et fichiers de logs n'ont pas été migrés automatiquement, il faut les transférer à la main de l'ancien dossier d'installation vers le nouveau.
                <br/><br/>
                Par mesure de sécurité, veuillez supprimer le dossier DISTMAJ .
                <br/><br/>
                <h3 class="box-title"><a href="../login.php" class="btn btn-sm btn-info">Accéder à l'outil</a></h3>
            </center>
        </div>
    </div>

    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

</body>
</html>
