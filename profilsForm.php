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
            <!-- general form elements disabled -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un profil</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="<?= isset($_GET['id']) ? 'profilsUpdate.php?id='.$_GET['id'] : 'profilsAdd.php'?>" method="POST">
                        <?php
                        $query = $db->prepare('SELECT * FROM PROFILS WHERE idProfil=:idProfil;');
                        $query->execute(array('idProfil' => $_GET['id']));
                        $data = $query->fetch();
                        ?>

                        <div class="form-group">
                            <label>Libellé: <small style="color:grey;">Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($_GET['id']) ? $data['libelleProfil'] : ''?>" name="libelleProfil" required>
                        </div>
                        <div class="form-group">
                            <label>Description:</label>
                            <textarea class="form-control" rows="3" name="descriptifProfil"><?= isset($_GET['id']) ? $data['descriptifProfil'] : '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Connexion à <?php echo $APPNAME;?>:</label>
                            </br>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="connexion_connexion" <?php if (isset($_GET['id']) AND ($data['connexion_connexion']==1)) {echo 'checked';} ?>> Autorisé à se connecter à <?php echo $APPNAME;?>
                                </label>
                            </div>
                            </br>
                        </div>
                        <div class="form-group">
                            <label>Administration de <?php echo $APPNAME;?>:</label>
                            </br>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="appli_conf" <?php if (isset($_GET['id']) AND ($data['appli_conf']==1)) {echo 'checked';} ?>> Modifier la configuration générale de <?php echo $APPNAME;?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="annuaire_mdp" <?php if (isset($_GET['id']) AND ($data['annuaire_mdp']==1)) {echo 'checked';} ?>> Réinitialiser les mots de passe des autres utilisateurs
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="verrouIP" <?php if(isset($_GET['id']) AND $data['verrouIP'] == 1) { echo 'checked'; } ?>> Gérer les adresses IP bloquées
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="logs_lecture" <?php if(isset($_GET['id']) AND $data['logs_lecture'] == 1) { echo 'checked'; } ?>> Lire les logs
                                </label>
                            </div>
                            </br>
                        </div>
                        <div class="form-group">
                            <label>Notifications:</label>
                            </br>
                            <div class="notifications">
                                <input type="radio" name="notifications" id="optionsRadios1" value="0" <?php
                                if (isset($_GET['id']) AND $data['notifications']==0)
                                {
                                    echo "checked";
                                }
                                ?>>
                                Notifications mail désactivées
                            </div>
                            <div class="notifications">
                                <input type="radio" name="notifications" id="optionsRadios2" value="1" <?php
                                if (isset($_GET['id']) AND $data['notifications']==1)
                                {
                                    echo "checked";
                                }
                                ?>>
                                Notifications mail uniquement sur alerte
                            </div>
                            <div class="notifications">
                                <input type="radio" name="notifications" id="optionsRadios3" value="2" <?php
                                if (isset($_GET['id']) AND $data['notifications']==2)
                                {
                                    echo "checked";
                                }
                                ?>>
                                Notifications mail journalières
                            </div>
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
                                <td><input <?php if(isset($_GET['id']) AND $data['lots_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lots_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['lots_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lots_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['lots_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lots_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['lots_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lots_suppression"></td>
                            </tr>
                            <tr>
                                <td>Sacs</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['sac_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['sac_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['sac_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['sac_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac_suppression"></td>
                            </tr>
                            <tr>
                                <td>Emplacements</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['sac2_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac2_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['sac2_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac2_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['sac2_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac2_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['sac2_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="sac2_suppression"></td>
                            </tr>
                            <tr>
                                <td>Matériels/Consommables</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['materiel_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="materiel_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['materiel_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="materiel_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['materiel_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="materiel_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['materiel_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="materiel_suppression"></td>
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
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_canal_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_canal_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_canal_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_canal_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_canal_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_canal_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_canal_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_canal_suppression"></td>
                            </tr>
                            <tr>
                                <td>Plans de fréquences</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_plan_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_plan_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_plan_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_plan_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_plan_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_plan_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_plan_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_plan_suppression"></td>
                            </tr>
                            <tr>
                                <td>Equipements de transmission</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_equipement_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_equipement_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_equipement_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_equipement_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_equipement_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_equipement_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['vhf_equipement_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="vhf_equipement_suppression"></td>
                            </tr>
                            <tr>
                                <th>ANNUAIRE</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>Annuaire</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['annuaire_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="annuaire_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['annuaire_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="annuaire_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['annuaire_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="annuaire_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['annuaire_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="annuaire_suppression"></td>
                            </tr>
                            <tr>
                                <td>Profils</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['profils_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="profils_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['profils_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="profils_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['profils_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="profils_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['profils_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="profils_suppression"></td>
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
                                <td><input <?php if(isset($_GET['id']) AND $data['categories_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="categories_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['categories_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="categories_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['categories_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="categories_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['categories_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="categories_suppression"></td>
                            </tr>
                            <tr>
                                <td>Fournisseurs</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['fournisseurs_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="fournisseurs_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['fournisseurs_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="fournisseurs_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['fournisseurs_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="fournisseurs_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['fournisseurs_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="fournisseurs_suppression"></td>
                            </tr>
                            <tr>
                                <td>Référentiels</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['typesLots_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="typesLots_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['typesLots_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="typesLots_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['typesLots_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="typesLots_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['typesLots_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="typesLots_suppression"></td>
                            </tr>
                            <tr>
                                <td>Lieux</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['lieux_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lieux_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['lieux_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lieux_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['lieux_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lieux_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['lieux_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="lieux_suppression"></td>
                            </tr>
                            <tr>
                                <td>Catalogue</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['catalogue_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="catalogue_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['catalogue_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="catalogue_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['catalogue_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="catalogue_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['catalogue_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="catalogue_suppression"></td>
                            </tr>
                            <tr>
                                <td>Messages généraux</td>
                                <td></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['messages_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="messages_ajout"></td>
                                <td></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['messages_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="messages_suppression"></td>
                            </tr>
                        </table>
                        <table class="table table-bordered">
                            <tr>
                                <th>COMMANDES</th>
                                <th>Lecture</th>
                                <th>Ajout/Modification</th>
                                <th>Valider</th>
                                <th>Etre en charge</th>
                                <th>Abandonner/Supprimer</th>
                            </tr>
                            <tr>
                                <td>Commandes</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['commande_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['commande_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['commande_valider'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_valider"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['commande_etreEnCharge'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_etreEnCharge"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['commande_abandonner'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="commande_abandonner"></td>
                            </tr>
                            <tr>
                                <td>Centres de coûts</td>
                                <td><input <?php if(isset($_GET['id']) AND $data['cout_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="cout_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['cout_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="cout_ajout"></td>
                                <td></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['cout_etreEnCharge'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="cout_etreEnCharge"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['cout_supprimer'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="cout_supprimer"></td>
                            </tr>
                        </table>
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
                                <td><input <?php if(isset($_GET['id']) AND $data['reserve_lecture'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="reserve_lecture"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['reserve_ajout'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="reserve_ajout"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['reserve_modification'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="reserve_modification"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['reserve_suppression'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="reserve_suppression"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['reserve_cmdVersReserve'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="reserve_cmdVersReserve"></td>
                                <td><input <?php if(isset($_GET['id']) AND $data['reserve_ReserveVersLot'] == 1) { echo 'checked'; } ?> type="checkbox" value="1" name="reserve_ReserveVersLot"></td>
                            </tr>
                        </table>
                        <div class="box-footer">
                            <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                            <button type="submit" class="btn btn-info pull-right"><?= isset($_GET['id']) ? 'Modifier' : 'Ajouter'?></button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->

            </div>

            <?php
                if(isset($_GET['id']))
                { ?>
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
            <?php } ?>

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
