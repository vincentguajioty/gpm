<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 502;
require_once('logCheck.php');
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'config/config.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Formulaire de contact
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="faq.php">A propos</a></li>
                <li class="active">Contact du developpeur</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box box-info">
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" class="spinnerAttenteSubmit" action="contactSend.php" method="POST">
                        <!-- text input -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nom:</label>
                                    <input type="text" class="form-control" value="<?php echo $_SESSION['nomPersonne'];?>" name="nom">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Prénom:</label>
                                    <input type="text" class="form-control" value="<?php echo $_SESSION['prenomPersonne'];?>" name="prenom">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Adresse mail:</label>
                                    <input type="text" class="form-control" value="<?php echo $_SESSION['mailPersonne'];?>" name="mail">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input type="text" class="form-control" value="<?php echo $_SESSION['telPersonne'];?>" name="tel">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Société/Association:</label>
                                    <input type="text" class="form-control" name="entite">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fonction:</label>
                                    <input type="text" class="form-control" value="<?php echo $_SESSION['fonctionPersonne'];?>" name="fonction">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Je veux: </label>
                                    <select class="form-control" name="motif">
                                        <option>Demander une aide à l'utilisation</option>
                                        <option>Faire une suggestion</option>
                                        <option>Signaler un dysfonctionnement dans un module</option>
                                        <option>Signaler une faille de sécurité</option>
                                        <option>Autre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Sur: </label>
                                    <select class="form-control" name="module">
                                        <option>Lots/Sacs/Emplacements</option>
                                        <option>Matériel opérationnel</option>
                                        <option>Catalogue du matériel</option>
                                        <option>Référentiels</option>
                                        <option>Commandes</option>
                                        <option>Réserve</option>
                                        <option>Transferts de matériel</option>
                                        <option>Véhicules</option>
                                        <option>Transmissions</option>
                                        <option>Tenues</option>
                                        <option>Annuaire</option>
                                        <option>Profils</option>
                                        <option>Délégations</option>
                                        <option>Base documentaire</option>
                                        <option>ToDoList</option>
                                        <option>Messages généraux</option>
                                        <option>Messages mails</option>
                                        <option>Notifications</option>
                                        <option>Actions massives en base</option>
                                        <option>Paramètres fonctionnels (catégories, états , lieux, fournisseurs ...)</option>
                                        <option>Paramètres techniques de l'application</option>
                                        <option>Autre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ma version de GPM:</label>
                                    <input type="text" class="form-control" value="DB<?php echo $VERSION;?> - WEB<?php echo $VERSIONCHECK;?>" name="version">
                                </div>
                            </div>
                        </div>
                        <!--<div class="form-group">
                            <label>Message:</label>
                            <textarea class="form-control" rows="8" name="contenu"></textarea>
                        </div>-->
                        <div class="form-group">
                            <label>Message:</label>
                        	<textarea name="contenu" class="textareaHTML" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Envoyer</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include('footer.php'); ?>


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php include('scripts.php'); ?>
</body>
</html>
