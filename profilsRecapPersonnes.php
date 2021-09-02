<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 402;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['profils_lecture']==0 OR $_SESSION['annuaire_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Récapitulatif des profils / Vue utilisateurs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Profils</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box box-success">
            	<?php
            		$users = $db->query('SELECT * FROM VIEW_HABILITATIONS;');
            		$users = $users->fetchall();
            	?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered">
                    	<tr>
                            <th>ADMINISTRATION</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    	<tr>
                            <td>Connexion</td>
                            <td>
                            	<?php foreach($users as $user){if($user['connexion_connexion']){echo $user['identifiant'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Modifier la configuration générale de <?php echo $APPNAME;?></td>
                            <td>
                            	<?php foreach($users as $user){if($user['appli_conf']){echo $user['identifiant'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Se connecter même en mode maintenance</td>
                            <td>
                            	<?php foreach($users as $user){if($user['maintenance']){echo $user['identifiant'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Se connecter entant qu'autre utilisateur</td>
                            <td>
                            	<?php foreach($users as $user){if($user['delegation']){echo $user['identifiant'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Réinitialiser les mots de passe des autres utilisateurs</td>
                            <td>
                            	<?php foreach($users as $user){if($user['annuaire_mdp']){echo $user['identifiant'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Gérer les adresses IP bloquées</td>
                            <td>
                            	<?php foreach($users as $user){if($user['verrouIP']){echo $user['identifiant'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Mener des actions massives directement en base</td>
                            <td>
                            	<?php foreach($users as $user){if($user['actionsMassives']){echo $user['identifiant'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Autorisé à recevoir les notifications journalières par mail</td>
                            <td>
                            	<?php foreach($users as $user){if($user['notifications']){echo $user['identifiant'].'<br/>';}} ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="box box-success">
            	<div class="box-body">
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
                            <td><?php foreach($users as $user){if($user['lots_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['lots_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['lots_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['lots_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Sacs</td>
                            <td><?php foreach($users as $user){if($user['sac_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['sac_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['sac_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['sac_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Emplacements</td>
                            <td><?php foreach($users as $user){if($user['sac2_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['sac2_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['sac2_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['sac2_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Matériels/Consommables</td>
                            <td><?php foreach($users as $user){if($user['materiel_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['materiel_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['materiel_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['materiel_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
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
                            <td><?php foreach($users as $user){if($user['vhf_canal_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_canal_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_canal_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_canal_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Plans de fréquences</td>
                            <td><?php foreach($users as $user){if($user['vhf_plan_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_plan_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_plan_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_plan_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Equipements de transmission</td>
                            <td><?php foreach($users as $user){if($user['vhf_equipement_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_equipement_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_equipement_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vhf_equipement_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
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
                            <td><?php foreach($users as $user){if($user['vehicules_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehicules_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehicules_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehicules_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Désinfections</td>
                            <td><?php foreach($users as $user){if($user['desinfections_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['desinfections_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['desinfections_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['desinfections_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Taches de maintenance</td>
                            <td><?php foreach($users as $user){if($user['vehiculeHealth_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehiculeHealth_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehiculeHealth_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehiculeHealth_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
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
                            <td><?php foreach($users as $user){if($user['tenues_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['tenues_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['tenues_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['tenues_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Catalogue des tenues</td>
                            <td><?php foreach($users as $user){if($user['tenuesCatalogue_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['tenuesCatalogue_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['tenuesCatalogue_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['tenuesCatalogue_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Cautions</td>
                            <td><?php foreach($users as $user){if($user['cautions_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['cautions_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['cautions_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['cautions_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
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
                            <td><?php foreach($users as $user){if($user['categories_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['categories_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['categories_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['categories_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Fournisseurs</td>
                            <td><?php foreach($users as $user){if($user['fournisseurs_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['fournisseurs_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['fournisseurs_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['fournisseurs_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Référentiels</td>
                            <td><?php foreach($users as $user){if($user['typesLots_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['typesLots_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['typesLots_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['typesLots_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Lieux</td>
                            <td><?php foreach($users as $user){if($user['lieux_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['lieux_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['lieux_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['lieux_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Catalogue</td>
                            <td><?php foreach($users as $user){if($user['catalogue_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['catalogue_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['catalogue_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['catalogue_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Types de véhicules</td>
                            <td><?php foreach($users as $user){if($user['vehicules_types_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehicules_types_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehicules_types_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehicules_types_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Types de désinfections</td>
                            <td><?php foreach($users as $user){if($user['typesDesinfections_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['typesDesinfections_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['typesDesinfections_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['typesDesinfections_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Types de taches de maintenance</td>
                            <td><?php foreach($users as $user){if($user['vehiculeHealthType_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehiculeHealthType_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehiculeHealthType_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['vehiculeHealthType_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Carburants</td>
                            <td><?php foreach($users as $user){if($user['carburants_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['carburants_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['carburants_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['carburants_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Etats</td>
                            <td><?php foreach($users as $user){if($user['etats_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['etats_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['etats_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['etats_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="box box-success">
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ALERTES BENEVOLES</th>
                            <th>Lecture</th>
                            <th>Etre en charge</th>
                            <th>Affecter à un tier</th>
                        </tr>
                        <tr>
                            <td>Lots</td>
                            <td><?php foreach($users as $user){if($user['alertesBenevolesLots_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['alertesBenevolesLots_affectation']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['alertesBenevolesLots_affectationTier']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Véhicules</td>
                            <td><?php foreach($users as $user){if($user['alertesBenevolesVehicules_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['alertesBenevolesVehicules_affectation']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['alertesBenevolesVehicules_affectationTier']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="box box-success">
            	<div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>COMMANDES</th>
                            <th>Lecture</th>
                            <th>Ajout/Modification</th>
                            <th>Valideur universel</th>
                            <th>Etre en charge</th>
                            <th>Abandonner/Supprimer</th>
                        </tr>
                        <tr>
                            <td>Commandes</td>
                            <td><?php foreach($users as $user){if($user['commande_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['commande_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['commande_valider_delegate']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['commande_etreEnCharge']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['commande_abandonner']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Centres de coûts</td>
                            <td><?php foreach($users as $user){if($user['cout_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['cout_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td></td>
                            <td><?php foreach($users as $user){if($user['cout_etreEnCharge']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['cout_supprimer']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="box box-success">
            	<div class="box-body">
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
                            <td><?php foreach($users as $user){if($user['reserve_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['reserve_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['reserve_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['reserve_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['reserve_cmdVersReserve']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['reserve_ReserveVersLot']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="box box-success">
            	<div class="box-body">
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
                            <td><?php foreach($users as $user){if($user['annuaire_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['annuaire_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['annuaire_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td></td>
                            <td><?php foreach($users as $user){if($user['annuaire_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Profils</td>
                            <td><?php foreach($users as $user){if($user['profils_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['profils_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td><?php foreach($users as $user){if($user['profils_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
                            <td></td>
                            <td><?php foreach($users as $user){if($user['profils_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
                        </tr>
	                    <tr>
	                        <td>Messages généraux</td>
	                        <td></td>
	                        <td><?php foreach($users as $user){if($user['messages_ajout']){echo $user['identifiant'].'<br/>';}} ?></td>
	                        <td></td>
	                        <td></td>
	                        <td><?php foreach($users as $user){if($user['messages_suppression']){echo $user['identifiant'].'<br/>';}} ?></td>
	                    </tr>
	                    <tr>
	                        <td>Messages mails</td>
	                        <td></td>
	                        <td><?php foreach($users as $user){if($user['contactMailGroupe']){echo $user['identifiant'].'<br/>';}} ?></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                    </tr>
	                    <tr>
	                        <td>ToDoList</td>
	                        <td><?php foreach($users as $user){if($user['todolist_lecture']){echo $user['identifiant'].'<br/>';}} ?></td>
	                        <td></td>
	                        <td><?php foreach($users as $user){if($user['todolist_modification']){echo $user['identifiant'].'<br/>';}} ?></td>
	                        <td><?php foreach($users as $user){if($user['todolist_perso']){echo $user['identifiant'].'<br/>';}} ?></td>
	                        <td></td>
	                    </tr>
	                </table>
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
