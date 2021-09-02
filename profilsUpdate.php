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
    $_POST['connexion_connexion']             = ($_POST['connexion_connexion']             == 1) ? 1 : 0;
    $_POST['annuaire_lecture']                = ($_POST['annuaire_lecture']                == 1) ? 1 : 0;
    $_POST['annuaire_ajout']                  = ($_POST['annuaire_ajout']                  == 1) ? 1 : 0;
    $_POST['annuaire_modification']           = ($_POST['annuaire_modification']           == 1) ? 1 : 0;
    $_POST['annuaire_mdp']                    = ($_POST['annuaire_mdp']                    == 1) ? 1 : 0;
    $_POST['annuaire_suppression']            = ($_POST['annuaire_suppression']            == 1) ? 1 : 0;
    $_POST['profils_lecture']                 = ($_POST['profils_lecture']                 == 1) ? 1 : 0;
    $_POST['profils_ajout']                   = ($_POST['profils_ajout']                   == 1) ? 1 : 0;
    $_POST['profils_modification']            = ($_POST['profils_modification']            == 1) ? 1 : 0;
    $_POST['profils_suppression']             = ($_POST['profils_suppression']             == 1) ? 1 : 0;
    $_POST['categories_lecture']              = ($_POST['categories_lecture']              == 1) ? 1 : 0;
    $_POST['categories_ajout']                = ($_POST['categories_ajout']                == 1) ? 1 : 0;
    $_POST['categories_modification']         = ($_POST['categories_modification']         == 1) ? 1 : 0;
    $_POST['categories_suppression']          = ($_POST['categories_suppression']          == 1) ? 1 : 0;
    $_POST['fournisseurs_lecture']            = ($_POST['fournisseurs_lecture']            == 1) ? 1 : 0;
    $_POST['fournisseurs_ajout']              = ($_POST['fournisseurs_ajout']              == 1) ? 1 : 0;
    $_POST['fournisseurs_modification']       = ($_POST['fournisseurs_modification']       == 1) ? 1 : 0;
    $_POST['fournisseurs_suppression']        = ($_POST['fournisseurs_suppression']        == 1) ? 1 : 0;
    $_POST['typesLots_lecture']               = ($_POST['typesLots_lecture']               == 1) ? 1 : 0;
    $_POST['typesLots_ajout']                 = ($_POST['typesLots_ajout']                 == 1) ? 1 : 0;
    $_POST['typesLots_modification']          = ($_POST['typesLots_modification']          == 1) ? 1 : 0;
    $_POST['typesLots_suppression']           = ($_POST['typesLots_suppression']           == 1) ? 1 : 0;
    $_POST['lieux_lecture']                   = ($_POST['lieux_lecture']                   == 1) ? 1 : 0;
    $_POST['lieux_ajout']                     = ($_POST['lieux_ajout']                     == 1) ? 1 : 0;
    $_POST['lieux_modification']              = ($_POST['lieux_modification']              == 1) ? 1 : 0;
    $_POST['lieux_suppression']               = ($_POST['lieux_suppression']               == 1) ? 1 : 0;
    $_POST['lots_lecture']                    = ($_POST['lots_lecture']                    == 1) ? 1 : 0;
    $_POST['lots_ajout']                      = ($_POST['lots_ajout']                      == 1) ? 1 : 0;
    $_POST['lots_modification']               = ($_POST['lots_modification']               == 1) ? 1 : 0;
    $_POST['lots_suppression']                = ($_POST['lots_suppression']                == 1) ? 1 : 0;
    $_POST['sac_lecture']                     = ($_POST['sac_lecture']                     == 1) ? 1 : 0;
    $_POST['sac_ajout']                       = ($_POST['sac_ajout']                       == 1) ? 1 : 0;
    $_POST['sac_modification']                = ($_POST['sac_modification']                == 1) ? 1 : 0;
    $_POST['sac_suppression']                 = ($_POST['sac_suppression']                 == 1) ? 1 : 0;
    $_POST['sac2_lecture']                    = ($_POST['sac2_lecture']                    == 1) ? 1 : 0;
    $_POST['sac2_ajout']                      = ($_POST['sac2_ajout']                      == 1) ? 1 : 0;
    $_POST['sac2_modification']               = ($_POST['sac2_modification']               == 1) ? 1 : 0;
    $_POST['sac2_suppression']                = ($_POST['sac2_suppression']                == 1) ? 1 : 0;
    $_POST['catalogue_lecture']               = ($_POST['catalogue_lecture']               == 1) ? 1 : 0;
    $_POST['catalogue_ajout']                 = ($_POST['catalogue_ajout']                 == 1) ? 1 : 0;
    $_POST['catalogue_modification']          = ($_POST['catalogue_modification']          == 1) ? 1 : 0;
    $_POST['catalogue_suppression']           = ($_POST['catalogue_suppression']           == 1) ? 1 : 0;
    $_POST['materiel_lecture']                = ($_POST['materiel_lecture']                == 1) ? 1 : 0;
    $_POST['materiel_ajout']                  = ($_POST['materiel_ajout']                  == 1) ? 1 : 0;
    $_POST['materiel_modification']           = ($_POST['materiel_modification']           == 1) ? 1 : 0;
    $_POST['materiel_suppression']            = ($_POST['materiel_suppression']            == 1) ? 1 : 0;
    $_POST['messages_ajout']                  = ($_POST['messages_ajout']                  == 1) ? 1 : 0;
    $_POST['messages_suppression']            = ($_POST['messages_suppression']            == 1) ? 1 : 0;
    $_POST['verrouIP']                        = ($_POST['verrouIP']                        == 1) ? 1 : 0;
    $_POST['commande_lecture']                = ($_POST['commande_lecture']                == 1) ? 1 : 0;
    $_POST['commande_ajout']                  = ($_POST['commande_ajout']                  == 1) ? 1 : 0;
    $_POST['commande_valider']                = ($_POST['commande_valider']                == 1) ? 1 : 0;
    $_POST['commande_valider_seuil']          = ($_POST['commande_valider_seuil']          == Null) ? Null : $_POST['commande_valider_seuil'];
    $_POST['commande_valider_delegate']       = ($_POST['commande_valider_delegate']       == 1) ? 1 : 0;
    $_POST['commande_etreEnCharge']           = ($_POST['commande_etreEnCharge']           == 1) ? 1 : 0;
    $_POST['commande_abandonner']             = ($_POST['commande_abandonner']             == 1) ? 1 : 0;
    $_POST['cout_lecture']                    = ($_POST['cout_lecture']                    == 1) ? 1 : 0;
    $_POST['cout_ajout']                      = ($_POST['cout_ajout']                      == 1) ? 1 : 0;
    $_POST['cout_etreEnCharge']               = ($_POST['cout_etreEnCharge']               == 1) ? 1 : 0;
    $_POST['cout_supprimer']                  = ($_POST['cout_supprimer']                  == 1) ? 1 : 0;
    $_POST['appli_conf']                      = ($_POST['appli_conf']                      == 1) ? 1 : 0;
    $_POST['reserve_lecture']                 = ($_POST['reserve_lecture']                 == 1) ? 1 : 0;
    $_POST['reserve_ajout']                   = ($_POST['reserve_ajout']                   == 1) ? 1 : 0;
    $_POST['reserve_modification']            = ($_POST['reserve_modification']            == 1) ? 1 : 0;
    $_POST['reserve_suppression']             = ($_POST['reserve_suppression']             == 1) ? 1 : 0;
    $_POST['reserve_cmdVersReserve']          = ($_POST['reserve_cmdVersReserve']          == 1) ? 1 : 0;
    $_POST['reserve_ReserveVersLot']          = ($_POST['reserve_ReserveVersLot']          == 1) ? 1 : 0;
    $_POST['vhf_canal_lecture']               = ($_POST['vhf_canal_lecture']               == 1) ? 1 : 0;
    $_POST['vhf_canal_ajout']                 = ($_POST['vhf_canal_ajout']                 == 1) ? 1 : 0;
    $_POST['vhf_canal_modification']          = ($_POST['vhf_canal_modification']          == 1) ? 1 : 0;
    $_POST['vhf_canal_suppression']           = ($_POST['vhf_canal_suppression']           == 1) ? 1 : 0;
    $_POST['vhf_plan_lecture']                = ($_POST['vhf_plan_lecture']                == 1) ? 1 : 0;
    $_POST['vhf_plan_ajout']                  = ($_POST['vhf_plan_ajout']                  == 1) ? 1 : 0;
    $_POST['vhf_plan_modification']           = ($_POST['vhf_plan_modification']           == 1) ? 1 : 0;
    $_POST['vhf_plan_suppression']            = ($_POST['vhf_plan_suppression']            == 1) ? 1 : 0;
    $_POST['vhf_equipement_lecture']          = ($_POST['vhf_equipement_lecture']          == 1) ? 1 : 0;
    $_POST['vhf_equipement_ajout']            = ($_POST['vhf_equipement_ajout']            == 1) ? 1 : 0;
    $_POST['vhf_equipement_modification']     = ($_POST['vhf_equipement_modification']     == 1) ? 1 : 0;
    $_POST['vhf_equipement_suppression']      = ($_POST['vhf_equipement_suppression']      == 1) ? 1 : 0;
    $_POST['vehicules_lecture']               = ($_POST['vehicules_lecture']               == 1) ? 1 : 0;
    $_POST['vehicules_ajout']                 = ($_POST['vehicules_ajout']                 == 1) ? 1 : 0;
    $_POST['vehicules_modification']          = ($_POST['vehicules_modification']          == 1) ? 1 : 0;
    $_POST['vehicules_suppression']           = ($_POST['vehicules_suppression']           == 1) ? 1 : 0;
    $_POST['vehicules_types_lecture']         = ($_POST['vehicules_types_lecture']         == 1) ? 1 : 0;
    $_POST['vehicules_types_ajout']           = ($_POST['vehicules_types_ajout']           == 1) ? 1 : 0;
    $_POST['vehicules_types_modification']    = ($_POST['vehicules_types_modification']    == 1) ? 1 : 0;
    $_POST['vehicules_types_suppression']     = ($_POST['vehicules_types_suppression']     == 1) ? 1 : 0;
    $_POST['maintenance']                     = ($_POST['maintenance']                     == 1) ? 1 : 0;
    $_POST['todolist_perso']                  = ($_POST['todolist_perso']                  == 1) ? 1 : 0;
    $_POST['todolist_lecture']                = ($_POST['todolist_lecture']                == 1) ? 1 : 0;
    $_POST['todolist_modification']           = ($_POST['todolist_modification']           == 1) ? 1 : 0;
    $_POST['contactMailGroupe']               = ($_POST['contactMailGroupe']               == 1) ? 1 : 0;
    $_POST['tenues_lecture']                  = ($_POST['tenues_lecture']                  == 1) ? 1 : 0;
    $_POST['tenues_ajout']                    = ($_POST['tenues_ajout']                    == 1) ? 1 : 0;
    $_POST['tenues_modification']             = ($_POST['tenues_modification']             == 1) ? 1 : 0;
    $_POST['tenues_suppression']              = ($_POST['tenues_suppression']              == 1) ? 1 : 0;
    $_POST['tenuesCatalogue_lecture']         = ($_POST['tenuesCatalogue_lecture']         == 1) ? 1 : 0;
    $_POST['tenuesCatalogue_ajout']           = ($_POST['tenuesCatalogue_ajout']           == 1) ? 1 : 0;
    $_POST['tenuesCatalogue_modification']    = ($_POST['tenuesCatalogue_modification']    == 1) ? 1 : 0;
    $_POST['tenuesCatalogue_suppression']     = ($_POST['tenuesCatalogue_suppression']     == 1) ? 1 : 0;
    $_POST['cautions_lecture']                = ($_POST['cautions_lecture']                == 1) ? 1 : 0;
    $_POST['cautions_ajout']                  = ($_POST['cautions_ajout']                  == 1) ? 1 : 0;
    $_POST['cautions_modification']           = ($_POST['cautions_modification']           == 1) ? 1 : 0;
    $_POST['cautions_suppression']            = ($_POST['cautions_suppression']            == 1) ? 1 : 0;
    $_POST['etats_lecture']                   = ($_POST['etats_lecture']                   == 1) ? 1 : 0;
    $_POST['etats_ajout']                     = ($_POST['etats_ajout']                     == 1) ? 1 : 0;
    $_POST['etats_modification']              = ($_POST['etats_modification']              == 1) ? 1 : 0;
    $_POST['etats_suppression']               = ($_POST['etats_suppression']               == 1) ? 1 : 0;
    $_POST['notifications']                   = ($_POST['notifications']                   == 1) ? 1 : 0;
    $_POST['actionsMassives']                 = ($_POST['actionsMassives']                 == 1) ? 1 : 0;
    $_POST['delegation']                      = ($_POST['delegation']                      == 1) ? 1 : 0;
    $_POST['desinfections_lecture']           = ($_POST['desinfections_lecture']           == 1) ? 1 : 0;
    $_POST['desinfections_ajout']             = ($_POST['desinfections_ajout']             == 1) ? 1 : 0;
    $_POST['desinfections_modification']      = ($_POST['desinfections_modification']      == 1) ? 1 : 0;
    $_POST['desinfections_suppression']       = ($_POST['desinfections_suppression']       == 1) ? 1 : 0;
    $_POST['typesDesinfections_lecture']      = ($_POST['typesDesinfections_lecture']      == 1) ? 1 : 0;
    $_POST['typesDesinfections_ajout']        = ($_POST['typesDesinfections_ajout']        == 1) ? 1 : 0;
    $_POST['typesDesinfections_modification'] = ($_POST['typesDesinfections_modification'] == 1) ? 1 : 0;
    $_POST['typesDesinfections_suppression']  = ($_POST['typesDesinfections_suppression']  == 1) ? 1 : 0;
    $_POST['carburants_lecture']              = ($_POST['carburants_lecture']              == 1) ? 1 : 0;
    $_POST['carburants_ajout']                = ($_POST['carburants_ajout']                == 1) ? 1 : 0;
    $_POST['carburants_modification']         = ($_POST['carburants_modification']         == 1) ? 1 : 0;
    $_POST['carburants_suppression']          = ($_POST['carburants_suppression']          == 1) ? 1 : 0;
    $_POST['vehiculeHealthType_lecture']      = ($_POST['vehiculeHealthType_lecture']      == 1) ? 1 : 0;
    $_POST['vehiculeHealthType_ajout']        = ($_POST['vehiculeHealthType_ajout']        == 1) ? 1 : 0;
    $_POST['vehiculeHealthType_modification'] = ($_POST['vehiculeHealthType_modification'] == 1) ? 1 : 0;
    $_POST['vehiculeHealthType_suppression']  = ($_POST['vehiculeHealthType_suppression']  == 1) ? 1 : 0;
    $_POST['vehiculeHealth_lecture']          = ($_POST['vehiculeHealth_lecture']          == 1) ? 1 : 0;
    $_POST['vehiculeHealth_ajout']            = ($_POST['vehiculeHealth_ajout']            == 1) ? 1 : 0;
    $_POST['vehiculeHealth_modification']     = ($_POST['vehiculeHealth_modification']     == 1) ? 1 : 0;
    $_POST['vehiculeHealth_suppression']      = ($_POST['vehiculeHealth_suppression']      == 1) ? 1 : 0;

    $query = $db->prepare('
        UPDATE
            PROFILS
        SET
            libelleProfil                   = :libelleProfil,
            descriptifProfil                = :descriptifProfil,
            connexion_connexion             = :connexion_connexion,
            annuaire_lecture                = :annuaire_lecture,
            annuaire_ajout                  = :annuaire_ajout,
            annuaire_modification           = :annuaire_modification,
            annuaire_mdp                    = :annuaire_mdp,
            annuaire_suppression            = :annuaire_suppression,
            profils_lecture                 = :profils_lecture,
            profils_ajout                   = :profils_ajout,
            profils_modification            = :profils_modification,
            profils_suppression             = :profils_suppression,
            categories_lecture              = :categories_lecture,
            categories_ajout                = :categories_ajout,
            categories_modification         = :categories_modification,
            categories_suppression          = :categories_suppression,
            fournisseurs_lecture            = :fournisseurs_lecture,
            fournisseurs_ajout              = :fournisseurs_ajout,
            fournisseurs_modification       = :fournisseurs_modification,
            fournisseurs_suppression        = :fournisseurs_suppression,
            typesLots_lecture               = :typesLots_lecture,
            typesLots_ajout                 = :typesLots_ajout,
            typesLots_modification          = :typesLots_modification,
            typesLots_suppression           = :typesLots_suppression,
            lieux_lecture                   = :lieux_lecture,
            lieux_ajout                     = :lieux_ajout,
            lieux_modification              = :lieux_modification,
            lieux_suppression               = :lieux_suppression,
            lots_lecture                    = :lots_lecture,
            lots_ajout                      = :lots_ajout,
            lots_modification               = :lots_modification,
            lots_suppression                = :lots_suppression,
            sac_lecture                     = :sac_lecture,
            sac_ajout                       = :sac_ajout,
            sac_modification                = :sac_modification,
            sac_suppression                 = :sac_suppression,
            sac2_lecture                    = :sac2_lecture,
            sac2_ajout                      = :sac2_ajout,
            sac2_modification               = :sac2_modification,
            sac2_suppression                = :sac2_suppression,
            catalogue_lecture               = :catalogue_lecture,
            catalogue_ajout                 = :catalogue_ajout,
            catalogue_modification          = :catalogue_modification,
            catalogue_suppression           = :catalogue_suppression,
            materiel_lecture                = :materiel_lecture,
            materiel_ajout                  = :materiel_ajout,
            materiel_modification           = :materiel_modification,
            materiel_suppression            = :materiel_suppression,
            messages_ajout                  = :messages_ajout,
            messages_suppression            = :messages_suppression,
            verrouIP                        = :verrouIP,
            commande_lecture                = :commande_lecture,
            commande_ajout                  = :commande_ajout,
            commande_valider                = :commande_valider,
            commande_valider_seuil          = :commande_valider_seuil,
            commande_valider_delegate       = :commande_valider_delegate,
            commande_etreEnCharge           = :commande_etreEnCharge,
            commande_abandonner             = :commande_abandonner,
            cout_lecture                    = :cout_lecture,
            cout_ajout                      = :cout_ajout,
            cout_etreEnCharge               = :cout_etreEnCharge,
            cout_supprimer                  = :cout_supprimer,
            appli_conf                      = :appli_conf,
            reserve_lecture                 = :reserve_lecture,
            reserve_ajout                   = :reserve_ajout,
            reserve_modification            = :reserve_modification,
            reserve_suppression             = :reserve_suppression,
            reserve_cmdVersReserve          = :reserve_cmdVersReserve,
            reserve_ReserveVersLot          = :reserve_ReserveVersLot,
            vhf_canal_lecture               = :vhf_canal_lecture,
            vhf_canal_ajout                 = :vhf_canal_ajout,
            vhf_canal_modification          = :vhf_canal_modification,
            vhf_canal_suppression           = :vhf_canal_suppression,
            vhf_plan_lecture                = :vhf_plan_lecture,
            vhf_plan_ajout                  = :vhf_plan_ajout,
            vhf_plan_modification           = :vhf_plan_modification,
            vhf_plan_suppression            = :vhf_plan_suppression,
            vhf_equipement_lecture          = :vhf_equipement_lecture,
            vhf_equipement_ajout            = :vhf_equipement_ajout,
            vhf_equipement_modification     = :vhf_equipement_modification,
            vhf_equipement_suppression      = :vhf_equipement_suppression,
            vehicules_lecture               = :vehicules_lecture,
            vehicules_ajout                 = :vehicules_ajout,
            vehicules_modification          = :vehicules_modification,
            vehicules_suppression           = :vehicules_suppression,
            vehicules_types_lecture         = :vehicules_types_lecture,
            vehicules_types_ajout           = :vehicules_types_ajout,
            vehicules_types_modification    = :vehicules_types_modification,
            vehicules_types_suppression     = :vehicules_types_suppression,
            maintenance                     = :maintenance,
            todolist_perso                  = :todolist_perso,
            todolist_lecture                = :todolist_lecture,
            todolist_modification           = :todolist_modification,
            contactMailGroupe               = :contactMailGroupe,
            tenues_lecture                  = :tenues_lecture,
            tenues_ajout                    = :tenues_ajout,
            tenues_modification             = :tenues_modification,
            tenues_suppression              = :tenues_suppression,
            tenuesCatalogue_lecture         = :tenuesCatalogue_lecture,
            tenuesCatalogue_ajout           = :tenuesCatalogue_ajout,
            tenuesCatalogue_modification    = :tenuesCatalogue_modification,
            tenuesCatalogue_suppression     = :tenuesCatalogue_suppression,
            cautions_lecture                = :cautions_lecture,
            cautions_ajout                  = :cautions_ajout,
            cautions_modification           = :cautions_modification,
            cautions_suppression            = :cautions_suppression,
            etats_lecture                   = :etats_lecture,
            etats_ajout                     = :etats_ajout,
            etats_modification              = :etats_modification,
            etats_suppression               = :etats_suppression,
            notifications                   = :notifications,
            actionsMassives                 = :actionsMassives,
            delegation                      = :delegation,
            desinfections_lecture           = :desinfections_lecture,
            desinfections_ajout             = :desinfections_ajout,
            desinfections_modification      = :desinfections_modification,
            desinfections_suppression       = :desinfections_suppression,
            typesDesinfections_lecture      = :typesDesinfections_lecture,
            typesDesinfections_ajout        = :typesDesinfections_ajout,
            typesDesinfections_modification = :typesDesinfections_modification,
            typesDesinfections_suppression  = :typesDesinfections_suppression,
            carburants_lecture              = :carburants_lecture,
            carburants_ajout                = :carburants_ajout,
            carburants_modification         = :carburants_modification,
            carburants_suppression          = :carburants_suppression,
            vehiculeHealthType_lecture      = :vehiculeHealthType_lecture,
            vehiculeHealthType_ajout        = :vehiculeHealthType_ajout,
            vehiculeHealthType_modification = :vehiculeHealthType_modification,
            vehiculeHealthType_suppression  = :vehiculeHealthType_suppression,
            vehiculeHealth_lecture          = :vehiculeHealth_lecture,
            vehiculeHealth_ajout            = :vehiculeHealth_ajout,
            vehiculeHealth_modification     = :vehiculeHealth_modification,
            vehiculeHealth_suppression      = :vehiculeHealth_suppression
        WHERE
            idProfil                     = :idProfil
        ;');
    $query->execute(array(
        'libelleProfil'                   => $_POST['libelleProfil'],
        'descriptifProfil'                => $_POST['descriptifProfil'],
        'connexion_connexion'             => $_POST['connexion_connexion'],
        'annuaire_lecture'                => $_POST['annuaire_lecture'],
        'annuaire_ajout'                  => $_POST['annuaire_ajout'],
        'annuaire_modification'           => $_POST['annuaire_modification'],
        'annuaire_mdp'                    => $_POST['annuaire_mdp'],
        'annuaire_suppression'            => $_POST['annuaire_suppression'],
        'profils_lecture'                 => $_POST['profils_lecture'],
        'profils_ajout'                   => $_POST['profils_ajout'],
        'profils_modification'            => $_POST['profils_modification'],
        'profils_suppression'             => $_POST['profils_suppression'],
        'categories_lecture'              => $_POST['categories_lecture'],
        'categories_ajout'                => $_POST['categories_ajout'],
        'categories_modification'         => $_POST['categories_modification'],
        'categories_suppression'          => $_POST['categories_suppression'],
        'fournisseurs_lecture'            => $_POST['fournisseurs_lecture'],
        'fournisseurs_ajout'              => $_POST['fournisseurs_ajout'],
        'fournisseurs_modification'       => $_POST['fournisseurs_modification'],
        'fournisseurs_suppression'        => $_POST['fournisseurs_suppression'],
        'typesLots_lecture'               => $_POST['typesLots_lecture'],
        'typesLots_ajout'                 => $_POST['typesLots_ajout'],
        'typesLots_modification'          => $_POST['typesLots_modification'],
        'typesLots_suppression'           => $_POST['typesLots_suppression'],
        'lieux_lecture'                   => $_POST['lieux_lecture'],
        'lieux_ajout'                     => $_POST['lieux_ajout'],
        'lieux_modification'              => $_POST['lieux_modification'],
        'lieux_suppression'               => $_POST['lieux_suppression'],
        'lots_lecture'                    => $_POST['lots_lecture'],
        'lots_ajout'                      => $_POST['lots_ajout'],
        'lots_modification'               => $_POST['lots_modification'],
        'lots_suppression'                => $_POST['lots_suppression'],
        'sac_lecture'                     => $_POST['sac_lecture'],
        'sac_ajout'                       => $_POST['sac_ajout'],
        'sac_modification'                => $_POST['sac_modification'],
        'sac_suppression'                 => $_POST['sac_suppression'],
        'sac2_lecture'                    => $_POST['sac2_lecture'],
        'sac2_ajout'                      => $_POST['sac2_ajout'],
        'sac2_modification'               => $_POST['sac2_modification'],
        'sac2_suppression'                => $_POST['sac2_suppression'],
        'catalogue_lecture'               => $_POST['catalogue_lecture'],
        'catalogue_ajout'                 => $_POST['catalogue_ajout'],
        'catalogue_modification'          => $_POST['catalogue_modification'],
        'catalogue_suppression'           => $_POST['catalogue_suppression'],
        'materiel_lecture'                => $_POST['materiel_lecture'],
        'materiel_ajout'                  => $_POST['materiel_ajout'],
        'materiel_modification'           => $_POST['materiel_modification'],
        'materiel_suppression'            => $_POST['materiel_suppression'],
        'idProfil'                        => $_GET['id'],
        'messages_ajout'                  => $_POST['messages_ajout'],
        'messages_suppression'            => $_POST['messages_suppression'],
        'verrouIP'                        => $_POST['verrouIP'],
        'commande_lecture'                => $_POST['commande_lecture'],
        'commande_ajout'                  => $_POST['commande_ajout'],
        'commande_valider'                => $_POST['commande_valider'],
        'commande_valider_seuil'          => $_POST['commande_valider_seuil'],
        'commande_valider_delegate'       => $_POST['commande_valider_delegate'],
        'commande_etreEnCharge'           => $_POST['commande_etreEnCharge'],
        'commande_abandonner'             => $_POST['commande_abandonner'],
        'cout_lecture'                    => $_POST['cout_lecture'],
        'cout_ajout'                      => $_POST['cout_ajout'],
        'cout_etreEnCharge'               => $_POST['cout_etreEnCharge'],
        'cout_supprimer'                  => $_POST['cout_supprimer'],
        'appli_conf'                      => $_POST['appli_conf'],
        'reserve_lecture'                 => $_POST['reserve_lecture'],
        'reserve_ajout'                   => $_POST['reserve_ajout'],
        'reserve_modification'            => $_POST['reserve_modification'],
        'reserve_suppression'             => $_POST['reserve_suppression'],
        'reserve_cmdVersReserve'          => $_POST['reserve_cmdVersReserve'],
        'reserve_ReserveVersLot'          => $_POST['reserve_ReserveVersLot'],
        'vhf_canal_lecture'               => $_POST['vhf_canal_lecture'],
        'vhf_canal_ajout'                 => $_POST['vhf_canal_ajout'],
        'vhf_canal_modification'          => $_POST['vhf_canal_modification'],
        'vhf_canal_suppression'           => $_POST['vhf_canal_suppression'],
        'vhf_plan_lecture'                => $_POST['vhf_plan_lecture'],
        'vhf_plan_ajout'                  => $_POST['vhf_plan_ajout'],
        'vhf_plan_modification'           => $_POST['vhf_plan_modification'],
        'vhf_plan_suppression'            => $_POST['vhf_plan_suppression'],
        'vhf_equipement_lecture'          => $_POST['vhf_equipement_lecture'],
        'vhf_equipement_ajout'            => $_POST['vhf_equipement_ajout'],
        'vhf_equipement_modification'     => $_POST['vhf_equipement_modification'],
        'vhf_equipement_suppression'      => $_POST['vhf_equipement_suppression'],
        'vehicules_lecture'               => $_POST['vehicules_lecture'],
        'vehicules_ajout'                 => $_POST['vehicules_ajout'],
        'vehicules_modification'          => $_POST['vehicules_modification'],
        'vehicules_suppression'           => $_POST['vehicules_suppression'],
        'vehicules_types_lecture'         => $_POST['vehicules_types_lecture'],
        'vehicules_types_ajout'           => $_POST['vehicules_types_ajout'],
        'vehicules_types_modification'    => $_POST['vehicules_types_modification'],
        'vehicules_types_suppression'     => $_POST['vehicules_types_suppression'],
        'maintenance'                     => $_POST['maintenance'],
        'todolist_perso'                  => $_POST['todolist_perso'],
        'todolist_lecture'                => $_POST['todolist_lecture'],
        'todolist_modification'           => $_POST['todolist_modification'],
        'tenues_lecture'                  => $_POST['tenues_lecture'],
        'tenues_ajout'                    => $_POST['tenues_ajout'],
        'tenues_modification'             => $_POST['tenues_modification'],
        'tenues_suppression'              => $_POST['tenues_suppression'],
        'tenuesCatalogue_lecture'         => $_POST['tenuesCatalogue_lecture'],
        'tenuesCatalogue_ajout'           => $_POST['tenuesCatalogue_ajout'],
        'tenuesCatalogue_modification'    => $_POST['tenuesCatalogue_modification'],
        'tenuesCatalogue_suppression'     => $_POST['tenuesCatalogue_suppression'],
        'cautions_lecture'                => $_POST['cautions_lecture'],
        'cautions_ajout'                  => $_POST['cautions_ajout'],
        'cautions_modification'           => $_POST['cautions_modification'],
        'cautions_suppression'            => $_POST['cautions_suppression'],
        'contactMailGroupe'               => $_POST['contactMailGroupe'],
        'etats_lecture'                   => $_POST['etats_lecture'],
        'etats_ajout'                     => $_POST['etats_ajout'],
        'etats_modification'              => $_POST['etats_modification'],
        'etats_suppression'               => $_POST['etats_suppression'],
        'notifications'                   => $_POST['notifications'],
        'actionsMassives'                 => $_POST['actionsMassives'],
        'delegation'                      => $_POST['delegation'],
        'desinfections_lecture'           => $_POST['desinfections_lecture'],
        'desinfections_ajout'             => $_POST['desinfections_ajout'],
        'desinfections_modification'      => $_POST['desinfections_modification'],
        'desinfections_suppression'       => $_POST['desinfections_suppression'],
        'typesDesinfections_lecture'      => $_POST['typesDesinfections_lecture'],
        'typesDesinfections_ajout'        => $_POST['typesDesinfections_ajout'],
        'typesDesinfections_modification' => $_POST['typesDesinfections_modification'],
        'typesDesinfections_suppression'  => $_POST['typesDesinfections_suppression'],
        'carburants_lecture'              => $_POST['carburants_lecture'],
        'carburants_ajout'                => $_POST['carburants_ajout'],
        'carburants_modification'         => $_POST['carburants_modification'],
        'carburants_suppression'          => $_POST['carburants_suppression'],
        'vehiculeHealthType_lecture'      => $_POST['vehiculeHealthType_lecture'],
        'vehiculeHealthType_ajout'        => $_POST['vehiculeHealthType_ajout'],
        'vehiculeHealthType_modification' => $_POST['vehiculeHealthType_modification'],
        'vehiculeHealthType_suppression'  => $_POST['vehiculeHealthType_suppression'],
        'vehiculeHealth_lecture'          => $_POST['vehiculeHealth_lecture'],
        'vehiculeHealth_ajout'            => $_POST['vehiculeHealth_ajout'],
        'vehiculeHealth_modification'     => $_POST['vehiculeHealth_modification'],
        'vehiculeHealth_suppression'      => $_POST['vehiculeHealth_suppression'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du profil " . $_POST['libelleProfil'], '1', NULL);
            $_SESSION['returnMessage'] = 'Profil modifié avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification du profil " . $_POST['libelleProfil'], '2', NULL);
            $_SESSION['returnMessage'] = "Un profil existe déjà avec le même libellé. Merci de changer le libellé";
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du profil " . $_POST['libelleProfil'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de la modification du profil.";
            $_SESSION['returnType'] = '2';
    }

    majIndicateursProfil($_GET['id']);
    majNotificationsProfil($_GET['id']);
    majValideursProfil($_GET['id']);

    echo "<script type='text/javascript'>document.location.replace('profils.php');</script>";
}
?>