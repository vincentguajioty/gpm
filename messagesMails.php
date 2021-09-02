<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 404;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['contactMailGroupe']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
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
                Contact des équipes par mail
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Contact des équipes</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box box-info">
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" class="spinnerAttenteSubmit" action="messagesMailsSend.php" method="POST">
                        <!-- text input -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Destinataires (individuels):</label>
                                    <select class="form-control select2" style="width: 100%;" name="idPersonne[]" multiple>
                                        <optgroup label="Utilisateurs actifs">
	                                        <?php
	                                            $query2 = $db->query('SELECT * FROM VIEW_HABILITATIONS WHERE connexion_connexion = 1 AND mailPersonne IS NOT NULL AND mailPersonne <> \'\' ORDER BY nomPersonne, prenomPersonne;');
	                                            while ($data2 = $query2->fetch())
	                                            {
	                                                echo '<option value=' . $data2['idPersonne'] . '>' . $data2['nomPersonne'] . ' ' . $data2['prenomPersonne'] . '</option>';
	                                            }
	                                            $query2->closeCursor();
	                                        ?>
	                                    </optgroup>
	                                    <optgroup label="Utilisateurs désactivés">
	                                    	<?php
	                                            $query2 = $db->query('SELECT * FROM VIEW_HABILITATIONS WHERE (connexion_connexion = 0 OR connexion_connexion IS Null) AND mailPersonne IS NOT NULL AND mailPersonne <> \'\' ORDER BY nomPersonne, prenomPersonne;');
	                                            while ($data2 = $query2->fetch())
	                                            {
	                                                echo '<option value=' . $data2['idPersonne'] . '>' . $data2['nomPersonne'] . ' ' . $data2['prenomPersonne'] . '</option>';
	                                            }
	                                            $query2->closeCursor();
	                                        ?>
	                                    </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Destinataires (profils):</label>
                                    <select class="form-control select2" style="width: 100%;" name="idProfil[]" multiple>
                                        <?php
                                            $query2 = $db->query('SELECT * FROM PROFILS ORDER BY libelleProfil;');
                                            while ($data2 = $query2->fetch())
                                            {
                                                echo '<option value=' . $data2['idProfil'] . '>' . $data2['libelleProfil'] . '</option>';
                                            }
                                            $query2->closeCursor();
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Sujet:</label>
                            <input type="text" class="form-control" name="sujet" required>
                        </div>
                        <div class="form-group">
                            <label>Message:</label>
                            <textarea name="contenu" class="textareaHTML" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" required></textarea>
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
