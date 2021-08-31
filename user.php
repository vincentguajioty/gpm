<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 000;
require_once('logCheck.php');

?>
<?php include('header.php'); require_once('config/config.php'); ?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once('config/config.php'); ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
	        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Mon compte
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Mon compte</li>
            </ol>
        </section>
		
        <!-- Main content -->
        <section class="content">
        	<?php include('confirmationBox.php'); ?>
        	<div class="row">
        		<div class="col-md-8 col-sm-12 col-xs-12">
		            <div class="box box-info">
		                <div class="box-header with-border">
		                    <h3 class="box-title">Mes informations personnelles</h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                    <form role="form" action="userUpdate.php" method="POST">
		                        <!-- text input -->
		                        <div class="form-group">
		                            <label>Mon identifiant:</label>
		                            <input type="text" class="form-control" value="<?=$_SESSION['identifiant']?>"
		                                   name="identifiant" disabled>
		                        </div>
		                        <div class="form-group">
		                            <label>Nom:</label>
		                            <input type="text" class="form-control" value="<?=$_SESSION['nomPersonne']?>"
		                                   name="nomPersonne">
		                        </div>
		                        <div class="form-group">
		                            <label>Prénom:</label>
		                            <input type="text" class="form-control" value="<?=$_SESSION['prenomPersonne']?>"
		                                   name="prenomPersonne">
		                        </div>
		                        <div class="form-group">
		                            <label>Adresse eMail:</label>
		                            <input type="email" class="form-control" value="<?=$_SESSION['mailPersonne']?>"
		                                   name="mailPersonne">
		                        </div>
		                        <div class="form-group">
		                            <label>Téléphone</label>
		                            <input type="tel" class="form-control" value="<?=$_SESSION['telPersonne']?>"
		                                   name="telPersonne">
		                        </div>
		                        <div class="form-group">
		                            <label>Fonction</label>
		                            <input type="tel" class="form-control" value="<?=$_SESSION['fonction']?>"
		                                   name="fonction">
		                        </div>
		                        <div class="form-group">
		                            <label>Mon profil <?php echo $APPNAME;?>:</label>
		                            <input type="text" class="form-control" value="<?=$_SESSION['libelleProfil']?>"
		                                   name="libelleProfil" disabled>
		                        </div>
		                        <div class="box-footer">
		                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
		                        </div>
		                    </form>
		                </div>
		            </div>
		        </div>
		        <div class="col-md-4 col-sm-12 col-xs-12">
		            <div class="box box-info">
		                <div class="box-header with-border">
		                    <h3 class="box-title">Mon mot de passe</h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                    <form role="form" action="userPWD.php" method="POST">
		                        <!-- text input -->
		                        <div class="form-group">
		                            <label>Mon mot de passe Actuel:</label>
		                            <input type="password" class="form-control" placeholder="*****"
		                                   name="old" required>
		                        </div>
		                        <div class="form-group">
		                            <label>Nouveau mot de passe:</label>
		                            <input type="password" class="form-control" placeholder="*****"
		                                   name="new1" required>
		                        </div>
		                        <div class="form-group">
		                            <label>Saisir à nouveau le nouveau mot de passe:</label>
		                            <input type="password" class="form-control" placeholder="*****"
		                                   name="new2" required>
		                        </div>
		                        <div class="box-footer">
		                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
		                        </div>
		                    </form>
		                </div>
		            </div>
		         </div>
		         <div class="col-md-4 col-sm-12 col-xs-12">
		        	<div class="box box-info">
		                <div class="box-header with-border">
		                    <h3 class="box-title">Mes paramètres personnels</h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                	<form role="form" action="userConfig.php" method="POST">
		                        <div class="form-group">
		                            <label>Rafraichissement automatique de la page d'accueil (secondes) :</label>
		                            <input type="number" min="10" class="form-control" value="<?= $_SESSION['conf_accueilRefresh'] ?>" name="conf_accueilRefresh" required>
		                        </div>
		                        <div class="form-group">
		                        	<?php
		                        		$lots = $_SESSION['lots_lecture'] OR $_SESSION['sac_lecture'] OR $_SESSION['sac2_lecture'] OR $_SESSION['materiel_lecture'];
		                        		$reserves = $_SESSION['reserve_lecture'];
		                        		$vehicules = $_SESSION['vehicules_lecture'];
		                        	?>
		                            <label>Présence des indicateurs sur la page d'accueil:</label><br/>
			                        <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur1Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur1Accueil" <?php if ($lots == 0){echo 'disabled';} ?>> Matériels périmés (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur2Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur2Accueil" <?php if ($lots == 0){echo 'disabled';} ?>> Matériels manquants (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur3Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur3Accueil" <?php if ($lots == 0){echo 'disabled';} ?>> Lots en attente d'inventaire</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur4Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur4Accueil" <?php if ($lots == 0){echo 'disabled';} ?>> Lots non conformes</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur5Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur5Accueil" <?php if ($reserves == 0){echo 'disabled';} ?>> Matériels périmés (réserve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur6Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur6Accueil" <?php if ($reserves == 0){echo 'disabled';} ?>> Matériels manquants (réserve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur7Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur7Accueil" <?php if ($vehicules == 0){echo 'disabled';} ?>> Assurances véhicules</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur8Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur8Accueil" <?php if ($vehicules == 0){echo 'disabled';} ?>> Contrôles techniques et révisions véhicules</label>
	                                </div>
		                        </div>
		                        <div class="form-group">
		                        	<?php
		                        		$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE pe LEFT OUTER JOIN PROFILS po ON pe.idProfil = po.idProfil WHERE idPersonne = :idPersonne');
		                        		$query->execute(array('idPersonne' => $_SESSION['idPersonne']));
		                        		$data = $query->fetch();
		                        	?>
		                            <label>Abonnements aux notifications journalières par mail:</label><br/>
			                        <div class="checkbox">
	                                	<label><input <?php if($data['notif_lots_manquants'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_lots_manquants" <?php if ($lots == 0){echo 'disabled';} ?>> Matériels manquants (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_lots_peremptions'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_lots_peremptions" <?php if ($lots == 0){echo 'disabled';} ?>> Matériels périmés (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_lots_inventaires'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_lots_inventaires" <?php if ($lots == 0){echo 'disabled';} ?>> Inventaires (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_lots_conformites'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_lots_conformites" <?php if ($lots == 0){echo 'disabled';} ?>> Lots non conformes</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_reserves_manquants'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_reserves_manquants" <?php if ($reserves == 0){echo 'disabled';} ?>> Matériels manquants (réserve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_reserves_peremptions'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_reserves_peremptions" <?php if ($reserves == 0){echo 'disabled';} ?>> Matériels périmés (réserve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_reserves_inventaires'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_reserves_inventaires" <?php if ($reserves == 0){echo 'disabled';} ?>> Inventaires (réserve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_vehicules_assurances'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_vehicules_assurances" <?php if ($vehicules == 0){echo 'disabled';} ?>> Assurances véhicules</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_vehicules_revisions'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_vehicules_revisions" <?php if ($vehicules == 0){echo 'disabled';} ?>> Révisions véhicules</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_vehicules_ct'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_vehicules_ct" <?php if ($vehicules == 0){echo 'disabled';} ?>> Contrôles techniques véhicules</label>
	                                </div>
		                        </div>
		                        <div class="box-footer">
		                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
		                        </div>
		                    </form>
		                </div>
		             </div>
		         </div>
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
