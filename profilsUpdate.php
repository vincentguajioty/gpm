<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['profils_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $connexion_connexion = ($_POST['connexion_connexion'] ==1) ? 1 : 0;
    $logs_lecture = ($_POST['logs_lecture'] ==1) ? 1 : 0;
    $annuaire_lecture = ($_POST['annuaire_lecture'] ==1) ? 1 : 0;
    $annuaire_ajout = ($_POST['annuaire_ajout'] ==1) ? 1 : 0;
    $annuaire_modification = ($_POST['annuaire_modification'] ==1) ? 1 : 0;
    $annuaire_mdp = ($_POST['annuaire_mdp'] ==1) ? 1 : 0;
    $annuaire_suppression = ($_POST['annuaire_suppression'] ==1) ? 1 : 0;
    $profils_lecture = ($_POST['profils_lecture'] ==1) ? 1 : 0;
    $profils_ajout = ($_POST['profils_ajout'] ==1) ? 1 : 0;
    $profils_modification = ($_POST['profils_modification'] ==1) ? 1 : 0;
    $profils_suppression = ($_POST['profils_suppression'] ==1) ? 1 : 0;
    $categories_lecture = ($_POST['categories_lecture'] ==1) ? 1 : 0;
    $categories_ajout = ($_POST['categories_ajout'] ==1) ? 1 : 0;
    $categories_modification = ($_POST['categories_modification'] ==1) ? 1 : 0;
    $categories_suppression = ($_POST['categories_suppression'] ==1) ? 1 : 0;
    $fournisseurs_lecture = ($_POST['fournisseurs_lecture'] ==1) ? 1 : 0;
    $fournisseurs_ajout = ($_POST['fournisseurs_ajout'] ==1) ? 1 : 0;
    $fournisseurs_modification = ($_POST['fournisseurs_modification'] ==1) ? 1 : 0;
    $fournisseurs_suppression = ($_POST['fournisseurs_suppression'] ==1) ? 1 : 0;
    $typesLots_lecture = ($_POST['typesLots_lecture'] ==1) ? 1 : 0;
    $typesLots_ajout = ($_POST['typesLots_ajout'] ==1) ? 1 : 0;
    $typesLots_modification = ($_POST['typesLots_modification'] ==1) ? 1 : 0;
    $typesLots_suppression = ($_POST['typesLots_suppression'] ==1) ? 1 : 0;
    $lieux_lecture = ($_POST['lieux_lecture'] ==1) ? 1 : 0;
    $lieux_ajout = ($_POST['lieux_ajout'] ==1) ? 1 : 0;
    $lieux_modification = ($_POST['lieux_modification'] ==1) ? 1 : 0;
    $lieux_suppression = ($_POST['lieux_suppression'] ==1) ? 1 : 0;
    $lots_lecture = ($_POST['lots_lecture'] ==1) ? 1 : 0;
    $lots_ajout = ($_POST['lots_ajout'] ==1) ? 1 : 0;
    $lots_modification = ($_POST['lots_modification'] ==1) ? 1 : 0;
    $lots_suppression = ($_POST['lots_suppression'] ==1) ? 1 : 0;
    $sac_lecture = ($_POST['sac_lecture'] ==1) ? 1 : 0;
    $sac_ajout = ($_POST['sac_ajout'] ==1) ? 1 : 0;
    $sac_modification = ($_POST['sac_modification'] ==1) ? 1 : 0;
    $sac_suppression = ($_POST['sac_suppression'] ==1) ? 1 : 0;
    $sac2_lecture = ($_POST['sac2_lecture'] ==1) ? 1 : 0;
    $sac2_ajout = ($_POST['sac2_ajout'] ==1) ? 1 : 0;
    $sac2_modification = ($_POST['sac2_modification'] ==1) ? 1 : 0;
    $sac2_suppression = ($_POST['sac2_suppression'] ==1) ? 1 : 0;
    $catalogue_lecture = ($_POST['catalogue_lecture'] ==1) ? 1 : 0;
    $catalogue_ajout = ($_POST['catalogue_ajout'] ==1) ? 1 : 0;
    $catalogue_modification = ($_POST['catalogue_modification'] ==1) ? 1 : 0;
    $catalogue_suppression = ($_POST['catalogue_suppression'] ==1) ? 1 : 0;
    $materiel_lecture = ($_POST['materiel_lecture'] ==1) ? 1 : 0;
    $materiel_ajout = ($_POST['materiel_ajout'] ==1) ? 1 : 0;
    $materiel_modification = ($_POST['materiel_modification'] ==1) ? 1 : 0;
    $materiel_suppression = ($_POST['materiel_suppression'] ==1) ? 1 : 0;
    $messages_ajout = ($_POST['messages_ajout'] ==1) ? 1 : 0;
    $messages_suppression = ($_POST['messages_suppression'] ==1) ? 1 : 0;
    $verrouIP = ($_POST['verrouIP'] ==1) ? 1 : 0;
    $commande_lecture = ($_POST['commande_lecture'] ==1) ? 1 : 0;
    $commande_ajout = ($_POST['commande_ajout'] ==1) ? 1 : 0;
    $commande_valider = ($_POST['commande_valider'] ==1) ? 1 : 0;
    $commande_etreEnCharge = ($_POST['commande_etreEnCharge'] ==1) ? 1 : 0;
    $commande_abandonner = ($_POST['commande_abandonner'] ==1) ? 1 : 0;
    $cout_lecture = ($_POST['cout_lecture'] ==1) ? 1 : 0;
    $cout_ajout = ($_POST['cout_ajout'] ==1) ? 1 : 0;
    $cout_etreEnCharge = ($_POST['cout_etreEnCharge'] ==1) ? 1 : 0;
    $cout_supprimer = ($_POST['cout_supprimer'] ==1) ? 1 : 0;
    $appli_conf = ($_POST['appli_conf'] ==1) ? 1 : 0;
    $reserve_lecture = ($_POST['reserve_lecture'] ==1) ? 1 : 0;
    $reserve_ajout = ($_POST['reserve_ajout'] ==1) ? 1 : 0;
    $reserve_modification = ($_POST['reserve_modification'] ==1) ? 1 : 0;
    $reserve_suppression = ($_POST['reserve_suppression'] ==1) ? 1 : 0;
    $reserve_cmdVersReserve = ($_POST['reserve_cmdVersReserve'] ==1) ? 1 : 0;
    $reserve_ReserveVersLot = ($_POST['reserve_ReserveVersLot'] ==1) ? 1 : 0;
    

    $query = $db->prepare('UPDATE PROFILS SET libelleProfil = :libelleProfil, descriptifProfil = :descriptifProfil, connexion_connexion = :connexion_connexion, logs_lecture = :logs_lecture, annuaire_lecture = :annuaire_lecture, annuaire_ajout = :annuaire_ajout, annuaire_modification = :annuaire_modification, annuaire_mdp = :annuaire_mdp, annuaire_suppression = :annuaire_suppression, profils_lecture = :profils_lecture, profils_ajout = :profils_ajout, profils_modification = :profils_modification, profils_suppression = :profils_suppression, categories_lecture = :categories_lecture, categories_ajout = :categories_ajout, categories_modification = :categories_modification, categories_suppression = :categories_suppression, fournisseurs_lecture = :fournisseurs_lecture, fournisseurs_ajout = :fournisseurs_ajout, fournisseurs_modification = :fournisseurs_modification, fournisseurs_suppression = :fournisseurs_suppression, typesLots_lecture = :typesLots_lecture, typesLots_ajout = :typesLots_ajout, typesLots_modification = :typesLots_modification, typesLots_suppression = :typesLots_suppression, lieux_lecture = :lieux_lecture, lieux_ajout = :lieux_ajout, lieux_modification = :lieux_modification, lieux_suppression = :lieux_suppression, lots_lecture = :lots_lecture, lots_ajout = :lots_ajout, lots_modification = :lots_modification, lots_suppression = :lots_suppression, sac_lecture = :sac_lecture, sac_ajout = :sac_ajout, sac_modification = :sac_modification, sac_suppression = :sac_suppression, sac2_lecture = :sac2_lecture, sac2_ajout = :sac2_ajout, sac2_modification = :sac2_modification, sac2_suppression = :sac2_suppression, catalogue_lecture = :catalogue_lecture, catalogue_ajout = :catalogue_ajout, catalogue_modification = :catalogue_modification, catalogue_suppression = :catalogue_suppression, materiel_lecture = :materiel_lecture, materiel_ajout = :materiel_ajout, materiel_modification = :materiel_modification, materiel_suppression = :materiel_suppression, messages_ajout = :messages_ajout, messages_suppression = :messages_suppression, notifications = :notifications, verrouIP = :verrouIP, commande_lecture = :commande_lecture, commande_ajout = :commande_ajout, commande_valider = :commande_valider, commande_etreEnCharge = :commande_etreEnCharge, commande_abandonner = :commande_abandonner, cout_lecture = :cout_lecture, cout_ajout = :cout_ajout, cout_etreEnCharge = :cout_etreEnCharge, cout_supprimer = :cout_supprimer, appli_conf = :appli_conf, reserve_lecture = :reserve_lecture, reserve_ajout = :reserve_ajout, reserve_modification = :reserve_modification, reserve_suppression = :reserve_suppression, reserve_cmdVersReserve = :reserve_cmdVersReserve, reserve_ReserveVersLot = :reserve_ReserveVersLot WHERE idProfil = :idProfil;');
    $query->execute(array(
        'libelleProfil'  =>  $_POST['libelleProfil'],
        'descriptifProfil'  =>  $_POST['descriptifProfil'],
        'connexion_connexion'  =>  $connexion_connexion,
        'logs_lecture'  =>  $logs_lecture,
        'annuaire_lecture'  =>  $annuaire_lecture,
        'annuaire_ajout'  =>  $annuaire_ajout,
        'annuaire_modification'  =>  $annuaire_modification,
        'annuaire_mdp'  =>  $annuaire_mdp,
        'annuaire_suppression'  =>  $annuaire_suppression,
        'profils_lecture'  =>  $profils_lecture,
        'profils_ajout'  =>  $profils_ajout,
        'profils_modification'  =>  $profils_modification,
        'profils_suppression'  =>  $profils_suppression,
        'categories_lecture'  =>  $categories_lecture,
        'categories_ajout'  =>  $categories_ajout,
        'categories_modification'  =>  $categories_modification,
        'categories_suppression'  =>  $categories_suppression,
        'fournisseurs_lecture'  =>  $fournisseurs_lecture,
        'fournisseurs_ajout'  =>  $fournisseurs_ajout,
        'fournisseurs_modification'  =>  $fournisseurs_modification,
        'fournisseurs_suppression'  =>  $fournisseurs_suppression,
        'typesLots_lecture'  =>  $typesLots_lecture,
        'typesLots_ajout'  =>  $typesLots_ajout,
        'typesLots_modification'  =>  $typesLots_modification,
        'typesLots_suppression'  =>  $typesLots_suppression,
        'lieux_lecture'  =>  $lieux_lecture,
        'lieux_ajout'  =>  $lieux_ajout,
        'lieux_modification'  =>  $lieux_modification,
        'lieux_suppression'  =>  $lieux_suppression,
        'lots_lecture'  =>  $lots_lecture,
        'lots_ajout'  =>  $lots_ajout,
        'lots_modification'  =>  $lots_modification,
        'lots_suppression'  =>  $lots_suppression,
        'sac_lecture'  =>  $sac_lecture,
        'sac_ajout'  =>  $sac_ajout,
        'sac_modification'  =>  $sac_modification,
        'sac_suppression'  =>  $sac_suppression,
        'sac2_lecture'  =>  $sac2_lecture,
        'sac2_ajout'  =>  $sac2_ajout,
        'sac2_modification'  =>  $sac2_modification,
        'sac2_suppression'  =>  $sac2_suppression,
        'catalogue_lecture'  =>  $catalogue_lecture,
        'catalogue_ajout'  =>  $catalogue_ajout,
        'catalogue_modification'  =>  $catalogue_modification,
        'catalogue_suppression'  =>  $catalogue_suppression,
        'materiel_lecture'  =>  $materiel_lecture,
        'materiel_ajout'  =>  $materiel_ajout,
        'materiel_modification'  =>  $materiel_modification,
        'materiel_suppression'  =>  $materiel_suppression,
        'idProfil' => $_GET['id'],
        'messages_ajout' => $messages_ajout,
        'messages_suppression' => $messages_suppression,
        'notifications' => $_POST['notifications'],
        'verrouIP' => $verrouIP,
        'commande_lecture' => $commande_lecture,
	    'commande_ajout' => $commande_ajout,
	    'commande_valider' => $commande_valider,
	    'commande_etreEnCharge' => $commande_etreEnCharge,
	    'commande_abandonner' => $commande_abandonner,
		'cout_lecture' => $cout_lecture,
	    'cout_ajout' => $cout_ajout,
	    'cout_etreEnCharge' => $cout_etreEnCharge,
	    'cout_supprimer' => $cout_supprimer,
	    'appli_conf' => $appli_conf,
	    'reserve_lecture' => $reserve_lecture,
	    'reserve_ajout' => $reserve_ajout,
	    'reserve_modification' => $reserve_modification,
	    'reserve_suppression' => $reserve_suppression,
	    'reserve_cmdVersReserve' => $reserve_cmdVersReserve,
	    'reserve_ReserveVersLot' => $reserve_ReserveVersLot
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du profil " . $_POST['libelleProfil'], '3');
            $_SESSION['returnMessage'] = 'Profil modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du profil " . $_POST['libelleProfil'], '5');
            $_SESSION['returnMessage'] = "Un profil existe déjà avec le même libellé. Merci de changer le libellé";
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du profil " . $_POST['libelleProfil'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du profil.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>javascript:history.go(-2);</script>";
}
?>