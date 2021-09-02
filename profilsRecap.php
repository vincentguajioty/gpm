<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 402;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['profils_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Récapitulatif des profils
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
            		$profils = $db->query('SELECT * FROM PROFILS;');
            		$profils = $profils->fetchall();
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
                            	<?php foreach($profils as $profil){if($profil['connexion_connexion']){echo $profil['libelleProfil'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Modifier la configuration générale de <?php echo $APPNAME;?></td>
                            <td>
                            	<?php foreach($profils as $profil){if($profil['appli_conf']){echo $profil['libelleProfil'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Se connecter même en mode maintenance</td>
                            <td>
                            	<?php foreach($profils as $profil){if($profil['maintenance']){echo $profil['libelleProfil'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Se connecter entant qu'autre utilisateur</td>
                            <td>
                            	<?php foreach($profils as $profil){if($profil['delegation']){echo $profil['libelleProfil'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Réinitialiser les mots de passe des autres utilisateurs</td>
                            <td>
                            	<?php foreach($profils as $profil){if($profil['annuaire_mdp']){echo $profil['libelleProfil'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Gérer les adresses IP bloquées</td>
                            <td>
                            	<?php foreach($profils as $profil){if($profil['verrouIP']){echo $profil['libelleProfil'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Mener des actions massives directement en base</td>
                            <td>
                            	<?php foreach($profils as $profil){if($profil['actionsMassives']){echo $profil['libelleProfil'].'<br/>';}} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Autorisé à recevoir les notifications journalières par mail</td>
                            <td>
                            	<?php foreach($profils as $profil){if($profil['notifications']){echo $profil['libelleProfil'].'<br/>';}} ?>
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
                            <td><?php foreach($profils as $profil){if($profil['lots_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['lots_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['lots_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['lots_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Sacs</td>
                            <td><?php foreach($profils as $profil){if($profil['sac_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['sac_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['sac_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['sac_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Emplacements</td>
                            <td><?php foreach($profils as $profil){if($profil['sac2_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['sac2_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['sac2_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['sac2_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Matériels/Consommables</td>
                            <td><?php foreach($profils as $profil){if($profil['materiel_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['materiel_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['materiel_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['materiel_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
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
                            <td><?php foreach($profils as $profil){if($profil['vhf_canal_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_canal_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_canal_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_canal_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Plans de fréquences</td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_plan_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_plan_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_plan_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_plan_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Equipements de transmission</td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_equipement_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_equipement_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_equipement_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vhf_equipement_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
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
                            <td><?php foreach($profils as $profil){if($profil['vehicules_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vehicules_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vehicules_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vehicules_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Désinfections</td>
                            <td><?php foreach($profils as $profil){if($profil['desinfections_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['desinfections_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['desinfections_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['desinfections_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
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
                            <td><?php foreach($profils as $profil){if($profil['tenues_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['tenues_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['tenues_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['tenues_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Catalogue des tenues</td>
                            <td><?php foreach($profils as $profil){if($profil['tenuesCatalogue_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['tenuesCatalogue_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['tenuesCatalogue_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['tenuesCatalogue_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Cautions</td>
                            <td><?php foreach($profils as $profil){if($profil['cautions_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['cautions_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['cautions_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['cautions_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
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
                            <td><?php foreach($profils as $profil){if($profil['categories_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['categories_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['categories_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['categories_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Fournisseurs</td>
                            <td><?php foreach($profils as $profil){if($profil['fournisseurs_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['fournisseurs_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['fournisseurs_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['fournisseurs_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Référentiels</td>
                            <td><?php foreach($profils as $profil){if($profil['typesLots_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['typesLots_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['typesLots_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['typesLots_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Lieux</td>
                            <td><?php foreach($profils as $profil){if($profil['lieux_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['lieux_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['lieux_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['lieux_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Catalogue</td>
                            <td><?php foreach($profils as $profil){if($profil['catalogue_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['catalogue_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['catalogue_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['catalogue_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Types de véhicules</td>
                            <td><?php foreach($profils as $profil){if($profil['vehicules_types_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vehicules_types_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vehicules_types_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['vehicules_types_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Types de désinfections</td>
                            <td><?php foreach($profils as $profil){if($profil['typesDesinfections_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['typesDesinfections_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['typesDesinfections_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['typesDesinfections_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Etats</td>
                            <td><?php foreach($profils as $profil){if($profil['etats_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['etats_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['etats_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['etats_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
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
                            <th>Valider</th>
                            <th>Valider à la place de</th>
                            <th>Etre en charge</th>
                            <th>Abandonner/Supprimer</th>
                        </tr>
                        <tr>
                            <td>Commandes</td>
                            <td><?php foreach($profils as $profil){if($profil['commande_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['commande_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['commande_valider']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['commande_valider_delegate']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['commande_etreEnCharge']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['commande_abandonner']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Centres de coûts</td>
                            <td><?php foreach($profils as $profil){if($profil['cout_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['cout_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td></td>
                            <td></td>
                            <td><?php foreach($profils as $profil){if($profil['cout_etreEnCharge']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['cout_supprimer']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
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
                            <td><?php foreach($profils as $profil){if($profil['reserve_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['reserve_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['reserve_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['reserve_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['reserve_cmdVersReserve']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['reserve_ReserveVersLot']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
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
                            <td><?php foreach($profils as $profil){if($profil['annuaire_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['annuaire_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['annuaire_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td></td>
                            <td><?php foreach($profils as $profil){if($profil['annuaire_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
                        <tr>
                            <td>Profils</td>
                            <td><?php foreach($profils as $profil){if($profil['profils_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['profils_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td><?php foreach($profils as $profil){if($profil['profils_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                            <td></td>
                            <td><?php foreach($profils as $profil){if($profil['profils_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
                        </tr>
	                    <tr>
	                        <td>Messages généraux</td>
	                        <td></td>
	                        <td><?php foreach($profils as $profil){if($profil['messages_ajout']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
	                        <td></td>
	                        <td></td>
	                        <td><?php foreach($profils as $profil){if($profil['messages_suppression']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
	                    </tr>
	                    <tr>
	                        <td>Messages mails</td>
	                        <td></td>
	                        <td><?php foreach($profils as $profil){if($profil['contactMailGroupe']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                    </tr>
	                    <tr>
	                        <td>ToDoList</td>
	                        <td><?php foreach($profils as $profil){if($profil['todolist_lecture']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
	                        <td></td>
	                        <td><?php foreach($profils as $profil){if($profil['todolist_modification']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
	                        <td><?php foreach($profils as $profil){if($profil['todolist_perso']){echo $profil['libelleProfil'].'<br/>';}} ?></td>
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
