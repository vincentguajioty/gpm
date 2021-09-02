<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['profils_lecture']==1)
{ ?>
    
    <?php
	    $query = $db->prepare('SELECT * FROM VIEW_HABILITATIONS WHERE idPersonne=:idPersonne;');
	    $query->execute(array('idPersonne' => $_GET['id']));
	    $data = $query->fetch();
	    $query->closeCursor();
    ?>
    
    <div class="modal fade" id="modalAnnuaireAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aperçu des habilitations</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Connexion à <?php echo $APPNAME;?>:</label>
                        </br>
                        <?php if($data['connexion_connexion'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Autorisé à se connecter à <?php echo $APPNAME;?>
                        </br>
                    </div>
                    <div class="form-group">
                        <label>Administration de <?php echo $APPNAME;?>:</label>
                        </br>
                        <?php if($data['appli_conf'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Modifier la configuration générale de <?php echo $APPNAME;?>
                        </br>
                        <?php if($data['annuaire_mdp'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Réinitialiser les mots de passe des autres utilisateurs
                        </br>
                        <?php if($data['maintenance'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Se connecter même en mode maitenance
                        </br>
                        <?php if($data['verrouIP'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Gérer les adresses IP bloquées
                        </br>
                        <?php if($data['logs_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Lire les logs
                        </br>
                        <?php if($data['actionsMassives'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Mener des actions massives directement en base
                        </br>
                    </div>
                    <div class="form-group">
                        <label>Notifications journalières par mail:</label>
                        </br>
                        <?php if($data['notifications'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?> Autorisé à recevoir les notifications journalières par mail
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
                            <td><?php if($data['lots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['lots_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['lots_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['lots_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Sacs</td>
                            <td><?php if($data['sac_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['sac_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['sac_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['sac_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Emplacements</td>
                            <td><?php if($data['sac2_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['sac2_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['sac2_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['sac2_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Matériels/Consommables</td>
                            <td><?php if($data['materiel_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['materiel_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['materiel_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['materiel_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                            <td><?php if($data['vhf_canal_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_canal_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_canal_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_canal_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Plans de fréquences</td>
                            <td><?php if($data['vhf_plan_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_plan_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_plan_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_plan_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Equipements de transmission</td>
                            <td><?php if($data['vhf_equipement_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_equipement_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_equipement_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vhf_equipement_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                            <td><?php if($data['vehicules_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vehicules_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vehicules_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vehicules_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                            <td><?php if($data['tenues_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['tenues_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['tenues_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['tenues_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Catalogue des tenues</td>
                            <td><?php if($data['tenuesCatalogue_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['tenuesCatalogue_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['tenuesCatalogue_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['tenuesCatalogue_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Cautions</td>
                            <td><?php if($data['cautions_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['cautions_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['cautions_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['cautions_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                            <td><?php if($data['categories_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['categories_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['categories_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['categories_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Fournisseurs</td>
                            <td><?php if($data['fournisseurs_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['fournisseurs_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['fournisseurs_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['fournisseurs_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Référentiels</td>
                            <td><?php if($data['typesLots_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['typesLots_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['typesLots_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['typesLots_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Lieux</td>
                            <td><?php if($data['lieux_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['lieux_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['lieux_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['lieux_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Catalogue</td>
                            <td><?php if($data['catalogue_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['catalogue_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['catalogue_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['catalogue_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Types de véhicules</td>
                            <td><?php if($data['vehicules_types_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vehicules_types_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vehicules_types_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['vehicules_types_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Etats</td>
                            <td><?php if($data['etats_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['etats_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['etats_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['etats_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                            <td><?php if($data['commande_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['commande_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['commande_valider'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['commande_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['commande_abandonner'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Centres de coûts</td>
                            <td><?php if($data['cout_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['cout_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td></td>
                            <td><?php if($data['cout_etreEnCharge'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['cout_supprimer'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                            <td><?php if($data['reserve_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['reserve_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['reserve_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['reserve_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['reserve_cmdVersReserve'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['reserve_ReserveVersLot'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
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
                            <td><?php if($data['annuaire_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['annuaire_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['annuaire_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td></td>
                            <td><?php if($data['annuaire_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Profils</td>
                            <td><?php if($data['profils_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['profils_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['profils_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td></td>
                            <td><?php if($data['profils_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Messages généraux</td>
                            <td></td>
                            <td><?php if($data['messages_ajout'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td></td>
                            <td></td>
                            <td><?php if($data['messages_suppression'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                        </tr>
                        <tr>
                            <td>Messages mails</td>
                            <td></td>
                            <td><?php if($data['contactMailGroupe'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>ToDoList</td>
                            <td><?php if($data['todolist_lecture'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td></td>
                            <td><?php if($data['todolist_modification'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td><?php if($data['todolist_perso'] == 1) { echo '<i class="fa fa-check"></i>'; }else{ echo '<i class="fa fa-close"></i>'; } ?></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>