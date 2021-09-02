<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 505;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['appli_conf']==0)
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
                Configuration générale de <?php echo $APPNAME; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Configuration générale</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <!-- general form elements disabled -->
            <div class="box box-info">
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="appliConfUpdate.php" method="POST">
                        <?php
                        $query = $db->query('SELECT * FROM CONFIG;');
                        $data = $query->fetch();
                        ?>

                        <div class="form-group">
                            <label>Nom de l'application:</label>
                            <input type="text" class="form-control" value="<?=$data['appname']?>" name="appname" required>
                        </div>
                        <div class="form-group">
                            <label>Couleur du site:</label>
                            <select class="form-control select2" style="width: 100%;" name="sitecolor">
                                <option value="blue" <?php if($data['sitecolor']=='blue'){echo 'selected';}?>>Bleu</option>
                                <option value="black" <?php if($data['sitecolor']=='black'){echo 'selected';}?>>Noir</option>
                                <option value="purple" <?php if($data['sitecolor']=='purple'){echo 'selected';}?>>Violet</option>
                                <option value="green" <?php if($data['sitecolor']=='green'){echo 'selected';}?>>Vert</option>
                                <option value="red" <?php if($data['sitecolor']=='red'){echo 'selected';}?>>Rouge</option>
                                <option value="yellow" <?php if($data['sitecolor']=='yellow'){echo 'selected';}?>>Orange</option>
                                <option value="blue-light" <?php if($data['sitecolor']=='blue-light'){echo 'selected';}?>>Bleu clair</option>
                                <option value="black-light" <?php if($data['sitecolor']=='black-light'){echo 'selected';}?>>Noir clair</option>
                                <option value="purple-light" <?php if($data['sitecolor']=='purple-light'){echo 'selected';}?>>Violet clair</option>
                                <option value="green-light" <?php if($data['sitecolor']=='green-light'){echo 'selected';}?>>Vert clair</option>
                                <option value="red-light" <?php if($data['sitecolor']=='red-light'){echo 'selected';}?>>Rouge clair</option>
                                <option value="yellow-light" <?php if($data['sitecolor']=='yellow-light'){echo 'selected';}?>>Orange clair</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>URL du site:</label>
                            <input type="text" class="form-control" value="<?=$data['urlsite']?>" name="urlsite" required>
                        </div>
                        <div class="form-group">
                            <label>Adresse mail expéditrice des notifications:</label>
                            <input type="text" class="form-control" value="<?=$data['mailserver']?>" name="mailserver" required>
                            <input type="checkbox" value="1" name="mailcopy" <?php if($data['mailcopy']==1){echo "checked";}?>> Cette adresse doit être copiste de tous les mails envoyés par la plateforme.
                        </div>
                        <div class="form-group">
                            <label>Temps avant déconnexion d'un utilisateur inactif (minutes):</label>
                            <input type="number" class="form-control" value="<?=$data['logouttemp']?>" min="1" name="logouttemp" required>
                        </div>
						<div class="form-group">
	                        <label>Mot de sécurité sur les confirmations de suppression: <small style="color:grey;">Requis</small></label>
	                        <input type="text" class="form-control" name="confirmationSuppression" value="<?= $data['confirmationSuppression'] ?>" required>
	                    </div>
                        <div class="form-group">
                            <label>Mots de passe oubliés:</label>
                            <br/>
                            <input type="checkbox" value="1" name="resetPassword" <?php if($data['resetPassword']==1){echo "checked";}?>> Les utilisateurs peuvent réinitialiser leur mot de passe oublié par mail.
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->

            </div>

        </section>
        <!-- /.content -->

        <section class="content-header">
            <h1>
                Gestion des sels de mots de passe
            </h1>
        </section>

        <section class="content">
            <div class="box box-warning">
                <!-- /.box-header -->
                <div class="box-body">
                    <?php
                        if($RESETPASSWORD==0)
                        {
                            echo '<div class="alert alert-danger">';
                            echo '<i class="icon fa fa-warning"></i> La modification des sels de mots de passe va invalider tous les mots de passe actuels. La fonction de réinitialiation par email des mots de passe étant désactivée, les utilisateurs NE POURRONT PLUS ACCEDER A LEUR COMPTE !';
                            echo '</div>';
                        }
                        else
                        {
                            echo '<div class="alert alert-warning">';
                            echo '<i class="icon fa fa-warning"></i> La modification des sels de mots de passe va invalider tous les mots de passe actuels. Les utilisateurs devront passer par la fonctionnalité de réinitialisation de mot de passe par mail afin de pouvoir se reconnecter.';
                            echo '</div>';
                        }
                    ?>
                    <form role="form" action="appliConfUpdateSel.php" method="POST">
                        <div class="form-group">
                            <label>Sel de pré-hash:</label>
                            <input type="text" class="form-control" value="<?=$data['selPre']?>" name="selPre">
                        </div>
                        <div class="form-group">
                            <label>Sel de post-hash:</label>
                            <input type="text" class="form-control" value="<?=$data['selPost']?>" name="selPost">
                        </div>
                        <div class="form-group">
                            <label>Mot de passe de la session actuelle:</label>
                            <input type="password" class="form-control" name="pwd">
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->

            </div>

        </section>

        <section class="content-header">
            <h1>
                Gestion des notifications mail envoyées par <?php echo $APPNAME; ?> lors du processus de commandes
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box box-info">
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="appliConfUpdateNotif.php" method="POST">
                        <?php
                        $query = $db->query('SELECT * FROM CONFIG;');
                        $data = $query->fetch();
                        ?>
                        <table class="table table-bordered">
                            <tr>
                                <th></th>
                                <th>Demandeur</th>
                                <th>Valideur</th>
                                <th>Gérant</th>
                                <th>Observateur</th>
                            </tr>
                            <tr>
                                <td>La demande de validation est faite</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_validation'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_validation"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_validation'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_validation"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_validation'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_validation"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_validation'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_validation"></td>
                            </tr>
                            <tr>
                                <td>La validation est positive</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_validationOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_validationOK"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_validationOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_validationOK"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_validationOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_validationOK"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_validationOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_validationOK"></td>
                            </tr>
                            <tr>
                                <td>La validation est négative</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_validationNOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_validationNOK"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_validationNOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_validationNOK"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_validationNOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_validationNOK"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_validationNOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_validationNOK"></td>
                            </tr>
                            <tr>
                                <td>La commande est passée chez le fournisseur</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_passee'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_passee"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_passee'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_passee"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_passee'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_passee"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_passee'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_passee"></td>
                            </tr>
                            <tr>
                                <td>La commande est receptionnée sans SAV</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_livraisonOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_livraisonOK"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_livraisonOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_livraisonOK"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_livraisonOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_livraisonOK"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_livraisonOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_livraisonOK"></td>
                            </tr>
                            <tr>
                                <td>La commande est receptionnée avec SAV</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_livraisonNOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_livraisonNOK"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_livraisonNOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_livraisonNOK"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_livraisonNOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_livraisonNOK"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_livraisonNOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_livraisonNOK"></td>
                            </tr>
                            <tr>
                                <td>Le SAV est clos</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_savOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_savOK"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_savOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_savOK"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_savOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_savOK"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_savOK'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_savOK"></td>
                            </tr>
                            <tr>
                                <td>La commande est close</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_cloture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_cloture"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_cloture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_cloture"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_cloture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_cloture"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_cloture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_cloture"></td>
                            </tr>
                            <tr>
                                <td>La commande est abandonnée</td>
                                <td><input <?php if($data['notifications_commandes_demandeur_abandon'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_demandeur_abandon"></td>
                                <td><input <?php if($data['notifications_commandes_valideur_abandon'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_abandon"></td>
                                <td><input <?php if($data['notifications_commandes_affectee_abandon'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_affectee_abandon"></td>
                                <td><input <?php if($data['notifications_commandes_observateur_abandon'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_observateur_abandon"></td>
                            </tr>

                        </table>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->

            </div>

        </section>
        
        <section class="content-header">
            <h1>
                Valideurs ajoutés par défaut lors de la création d'une commande
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box box-info">
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="appliConfUpdateValideurs.php" method="POST">
                        <?php
                        $query = $db->query('SELECT * FROM COMMANDES_VALIDEURS_DEFAULT;');
                        ?>
                        
						<select class="form-control select2" style="width: 100%;" name="idPersonne[]" multiple>
                            <?php
                                $query2 = $db->query('
                                	SELECT
                                		p.idPersonne,
                                		p.identifiant,
                                		v.idPersonne as selectionnee
                                	FROM
                                		PERSONNE_REFERENTE p
                                		LEFT OUTER JOIN COMMANDES_VALIDEURS_DEFAULT v ON p.idPersonne = v.idPersonne
                                		LEFT OUTER JOIN VIEW_HABILITATIONS h ON p.idPersonne = h.idPersonne
                                	WHERE
                                		commande_lecture = 1
                                		AND commande_valider = 1
                                	ORDER BY
                                		identifiant;');
                                while ($data2 = $query2->fetch())
                                {
                                    
                                    echo '<option value=' . $data2['idPersonne'];

					                if (isset($data2['selectionnee']) AND $data2['selectionnee'] != Null)
					                {
					                    echo " selected ";
					                }
					                echo '>' . $data2['identifiant'] . '</option>';
                                }
                            
                            ?>
                        </select>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->

            </div>

        </section>
        
        <section class="content-header">
            <h1>
                Délais de notification des véhicules
            </h1>
        </section>
        
        <section class="content">
            <div class="box box-warning">
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="appliConfUpdateVehicules.php" method="POST">
                        <div class="form-group">
                            <label>Jours d'anticipation des CT:</label>
                            <input type="number" min="0" class="form-control" value="<?=$data['vehicules_ct_delais_notif']?>" name="vehicules_ct_delais_notif" required>
                        </div>
                        <div class="form-group">
                            <label>Jours d'anticipation des révisions:</label>
                            <input type="number" min="0" class="form-control" value="<?=$data['vehicules_revision_delais_notif']?>" name="vehicules_revision_delais_notif" required>
                        </div>
						<div class="form-group">
                            <label>Jours d'anticipation des péremptions d'assurance:</label>
                            <input type="number" min="0" class="form-control" value="<?=$data['vehicules_assurance_delais_notif']?>" name="vehicules_assurance_delais_notif" required>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Modifier</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->

            </div>

        </section>

        <section class="content-header">
            <h1>
                Gestion du mode maintenance
            </h1>
        </section>

        <section class="content">
            <!-- general form elements disabled -->
            <div class="box box-warning">
                <!-- /.box-header -->
                <div class="box-body">
                    <a data-toggle="modal" class="btn btn-sm btn-danger" <?php if ($MAINTENANCE==0){echo 'data-target="#activateSorryPage"';}else{echo 'disabled';} ?>>Activer le mode maintenance</a>
                    <a data-toggle="modal" class="btn btn-sm btn-success" <?php if ($MAINTENANCE==1){echo 'data-target="#disactivateSorryPage"';}else{echo 'disabled';} ?>>Désactiver le mode maintenance</a>
                </div>
                <!-- /.box-body -->

            </div>

        </section>

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



<div class="modal fade modal-danger" id="activateSorryPage">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Activation du mode maintenance</h4>
            </div>
            <div class="modal-body">
                Attention, cette action va activer le mode maintenance: seuls les utilsateurs bénéficiant d'un profil adéquat pourront contiuer à se connecter.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <a href="appliConfSorryPageON.php"><button type="button" class="btn btn-default pull-right">Activer le mode maintenance</button></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-danger" id="disactivateSorryPage">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Désactivation du mode maintenance</h4>
            </div>
            <div class="modal-body">
                Attention, cette action va désactiver le mode maintenance, le site redeviendra disponible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <a href="appliConfSorryPageOFF.php"><button type="button" class="btn btn-default pull-right">Désactiver le mode maintenance</button></a>
            </div>
        </div>
    </div>
</div>


</html>
