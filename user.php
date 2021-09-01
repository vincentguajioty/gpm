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
	        		<div class="col-md-12">
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
			                        <div class="box-footer">
			                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
			                        </div>
			                    </form>
			                </div>
			            </div>
			        </div>
			        <div class="col-md-12">
			            <div class="box box-info">
			                <div class="box-header with-border">
			                    <h3 class="box-title">Mes habilitations sur <?php echo $APPNAME;?></h3>
			                </div>
			                <!-- /.box-header -->
			                <div class="box-body">
				                <div class="form-group">
				                    <label>Connexion à <?php echo $APPNAME;?>:</label>
				                    </br>
				                    <div class="checkbox">
				                        <label>
				                            <input disabled type="checkbox" value="1" name="connexion_connexion" <?php if ($_SESSION['connexion_connexion']==1) {echo 'checked';} ?>> Autorisé à se connecter à <?php echo $APPNAME;?>
				                        </label>
				                    </div>
				                    </br>
				                </div>
				                <div class="form-group">
				                    <label>Administration de <?php echo $APPNAME;?>:</label>
				                    </br>
				                    <div class="checkbox">
				                        <label>
				                            <input disabled type="checkbox" value="1" name="appli_conf" <?php if ($_SESSION['appli_conf']==1) {echo 'checked';} ?>> Modifier la configuration générale de <?php echo $APPNAME;?>
				                        </label>
				                    </div>
				                    <div class="checkbox">
				                        <label>
				                            <input disabled type="checkbox" value="1" name="annuaire_mdp" <?php if ($_SESSION['annuaire_mdp']==1) {echo 'checked';} ?>> Réinitialiser les mots de passe des autres utilisateurs
				                        </label>
				                    </div>
				                    <div class="checkbox">
				                        <label>
				                            <input disabled type="checkbox" value="1" name="maintenance" <?php if ($_SESSION['maintenance']==1) {echo 'checked';} ?>> Se connecter même en mode maitenance
				                        </label>
				                    </div>
				                    <div class="checkbox">
				                        <label>
				                            <input disabled type="checkbox" value="1" name="verrouIP" <?php if($_SESSION['verrouIP'] == 1) { echo 'checked'; } ?>> Gérer les adresses IP bloquées
				                        </label>
				                    </div>
				                    <div class="checkbox">
				                        <label>
				                            <input disabled type="checkbox" value="1" name="logs_lecture" <?php if($_SESSION['logs_lecture'] == 1) { echo 'checked'; } ?>> Lire les logs
				                        </label>
				                    </div>
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
				                        <td>Matériels/Consommables</td>
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
				                        <td>Plans de fréquences</td>
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
				                        <td>Véhicules</td>
				                        <td><?php if($_SESSION['vehicules_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['vehicules_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['vehicules_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['vehicules_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
				                        <th>PARAMETRES</th>
				                        <th></th>
				                        <th></th>
				                        <th></th>
				                        <th></th>
				                    </tr>
				                    <tr>
				                        <td>Catégories</td>
				                        <td><?php if($_SESSION['categories_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['categories_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['categories_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['categories_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                    </tr>
				                    <tr>
				                        <td>Fournisseurs</td>
				                        <td><?php if($_SESSION['fournisseurs_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['fournisseurs_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['fournisseurs_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['fournisseurs_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                    </tr>
				                    <tr>
				                        <td>Référentiels</td>
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
				                        <td>Types de véhicules</td>
				                        <td><?php if($_SESSION['vehicules_types_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['vehicules_types_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['vehicules_types_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['vehicules_types_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                    </tr>
				                </table>
				                <br/>
				                <table class="table table-bordered">
				                    <tr>
				                        <th>COMMANDES</th>
				                        <th>Lecture</th>
				                        <th>Ajout Modification</th>
				                        <th>Valider</th>
				                        <th>Etre en charge</th>
				                        <th>Abandonner Supprimer</th>
				                    </tr>
				                    <tr>
				                        <td>Commandes</td>
				                        <td><?php if($_SESSION['commande_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['commande_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['commande_valider'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['commande_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                        <td><?php if($_SESSION['commande_abandonner'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
				                    </tr>
				                    <tr>
				                        <td>Centres de coûts</td>
				                        <td><?php if($_SESSION['cout_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
				                        <th>Intégrer du matériel dans la réserve suite à une commande</th>
				                        <th>Sortir du matériel de la réserve pour l'intégrer à un lot</th>
				                    </tr>
				                    <tr>
				                        <td>Réserve</td>
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
				                        <td>Messages généraux</td>
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
			        </div>
		        </div>
		        <div class="col-md-4 col-sm-12 col-xs-12">
			        <div class="col-md-12">
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
			        <div class="col-md-12">
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
			                        		$tenues = $_SESSION['tenues_lecture'] OR $_SESSION['tenuesCatalogue_lecture'];
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
		                                <div class="checkbox">
		                                	<label><input <?php if($_SESSION['conf_indicateur9Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur9Accueil" <?php if ($tenues == 0){echo 'disabled';} ?>> Stock des tenues</label>
		                                </div>
		                                <div class="checkbox">
		                                	<label><input <?php if($_SESSION['conf_indicateur10Accueil'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="conf_indicateur10Accueil" <?php if ($tenues == 0){echo 'disabled';} ?>> Non retour de tenues</label>
		                                </div>
			                        </div>
			                        <div class="form-group">
			                        	<?php
			                        		$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne');
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
		                                <div class="checkbox">
		                                	<label><input <?php if($data['notif_tenues_stock'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_tenues_stock" <?php if ($tenues == 0){echo 'disabled';} ?>> Stock des tenues</label>
		                                </div>
		                                <div class="checkbox">
		                                	<label><input <?php if($data['notif_tenues_retours'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notif_tenues_retours" <?php if ($tenues == 0){echo 'disabled';} ?>> Non retour de tenues</label>
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
