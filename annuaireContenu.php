<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 401;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['annuaire_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    
    <?php
        $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN VIEW_HABILITATIONS v ON p.idPersonne = v.idPersonne WHERE p.idPersonne = :idPersonne;');
        $query->execute(array('idPersonne'=>$_GET['id']));
        $personne = $query->fetch();
    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?= $personne['nomPersonne'] ?> <?= $personne['prenomPersonne'] ?> (<?= $personne['identifiant'] ?>)
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="annuaire.php">Annuaire</a></li>
                <li class="active"><?= $personne['identifiant'] ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <i class="fa fa-wrench"></i>
                            <h3 class="box-title">Actions d'administration du compte</h3>
                        </div>
                        <div class="box-body">
                            <?php if ($_SESSION['annuaire_modification']==1) {?>
                                <a href="annuaireSetDashboard.php?id=<?=$_GET['id']?>" class="btn btn-success spinnerAttenteClick" onclick="return confirm('Etes-vous sûr de vouloir réactiver les indicateurs sur la page d\'accueil de l\'utilisateur ?');" title="Forcer les indicateurs sur la page d'accueil"><i class="fa fa-dashboard"></i> Réactiver les indicateurs sur la page d'accueil</a>
                            <?php }?>

                            <?php if ($_SESSION['annuaire_modification']==1) {?>
                                <a href="annuaireSetMail.php?id=<?=$_GET['id']?>" class="btn btn-success spinnerAttenteClick" onclick="return confirm('Etes-vous sûr de vouloir réactiver les notifications pour cet utilisateur ?');" title="Forcer l'activation des notifications"><i class="fa fa-envelope"></i> Réactiver les notifications par mail</a>
                            <?php }?>

                            <?php if ($_SESSION['annuaire_mdp']==1) {?>
                                <a href="annuaireRAZdoubleFactor.php?id=<?=$_GET['id']?>" class="btn btn-info spinnerAttenteClick" onclick="return confirm('Etes-vous sûr de vouloir désactiver la double authentification pour cet utilisateur ?');" title="Désactiver le MFA"><i class="fa fa-unlock"></i> Désactiver la double authentification</a>
                            <?php }?>

                            <?php if ($_SESSION['annuaire_mdp']==1) {?>
                                <a href="annuaireRAZ.php?id=<?=$_GET['id']?>" class="btn btn-info spinnerAttenteClick" onclick="return confirm('Etes-vous sûr de vouloir réinitialiser ce mot de passe (le nouveau mot de passe prendra la valeur de l\'identifiant) ce qui va également désactiver la double authentification ?');" title="Réinitialiser le mot de passe"><i class="fa fa-lock"></i> Réinitialiser le mot de passe de l'utilisateur</a>
                            <?php }?>
                            
                            <?php if ($_SESSION['delegation']==1 AND $_SESSION['DELEGATION_ACTIVE']==0 AND $_SESSION['idPersonne']!=$_GET['id']) {?>
                                <a href="loginDelegate.php?idDelegate=<?=$_GET['id']?>" class="btn btn-warning spinnerAttenteClick" title="Se connecter entant que"><i class="fa fa-user"></i> Se connecter entant que (délégation)</a>
                            <?php }?>

                            <?php if ($_SESSION['annuaire_suppression']==1) {?>
                                <a href="modalDeleteConfirm.php?case=annuaireDelete&id=<?=$_GET['id']?>" class="btn spinnerAttenteClick btn-danger modal-form pull-right" title="Supprimer"><i class="fa fa-trash"></i> Supprimer l'utilisateur</a>
                            <?php }?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="box box-info">
                        <?php if ($_SESSION['annuaire_modification']==1) {?> <form role="form" action="annuaireUpdate.php?id=<?= $_GET['id'] ?>" method="POST"><?php }?>
                            <div class="box-header with-border">
                                <i class="fa fa-user"></i>
                                <h3 class="box-title">Modifier les propriétés de l'utilisateur</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label>Identifiant de connexion: <small style="color:grey;"> Requis</small></label>
                                    <input type="text" class="form-control" value="<?= isset($personne['identifiant']) ? $personne['identifiant'] : ''?>" name="identifiant" required>
                                </div>
                                <div class="form-group">
                                    <label>Nom:</label>
                                    <input type="text" class="form-control" value="<?= isset($personne['nomPersonne']) ? $personne['nomPersonne'] : ''?>" name="nomPersonne">
                                </div>
                                <div class="form-group">
                                    <label>Prénom:</label>
                                    <input type="text" class="form-control" value="<?= isset($personne['prenomPersonne']) ? $personne['prenomPersonne'] : ''?>" name="prenomPersonne">
                                </div>
                                <div class="form-group">
                                    <label>Adresse mail:</label>
                                    <input type="text" class="form-control" value="<?= isset($personne['mailPersonne']) ? $personne['mailPersonne'] : ''?>" name="mailPersonne">
                                </div>
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input type="text" class="form-control" value="<?= isset($personne['telPersonne']) ? $personne['telPersonne'] : ''?>" name="telPersonne">
                                </div>
                                <div class="form-group">
                                    <label>Fonction:</label>
                                    <input type="text" class="form-control" value="<?= isset($personne['fonction']) ? $personne['fonction'] : ''?>" name="fonction">
                                </div>
                                <div class="form-group">
                                    <label>Profil d'habilitation: </label>
                                    <select class="form-control select2" style="width: 100%;" name="idProfil[]" multiple <?php if($_SESSION['profils_modification']==0){ echo 'disabled'; } ?>>
                                        <?php
                                        $query2 = $db->prepare('
                                            SELECT
                                                ao.*,
                                                (SELECT idPersonne FROM PROFILS_PERSONNES aop WHERE ao.idProfil = aop.idProfil AND aop.idPersonne = :idPersonne) as idPersonne
                                            FROM
                                                PROFILS ao
                                            ORDER BY
                                                libelleProfil;');
                                        $query2->execute(array('idPersonne' => $_GET['id']));

                                        while ($data2 = $query2->fetch())
                                        {
                                            
                                            echo '<option value=' . $data2['idProfil'];

                                            if (isset($data2['idPersonne']) AND $data2['idPersonne'])
                                            {
                                                echo " selected ";
                                            }
                                            echo '>' . $data2['libelleProfil'] . '</option>';
                                        }
                                        $query2->closeCursor();?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Dernière Connexion:</label>
                                    <input type="text" class="form-control" value="<?= isset($personne['derniereConnexion']) ? $personne['derniereConnexion'] : 'Aucune connexion'?>" disabled>
                                </div>
                            </div>
                            <?php if ($_SESSION['annuaire_modification']==1) {?>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary pull-right">Modifier</button>
                                </div>
                            <?php }?>
                        <?php if ($_SESSION['annuaire_modification']==1) {?></form><?php }?>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">Visualiser les paramètres personnels</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Rafraichissement automatique de la page d'accueil (secondes) :</label>
                                <input type="number" min="10" class="form-control" value="<?= $personne['conf_accueilRefresh'] ?>" name="conf_accueilRefresh" disabled>
                            </div>
                            <div class="form-group">
                                <label>Nombre de ligne par défaut dans les tableaux:</label>
                                <select class="form-control select2" style="width: 100%;" name="tableRowPerso" disabled>
                                    <option value="10" <?php if($personne['tableRowPerso']==10){echo 'selected';} ?>>10</option>
                                    <option value="25" <?php if($personne['tableRowPerso']==25){echo 'selected';} ?>>25</option>
                                    <option value="50" <?php if($personne['tableRowPerso']==50){echo 'selected';} ?>>50</option>
                                    <option value="75" <?php if($personne['tableRowPerso']==75){echo 'selected';} ?>>75</option>
                                    <option value="100" <?php if($personne['tableRowPerso']==100){echo 'selected';} ?>>100</option>
                                    <option value="-1" <?php if($personne['tableRowPerso']==-1){echo 'selected';} ?>>Tous</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <?php
                                    $lots                      = $personne['lots_lecture'] OR $personne['sac_lecture'] OR $personne['sac2_lecture'] OR $personne['materiel_lecture'];
                                    $reserves                  = $personne['reserve_lecture'];
                                    $vehicules                 = $personne['vehicules_lecture'];
                                    $desinfections             = $personne['desinfections_lecture'];
                                    $health                    = $personne['vehiculeHealth_lecture'];
                                    $tenues                    = $personne['tenues_lecture'] OR $personne['tenuesCatalogue_lecture'];
                                    $alertesBenevolesLots      = $personne['alertesBenevolesLots_lecture'];
                                    $alertesBenevolesVehicules = $personne['alertesBenevolesVehicules_lecture'];
                                ?>
                                <label>Présence des indicateurs sur la page d'accueil:</label><br/>
                                
                            	<?php if ($lots > 0){ if($personne['conf_indicateur1Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Matériels périmés (lots)
                            	<br/>
                            	<?php if ($lots > 0){ if($personne['conf_indicateur2Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Matériels manquants (lots)
                            	<br/>
                            	<?php if ($lots > 0){ if($personne['conf_indicateur3Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Lots en attente d'inventaire
                            	<br/>
                            	<?php if ($lots > 0){ if($personne['conf_indicateur4Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Lots non conformes
                            	<br/>
                            	<?php if ($reserves > 0){ if($personne['conf_indicateur5Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Matériels périmés (réserve)
                            	<br/>
                            	<?php if ($reserves > 0){ if($personne['conf_indicateur6Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Matériels manquants (réserve)
                            	<br/>
                            	<?php if ($vehicules > 0){ if($personne['conf_indicateur7Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Assurances véhicules
                            	<br/>
                                <?php if ($vehicules > 0){ if($personne['conf_indicateur8Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Contrôles techniques et révisions véhicules
                                <br/>
                                <?php if ($desinfections > 0){ if($personne['conf_indicateur11Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Désinfections véhicules
                            	<br/>
                                <?php if ($health > 0){ if($personne['conf_indicateur12Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Maintenance régulière des véhicules
                                <br/>
                            	<?php if ($tenues > 0){ if($personne['conf_indicateur9Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Stock des tenues
                            	<br/>
                            	<?php if ($tenues > 0){ if($personne['conf_indicateur10Accueil'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Non retour de tenues
                            </div>
                            <div class="form-group">
                                <?php
                                    $query = $db->prepare('SELECT p.*, h.notifications FROM PERSONNE_REFERENTE p JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne WHERE p.idPersonne = :idPersonne;');
                                    $query->execute(array('idPersonne' => $personne['idPersonne']));
                                    $data = $query->fetch();
                                ?>
                                <label>Abonnements aux notifications journalières par mail:</label><br/>
                                <?php if ($lots > 0 AND $data['notifications']==1){ if($data['notif_lots_manquants'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Matériels manquants (lots)
                                <br/>
                                <?php if ($lots > 0 AND $data['notifications']==1){ if($data['notif_lots_peremptions'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Matériels périmés (lots)
                                <br/>
                                <?php if ($lots > 0 AND $data['notifications']==1){ if($data['notif_lots_inventaires'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Inventaires (lots)
                                <br/>
                                <?php if ($lots > 0 AND $data['notifications']==1){ if($data['notif_lots_conformites'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Lots non conformes
                                <br/>
                                <?php if ($reserves > 0 AND $data['notifications']==1){ if($data['notif_reserves_manquants'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Matériels manquants (réserve)
                                <br/>
                                <?php if ($reserves > 0 AND $data['notifications']==1){ if($data['notif_reserves_peremptions'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Matériels périmés (réserve)
                                <br/>
                                <?php if ($reserves > 0 AND $data['notifications']==1){ if($data['notif_reserves_inventaires'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Inventaires (réserve)
                                <br/>
                                <?php if ($vehicules > 0 AND $data['notifications']==1){ if($data['notif_vehicules_assurances'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Assurances véhicules
                                <br/>
                                <?php if ($vehicules > 0 AND $data['notifications']==1){ if($data['notif_vehicules_revisions'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Révisions véhicules
                                <br/>
                                <?php if ($desinfections > 0 AND $data['notifications']==1){ if($data['notif_vehicules_desinfections'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Désinfections véhicules
                                <br/>
                                <?php if ($vehicules > 0 AND $data['notifications']==1){ if($data['notif_vehicules_ct'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Contrôles techniques véhicules
                                <br/>
                                <?php if ($health > 0 AND $data['notifications']==1){ if($data['notif_vehicules_health'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Maintenance régulière véhicules
                                <br/>
                                <?php if ($tenues > 0 AND $data['notifications']==1){ if($data['notif_tenues_stock'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Stock des tenues
                                <br/>
                                <?php if ($tenues > 0 AND $data['notifications']==1){ if($data['notif_tenues_retours'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Non retour de tenues
                            </div>
                            <div class="form-group">
                                <label>Notifications en temps réel sur alerte bénévole:</label><br/>
                                <?php if ($alertesBenevolesLots > 0 AND $data['notifications']==1){ if($data['notif_benevoles_lots'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Alerte remontée par un bénévole sur un lot opérationnel
                                <br/>
                                <?php if ($alertesBenevolesVehicules > 0 AND $data['notifications']==1){ if($data['notif_benevoles_vehicules'] == 1) { echo '<i class="fa fa-check"></i>'; }else{echo '<i class="fa fa-minus"></i>';}}else{echo '<i class="fa fa-close"></i>';} ?> Alerte remontée par un bénévole sur un véhicule
                            </div>
                        </div>
                        <div class="box-footer with-border">
                        	Légende: <i class="fa fa-check"></i> Activé | <i class="fa fa-minus"></i> Désactivé par l'utilisateur | <i class="fa fa-close"></i> Désactivé par le profil
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">Couleurs personnelles du calendrier</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
				                <label>Lots péremptions</label>
				                <input type="text" name="agenda_lots_peremption" value="<?=$data['agenda_lots_peremption']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Réserves péremptions</label>
				                <input type="text" name="agenda_reserves_peremption" value="<?=$data['agenda_reserves_peremption']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Lots inventaires à faire</label>
				                <input type="text" name="agenda_lots_inventaireAF" value="<?=$data['agenda_lots_inventaireAF']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Lots inventaires faits</label>
				                <input type="text" name="agenda_lots_inventaireF" value="<?=$data['agenda_lots_inventaireF']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Commandes Livraisons</label>
				                <input type="text" name="agenda_commandes_livraison" value="<?=$data['agenda_commandes_livraison']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Véhicules Révisions</label>
				                <input type="text" name="agenda_vehicules_revision" value="<?=$data['agenda_vehicules_revision']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Véhicules CT</label>
				                <input type="text" name="agenda_vehicules_ct" value="<?=$data['agenda_vehicules_ct']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Véhicules assurance</label>
				                <input type="text" name="agenda_vehicules_assurance" value="<?=$data['agenda_vehicules_assurance']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Véhicules taches de maintenance ponctuelle</label>
				                <input type="text" name="agenda_vehicules_maintenance" value="<?=$data['agenda_vehicules_maintenance']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Véhicules taches de maintenance régulière faite</label>
				                <input type="text" name="agenda_healthF" value="<?=$data['agenda_healthF']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Véhicules taches de maintenance régulière à faire</label>
				                <input type="text" name="agenda_healthAF" value="<?=$data['agenda_healthAF']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Vehicules désinfections faites</label>
				                <input type="text" name="agenda_desinfections_desinfectionF" value="<?=$data['agenda_desinfections_desinfectionF']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Vehicules désinfections à faire</label>
				                <input type="text" name="agenda_desinfections_desinfectionAF" value="<?=$data['agenda_desinfections_desinfectionAF']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Réserves inventaires à faire</label>
				                <input type="text" name="agenda_reserves_inventaireAF" value="<?=$data['agenda_reserves_inventaireAF']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Réserves inventaires faits</label>
				                <input type="text" name="agenda_reserves_inventaireF" value="<?=$data['agenda_reserves_inventaireF']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>Tenues</label>
				                <input type="text" name="agenda_tenues_tenues" value="<?=$data['agenda_tenues_tenues']?>" class="form-control my-colorpicker1" disabled>
				            </div>
				            <div class="form-group">
				                <label>ToDoList</label>
				                <input type="text" name="agenda_tenues_toDoList" value="<?=$data['agenda_tenues_toDoList']?>" class="form-control my-colorpicker1" disabled>
				            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <i class="fa fa-users"></i>
                            <h3 class="box-title">Bilan des habilitations de l'utilisateur</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Connexion à <?php echo $APPNAME;?>:</label>
                                </br>
                                <?php if($personne['connexion_connexion'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Autorisé à se connecter à <?php echo $APPNAME;?>
                                </br>
                            </div>
                            <div class="form-group">
                                <label>Administration de <?php echo $APPNAME;?>:</label>
                                </br>
                                <?php if($personne['appli_conf'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Modifier la configuration générale de <?php echo $APPNAME;?>
                                </br>
                                <?php if($personne['annuaire_mdp'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Réinitialiser les mots de passe des autres utilisateurs
                                </br>
                                <?php if($personne['delegation'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Se connecter entant qu'autre utilisateur
                                </br>
                                <?php if($personne['maintenance'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Se connecter même en mode maitenance
                                </br>
                                <?php if($personne['verrouIP'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Gérer les adresses IP bloquées
                                </br>
                                <?php if($personne['actionsMassives'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Mener des actions massives directement en base
                                </br>
                            </div>
                            <div class="form-group">
                                <label>Notifications journalières par mail:</label>
                                </br>
                                <?php if($personne['notifications'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Autorisé à recevoir les notifications journalières par mail
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
                                    <td><?php if($personne['lots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['lots_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['lots_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['lots_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Sacs</td>
                                    <td><?php if($personne['sac_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['sac_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['sac_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['sac_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Emplacements</td>
                                    <td><?php if($personne['sac2_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['sac2_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['sac2_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['sac2_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Matériels/Consommables</td>
                                    <td><?php if($personne['materiel_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['materiel_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['materiel_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['materiel_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                                    <td><?php if($personne['vhf_canal_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_canal_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_canal_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_canal_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Plans de fréquences</td>
                                    <td><?php if($personne['vhf_plan_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_plan_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_plan_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_plan_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Equipements de transmission</td>
                                    <td><?php if($personne['vhf_equipement_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_equipement_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_equipement_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vhf_equipement_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                                    <td><?php if($personne['vehicules_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehicules_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehicules_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehicules_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Désinfections</td>
                                    <td><?php if($personne['desinfections_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['desinfections_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['desinfections_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['desinfections_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Taches de maintenance</td>
                                    <td><?php if($personne['vehiculeHealth_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehiculeHealth_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehiculeHealth_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehiculeHealth_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                                    <td><?php if($personne['tenues_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['tenues_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['tenues_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['tenues_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Catalogue des tenues</td>
                                    <td><?php if($personne['tenuesCatalogue_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['tenuesCatalogue_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['tenuesCatalogue_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['tenuesCatalogue_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Cautions</td>
                                    <td><?php if($personne['cautions_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['cautions_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['cautions_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['cautions_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                                    <td><?php if($personne['categories_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['categories_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['categories_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['categories_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Codes Barre</td>
                                    <td><?php if($personne['codeBarre_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['codeBarre_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['codeBarre_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['codeBarre_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Fournisseurs</td>
                                    <td><?php if($personne['fournisseurs_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['fournisseurs_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['fournisseurs_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['fournisseurs_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Référentiels</td>
                                    <td><?php if($personne['typesLots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['typesLots_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['typesLots_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['typesLots_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Lieux</td>
                                    <td><?php if($personne['lieux_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['lieux_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['lieux_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['lieux_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Catalogue</td>
                                    <td><?php if($personne['catalogue_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['catalogue_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['catalogue_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['catalogue_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Types de véhicules</td>
                                    <td><?php if($personne['vehicules_types_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehicules_types_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehicules_types_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehicules_types_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Types de désinfections</td>
                                    <td><?php if($personne['typesDesinfections_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['typesDesinfections_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['typesDesinfections_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['typesDesinfections_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Types de taches de maintenance</td>
                                    <td><?php if($personne['vehiculeHealthType_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehiculeHealthType_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehiculeHealthType_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['vehiculeHealthType_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Carburants</td>
                                    <td><?php if($personne['carburants_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['carburants_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['carburants_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['carburants_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Etats</td>
                                    <td><?php if($personne['etats_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['etats_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['etats_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['etats_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table table-bordered">
                                <tr>
                                    <th>ALERTES BENEVOLES</th>
                                    <th>Lecture</th>
                                    <th>Etre en charge</th>
                                    <th>Affecter à un tier</th>
                                </tr>
                                <tr>
                                    <td>Lots</td>
                                    <td><?php if($personne['alertesBenevolesLots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['alertesBenevolesLots_affectation'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['alertesBenevolesLots_affectationTier'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Véhicules</td>
                                    <td><?php if($personne['alertesBenevolesVehicules_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['alertesBenevolesVehicules_affectation'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['alertesBenevolesVehicules_affectationTier'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table table-bordered">
                                <tr>
                                    <th>COMMANDES</th>
                                    <th>Lecture</th>
                                    <th>Ajout Modification</th>
                                    <th>Valideur universel</th>
                                    <th>Etre en charge</th>
                                    <th>Abandonner Supprimer</th>
                                </tr>
                                <tr>
                                    <td>Commandes</td>
                                    <td><?php if($personne['commande_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['commande_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['commande_valider_delegate'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['commande_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['commande_abandonner'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Centres de coûts</td>
                                    <td><?php if($personne['cout_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['cout_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                    <td><?php if($personne['cout_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['cout_supprimer'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                                    <td><?php if($personne['reserve_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['reserve_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['reserve_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['reserve_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['reserve_cmdVersReserve'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['reserve_ReserveVersLot'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                                    <td><?php if($personne['annuaire_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['annuaire_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['annuaire_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                    <td><?php if($personne['annuaire_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Profils</td>
                                    <td><?php if($personne['profils_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['profils_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['profils_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                    <td><?php if($personne['profils_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Messages généraux</td>
                                    <td></td>
                                    <td><?php if($personne['messages_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                    <td></td>
                                    <td><?php if($personne['messages_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                </tr>
                                <tr>
                                    <td>Messages mails</td>
                                    <td></td>
                                    <td><?php if($personne['contactMailGroupe'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>ToDoList</td>
                                    <td><?php if($personne['todolist_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                    <td><?php if($personne['todolist_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td><?php if($personne['todolist_perso'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        <div class="box-footer with-border">
                        	Légende: <i class="fa fa-check"></i> Activé | <i class="fa fa-minus"></i> Désactivé par l'utilisateur | <i class="fa fa-close"></i> Désactivé par le profil
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <i class="fa fa-euro"></i>
                            <h3 class="box-title">Habilitations sur les centres de coûts</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Libelle</th>
                                        <th>Droits</th>
                                        <th>Validation de commandes</th>
                                        <th>Droits étendus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query2 = $db->prepare('SELECT * FROM CENTRE_COUTS c LEFT OUTER JOIN CENTRE_COUTS_PERSONNES p ON c.idCentreDeCout = p.idCentreDeCout WHERE p.idPersonne = :idPersonne ORDER BY libelleCentreDecout DESC');
                                $query2->execute(array('idPersonne'=>$data['idPersonne']));
                                while ($data2 = $query2->fetch())
                                {?>
                                    <tr>
                                        <td><?php echo $data2['idCentreDeCout']; ?></td>
                                        <td><?php echo $data2['libelleCentreDecout']; ?></td>
                                        <td>
                                            <?php
                                                if(centreCoutsEstCharge($data['idPersonne'],$data2['idCentreDeCout'])==1)
                                                {
                                                    echo '<span class="badge bg-green">Actif</span>';
                                                }
                                                else
                                                {
                                                    echo '<span class="badge bg-yellow">Inactif</span>';
                                                }
                                            ?>
                                        </td>
                                        <td><?php if($data2['montantMaxValidation']!=Null AND $data2['montantMaxValidation']>=0){echo $data2['montantMaxValidation'].' €';}else{echo '<span class="badge bg-yellow">Illimité</span>';}?></td>
                                        <td><?php if($data2['depasseBudget']){echo '<span class="badge bg-yellow">Dépassement de budget autorisé</span><br/>';}?><?php if($data2['validerClos']){echo '<span class="badge bg-yellow">Opérer sur le centre clos</span>';}?></td>
                                    </tr>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                                </tbody>


                            </table>
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
