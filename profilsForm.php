<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 402;
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
                Gestion des profils des utilisateurs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="profils.php">Profils</a></li>
                <li class="active">Ajouter/Modifier un profil</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php
            if ($_GET['id'] == 0) {
                ?>
                <!-- general form elements disabled -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ajout d'un profil</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="profilsAdd.php" method="POST">
                            <!-- text input -->
                            <div class="form-group">
                                <label>Libellé:<small style="color:grey;"> Requis</small></label>
                                <input type="text" class="form-control" placeholder="Libellé du profil à ajouter" name="libelleProfil" required>
                            </div>
                            <div class="form-group">
                                <label>Description:</label>
                                <textarea class="form-control" rows="3" placeholder="Spécifiez l'utilité du profil" name="descriptifProfil"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Connexion à <?php echo $APPNAME;?>:</label>
                                </br>
                                <div class="checkbox">
	                                <label>
	                                    <input type="checkbox" value="1" name="connexion_connexion"> Autorisé à se connecter à <?php echo $APPNAME;?>
	                                </label>
	                            </div>
                                </br>
                            </div>
                            <div class="form-group">
                                <label>Paramétrage général de <?php echo $APPNAME;?>:</label>
                                </br>
                                <div class="checkbox">
	                                <label>
	                                    <input type="checkbox" value="1" name="appli_conf"> Autorisé à modifier la configuration de <?php echo $APPNAME;?>
	                                </label>
	                            </div>
                                </br>
                            </div>
                            <div class="form-group">
                                <label>Notifications:</label>
                                </br>
                                <div class="notifications">
                                    <input type="radio" name="notifications" id="optionsRadios1" value="0" checked> Notifications mail désactivées
                                </div>
                                <div class="notifications">
                                    <input type="radio" name="notifications" id="optionsRadios2" value="1"> Notifications mail uniquement sur alerte
                                </div>
                                <div class="notifications">
                                    <input type="radio" name="notifications" id="optionsRadios3" value="2"> Notifications mail journalières
                                </div>
                                </br>
                            </div>
                            <div class="form-group">
                                <label>Réinitialisation des mots de passe:</label>
                                </br>
                                <div class="checkbox">
	                                <label>
	                                    <input type="checkbox" value="1" name="annuaire_mdp"> Réinitialiser les mots de passe des autres utilisateurs
	                                </label>
	                            </div>
                                </br>
                            </div>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Modules</th>
                                    <th>Lecture</th>
                                    <th>Ajout</th>
                                    <th>Modification</th>
                                    <th>Suppression</th>
                                </tr>
                                <tr>
                                    <td>Logs</td>
                                    <td><input type="checkbox" value="1" name="logs_lecture"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Verouillage IP</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="checkbox" value="1" name="verrouIP"></td>
                                </tr>
                                <tr>
                                    <td>Annuaire</td>
                                    <td><input type="checkbox" value="1" name="annuaire_lecture"></td>
                                    <td><input type="checkbox" value="1" name="annuaire_ajout"></td>
                                    <td><input type="checkbox" value="1" name="annuaire_modification"></td>
                                    <td><input type="checkbox" value="1" name="annuaire_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Profils</td>
                                    <td><input type="checkbox" value="1" name="profils_lecture"></td>
                                    <td><input type="checkbox" value="1" name="profils_ajout"></td>
                                    <td><input type="checkbox" value="1" name="profils_modification"></td>
                                    <td><input type="checkbox" value="1" name="profils_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Catégories</td>
                                    <td><input type="checkbox" value="1" name="categories_lecture"></td>
                                    <td><input type="checkbox" value="1" name="categories_ajout"></td>
                                    <td><input type="checkbox" value="1" name="categories_modification"></td>
                                    <td><input type="checkbox" value="1" name="categories_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Fournisseurs</td>
                                    <td><input type="checkbox" value="1" name="fournisseurs_lecture"></td>
                                    <td><input type="checkbox" value="1" name="fournisseurs_ajout"></td>
                                    <td><input type="checkbox" value="1" name="fournisseurs_modification"></td>
                                    <td><input type="checkbox" value="1" name="fournisseurs_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Référentiels</td>
                                    <td><input type="checkbox" value="1" name="typesLots_lecture"></td>
                                    <td><input type="checkbox" value="1" name="typesLots_ajout"></td>
                                    <td><input type="checkbox" value="1" name="typesLots_modification"></td>
                                    <td><input type="checkbox" value="1" name="typesLots_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Etats</td>
                                    <td><input type="checkbox" value="1" name="etats_lecture"></td>
                                    <td><input type="checkbox" value="1" name="etats_ajout"></td>
                                    <td><input type="checkbox" value="1" name="etats_modification"></td>
                                    <td><input type="checkbox" value="1" name="etats_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Lieux</td>
                                    <td><input type="checkbox" value="1" name="lieux_lecture"></td>
                                    <td><input type="checkbox" value="1" name="lieux_ajout"></td>
                                    <td><input type="checkbox" value="1" name="lieux_modification"></td>
                                    <td><input type="checkbox" value="1" name="lieux_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Lots</td>
                                    <td><input type="checkbox" value="1" name="lots_lecture"></td>
                                    <td><input type="checkbox" value="1" name="lots_ajout"></td>
                                    <td><input type="checkbox" value="1" name="lots_modification"></td>
                                    <td><input type="checkbox" value="1" name="lots_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Sacs</td>
                                    <td><input type="checkbox" value="1" name="sac_lecture"></td>
                                    <td><input type="checkbox" value="1" name="sac_ajout"></td>
                                    <td><input type="checkbox" value="1" name="sac_modification"></td>
                                    <td><input type="checkbox" value="1" name="sac_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Emplacements</td>
                                    <td><input type="checkbox" value="1" name="sac2_lecture"></td>
                                    <td><input type="checkbox" value="1" name="sac2_ajout"></td>
                                    <td><input type="checkbox" value="1" name="sac2_modification"></td>
                                    <td><input type="checkbox" value="1" name="sac2_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Catalogue</td>
                                    <td><input type="checkbox" value="1" name="catalogue_lecture"></td>
                                    <td><input type="checkbox" value="1" name="catalogue_ajout"></td>
                                    <td><input type="checkbox" value="1" name="catalogue_modification"></td>
                                    <td><input type="checkbox" value="1" name="catalogue_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Matériels/Consommables</td>
                                    <td><input type="checkbox" value="1" name="materiel_lecture"></td>
                                    <td><input type="checkbox" value="1" name="materiel_ajout"></td>
                                    <td><input type="checkbox" value="1" name="materiel_modification"></td>
                                    <td><input type="checkbox" value="1" name="materiel_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Messages généraux</td>
                                    <td></td>
                                    <td><input type="checkbox" value="1" name="messages_ajout"></td>
                                    <td></td>
                                    <td><input type="checkbox" value="1" name="messages_suppression"></td>
                                </tr>
                            </table>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Modules</th>
                                    <th>Lecture</th>
                                    <th>Ajout/Modification</th>
                                    <th>Valider</th>
                                    <th>Etre en charge</th>
                                    <th>Abandonner/Supprimer</th>
                                </tr>
                                <tr>
                                    <td>Commandes</td>
                                    <td><input type="checkbox" value="1" name="commande_lecture"></td>
                                    <td><input type="checkbox" value="1" name="commande_ajout"></td>
                                    <td><input type="checkbox" value="1" name="commande_valider"></td>
                                    <td><input type="checkbox" value="1" name="commande_etreEnCharge"></td>
                                    <td><input type="checkbox" value="1" name="commande_abandonner"></td>
                                </tr>
                                <tr>
                                    <td>Centres de coûts</td>
                                    <td><input type="checkbox" value="1" name="cout_lecture"></td>
                                    <td><input type="checkbox" value="1" name="cout_ajout"></td>
                                    <td></td>
                                    <td><input type="checkbox" value="1" name="cout_etreEnCharge"></td>
                                    <td><input type="checkbox" value="1" name="cout_supprimer"></td>
                                </tr>
                            </table>
                            <div class="box-footer">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->

                </div>

                <?php
            }
            else {
                ?>

                <!-- general form elements disabled -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Modification d'un profil</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="profilsUpdate.php?id=<?=$_GET['id']?>" method="POST">
                            <?php
                            $query = $db->prepare('SELECT * FROM PROFILS WHERE idProfil=:idProfil;');
                            $query->execute(array('idProfil' => $_GET['id']));
                            $data = $query->fetch();
                            ?>

                            <div class="form-group">
                                <label>Libellé:<small style="color:grey;"> Requis</small></label>
                                <input type="text" class="form-control" value="<?=$data['libelleProfil']?>" name="libelleProfil" required>
                            </div>
                            <div class="form-group">
                                <label>Description:</label>
                                <textarea class="form-control" rows="3" name="descriptifProfil"><?=$data['descriptifProfil']?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Connexion à <?php echo $APPNAME;?>:</label>
                                </br>
                                <div class="checkbox">
	                                <label>
	                                    <input type="checkbox" value="1" name="connexion_connexion" <?php if ($data['connexion_connexion']==1) {echo 'checked';} ?>> Autorisé à se connecter à <?php echo $APPNAME;?>
	                                </label>
	                            </div>
                                </br>
                            </div>
                            <div class="form-group">
                                <label>Paramétrage général de <?php echo $APPNAME;?>:</label>
                                </br>
                                <div class="checkbox">
	                                <label>
	                                    <input type="checkbox" value="1" name="appli_conf" <?php if ($data['appli_conf']==1) {echo 'checked';} ?>> Autorisé à modifier la configuration de <?php echo $APPNAME;?>
	                                </label>
	                            </div>
                                </br>
                            </div>
                            <div class="form-group">
                                <label>Notifications:</label>
                                </br>
                                <div class="notifications">
                                    <input type="radio" name="notifications" id="optionsRadios1" value="0" <?php
                                    if ($data['notifications']==0)
                                    {
                                        echo "checked";
                                    }
                                    ?>>
                                    Notifications mail désactivées
                                </div>
                                <div class="notifications">
                                    <input type="radio" name="notifications" id="optionsRadios2" value="1" <?php
                                    if ($data['notifications']==1)
                                    {
                                        echo "checked";
                                    }
                                    ?>>
                                    Notifications mail uniquement sur alerte
                                </div>
                                <div class="notifications">
                                    <input type="radio" name="notifications" id="optionsRadios3" value="2" <?php
                                    if ($data['notifications']==2)
                                    {
                                        echo "checked";
                                    }
                                    ?>>
                                    Notifications mail journalières
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Réinitialisation des mots de passe:</label>
                                </br>
                                <div class="checkbox">
	                                <label>
	                                    <input type="checkbox" value="1" name="annuaire_mdp" <?php if ($data['annuaire_mdp']==1) {echo 'checked';} ?>> Réinitialiser les mots de passe des autres utilisateurs
	                                </label>
	                            </div>
                                </br>
                            </div>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Modules</th>
                                    <th>Lecture</th>
                                    <th>Ajout</th>
                                    <th>Modification</th>
                                    <th>Suppression</th>
                                </tr>
                                <tr>
                                    <td>Logs</td>
                                    <td><input <?php if($data['logs_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="logs_lecture"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Verouillage IP</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input <?php if($data['verrouIP'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="verrouIP"></td>
                                </tr>
                                <tr>
                                    <td>Annuaire</td>
                                    <td><input <?php if($data['annuaire_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="annuaire_lecture"></td>
                                    <td><input <?php if($data['annuaire_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="annuaire_ajout"></td>
                                    <td><input <?php if($data['annuaire_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="annuaire_modification"></td>
                                    <td><input <?php if($data['annuaire_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="annuaire_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Profils</td>
                                    <td><input <?php if($data['profils_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="profils_lecture"></td>
                                    <td><input <?php if($data['profils_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="profils_ajout"></td>
                                    <td><input <?php if($data['profils_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="profils_modification"></td>
                                    <td><input <?php if($data['profils_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="profils_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Catégories</td>
                                    <td><input <?php if($data['categories_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="categories_lecture"></td>
                                    <td><input <?php if($data['categories_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="categories_ajout"></td>
                                    <td><input <?php if($data['categories_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="categories_modification"></td>
                                    <td><input <?php if($data['categories_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="categories_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Fournisseurs</td>
                                    <td><input <?php if($data['fournisseurs_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="fournisseurs_lecture"></td>
                                    <td><input <?php if($data['fournisseurs_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="fournisseurs_ajout"></td>
                                    <td><input <?php if($data['fournisseurs_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="fournisseurs_modification"></td>
                                    <td><input <?php if($data['fournisseurs_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="fournisseurs_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Référentiels</td>
                                    <td><input <?php if($data['typesLots_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="typesLots_lecture"></td>
                                    <td><input <?php if($data['typesLots_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="typesLots_ajout"></td>
                                    <td><input <?php if($data['typesLots_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="typesLots_modification"></td>
                                    <td><input <?php if($data['typesLots_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="typesLots_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Etats</td>
                                    <td><input <?php if($data['etats_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="etats_lecture"></td>
                                    <td><input <?php if($data['etats_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="etats_ajout"></td>
                                    <td><input <?php if($data['etats_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="etats_modification"></td>
                                    <td><input <?php if($data['etats_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="etats_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Lieux</td>
                                    <td><input <?php if($data['lieux_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lieux_lecture"></td>
                                    <td><input <?php if($data['lieux_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lieux_ajout"></td>
                                    <td><input <?php if($data['lieux_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lieux_modification"></td>
                                    <td><input <?php if($data['lieux_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lieux_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Lots</td>
                                    <td><input <?php if($data['lots_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lots_lecture"></td>
                                    <td><input <?php if($data['lots_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lots_ajout"></td>
                                    <td><input <?php if($data['lots_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lots_modification"></td>
                                    <td><input <?php if($data['lots_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lots_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Sacs</td>
                                    <td><input <?php if($data['sac_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac_lecture"></td>
                                    <td><input <?php if($data['sac_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac_ajout"></td>
                                    <td><input <?php if($data['sac_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac_modification"></td>
                                    <td><input <?php if($data['sac_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Emplacements</td>
                                    <td><input <?php if($data['sac2_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac2_lecture"></td>
                                    <td><input <?php if($data['sac2_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac2_ajout"></td>
                                    <td><input <?php if($data['sac2_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac2_modification"></td>
                                    <td><input <?php if($data['sac2_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac2_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Catalogue</td>
                                    <td><input <?php if($data['catalogue_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="catalogue_lecture"></td>
                                    <td><input <?php if($data['catalogue_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="catalogue_ajout"></td>
                                    <td><input <?php if($data['catalogue_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="catalogue_modification"></td>
                                    <td><input <?php if($data['catalogue_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="catalogue_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Matériels/Consommables</td>
                                    <td><input <?php if($data['materiel_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="materiel_lecture"></td>
                                    <td><input <?php if($data['materiel_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="materiel_ajout"></td>
                                    <td><input <?php if($data['materiel_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="materiel_modification"></td>
                                    <td><input <?php if($data['materiel_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="materiel_suppression"></td>
                                </tr>
                                <tr>
                                    <td>Messages généraux</td>
                                    <td></td>
                                    <td><input <?php if($data['messages_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="messages_ajout"></td>
                                    <td></td>
                                    <td><input <?php if($data['messages_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="messages_suppression"></td>
                                </tr>
                            </table>
                            <table class="table table-bordered">
                                <tr>
                                    <th></th>
                                    <th>Lecture</th>
                                    <th>Ajout/Modification</th>
                                    <th>Valider</th>
                                    <th>Etre en charge</th>
                                    <th>Abandonner/Supprimer</th>
                                </tr>
                                <tr>
                                    <td>Commandes</td>
                                    <td><input <?php if($data['commande_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_lecture"></td>
                                    <td><input <?php if($data['commande_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_ajout"></td>
                                    <td><input <?php if($data['commande_valider'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_valider"></td>
                                    <td><input <?php if($data['commande_etreEnCharge'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_etreEnCharge"></td>
                                    <td><input <?php if($data['commande_abandonner'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_abandonner"></td>
                                </tr>
                                <tr>
                                    <td>Centres de coûts</td>
                                    <td><input <?php if($data['cout_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="cout_lecture"></td>
                                    <td><input <?php if($data['cout_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="cout_ajout"></td>
                                    <td></td>
                                    <td><input <?php if($data['cout_etreEnCharge'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="cout_etreEnCharge"></td>
                                    <td><input <?php if($data['cout_supprimer'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="cout_supprimer"></td>
                                </tr>
                            </table>

                            <div class="box-footer">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <button type="submit" class="btn btn-info pull-right">Modifier</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->

                </div>
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Utilisateurs rattachés au profil</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <?php if ($_SESSION['annuaire_modification']==1) {?>
                            <form role="form" action="annuaireProfilOn.php?idProfil=<?=$_GET['id']?>" method="POST">
                                <div class="form-group">
                                    <select class="form-control select2" style="width: 100%;" name="identifiant">
                                        <?php
                                        $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idProfil = Null OR idProfil != :idProfil;');
                                        $query->execute(array(
                                            'idProfil' => $_GET['id']
                                        ));
                                        while ($data = $query->fetch())
                                        {
                                            ?>
                                            <option value="<?php echo $data['idPersonne']; ?>"><?php echo $data['identifiant']; ?></option>
                                            <?php
                                        }
                                        $query->closeCursor(); ?>
                                    </select>
                                    <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                                </div>
                            </form>
                        <?php }?>
                        <table id="tri2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Identifiant</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE p.idProfil = :idProfil;');
                            $query->execute(array(
                                'idProfil' => $_GET['id']
                            ));
                            while ($data = $query->fetch())
                            {?>
                                <tr>
                                    <td><?php echo $data['idPersonne']; ?></td>
                                    <td><?php echo $data['identifiant']; ?></td>
                                    <td><?php echo $data['nomPersonne']; ?></td>
                                    <td><?php echo $data['prenomPersonne']; ?></td>
                                    <td>
                                        <?php if ($_SESSION['annuaire_modification']==1) {?>
                                            <a href="annuaireForm.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                                        <?php }?>
                                        <?php if ($_SESSION['annuaire_modification']==1) {?>
                                            <a href="annuaireProfilOff.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir délier cet utilisteur de ce profil?');">Délier</a>
                                        <?php }?>
                                    </td>
                                </tr>
                                <?php
                            }
                            $query->closeCursor(); ?>
                            </tbody>


                        </table>
                    </div>
                    <!-- /.box-body -->

                </div>

                <?php
            }
            ?>

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
