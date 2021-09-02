<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['notifications_commandes_demandeur_validation']      = ($_POST['notifications_commandes_demandeur_validation']      == 1) ? 1 : 0;
    $_POST['notifications_commandes_demandeur_validationOK']    = ($_POST['notifications_commandes_demandeur_validationOK']    == 1) ? 1 : 0;
    $_POST['notifications_commandes_demandeur_validationNOK']   = ($_POST['notifications_commandes_demandeur_validationNOK']   == 1) ? 1 : 0;
    $_POST['notifications_commandes_demandeur_passee']          = ($_POST['notifications_commandes_demandeur_passee']          == 1) ? 1 : 0;
    $_POST['notifications_commandes_demandeur_livraisonOK']     = ($_POST['notifications_commandes_demandeur_livraisonOK']     == 1) ? 1 : 0;
    $_POST['notifications_commandes_demandeur_livraisonNOK']    = ($_POST['notifications_commandes_demandeur_livraisonNOK']    == 1) ? 1 : 0;
    $_POST['notifications_commandes_demandeur_savOK']           = ($_POST['notifications_commandes_demandeur_savOK']           == 1) ? 1 : 0;
    $_POST['notifications_commandes_demandeur_cloture']         = ($_POST['notifications_commandes_demandeur_cloture']         == 1) ? 1 : 0;
    $_POST['notifications_commandes_demandeur_abandon']         = ($_POST['notifications_commandes_demandeur_abandon']         == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_validation']       = ($_POST['notifications_commandes_valideur_validation']       == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_validationOK']     = ($_POST['notifications_commandes_valideur_validationOK']     == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_validationNOK']    = ($_POST['notifications_commandes_valideur_validationNOK']    == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_passee']           = ($_POST['notifications_commandes_valideur_passee']           == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_livraisonOK']      = ($_POST['notifications_commandes_valideur_livraisonOK']      == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_livraisonNOK']     = ($_POST['notifications_commandes_valideur_livraisonNOK']     == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_savOK']            = ($_POST['notifications_commandes_valideur_savOK']            == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_cloture']          = ($_POST['notifications_commandes_valideur_cloture']          == 1) ? 1 : 0;
    $_POST['notifications_commandes_valideur_abandon']          = ($_POST['notifications_commandes_valideur_abandon']          == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_validation']       = ($_POST['notifications_commandes_affectee_validation']       == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_validationOK']     = ($_POST['notifications_commandes_affectee_validationOK']     == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_validationNOK']    = ($_POST['notifications_commandes_affectee_validationNOK']    == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_passee']           = ($_POST['notifications_commandes_affectee_passee']           == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_livraisonOK']      = ($_POST['notifications_commandes_affectee_livraisonOK']      == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_livraisonNOK']     = ($_POST['notifications_commandes_affectee_livraisonNOK']     == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_savOK']            = ($_POST['notifications_commandes_affectee_savOK']            == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_cloture']          = ($_POST['notifications_commandes_affectee_cloture']          == 1) ? 1 : 0;
    $_POST['notifications_commandes_affectee_abandon']          = ($_POST['notifications_commandes_affectee_abandon']          == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_validation']    = ($_POST['notifications_commandes_observateur_validation']    == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_validationOK']  = ($_POST['notifications_commandes_observateur_validationOK']  == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_validationNOK'] = ($_POST['notifications_commandes_observateur_validationNOK'] == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_passee']        = ($_POST['notifications_commandes_observateur_passee']        == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_livraisonOK']   = ($_POST['notifications_commandes_observateur_livraisonOK']   == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_livraisonNOK']  = ($_POST['notifications_commandes_observateur_livraisonNOK']  == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_savOK']         = ($_POST['notifications_commandes_observateur_savOK']         == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_cloture']       = ($_POST['notifications_commandes_observateur_cloture']       == 1) ? 1 : 0;
    $_POST['notifications_commandes_observateur_abandon']       = ($_POST['notifications_commandes_observateur_abandon']       == 1) ? 1 : 0;

    $query = $db->prepare('
        UPDATE
            CONFIG
        SET 
            notifications_commandes_demandeur_validation      = :notifications_commandes_demandeur_validation ,
            notifications_commandes_demandeur_validationOK    = :notifications_commandes_demandeur_validationOK ,
            notifications_commandes_demandeur_validationNOK   = :notifications_commandes_demandeur_validationNOK ,
            notifications_commandes_demandeur_passee          = :notifications_commandes_demandeur_passee ,
            notifications_commandes_demandeur_livraisonOK     = :notifications_commandes_demandeur_livraisonOK ,
            notifications_commandes_demandeur_livraisonNOK    = :notifications_commandes_demandeur_livraisonNOK ,
            notifications_commandes_demandeur_savOK           = :notifications_commandes_demandeur_savOK ,
            notifications_commandes_demandeur_cloture         = :notifications_commandes_demandeur_cloture ,
            notifications_commandes_demandeur_abandon         = :notifications_commandes_demandeur_abandon ,
            notifications_commandes_valideur_validation       = :notifications_commandes_valideur_validation ,
            notifications_commandes_valideur_validationOK     = :notifications_commandes_valideur_validationOK ,
            notifications_commandes_valideur_validationNOK    = :notifications_commandes_valideur_validationNOK ,
            notifications_commandes_valideur_passee           = :notifications_commandes_valideur_passee ,
            notifications_commandes_valideur_livraisonOK      = :notifications_commandes_valideur_livraisonOK ,
            notifications_commandes_valideur_livraisonNOK     = :notifications_commandes_valideur_livraisonNOK ,
            notifications_commandes_valideur_savOK            = :notifications_commandes_valideur_savOK ,
            notifications_commandes_valideur_cloture          = :notifications_commandes_valideur_cloture ,
            notifications_commandes_valideur_abandon          = :notifications_commandes_valideur_abandon ,
            notifications_commandes_affectee_validation       = :notifications_commandes_affectee_validation ,
            notifications_commandes_affectee_validationOK     = :notifications_commandes_affectee_validationOK ,
            notifications_commandes_affectee_validationNOK    = :notifications_commandes_affectee_validationNOK ,
            notifications_commandes_affectee_passee           = :notifications_commandes_affectee_passee ,
            notifications_commandes_affectee_livraisonOK      = :notifications_commandes_affectee_livraisonOK ,
            notifications_commandes_affectee_livraisonNOK     = :notifications_commandes_affectee_livraisonNOK ,
            notifications_commandes_affectee_savOK            = :notifications_commandes_affectee_savOK ,
            notifications_commandes_affectee_cloture          = :notifications_commandes_affectee_cloture ,
            notifications_commandes_affectee_abandon          = :notifications_commandes_affectee_abandon ,
            notifications_commandes_observateur_validation    = :notifications_commandes_observateur_validation ,
            notifications_commandes_observateur_validationOK  = :notifications_commandes_observateur_validationOK ,
            notifications_commandes_observateur_validationNOK = :notifications_commandes_observateur_validationNOK ,
            notifications_commandes_observateur_passee        = :notifications_commandes_observateur_passee ,
            notifications_commandes_observateur_livraisonOK   = :notifications_commandes_observateur_livraisonOK ,
            notifications_commandes_observateur_livraisonNOK  = :notifications_commandes_observateur_livraisonNOK ,
            notifications_commandes_observateur_savOK         = :notifications_commandes_observateur_savOK ,
            notifications_commandes_observateur_cloture       = :notifications_commandes_observateur_cloture ,
            notifications_commandes_observateur_abandon       = :notifications_commandes_observateur_abandon
    ;');
    $query->execute(array(
        'notifications_commandes_demandeur_validation'      => $_POST['notifications_commandes_demandeur_validation'],
        'notifications_commandes_demandeur_validationOK'    => $_POST['notifications_commandes_demandeur_validationOK'],
        'notifications_commandes_demandeur_validationNOK'   => $_POST['notifications_commandes_demandeur_validationNOK'],
        'notifications_commandes_demandeur_passee'          => $_POST['notifications_commandes_demandeur_passee'],
        'notifications_commandes_demandeur_livraisonOK'     => $_POST['notifications_commandes_demandeur_livraisonOK'],
        'notifications_commandes_demandeur_livraisonNOK'    => $_POST['notifications_commandes_demandeur_livraisonNOK'],
        'notifications_commandes_demandeur_savOK'           => $_POST['notifications_commandes_demandeur_savOK'],
        'notifications_commandes_demandeur_cloture'         => $_POST['notifications_commandes_demandeur_cloture'],
        'notifications_commandes_demandeur_abandon'         => $_POST['notifications_commandes_demandeur_abandon'],
        'notifications_commandes_valideur_validation'       => $_POST['notifications_commandes_valideur_validation'],
        'notifications_commandes_valideur_validationOK'     => $_POST['notifications_commandes_valideur_validationOK'],
        'notifications_commandes_valideur_validationNOK'    => $_POST['notifications_commandes_valideur_validationNOK'],
        'notifications_commandes_valideur_passee'           => $_POST['notifications_commandes_valideur_passee'],
        'notifications_commandes_valideur_livraisonOK'      => $_POST['notifications_commandes_valideur_livraisonOK'],
        'notifications_commandes_valideur_livraisonNOK'     => $_POST['notifications_commandes_valideur_livraisonNOK'],
        'notifications_commandes_valideur_savOK'            => $_POST['notifications_commandes_valideur_savOK'],
        'notifications_commandes_valideur_cloture'          => $_POST['notifications_commandes_valideur_cloture'],
        'notifications_commandes_valideur_abandon'          => $_POST['notifications_commandes_valideur_abandon'],
        'notifications_commandes_affectee_validation'       => $_POST['notifications_commandes_affectee_validation'],
        'notifications_commandes_affectee_validationOK'     => $_POST['notifications_commandes_affectee_validationOK'],
        'notifications_commandes_affectee_validationNOK'    => $_POST['notifications_commandes_affectee_validationNOK'],
        'notifications_commandes_affectee_passee'           => $_POST['notifications_commandes_affectee_passee'],
        'notifications_commandes_affectee_livraisonOK'      => $_POST['notifications_commandes_affectee_livraisonOK'],
        'notifications_commandes_affectee_livraisonNOK'     => $_POST['notifications_commandes_affectee_livraisonNOK'],
        'notifications_commandes_affectee_savOK'            => $_POST['notifications_commandes_affectee_savOK'],
        'notifications_commandes_affectee_cloture'          => $_POST['notifications_commandes_affectee_cloture'],
        'notifications_commandes_affectee_abandon'          => $_POST['notifications_commandes_affectee_abandon'],
        'notifications_commandes_observateur_validation'    => $_POST['notifications_commandes_observateur_validation'],
        'notifications_commandes_observateur_validationOK'  => $_POST['notifications_commandes_observateur_validationOK'],
        'notifications_commandes_observateur_validationNOK' => $_POST['notifications_commandes_observateur_validationNOK'],
        'notifications_commandes_observateur_passee'        => $_POST['notifications_commandes_observateur_passee'],
        'notifications_commandes_observateur_livraisonOK'   => $_POST['notifications_commandes_observateur_livraisonOK'],
        'notifications_commandes_observateur_livraisonNOK'  => $_POST['notifications_commandes_observateur_livraisonNOK'],
        'notifications_commandes_observateur_savOK'         => $_POST['notifications_commandes_observateur_savOK'],
        'notifications_commandes_observateur_cloture'       => $_POST['notifications_commandes_observateur_cloture'],
        'notifications_commandes_observateur_abandon'       => $_POST['notifications_commandes_observateur_abandon']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la configuration des notifications globales du site", '1', NULL);
            $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Echec de la modification de la configuration des notifications globales du site", '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration.';
            $_SESSION['returnType'] = '2';
    }


    echo "<script>javascript:history.go(-2);</script>";
}
?>