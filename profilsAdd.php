<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['profils_ajout']==0)
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
    $commande_valider_delegate = ($_POST['commande_valider_delegate'] ==1) ? 1 : 0;
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
    $vhf_canal_lecture = ($_POST['vhf_canal_lecture'] ==1) ? 1 : 0;
    $vhf_canal_ajout = ($_POST['vhf_canal_ajout'] ==1) ? 1 : 0;
    $vhf_canal_modification = ($_POST['vhf_canal_modification'] ==1) ? 1 : 0;
    $vhf_canal_suppression = ($_POST['vhf_canal_suppression'] ==1) ? 1 : 0;
    $vhf_plan_lecture = ($_POST['vhf_plan_lecture'] ==1) ? 1 : 0;
    $vhf_plan_ajout = ($_POST['vhf_plan_ajout'] ==1) ? 1 : 0;
    $vhf_plan_modification = ($_POST['vhf_plan_modification'] ==1) ? 1 : 0;
    $vhf_plan_suppression = ($_POST['vhf_plan_suppression'] ==1) ? 1 : 0;
    $vhf_equipement_lecture = ($_POST['vhf_equipement_lecture'] ==1) ? 1 : 0;
    $vhf_equipement_ajout = ($_POST['vhf_equipement_ajout'] ==1) ? 1 : 0;
    $vhf_equipement_modification = ($_POST['vhf_equipement_modification'] ==1) ? 1 : 0;
    $vhf_equipement_suppression = ($_POST['vhf_equipement_suppression'] ==1) ? 1 : 0;
    $vehicules_lecture = ($_POST['vehicules_lecture'] ==1) ? 1 : 0;
    $vehicules_ajout = ($_POST['vehicules_ajout'] ==1) ? 1 : 0;
    $vehicules_modification = ($_POST['vehicules_modification'] ==1) ? 1 : 0;
    $vehicules_suppression = ($_POST['vehicules_suppression'] ==1) ? 1 : 0;
    $vehicules_types_lecture = ($_POST['vehicules_types_lecture'] ==1) ? 1 : 0;
    $vehicules_types_ajout = ($_POST['vehicules_types_ajout'] ==1) ? 1 : 0;
    $vehicules_types_modification = ($_POST['vehicules_types_modification'] ==1) ? 1 : 0;
    $vehicules_types_suppression = ($_POST['vehicules_types_suppression'] ==1) ? 1 : 0;
    $maintenance = ($_POST['maintenance'] ==1) ? 1 : 0;
    $todolist_perso = ($_POST['todolist_perso'] ==1) ? 1 : 0;
	$todolist_lecture = ($_POST['todolist_lecture'] ==1) ? 1 : 0;
	$todolist_modification = ($_POST['todolist_modification'] ==1) ? 1 : 0;
	$contactMailGroupe = ($_POST['contactMailGroupe'] ==1) ? 1 : 0;
	$tenues_lecture = ($_POST['tenues_lecture'] ==1) ? 1 : 0;
	$tenues_ajout = ($_POST['tenues_ajout'] ==1) ? 1 : 0;
	$tenues_modification = ($_POST['tenues_modification'] ==1) ? 1 : 0;
	$tenues_suppression = ($_POST['tenues_suppression'] ==1) ? 1 : 0;
	$tenuesCatalogue_lecture = ($_POST['tenuesCatalogue_lecture'] ==1) ? 1 : 0;
	$tenuesCatalogue_ajout = ($_POST['tenuesCatalogue_ajout'] ==1) ? 1 : 0;
	$tenuesCatalogue_modification = ($_POST['tenuesCatalogue_modification'] ==1) ? 1 : 0;
	$tenuesCatalogue_suppression = ($_POST['tenuesCatalogue_suppression'] ==1) ? 1 : 0;
	$cautions_lecture = ($_POST['cautions_lecture'] ==1) ? 1 : 0;
	$cautions_ajout = ($_POST['cautions_ajout'] ==1) ? 1 : 0;
	$cautions_modification = ($_POST['cautions_modification'] ==1) ? 1 : 0;
	$cautions_suppression = ($_POST['cautions_suppression'] ==1) ? 1 : 0;
	$etats_lecture = ($_POST['etats_lecture'] ==1) ? 1 : 0;
	$etats_ajout = ($_POST['etats_ajout'] ==1) ? 1 : 0;
	$etats_modification = ($_POST['etats_modification'] ==1) ? 1 : 0;
	$etats_suppression = ($_POST['etats_suppression'] ==1) ? 1 : 0;
	$notifications = ($_POST['notifications'] ==1) ? 1 : 0;
	$actionsMassives = ($_POST['actionsMassives'] ==1) ? 1 : 0;
	$delegation = ($_POST['delegation'] ==1) ? 1 : 0;

	$query = $db->prepare('INSERT INTO PROFILS(
		libelleProfil,
		descriptifProfil,
		connexion_connexion,
		logs_lecture,
		annuaire_lecture,
		annuaire_ajout,
		annuaire_modification,
		annuaire_mdp,
		annuaire_suppression,
		profils_lecture,
		profils_ajout,
		profils_modification,
		profils_suppression,
		categories_lecture,
		categories_ajout,
		categories_modification,
		categories_suppression,
		fournisseurs_lecture,
		fournisseurs_ajout,
		fournisseurs_modification,
		fournisseurs_suppression,
		typesLots_lecture,
		typesLots_ajout,
		typesLots_modification,
		typesLots_suppression,
		lieux_lecture,
		lieux_ajout,
		lieux_modification,
		lieux_suppression,
		lots_lecture,
		lots_ajout,
		lots_modification,
		lots_suppression,
		sac_lecture,
		sac_ajout,
		sac_modification,
		sac_suppression,
		sac2_lecture,
		sac2_ajout,
		sac2_modification,
		sac2_suppression,
		catalogue_lecture,
		catalogue_ajout,
		catalogue_modification,
		catalogue_suppression,
		materiel_lecture,
		materiel_ajout,
		materiel_modification,
		materiel_suppression,
		messages_ajout,
		messages_suppression,
		verrouIP,
		commande_lecture,
		commande_ajout,
		commande_valider,
		commande_valider_delegate,
		commande_etreEnCharge,
		commande_abandonner,
		cout_lecture,
		cout_ajout,
		cout_etreEnCharge,
		cout_supprimer,
		appli_conf,
		reserve_lecture,
		reserve_ajout,
		reserve_modification,
		reserve_suppression,
		reserve_cmdVersReserve,
		reserve_ReserveVersLot,
		vhf_canal_lecture,
		vhf_canal_ajout,
		vhf_canal_modification,
		vhf_canal_suppression,
		vhf_plan_lecture,
		vhf_plan_ajout,
		vhf_plan_modification,
		vhf_plan_suppression,
		vhf_equipement_lecture,
		vhf_equipement_ajout,
		vhf_equipement_modification,
		vhf_equipement_suppression,
		vehicules_lecture,
		vehicules_ajout,
		vehicules_modification,
		vehicules_suppression,
		vehicules_types_lecture,
		vehicules_types_ajout,
		vehicules_types_modification,
		vehicules_types_suppression,
		maintenance,
		todolist_perso,
		todolist_lecture,
		todolist_modification,
		contactMailGroupe,
		tenues_lecture,
		tenues_ajout,
		tenues_modification,
		tenues_suppression,
		tenuesCatalogue_lecture,
		tenuesCatalogue_ajout,
		tenuesCatalogue_modification,
		tenuesCatalogue_suppression,
		cautions_lecture,
		cautions_ajout,
		cautions_modification,
		cautions_suppression,
		etats_lecture,
		etats_ajout,
		etats_modification,
		etats_suppression,
		notifications,
		actionsMassives,
		delegation
	)VALUES(
		:libelleProfil,
		:descriptifProfil,
		:connexion_connexion,
		:logs_lecture,
		:annuaire_lecture,
		:annuaire_ajout,
		:annuaire_modification,
		:annuaire_mdp,
		:annuaire_suppression,
		:profils_lecture,
		:profils_ajout,
		:profils_modification,
		:profils_suppression,
		:categories_lecture,
		:categories_ajout,
		:categories_modification,
		:categories_suppression,
		:fournisseurs_lecture,
		:fournisseurs_ajout,
		:fournisseurs_modification,
		:fournisseurs_suppression,
		:typesLots_lecture,
		:typesLots_ajout,
		:typesLots_modification,
		:typesLots_suppression,
		:lieux_lecture,
		:lieux_ajout,
		:lieux_modification,
		:lieux_suppression,
		:lots_lecture,
		:lots_ajout,
		:lots_modification,
		:lots_suppression,
		:sac_lecture,
		:sac_ajout,
		:sac_modification,
		:sac_suppression,
		:sac2_lecture,
		:sac2_ajout,
		:sac2_modification,
		:sac2_suppression,
		:catalogue_lecture,
		:catalogue_ajout,
		:catalogue_modification,
		:catalogue_suppression,
		:materiel_lecture,
		:materiel_ajout,
		:materiel_modification,
		:materiel_suppression,
		:messages_ajout,
		:messages_suppression,
		:verrouIP,
		:commande_lecture,
		:commande_ajout,
		:commande_valider,
		:commande_valider_delegate,
		:commande_etreEnCharge,
		:commande_abandonner,
		:cout_lecture,
		:cout_ajout,
		:cout_etreEnCharge,
		:cout_supprimer,
		:appli_conf,
		:reserve_lecture,
		:reserve_ajout,
		:reserve_modification,
		:reserve_suppression,
		:reserve_cmdVersReserve,
		:reserve_ReserveVersLot,
		:vhf_canal_lecture,
		:vhf_canal_ajout,
		:vhf_canal_modification,
		:vhf_canal_suppression,
		:vhf_plan_lecture,
		:vhf_plan_ajout,
		:vhf_plan_modification,
		:vhf_plan_suppression,
		:vhf_equipement_lecture,
		:vhf_equipement_ajout,
		:vhf_equipement_modification,
		:vhf_equipement_suppression,
		:vehicules_lecture,
		:vehicules_ajout,
		:vehicules_modification,
		:vehicules_suppression,
		:vehicules_types_lecture,
		:vehicules_types_ajout,
		:vehicules_types_modification,
		:vehicules_types_suppression,
		:maintenance,
		:todolist_perso,
		:todolist_lecture,
		:todolist_modification,
		:contactMailGroupe,
		:tenues_lecture,
		:tenues_ajout,
		:tenues_modification,
		:tenues_suppression,
		:tenuesCatalogue_lecture,
		:tenuesCatalogue_ajout,
		:tenuesCatalogue_modification,
		:tenuesCatalogue_suppression,
		:cautions_lecture,
		:cautions_ajout,
		:cautions_modification,
		:cautions_suppression,
		:etats_lecture,
		:etats_ajout,
		:etats_modification,
		:etats_suppression,
		:notifications,
		:actionsMassives,
		:delegation
	);');
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
        'messages_ajout' => $messages_ajout,
        'messages_suppression' => $messages_suppression,
        'verrouIP' => $verrouIP,
        'commande_lecture' => $commande_lecture,
	    'commande_ajout' => $commande_ajout,
	    'commande_valider' => $commande_valider,
	    'commande_valider_delegate' => $commande_valider_delegate,
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
	    'reserve_ReserveVersLot' => $reserve_ReserveVersLot,
        'vhf_canal_lecture' => $vhf_canal_lecture,
        'vhf_canal_ajout' => $vhf_canal_ajout,
        'vhf_canal_modification' => $vhf_canal_modification,
        'vhf_canal_suppression' => $vhf_canal_suppression,
        'vhf_plan_lecture' => $vhf_plan_lecture,
        'vhf_plan_ajout' => $vhf_plan_ajout,
        'vhf_plan_modification' => $vhf_plan_modification,
        'vhf_plan_suppression' => $vhf_plan_suppression,
        'vhf_equipement_lecture' => $vhf_equipement_lecture,
        'vhf_equipement_ajout' => $vhf_equipement_ajout,
        'vhf_equipement_modification' => $vhf_equipement_modification,
        'vhf_equipement_suppression' => $vhf_equipement_suppression,
        'vehicules_lecture' => $vehicules_lecture,
        'vehicules_ajout' => $vehicules_ajout,
        'vehicules_modification' => $vehicules_modification,
        'vehicules_suppression' => $vehicules_suppression,
        'vehicules_types_lecture' => $vehicules_types_lecture,
        'vehicules_types_ajout' => $vehicules_types_ajout,
        'vehicules_types_modification' => $vehicules_types_modification,
        'vehicules_types_suppression' => $vehicules_types_suppression,
        'maintenance' => $maintenance,
        'todolist_perso' => $todolist_perso,
		'todolist_lecture' => $todolist_lecture,
		'todolist_modification' => $todolist_modification,
		'tenues_lecture' => $tenues_lecture,
		'tenues_ajout' => $tenues_ajout,
		'tenues_modification' => $tenues_modification,
		'tenues_suppression' => $tenues_suppression,
		'tenuesCatalogue_lecture' => $tenuesCatalogue_lecture,
		'tenuesCatalogue_ajout' => $tenuesCatalogue_ajout,
		'tenuesCatalogue_modification' => $tenuesCatalogue_modification,
		'tenuesCatalogue_suppression' => $tenuesCatalogue_suppression,
		'cautions_lecture' => $cautions_lecture,
		'cautions_ajout' => $cautions_ajout,
		'cautions_modification' => $cautions_modification,
		'cautions_suppression' => $cautions_suppression,
		'contactMailGroupe' => $contactMailGroupe,
		'etats_lecture' => $etats_lecture,
		'etats_ajout' => $etats_ajout,
		'etats_modification' => $etats_modification,
		'etats_suppression' => $etats_suppression,
		'notifications' => $notifications,
		'actionsMassives' => $actionsMassives,
		'delegation' => $delegation
));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout du profil " . $_POST['libelleProfil'], '2');
            $_SESSION['returnMessage'] = 'Profil ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de l'ajout du profil " . $_POST['libelleProfil'], '5');
            $_SESSION['returnMessage'] = "Un profil existe déjà avec le même libellé. Merci de changer le libellé";
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout du profil " . $_POST['libelleProfil'], '5');
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout du profil.";
            $_SESSION['returnType'] = '2';
    }

    $query = $db->query('SELECT MAX(idProfil) as idProfil FROM PROFILS;');
    $data = $query->fetch();
    majIndicateursProfil($data['idProfil']);
    majNotificationsProfil($data['idProfil']);

    echo "<script>javascript:history.go(-2);</script>";
}
?>