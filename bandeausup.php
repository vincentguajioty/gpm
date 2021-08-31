<?php
session_start();
require_once('logCheck.php');
require_once 'config/config.php';
?>
<header class="main-header">
    <!-- Logo -->
    <a href="index.php" class="logo">
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg spinnerAttenteClick"><b><?php echo $APPNAME; ?></b> <small><?php echo $VERSION; ?></small></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Aggrandir/Réduire</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">               

                <li class="dropdown user user-menu">
                    <a href="baseDocumentaire.php"><i class="fa fa-database"></i></a>
                </li>
                
                <?php if ($_SESSION['messages_ajout']==1 OR $_SESSION['messages_suppression']==1){ ?>
                    <li class="dropdown user user-menu">
                        <a href="messages.php"><i class="fa fa-bullhorn"></i></a>
                    </li>
                <?php } ?>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Mon Compte</a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <p>
                                <?php echo $_SESSION['nomPersonne'];?> <?php echo $_SESSION['prenomPersonne'];?>
                                <br/><?php echo $_SESSION['libelleProfil'];?> <a data-toggle="modal" data-target="#modalProfilView"><i class="fa fa-info-circle"></i></a>
                                <small>Dernière connexion: <?php echo $_SESSION['derniereConnexion'];?></small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="user.php" class="btn btn-default btn-flat">Mon compte</a>
                            </div>
                            <div class="pull-right">
                                <a href="logout.php" class="btn btn-default btn-flat">Déconnexion</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="dropdown user user-menu">
                    <a href="logout.php"><i class="fa fa-sign-out"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>



<!-- MODAL PROFIL PERSO -->
<div class="modal fade" id="modalProfilView">
    <div class="modal-dialog">
        <div class="modal-content">
        <?php
        	$query = $db->prepare('SELECT * FROM PROFILS WHERE idProfil = :idProfil;');
        	$query->execute(array(
		        'idProfil' => $_SESSION['idProfil']
    		));
    		$data = $query->fetch();
        ?>
            <div class="modal-header">
                <h4 class="modal-title">Détail de mon profil: <?php echo $data['libelleProfil'];?></h4>
            </div>
            <div class="modal-body">
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
                <div class="form-group">
                    <label>Notifications (lots et réserve):</label>
                    </br>
                    <div class="notifications">
                        <input disabled type="radio" name="notifications" id="optionsRadios1" value="0" <?php
                        if ($_SESSION['notifications']==0)
                        {
                            echo "checked";
                        }
                        ?>>
                        Notifications mail désactivées
                    </div>
                    <div class="notifications">
                        <input disabled type="radio" name="notifications" id="optionsRadios2" value="1" <?php
                        if ($_SESSION['notifications']==1)
                        {
                            echo "checked";
                        }
                        ?>>
                        Notifications mail uniquement sur alerte
                    </div>
                    <div class="notifications">
                        <input disabled type="radio" name="notifications" id="optionsRadios3" value="2" <?php
                        if ($_SESSION['notifications']==2)
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
                        <td><?php if($_SESSION['lots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['lots_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['lots_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['lots_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Sacs</td>
                        <td><?php if($_SESSION['sac_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['sac_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['sac_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['sac_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Emplacements</td>
                        <td><?php if($_SESSION['sac2_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['sac2_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['sac2_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['sac2_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Matériels/Consommables</td>
                        <td><?php if($_SESSION['materiel_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['materiel_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['materiel_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['materiel_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
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
                        <td><?php if($_SESSION['vhf_canal_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_canal_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_canal_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_canal_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Plans de fréquences</td>
                        <td><?php if($_SESSION['vhf_plan_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_plan_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_plan_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_plan_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Equipements de transmission</td>
                        <td><?php if($_SESSION['vhf_equipement_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_equipement_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_equipement_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vhf_equipement_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
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
                        <td><?php if($_SESSION['vehicules_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vehicules_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vehicules_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vehicules_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
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
                        <td><?php if($_SESSION['annuaire_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['annuaire_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['annuaire_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['annuaire_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Profils</td>
                        <td><?php if($_SESSION['profils_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['profils_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['profils_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['profils_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
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
                        <td><?php if($_SESSION['categories_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['categories_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['categories_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['categories_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Fournisseurs</td>
                        <td><?php if($_SESSION['fournisseurs_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['fournisseurs_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['fournisseurs_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['fournisseurs_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Référentiels</td>
                        <td><?php if($_SESSION['typesLots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['typesLots_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['typesLots_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['typesLots_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Lieux</td>
                        <td><?php if($_SESSION['lieux_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['lieux_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['lieux_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['lieux_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Catalogue</td>
                        <td><?php if($_SESSION['catalogue_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['catalogue_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['catalogue_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['catalogue_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Types de véhicules</td>
                        <td><?php if($_SESSION['vehicules_types_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vehicules_types_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vehicules_types_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['vehicules_types_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Messages généraux</td>
                        <td></td>
                        <td><?php if($_SESSION['messages_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td></td>
                        <td><?php if($_SESSION['messages_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                </table>
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
                        <td><?php if($_SESSION['commande_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['commande_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['commande_valider'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['commande_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['commande_abandonner'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                    <tr>
                        <td>Centres de coûts</td>
                        <td><?php if($_SESSION['cout_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['cout_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td></td>
                        <td><?php if($_SESSION['cout_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['cout_supprimer'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
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
                        <td><?php if($_SESSION['reserve_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['reserve_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['reserve_modification'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['reserve_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['reserve_cmdVersReserve'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                        <td><?php if($_SESSION['reserve_ReserveVersLot'] == 1) { echo '<i class="fa fa-check"></i>'; } ?></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <class="btn btn-default pull-left">Pour actualiser votre profil, reconnectez vous.</class>
            </div>
        </div>
    </div>
</div>





