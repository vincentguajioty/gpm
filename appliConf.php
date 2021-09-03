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
                Configuration
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

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#general" data-toggle="tab"><i class="fa fa-wrench"></i> Générale</a></li>
                    <li><a href="#ip" data-toggle="tab"><i class="fa fa-shield"></i> Verrouillage IP</a></li>
                    <li><a href="#captcha" data-toggle="tab"><i class="fa fa-user-secret"></i> reCaptcha V3</a></li>
                    <li><a href="#aes" data-toggle="tab"><i class="fa fa-shopping-cart"></i> Cryptage AES</a></li>
                    <li><a href="#sel" data-toggle="tab"><i class="fa fa-key"></i> Sels utilisateurs</a></li>
                    <li><a href="#notifCMD" data-toggle="tab"><i class="fa fa-envelope"></i> Notifications commandes</a></li>
                    <li><a href="#notifVEH" data-toggle="tab"><i class="fa fa-ambulance"></i> Véhicules</a></li>
                    <li><a href="#alertesBen" data-toggle="tab"><i class="fa fa-comment"></i> Alertes bénévoles</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="general">
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
                                <input type="checkbox" value="1" name="resetPassword" <?php if($data['resetPassword']==1){echo "checked";}?>> Les utilisateurs peuvent réinitialiser leur mot de passe oublié par mail. <?php if($RECAPTCHA_ENABLE != 1){ echo '<b>Afin de garantir une meilleure sécurité de la plateforme, il est recommandé d\'activer la fonctionnalité reCaptcha.</b>';}?>
                            </div>
                            <div class="box-footer">
                                <a data-toggle="modal" class="btn btn-sm btn-danger" <?php if ($MAINTENANCE==0){echo 'data-target="#activateSorryPage"';}else{echo 'disabled';} ?>>Activer le mode maintenance</a>
                                <a data-toggle="modal" class="btn btn-sm btn-success" <?php if ($MAINTENANCE==1){echo 'data-target="#disactivateSorryPage"';}else{echo 'disabled';} ?>>Désactiver le mode maintenance</a>
                                <button type="submit" class="btn btn-info pull-right">Modifier</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="tab-pane" id="ip">
                        <?php
							if($RECAPTCHA_ENABLE != 1)
							{
	                			echo '<div class="alert alert-warning">';
	                            echo '<i class="icon fa fa-warning"></i> Afin de garantir une meilleure sécurité de la plateforme, il est recommandé d\'activer la fonctionnalité reCaptcha.';
	                            echo '</div>';
							}
                        ?>
                        <form role="form" action="appliConfUpdateVerrIP.php" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bloquer à partir de X erreurs de mots de passe:</label>
                                        <input type="number" min="1" class="form-control" value="<?=$data['verrouillage_ip_occurances']?>" name="verrouillage_ip_occurances" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>En Y jours:</label>
                                        <input type="number" min="1" class="form-control" value="<?=$data['verrouillage_ip_temps']?>" name="verrouillage_ip_temps" required>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">Modifier</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane" id="captcha">
                        <form role="form" action="appliConfUpdateCaptcha.php" method="POST">
                            <div class="form-group">
                                <label>Activation:</label>
                                <br/>
                                <input type="checkbox" value="1" name="reCaptcha_enable" <?php if($data['reCaptcha_enable']==1){echo "checked";}?>> Activation du contrôle par reCaptcha V3 sur la page d'authentification, sur le module de réinitialisation du mot de passe, sur le module d'alerte bénévoles
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Site Key:</label>
                                        <input type="text" class="form-control" value="<?=$data['reCaptcha_siteKey']?>" name="reCaptcha_siteKey">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Secret Key:</label>
                                        <input type="text"class="form-control" value="<?=$data['reCaptcha_secretKey']?>" name="reCaptcha_secretKey">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Score minimum:</label>
                                        <input type="number" min="0" max="1" step="0.1" class="form-control" value="<?=$data['reCaptcha_scoreMin']?>" name="reCaptcha_scoreMin">
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">Modifier</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane" id="aes">
                        <?php
                            if($AESFOUR)
                            {
                                if(!isset($_SESSION['aesFour']))
                                {?>
                                    <a href="fournisseursAESgetPWD.php" class="btn btn-sm btn-info modal-form">Accéder à la gestion de la clef</a>
                                <?php
                                }
                                else
                                { ?>
                                    <div class="alert alert-warning">
                                        <i class="icon fa fa-warning"></i> Avant toute manipulation, il est très fortement recommandé <b>D'ACTIVER LE MODE DE MAINTENANCE</b> et de s'assurer que <b>PLUS PERSONNE N'EST CONNECTE SUR LA PLATEFORME</b>.
                                    </div>
                                    <a href="appliConfAESUpdateForm.php" class="btn btn-sm btn-warning modal-form">Modifier la clef et ré-encoder toutes les données</a>
                                    <a href="modalDeleteConfirm.php?case=appliConfAESDrop" class="btn btn-sm btn-danger modal-form">Désactiver la fonctionnalité et supprimer les données</a>
                                    <a href="fournisseursAESlock.php" class="btn btn-sm btn-info pull-right">Quitter le mode édition des données chiffrées</a>
                                <?php }
                            }
                            else
                            {?>
                                <div class="alert alert-info">
                                    <i class="icon fa fa-info"></i> La fonctionnalité n'a jamais été utilisée. Veuillez d'abord saisir la clef de chiffrement ci-dessous afin de chiffrer la valeur témoin et activer la fonctionnalité.
                                </div>
                                <form role="form" action="appliConfAESInitiate.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Clef de cryptage souhaitée</label>
                                                <input type="text" class="form-control" name="key1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Confirmation de la clef</label>
                                                <input type="text" class="form-control" name="key2" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <div class="alert alert-warning">
                                            <i class="icon fa fa-warning"></i> Votre clef de chiffrement n'est pas stockée dans la base de données. Il est <b>IMPOSSIBLE</b> de la retrouver en cas de perte ! Prenez en soin ! En cas de perte, les données chiffrées ne pourront <b>JAMAIS</b> être récupérées.
                                        </div>
                                        <button type="submit" class="btn btn-info pull-right">Initialiser la clef</button>
                                    </div>
                                </form>
                                <?php
                            }
                        ?>
                    </div>

                    <div class="tab-pane" id="sel">
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

                    <div class="tab-pane" id="notifCMD">
                        <form role="form" action="appliConfUpdateNotif.php" method="POST">
                            <?php
                            $query = $db->query('SELECT * FROM CONFIG;');
                            $data = $query->fetch();
                            ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th></th>
                                    <th>Demandeur</th>
                                    <th>Valideur<br/>(Responsable du centre de couts)</th>
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
                                    <td>La commande est prête à être intégrée au centre de couts</td>
                                    <td></td>
                                    <td><input <?php if($data['notifications_commandes_valideur_centreCout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="notifications_commandes_valideur_centreCout"></td>
                                    <td></td>
                                    <td></td>
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

                    <div class="tab-pane" id="notifVEH">
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

                    <div class="tab-pane" id="alertesBen">
						<?php
							if($RECAPTCHA_ENABLE != 1)
							{
	                			echo '<div class="alert alert-warning">';
	                            echo '<i class="icon fa fa-warning"></i> Attention, l\'ouverture de cette fonctionnalité sans avoir activé reCaptcha expose la plateforme à attaques par robot.';
	                            echo '</div>';
							}
                        ?>
                        <form role="form" action="appliConfUpdateAlertesBen.php" method="POST">
                            <div class="form-group">
                                <label>Lots:</label>
                                <br/>
                                <input type="checkbox" value="1" name="alertes_benevoles_lots" <?php if($data['alertes_benevoles_lots']==1){echo "checked";}?>> Les utilisateurs non-authentifiés peuvent remonter des alertes sur les lots opérationnels.
                            </div>
                            <div class="form-group">
                                <label>Véhicules:</label>
                                <br/>
                                <input type="checkbox" value="1" name="alertes_benevoles_vehicules" <?php if($data['alertes_benevoles_vehicules']==1){echo "checked";}?>> Les utilisateurs non-authentifiés peuvent remonter des alertes sur les véchicules.
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">Modifier</button>
                            </div>
                        </form>
                    </div>

                </div>
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
