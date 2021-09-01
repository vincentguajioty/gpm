<?php
session_start();
require_once 'config/bdd.php';
require_once 'config/config.php';

require_once('logCheck.php');

$query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
$query->execute(array(
    'idPersonne' => $_SESSION['idPersonne']
));
$data = $query->fetch();

$_SESSION['conf_indicateur1Accueil'] = $data['conf_indicateur1Accueil'];
$_SESSION['conf_indicateur2Accueil'] = $data['conf_indicateur2Accueil'];
$_SESSION['conf_indicateur3Accueil'] = $data['conf_indicateur3Accueil'];
$_SESSION['conf_indicateur4Accueil'] = $data['conf_indicateur4Accueil'];
$_SESSION['conf_indicateur5Accueil'] = $data['conf_indicateur5Accueil'];
$_SESSION['conf_indicateur6Accueil'] = $data['conf_indicateur6Accueil'];
$_SESSION['conf_indicateur7Accueil'] = $data['conf_indicateur7Accueil'];
$_SESSION['conf_indicateur8Accueil'] = $data['conf_indicateur8Accueil'];
$_SESSION['conf_indicateur9Accueil'] = $data['conf_indicateur9Accueil'];
$_SESSION['conf_indicateur10Accueil'] = $data['conf_indicateur10Accueil'];


$query = $db->prepare('SELECT * FROM VIEW_HABILITATIONS WHERE idPersonne = :idPersonne;');
$query->execute(array(
    'idPersonne' => $_SESSION['idPersonne']
));
$data = $query->fetch();

$_SESSION['connexion_connexion'] = $data['connexion_connexion'];
$_SESSION['logs_lecture'] = $data['logs_lecture'];
$_SESSION['annuaire_lecture'] = $data['annuaire_lecture'];
$_SESSION['annuaire_ajout'] = $data['annuaire_ajout'];
$_SESSION['annuaire_modification'] = $data['annuaire_modification'];
$_SESSION['annuaire_mdp'] = $data['annuaire_mdp'];
$_SESSION['annuaire_suppression'] = $data['annuaire_suppression'];
$_SESSION['profils_lecture'] = $data['profils_lecture'];
$_SESSION['profils_ajout'] = $data['profils_ajout'];
$_SESSION['profils_modification'] = $data['profils_modification'];
$_SESSION['profils_suppression'] = $data['profils_suppression'];
$_SESSION['categories_lecture'] = $data['categories_lecture'];
$_SESSION['categories_ajout'] = $data['categories_ajout'];
$_SESSION['categories_modification'] = $data['categories_modification'];
$_SESSION['categories_suppression'] = $data['categories_suppression'];
$_SESSION['fournisseurs_lecture'] = $data['fournisseurs_lecture'];
$_SESSION['fournisseurs_ajout'] = $data['fournisseurs_ajout'];
$_SESSION['fournisseurs_modification'] = $data['fournisseurs_modification'];
$_SESSION['fournisseurs_suppression'] = $data['fournisseurs_suppression'];
$_SESSION['typesLots_lecture'] = $data['typesLots_lecture'];
$_SESSION['typesLots_ajout'] = $data['typesLots_ajout'];
$_SESSION['typesLots_modification'] = $data['typesLots_modification'];
$_SESSION['typesLots_suppression'] = $data['typesLots_suppression'];
$_SESSION['lieux_lecture'] = $data['lieux_lecture'];
$_SESSION['lieux_ajout'] = $data['lieux_ajout'];
$_SESSION['lieux_modification'] = $data['lieux_modification'];
$_SESSION['lieux_suppression'] = $data['lieux_suppression'];
$_SESSION['lots_lecture'] = $data['lots_lecture'];
$_SESSION['lots_ajout'] = $data['lots_ajout'];
$_SESSION['lots_modification'] = $data['lots_modification'];
$_SESSION['lots_suppression'] = $data['lots_suppression'];
$_SESSION['sac_lecture'] = $data['sac_lecture'];
$_SESSION['sac_ajout'] = $data['sac_ajout'];
$_SESSION['sac_modification'] = $data['sac_modification'];
$_SESSION['sac_suppression'] = $data['sac_suppression'];
$_SESSION['sac2_lecture'] = $data['sac2_lecture'];
$_SESSION['sac2_ajout'] = $data['sac2_ajout'];
$_SESSION['sac2_modification'] = $data['sac2_modification'];
$_SESSION['sac2_suppression'] = $data['sac2_suppression'];
$_SESSION['catalogue_lecture'] = $data['catalogue_lecture'];
$_SESSION['catalogue_ajout'] = $data['catalogue_ajout'];
$_SESSION['catalogue_modification'] = $data['catalogue_modification'];
$_SESSION['catalogue_suppression'] = $data['catalogue_suppression'];
$_SESSION['materiel_lecture'] = $data['materiel_lecture'];
$_SESSION['materiel_ajout'] = $data['materiel_ajout'];
$_SESSION['materiel_modification'] = $data['materiel_modification'];
$_SESSION['materiel_suppression'] = $data['materiel_suppression'];
$_SESSION['messages_ajout'] = $data['messages_ajout'];
$_SESSION['messages_suppression'] = $data['messages_suppression'];
$_SESSION['verrouIP'] = $data['verrouIP'];
$_SESSION['commande_lecture'] = $data['commande_lecture'];
$_SESSION['commande_ajout'] = $data['commande_ajout'];
$_SESSION['commande_valider'] = $data['commande_valider'];
$_SESSION['commande_etreEnCharge'] = $data['commande_etreEnCharge'];
$_SESSION['commande_abandonner'] = $data['commande_abandonner'];
$_SESSION['cout_lecture'] = $data['cout_lecture'];
$_SESSION['cout_ajout'] = $data['cout_ajout'];
$_SESSION['cout_etreEnCharge'] = $data['cout_etreEnCharge'];
$_SESSION['cout_supprimer'] = $data['cout_supprimer'];
$_SESSION['appli_conf'] = $data['appli_conf'];
$_SESSION['reserve_lecture'] = $data['reserve_lecture'];
$_SESSION['reserve_ajout'] = $data['reserve_ajout'];
$_SESSION['reserve_modification'] = $data['reserve_modification'];
$_SESSION['reserve_suppression'] = $data['reserve_suppression'];
$_SESSION['reserve_cmdVersReserve'] = $data['reserve_cmdVersReserve'];
$_SESSION['reserve_ReserveVersLot'] = $data['reserve_ReserveVersLot'];
$_SESSION['vhf_canal_lecture'] = $data['vhf_canal_lecture'];
$_SESSION['vhf_canal_ajout'] = $data['vhf_canal_ajout'];
$_SESSION['vhf_canal_modification'] = $data['vhf_canal_modification'];
$_SESSION['vhf_canal_suppression'] = $data['vhf_canal_suppression'];
$_SESSION['vhf_plan_lecture'] = $data['vhf_plan_lecture'];
$_SESSION['vhf_plan_ajout'] = $data['vhf_plan_ajout'];
$_SESSION['vhf_plan_modification'] = $data['vhf_plan_modification'];
$_SESSION['vhf_plan_suppression'] = $data['vhf_plan_suppression'];
$_SESSION['vhf_equipement_lecture'] = $data['vhf_equipement_lecture'];
$_SESSION['vhf_equipement_ajout'] = $data['vhf_equipement_ajout'];
$_SESSION['vhf_equipement_modification'] = $data['vhf_equipement_modification'];
$_SESSION['vhf_equipement_suppression'] = $data['vhf_equipement_suppression'];
$_SESSION['vehicules_lecture'] = $data['vehicules_lecture'];
$_SESSION['vehicules_ajout'] = $data['vehicules_ajout'];
$_SESSION['vehicules_modification'] = $data['vehicules_modification'];
$_SESSION['vehicules_suppression'] = $data['vehicules_suppression'];
$_SESSION['vehicules_types_lecture'] = $data['vehicules_types_lecture'];
$_SESSION['vehicules_types_ajout'] = $data['vehicules_types_ajout'];
$_SESSION['vehicules_types_modification'] = $data['vehicules_types_modification'];
$_SESSION['vehicules_types_suppression'] = $data['vehicules_types_suppression'];
$_SESSION['tenues_lecture'] = $data['tenues_lecture'];
$_SESSION['tenues_ajout'] = $data['tenues_ajout'];
$_SESSION['tenues_modification'] = $data['tenues_modification'];
$_SESSION['tenues_suppression'] = $data['tenues_suppression'];
$_SESSION['tenuesCatalogue_lecture'] = $data['tenuesCatalogue_lecture'];
$_SESSION['tenuesCatalogue_ajout'] = $data['tenuesCatalogue_ajout'];
$_SESSION['tenuesCatalogue_modification'] = $data['tenuesCatalogue_modification'];
$_SESSION['tenuesCatalogue_suppression'] = $data['tenuesCatalogue_suppression'];
$_SESSION['cautions_lecture'] = $data['cautions_lecture'];
$_SESSION['cautions_ajout'] = $data['cautions_ajout'];
$_SESSION['cautions_modification'] = $data['cautions_modification'];
$_SESSION['cautions_suppression'] = $data['cautions_suppression'];
$_SESSION['maintenance'] = $data['maintenance'];
$_SESSION['todolist_perso'] = $data['todolist_perso'];
$_SESSION['todolist_lecture'] = $data['todolist_lecture'];
$_SESSION['todolist_modification'] = $data['todolist_modification'];
$_SESSION['contactMailGroupe'] = $data['contactMailGroupe'];

majIndicateursPersonne($_SESSION['idPersonne']);
majNotificationsPersonne($_SESSION['idPersonne']);

?>