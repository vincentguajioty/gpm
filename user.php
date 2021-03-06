<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 000;
require_once('logCheck.php');

?>
<?php include('header.php'); require_once('config/config.php'); ?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once('config/config.php'); ?>
    <?php require_once('plugins/authenticator/authenticator.php'); ?>

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
		                            <label>Pr??nom:</label>
		                            <input type="text" class="form-control" value="<?=$_SESSION['prenomPersonne']?>"
		                                   name="prenomPersonne">
		                        </div>
		                        <div class="form-group">
		                            <label>Adresse eMail:</label>
		                            <input type="email" class="form-control" value="<?=$_SESSION['mailPersonne']?>"
		                                   name="mailPersonne">
		                        </div>
		                        <div class="form-group">
		                            <label>T??l??phone</label>
		                            <input type="tel" class="form-control" value="<?=$_SESSION['telPersonne']?>"
		                                   name="telPersonne">
		                        </div>
		                        <div class="form-group">
		                            <label>Fonction</label>
		                            <input type="tel" class="form-control" value="<?=$_SESSION['fonction']?>"
		                                   name="fonction">
		                        </div>
		                        <center><i>Pour toute information ou r??clamation quant ?? la confidentialit?? des donn??es personnelles, veuillez contacter le r??f??rent informatique de la plateforme: <?=$MAILCNIL?></i></center>
		                        <div class="box-footer">
		                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
		                        </div>
		                    </form>
		                </div>
		            </div>
		            <div class="box box-info">
		                <div class="box-header with-border">
		                    <h3 class="box-title">Mes habilitations sur <?php echo $APPNAME;?> <a href="userReloadHabilitation.php" class="btn btn-xs spinnerAttenteClick"><i class="fa fa-refresh"></i> Rafraichir mes habilitations</a></h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
			                <div class="form-group">
			                    <label>Connexion ?? <?php echo $APPNAME;?>:</label>
			                    </br>
			                    <?php if($_SESSION['connexion_connexion'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Autoris?? ?? se connecter ?? <?php echo $APPNAME;?>
			                    </br>
			                </div>
			                <div class="form-group">
			                    <label>Administration de <?php echo $APPNAME;?>:</label>
			                    </br>
			                    <?php if($_SESSION['appli_conf'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Modifier la configuration g??n??rale de <?php echo $APPNAME;?>
			                    </br>
			                    <?php if($_SESSION['annuaire_mdp'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> R??initialiser les mots de passe des autres utilisateurs
			                    </br>
			                    <?php if($_SESSION['delegation'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Se connecter entant qu'autre utilisateur
			                    </br>
			                    <?php if($_SESSION['maintenance'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Se connecter m??me en mode maitenance
			                    </br>
			                    <?php if($_SESSION['verrouIP'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> G??rer les adresses IP bloqu??es
			                    </br>
			                    <?php if($_SESSION['actionsMassives'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Mener des actions massives directement en base
			                    </br>
			                </div>
			                <div class="form-group">
			                    <label>Notifications journali??res par mail:</label>
			                    </br>
			                    <?php if($_SESSION['notifications'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Autoris?? ?? recevoir les notifications journali??res par mail
			                    </br>
			                </div>
			                <table class="table table-bordered">
			                    <tr>
			                        <th></th>
			                        <th>Lecture</th>
			                        <th>Ajout</th>
			                        <th>Modification</th>
			                        <th>Suppression</th>
			                    </tr>
			                    <tr>
			                        <th>LOTS</th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                    </tr>
			                    <tr>
			                        <td>Lots</td>
			                        <td><?php if($_SESSION['lots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['lots_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['lots_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['lots_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Sacs</td>
			                        <td><?php if($_SESSION['sac_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['sac_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['sac_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['sac_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Emplacements</td>
			                        <td><?php if($_SESSION['sac2_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['sac2_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['sac2_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['sac2_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Mat??riels/Consommables</td>
			                        <td><?php if($_SESSION['materiel_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['materiel_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['materiel_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['materiel_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <th>TRANSMISSIONS</th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                    </tr>
			                    <tr>
			                        <td>Canaux</td>
			                        <td><?php if($_SESSION['vhf_canal_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_canal_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_canal_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_canal_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Plans de fr??quences</td>
			                        <td><?php if($_SESSION['vhf_plan_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_plan_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_plan_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_plan_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Equipements de transmission</td>
			                        <td><?php if($_SESSION['vhf_equipement_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_equipement_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_equipement_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vhf_equipement_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <th>VEHICULES</th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                    </tr>
			                    <tr>
			                        <td>V??hicules</td>
			                        <td><?php if($_SESSION['vehicules_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehicules_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehicules_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehicules_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>D??sinfections</td>
			                        <td><?php if($_SESSION['desinfections_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['desinfections_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['desinfections_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['desinfections_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Taches de maintenance</td>
			                        <td><?php if($_SESSION['vehiculeHealth_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehiculeHealth_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehiculeHealth_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehiculeHealth_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <th>TENUES</th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                    </tr>
			                    <tr>
			                        <td>Tenues</td>
			                        <td><?php if($_SESSION['tenues_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['tenues_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['tenues_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['tenues_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Catalogue des tenues</td>
			                        <td><?php if($_SESSION['tenuesCatalogue_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['tenuesCatalogue_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['tenuesCatalogue_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['tenuesCatalogue_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Cautions</td>
			                        <td><?php if($_SESSION['cautions_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['cautions_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['cautions_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['cautions_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <th>PARAMETRES</th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                        <th></th>
			                    </tr>
			                    <tr>
			                        <td>Cat??gories</td>
			                        <td><?php if($_SESSION['categories_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['categories_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['categories_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['categories_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Codes Barre</td>
			                        <td><?php if($_SESSION['codeBarre_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['codeBarre_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['codeBarre_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['codeBarre_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>R??f??rentiels</td>
			                        <td><?php if($_SESSION['typesLots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['typesLots_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['typesLots_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['typesLots_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Lieux</td>
			                        <td><?php if($_SESSION['lieux_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['lieux_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['lieux_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['lieux_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Catalogue</td>
			                        <td><?php if($_SESSION['catalogue_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['catalogue_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['catalogue_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['catalogue_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Types de v??hicules</td>
			                        <td><?php if($_SESSION['vehicules_types_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehicules_types_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehicules_types_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehicules_types_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Types de d??sinfections</td>
			                        <td><?php if($_SESSION['typesDesinfections_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['typesDesinfections_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['typesDesinfections_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['typesDesinfections_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Types de taches de maintenance</td>
			                        <td><?php if($_SESSION['vehiculeHealthType_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehiculeHealthType_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehiculeHealthType_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['vehiculeHealthType_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Carburants</td>
			                        <td><?php if($_SESSION['carburants_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['carburants_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['carburants_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['carburants_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Etats</td>
			                        <td><?php if($_SESSION['etats_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['etats_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['etats_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['etats_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                </table>
			                <br/>
                            <table class="table table-bordered">
                                <tr>
                                    <th></th>
                                    <th>Lecture</th>
                                    <th>Traitement</th>
                                    <th>Affecter ?? un tier</th>
                                    <th>Suppression</th>
                                </tr>
                                <tr>
                                    <th>ALERTES BENEVOLES</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td>Lots</td>
                                    <td><?php if($_SESSION['alertesBenevolesLots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($_SESSION['alertesBenevolesLots_affectation'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($_SESSION['alertesBenevolesLots_affectationTier'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>V??hicules</td>
                                    <td><?php if($_SESSION['alertesBenevolesVehicules_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($_SESSION['alertesBenevolesVehicules_affectation'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($_SESSION['alertesBenevolesVehicules_affectationTier'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>CONSOMMATION DES BENEVOLES</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td>Rapports de consommation</td>
                                    <td><?php if($_SESSION['consommationLots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($_SESSION['consommationLots_affectation'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($_SESSION['consommationLots_supression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br/>
			                <table class="table table-bordered">
			                    <tr>
			                        <th>COMMANDES</th>
			                        <th>Lecture</th>
			                        <th>Ajout</th>
			                        <th>Modification</th>
			                        <th>Valideur universel</th>
			                        <th>Etre en charge</th>
			                        <th>Abandonner Supprimer</th>
			                    </tr>
			                    <tr>
			                        <td>Commandes</td>
			                        <td><?php if($_SESSION['commande_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['commande_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['commande_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['commande_valider_delegate'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['commande_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['commande_abandonner'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Fournisseurs</td>
			                        <td><?php if($_SESSION['fournisseurs_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['fournisseurs_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['fournisseurs_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td></td>
			                        <td><?php if($_SESSION['fournisseurs_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Centres de co??ts</td>
			                        <td><?php if($_SESSION['cout_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['cout_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['cout_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td></td>
			                        <td><?php if($_SESSION['cout_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['cout_supprimer'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                </table>
			                <br/>
			                <table class="table table-bordered">
			                    <tr>
			                        <th>RESERVE</th>
			                        <th>Lecture</th>
			                        <th>Ajout</th>
			                        <th>Modification</th>
			                        <th>Supprimer</th>
			                        <th>Int??grer du mat??riel dans la r??serve suite ?? une commande</th>
			                        <th>Sortir du mat??riel de la r??serve pour l'int??grer ?? un lot</th>
			                    </tr>
			                    <tr>
			                        <td>R??serve</td>
			                        <td><?php if($_SESSION['reserve_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['reserve_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['reserve_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['reserve_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['reserve_cmdVersReserve'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['reserve_ReserveVersLot'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                </table>
			                <br/>
			                <table class="table table-bordered">
			                    <tr>
			                        <th>GESTION EQUIPE</th>
			                        <th>Lecture</th>
			                        <th>Ajout</th>
			                        <th>Modification</th>
			                        <th>Modification de sa propre liste</th>
			                        <th>Supprimer</th>
			                    </tr>
			                    <tr>
			                        <td>Annuaire</td>
			                        <td><?php if($_SESSION['annuaire_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['annuaire_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['annuaire_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td></td>
			                        <td><?php if($_SESSION['annuaire_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Profils</td>
			                        <td><?php if($_SESSION['profils_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['profils_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['profils_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td></td>
			                        <td><?php if($_SESSION['profils_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Messages g??n??raux</td>
			                        <td></td>
			                        <td><?php if($_SESSION['messages_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td></td>
			                        <td></td>
			                        <td><?php if($_SESSION['messages_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                    </tr>
			                    <tr>
			                        <td>Messages mails</td>
			                        <td></td>
			                        <td><?php if($_SESSION['contactMailGroupe'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td></td>
			                        <td></td>
			                        <td></td>
			                    </tr>
			                    <tr>
			                        <td>ToDoList</td>
			                        <td><?php if($_SESSION['todolist_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td></td>
			                        <td><?php if($_SESSION['todolist_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td><?php if($_SESSION['todolist_perso'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
			                        <td></td>
			                    </tr>
			                </table>
			            </div>
		            </div>
		            <?php
		            	if($_SESSION['cout_etreEnCharge'])
	            		{ ?>
				            <div class="box box-info">
				                <div class="box-header with-border">
				                    <h3 class="box-title">Mes habilitations sur les centres de co??ts</h3>
				                </div>
				                <!-- /.box-header -->
				                <div class="box-body">
				                    <table class="table table-bordered">
				                        <thead>
				                            <tr>
				                                <th>#</th>
				                                <th>Libelle</th>
				                                <th>Droits</th>
				                                <th>Validation de commandes</th>
				                                <th>Droits ??tendus</th>
				                            </tr>
				                        </thead>
				                        <tbody>
				                        <?php
				                        $query = $db->prepare('SELECT * FROM CENTRE_COUTS c LEFT OUTER JOIN CENTRE_COUTS_PERSONNES p ON c.idCentreDeCout = p.idCentreDeCout WHERE p.idPersonne = :idPersonne ORDER BY libelleCentreDecout DESC');
				                        $query->execute(array('idPersonne'=>$_SESSION['idPersonne']));
				                        while ($data = $query->fetch())
				                        {?>
				                            <tr>
				                                <td><?php echo $data['idCentreDeCout']; ?></td>
				                                <td><?php echo $data['libelleCentreDecout']; ?></td>
				                                <td>
		                                            <?php
		                                                if(centreCoutsEstCharge($_SESSION['idPersonne'],$data['idCentreDeCout'])==1)
		                                                {
		                                                    echo '<span class="badge bg-green">Actif</span>';
		                                                }
		                                                else
		                                                {
		                                                    echo '<span class="badge bg-yellow">Inactif</span>';
		                                                }
		                                            ?>
		                                        </td>
				                                <td><?php if($data['montantMaxValidation']!=Null AND $data['montantMaxValidation']>=0){echo $data['montantMaxValidation'].' ???';}else{echo '<span class="badge bg-yellow">Illimit??</span>';}?></td>
				                                <td><?php if($data['depasseBudget']){echo '<span class="badge bg-yellow">D??passement de budget autoris??</span><br/>';}?><?php if($data['validerClos']){echo '<span class="badge bg-yellow">Op??rer sur le centre clos</span>';}?></td>
				                            </tr>
				                            <?php
				                        }
				                        $query->closeCursor(); ?>
				                        </tbody>


				                    </table>
				                </div>
				            </div>
				    <?php } ?>
		        </div>
		        <div class="col-md-4 col-sm-12 col-xs-12">
		            <div class="box box-info">
		                <div class="box-header with-border">
		                    <h3 class="box-title">Mon mot de passe</h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                	<?php
		                		if($_SESSION['isActiveDirectory'])
		                		{ ?>
		                			<center>Ce compte est li?? ?? l'annuaire AD/LDAP. Le mot de passe ne peut pas ??tre modifi?? ici.</center>
		                		<?php }
		                		else
		                		{
			                		if ($_SESSION['DELEGATION_ACTIVE']==0)
			                		{ ?>
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
				                            <label>Saisir ?? nouveau le nouveau mot de passe:</label>
				                            <input type="password" class="form-control" placeholder="*****"
				                                   name="new2" required>
				                        </div>
				                        <div class="box-footer">
				                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
				                        </div>
				                    </form>
				                <?php } else { ?>
				                	<center>La modification du mot de passe est d??sactiv??e en mode d??l??gation.</center>
				                <?php }
				                } ?>
		                </div>
		            </div>
		            <div class="box box-info">
		            	<?php
		            		$fa = $db->prepare('SELECT doubleAuthSecret FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
                			$fa->execute(array('idPersonne' => $_SESSION['idPersonne']));
                			$fa = $fa->fetch();
		            	?>
		            	<div class="box-header with-border">
		                    <h3 class="box-title">Double authentification (actuellement <?php if(!is_null($fa['doubleAuthSecret'])){echo 'activ??e';}else{echo 'd??sactiv??e';} ?>)</h3>
		                </div>
		                <div class="box-body">
		                	<?php
		                		if ($_SESSION['DELEGATION_ACTIVE']==0)
		                		{
		                			if(is_null($fa['doubleAuthSecret']))
			                		{
			                			$Authenticator = new Authenticator();
			                			if (!isset($_SESSION['doubleAuthSecret_config']))
			                			{
			                				$_SESSION['doubleAuthSecret_config'] = $Authenticator->generateRandomSecret();
			                			}
			                			$qrCodeUrl = $Authenticator->getQR($_SESSION['identifiant'], $_SESSION['doubleAuthSecret_config'], $APPNAME);
			                			?>
			                			<form role="form" action="userDoubleAuthEnable.php" method="POST">
			                				<div class="form-group">
					                            <label>1 - T??l??chargez sur votre appareil mobile une application MFA (Google Authenticator, Microsoft Authenticator, ...)</label>
					                        </div>
			                				<div class="form-group">
					                            <label>2 - Dans l'application mobile, scannez le QRCode ci-dessous pour ajouter le compte (ou ajoutez le code manuellement):</label>
					                        </div>
			                				<center><img class="img-fluid" src="<?= $qrCodeUrl ?>"></center>
			                				<br/>
			                				<center><?=$_SESSION['doubleAuthSecret_config']?></center>
			                				<br/>
			                				<div class="form-group">
					                            <label>3 - Saisissez le code de s??curit?? obtenu:</label>
					                            <input type="text" class="form-control" placeholder="123456" name="code" required>
					                        </div>
					                        <button type="submit" class="btn btn-info pull-right"><i class="fa fa-lock"></i> Activer la double authentification</button>
			                			</form>
			                		<?php }
			                		else
			                		{ ?>
			                			<center><a href="userDoubleAuthDisable.php" class="btn btn-warning spinnerAttenteClick" onclick="return confirm('Etes-vous s??r de vouloir d??sactiver la double authentification ?');" title="D??sactiver la double authentification"><i class="fa fa-unlock"></i> D??sactiver la double authentification</a></center>
			                		<?php }
		                		}
		                		else
		                		{ ?>
			                		<center>La gestion de la double authentification est d??sactiv??e en mode d??l??gation.</center>
			                <?php } ?>
		                </div>
		            </div>
		        	<div class="box box-info">
		                <div class="box-header with-border">
		                    <h3 class="box-title">Mes param??tres personnels</h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                	<form role="form" action="userConfig.php" method="POST">
		                        <div class="form-group">
		                            <label>Rafraichissement auto de l'accueil (secondes) :</label>
		                            <input type="number" min="10" class="form-control" value="<?= $_SESSION['conf_accueilRefresh'] ?>" name="conf_accueilRefresh" required>
		                        </div>
		                        <div class="form-group">
		                            <label>Nombre de ligne par d??faut dans les tableaux:</label>
			                        <select class="form-control" style="width: 100%;" name="tableRowPerso">
			                            <option value="10" <?php if($_SESSION['tableRowPerso']==10){echo 'selected';} ?>>10</option>
			                            <option value="25" <?php if($_SESSION['tableRowPerso']==25){echo 'selected';} ?>>25</option>
			                            <option value="50" <?php if($_SESSION['tableRowPerso']==50){echo 'selected';} ?>>50</option>
			                            <option value="75" <?php if($_SESSION['tableRowPerso']==75){echo 'selected';} ?>>75</option>
			                            <option value="100" <?php if($_SESSION['tableRowPerso']==100){echo 'selected';} ?>>100</option>
			                            <option value="-1" <?php if($_SESSION['tableRowPerso']==-1){echo 'selected';} ?>>Tous</option>
			                        </select>
		                        </div>
		                        <div class="form-group">
		                            <label>Bandeau vertical de navigation:</label>
			                        <select class="form-control" style="width: 100%;" name="layout">
			                            <option value="fixed" <?php if($_SESSION['layout']=="fixed"){echo 'selected';} ?>>D??ploy??</option>
			                            <option value="sidebar-collapse" <?php if($_SESSION['layout']=="sidebar-collapse"){echo 'selected';} ?>>R??duit</option>
			                        </select>
		                        </div>
		                        <div class="form-group">
		                        	<?php
		                        		$lots = $_SESSION['lots_lecture'] OR $_SESSION['sac_lecture'] OR $_SESSION['sac2_lecture'] OR $_SESSION['materiel_lecture'];
		                        		$reserves = $_SESSION['reserve_lecture'];
		                        		$vehicules = $_SESSION['vehicules_lecture'];
		                        		$health = $_SESSION['vehiculeHealth_lecture'];
		                        		$desinfections = $_SESSION['desinfections_lecture'];
		                        		$tenues = $_SESSION['tenues_lecture'] OR $_SESSION['tenuesCatalogue_lecture'];
		                        		$alertesBenevolesLots      = $_SESSION['alertesBenevolesLots_lecture'];
                                    	$alertesBenevolesVehicules = $_SESSION['alertesBenevolesVehicules_lecture'];
		                        	?>
		                            <label>Pr??sence des indicateurs sur la page d'accueil:</label><br/>
		                            <?php
		                            	if ($lots+$reserves+$vehicules+$tenues+$health+$desinfections == 0)
		                            	{ ?>
		                            		<i><center>Votre profil actuel ne vous permet pas d'afficher des indicateurs sur votre dashboard.</center></i>
		                            	<?php }
		                            ?>
			                        <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur1Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur1Accueil" <?php if ($lots == 0){echo 'disabled';} ?>> Mat??riels p??rim??s (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur2Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur2Accueil" <?php if ($lots == 0){echo 'disabled';} ?>> Mat??riels manquants (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur3Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur3Accueil" <?php if ($lots == 0){echo 'disabled';} ?>> Lots en attente d'inventaire</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur4Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur4Accueil" <?php if ($lots == 0){echo 'disabled';} ?>> Lots non conformes</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur5Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur5Accueil" <?php if ($reserves == 0){echo 'disabled';} ?>> Mat??riels p??rim??s (r??serve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur6Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur6Accueil" <?php if ($reserves == 0){echo 'disabled';} ?>> Mat??riels manquants (r??serve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur11Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur11Accueil" <?php if ($desinfections == 0){echo 'disabled';} ?>> D??sinfections des v??hicules</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur12Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur12Accueil" <?php if ($health == 0){echo 'disabled';} ?>> Maintenance r??guli??re des v??hicules</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur9Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur9Accueil" <?php if ($tenues == 0){echo 'disabled';} ?>> Stock des tenues</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($_SESSION['conf_indicateur10Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur10Accueil" <?php if ($tenues == 0){echo 'disabled';} ?>> Non retour de tenues</label>
	                                </div>
		                        </div>
		                        <div class="form-group">
		                        	<?php
		                        		$query = $db->prepare('SELECT p.*, h.notifications FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;');
		                        		$query->execute(array('idPersonne' => $_SESSION['idPersonne']));
		                        		$data = $query->fetch();
		                        	?>
		                            <label>Abonnements aux notifications journali??res par mail:</label><br/>
		                            <?php
		                            	if ($data['notifications']==0 OR ($lots+$reserves+$vehicules+$tenues+$health+$desinfections+$alertesBenevolesVehicules+$alertesBenevolesLots == 0))
		                            	{ ?>
		                            		<i><center>Votre profil actuel ne vous permet pas de vous abonner aux notifications mail.</center></i>
		                            	<?php }
		                            ?>
			                        <div class="checkbox">
	                                	<label><input <?php if($data['notif_lots_manquants'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_lots_manquants" <?php if ($lots == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Mat??riels manquants (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_lots_peremptions'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_lots_peremptions" <?php if ($lots == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Mat??riels p??rim??s (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_lots_inventaires'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_lots_inventaires" <?php if ($lots == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Inventaires (lots)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_lots_conformites'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_lots_conformites" <?php if ($lots == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Lots non conformes</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_reserves_manquants'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_reserves_manquants" <?php if ($reserves == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Mat??riels manquants (r??serve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_reserves_peremptions'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_reserves_peremptions" <?php if ($reserves == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Mat??riels p??rim??s (r??serve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_reserves_inventaires'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_reserves_inventaires" <?php if ($reserves == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Inventaires (r??serve)</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_vehicules_desinfections'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_vehicules_desinfections" <?php if ($desinfections == 0 OR $data['notifications']==0){echo 'disabled';} ?>> D??sinfections v??hicules</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_vehicules_health'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_vehicules_health" <?php if ($health == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Maintenance r??guli??re des v??hicules</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_tenues_stock'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_tenues_stock" <?php if ($tenues == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Stock des tenues</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_tenues_retours'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_tenues_retours" <?php if ($tenues == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Non retour de tenues</label>
	                                </div>
	                                <div class="form-group">
                                        <label>Jours de r??ception des notifications: </label>
                                        <select class="form-control select2" style="width: 100%;" name="idCondition[]" multiple>
                                            <?php
                                                $query2 = $db->prepare('
                                                	SELECT
                                                		c.*,
                                                		a.idPersonne
                                                	FROM
                                                		NOTIFICATIONS_CONDITIONS c
                                                		LEFT OUTER JOIN (SELECT * FROM NOTIFICATIONS_ABONNEMENTS WHERE idPersonne = :idPersonne) a ON c.idCondition = a.idCondition
                                                	;');
								                $query2->execute(array('idPersonne' => $_SESSION['idPersonne']));
				
				                                while ($data2 = $query2->fetch())
				                                {
				                                    
				                                    echo '<option value=' . $data2['idCondition'];
				
									                if (isset($data2['idPersonne']) AND $data2['idPersonne']==$_SESSION['idPersonne'])
									                {
									                    echo " selected ";
									                }
									                echo '>' . $data2['libelleCondition'] . '</option>';
				                                }
                                           ?>
                                        </select>
                                    </div>
		                        </div>
		                        <div class="form-group">
		                            <label>Notifications en temps r??el sur alerte b??n??vole:</label><br/>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_benevoles_lots'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_benevoles_lots" <?php if ($alertesBenevolesLots == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Alerte remont??e par un b??n??vole sur un lot op??rationnel</label>
	                                </div>
	                                <div class="checkbox">
	                                	<label><input <?php if($data['notif_benevoles_vehicules'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_benevoles_vehicules" <?php if ($alertesBenevolesVehicules == 0 OR $data['notifications']==0){echo 'disabled';} ?>> Alerte remont??e par un b??n??vole sur un v??hicule</label>
	                                </div>
		                        </div>
		                        <div class="box-footer">
		                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
		                        </div>
		                    </form>
		                </div>
		             </div>
		            <div class="box box-info">
		                <div class="box-header with-border">
		                    <h3 class="box-title">Mes couleurs de calendrier</h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                	<form role="form" action="userCalendarColors.php" method="POST">
		                		<div class="form-group">
					                <label>Lots p??remptions</label>
					                <input type="text" name="agenda_lots_peremption" value="<?=$_SESSION['agenda_lots_peremption']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>R??serves p??remptions</label>
					                <input type="text" name="agenda_reserves_peremption" value="<?=$_SESSION['agenda_reserves_peremption']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>Lots inventaires ?? faire</label>
					                <input type="text" name="agenda_lots_inventaireAF" value="<?=$_SESSION['agenda_lots_inventaireAF']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>Lots inventaires faits</label>
					                <input type="text" name="agenda_lots_inventaireF" value="<?=$_SESSION['agenda_lots_inventaireF']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>Commandes Livraisons</label>
					                <input type="text" name="agenda_commandes_livraison" value="<?=$_SESSION['agenda_commandes_livraison']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>V??hicules R??visions</label>
					                <input type="text" name="agenda_vehicules_revision" value="<?=$_SESSION['agenda_vehicules_revision']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>V??hicules CT</label>
					                <input type="text" name="agenda_vehicules_ct" value="<?=$_SESSION['agenda_vehicules_ct']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>V??hicules assurance</label>
					                <input type="text" name="agenda_vehicules_assurance" value="<?=$_SESSION['agenda_vehicules_assurance']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>V??hicules taches de maintenance ponctuelle</label>
					                <input type="text" name="agenda_vehicules_maintenance" value="<?=$_SESSION['agenda_vehicules_maintenance']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>V??hicules taches de maintenance r??guli??re faite</label>
					                <input type="text" name="agenda_healthF" value="<?=$_SESSION['agenda_healthF']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>V??hicules taches de maintenance r??guli??re ?? faire</label>
					                <input type="text" name="agenda_healthAF" value="<?=$_SESSION['agenda_healthAF']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>Vehicules d??sinfections faites</label>
					                <input type="text" name="agenda_desinfections_desinfectionF" value="<?=$_SESSION['agenda_desinfections_desinfectionF']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>Vehicules d??sinfections ?? faire</label>
					                <input type="text" name="agenda_desinfections_desinfectionAF" value="<?=$_SESSION['agenda_desinfections_desinfectionAF']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>R??serves inventaires ?? faire</label>
					                <input type="text" name="agenda_reserves_inventaireAF" value="<?=$_SESSION['agenda_reserves_inventaireAF']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>R??serves inventaires faits</label>
					                <input type="text" name="agenda_reserves_inventaireF" value="<?=$_SESSION['agenda_reserves_inventaireF']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>Tenues</label>
					                <input type="text" name="agenda_tenues_tenues" value="<?=$_SESSION['agenda_tenues_tenues']?>" class="form-control my-colorpicker1">
					            </div>
					            <div class="form-group">
					                <label>ToDoList</label>
					                <input type="text" name="agenda_tenues_toDoList" value="<?=$_SESSION['agenda_tenues_toDoList']?>" class="form-control my-colorpicker1">
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
