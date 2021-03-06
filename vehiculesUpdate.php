<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vehicules_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
	$_POST['dateAchat'] = ($_POST['dateAchat'] == Null) ? Null : $_POST['dateAchat'];
    
    $_POST['idLieu'] = ($_POST['idLieu'] == Null) ? Null : $_POST['idLieu'];
    $_POST['idCarburant'] = ($_POST['idCarburant'] == Null) ? Null : $_POST['idCarburant'];
    $_POST['nbPlaces'] = ($_POST['nbPlaces'] == Null) ? Null : $_POST['nbPlaces'];
    $_POST['idVehiculesType'] = ($_POST['idVehiculesType'] == Null) ? Null : $_POST['idVehiculesType'];
    $_POST['idEtat'] = ($_POST['idEtat'] == Null) ? Null : $_POST['idEtat'];
    $_POST['idResponsable'] = ($_POST['idResponsable'] == Null) ? Null : $_POST['idResponsable'];
    $_POST['pneusAVhivers'] = ($_POST['pneusAVhivers'] == Null) ? Null : $_POST['pneusAVhivers'];
    $_POST['pneusARhivers'] = ($_POST['pneusARhivers'] == Null) ? Null : $_POST['pneusARhivers'];
    $_POST['climatisation'] = ($_POST['climatisation'] == Null) ? Null : $_POST['climatisation'];
    $_POST['signaletiqueOrange'] = ($_POST['signaletiqueOrange'] == Null) ? Null : $_POST['signaletiqueOrange'];
    $_POST['signaletiqueBleue'] = ($_POST['signaletiqueBleue'] == Null) ? Null : $_POST['signaletiqueBleue'];
    $_POST['signaletique2tons'] = ($_POST['signaletique2tons'] == Null) ? Null : $_POST['signaletique2tons'];
    $_POST['signaletique3tons'] = ($_POST['signaletique3tons'] == Null) ? Null : $_POST['signaletique3tons'];
    $_POST['pmv'] = ($_POST['pmv'] == Null) ? Null : $_POST['pmv'];
    $_POST['fleche'] = ($_POST['fleche'] == Null) ? Null : $_POST['fleche'];
    $_POST['nbCones'] = ($_POST['nbCones'] == Null) ? Null : $_POST['nbCones'];
    $_POST['poidsVehicule'] = ($_POST['poidsVehicule'] == Null) ? Null : $_POST['poidsVehicule'];
    $_POST['priseAlimentation220'] = ($_POST['priseAlimentation220'] == Null) ? Null : $_POST['priseAlimentation220'];
    $_POST['idVehiculesEtat'] = ($_POST['idVehiculesEtat'] == Null) ? Null : $_POST['idVehiculesEtat'];

	$_POST['affichageSyntheseDesinfections'] = ($_POST['affichageSyntheseDesinfections'] == 1) ? 1 : 0;
    $_POST['affichageSyntheseHealth'] = ($_POST['affichageSyntheseHealth'] == 1) ? 1 : 0;

    $query = $db->prepare('
        UPDATE
            VEHICULES
        SET
            libelleVehicule                = :libelleVehicule,
            immatriculation                = :immatriculation,
            marqueModele                   = :marqueModele,
            idLieu                         = :idLieu,
            nbPlaces                       = :nbPlaces,
            dimensions                     = :dimensions,
            idVehiculesType                = :idVehiculesType,
            idEtat                         = :idEtat,
            idResponsable                  = :idResponsable,
            dateAchat                      = :dateAchat,
            assuranceNumero                = :assuranceNumero,
            pneusAVhivers                  = :pneusAVhivers,
            pneusARhivers                  = :pneusARhivers,
            climatisation                  = :climatisation,
            signaletiqueOrange             = :signaletiqueOrange,
            signaletiqueBleue              = :signaletiqueBleue,
            signaletique2tons              = :signaletique2tons,
            signaletique3tons              = :signaletique3tons,
            pmv                            = :pmv,
            fleche                         = :fleche,
            nbCones                        = :nbCones,
            poidsVehicule                  = :poidsVehicule,
            priseAlimentation220           = :priseAlimentation220,
            remarquesVehicule              = :remarquesVehicule,
            idVehiculesEtat                = :idVehiculesEtat,
            idCarburant                    = :idCarburant,
            affichageSyntheseDesinfections = :affichageSyntheseDesinfections,
            affichageSyntheseHealth        = :affichageSyntheseHealth
        WHERE
            idVehicule                     = :idVehicule
        ;');
    $query->execute(array(
        'libelleVehicule'                => $_POST['libelleVehicule'],
        'immatriculation'                => $_POST['immatriculation'],
        'marqueModele'                   => $_POST['marqueModele'],
        'idLieu'                         => $_POST['idLieu'],
        'nbPlaces'                       => $_POST['nbPlaces'],
        'dimensions'                     => $_POST['dimensions'],
        'idVehiculesType'                => $_POST['idVehiculesType'],
        'idEtat'                         => $_POST['idEtat'],
        'idResponsable'                  => $_POST['idResponsable'],
        'dateAchat'                      => $_POST['dateAchat'],
        'assuranceNumero'                => $_POST['assuranceNumero'],
        'pneusAVhivers'                  => $_POST['pneusAVhivers'],
        'pneusARhivers'                  => $_POST['pneusARhivers'],
        'climatisation'                  => $_POST['climatisation'],
        'signaletiqueOrange'             => $_POST['signaletiqueOrange'],
        'signaletiqueBleue'              => $_POST['signaletiqueBleue'],
        'signaletique2tons'              => $_POST['signaletique2tons'],
        'signaletique3tons'              => $_POST['signaletique3tons'],
        'pmv'                            => $_POST['pmv'],
        'fleche'                         => $_POST['fleche'],
        'nbCones'                        => $_POST['nbCones'],
        'poidsVehicule'                  => $_POST['poidsVehicule'],
        'priseAlimentation220'           => $_POST['priseAlimentation220'],
        'remarquesVehicule'              => $_POST['remarquesVehicule'],
        'idVehiculesEtat'                => $_POST['idVehiculesEtat'],
        'idCarburant'                    => $_POST['idCarburant'],
        'idVehicule'                     => $_GET['id'],
        'affichageSyntheseDesinfections' => $_POST['affichageSyntheseDesinfections'],
        'affichageSyntheseHealth'        => $_POST['affichageSyntheseHealth'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification du v??hicule " . $_POST['libelleVehicule'], '1', NULL);
            $_SESSION['returnMessage'] = 'V??hicule modifi?? avec succ??s.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification du v??hicule " . $_POST['libelleVehicule'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification du v??hicule.';
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>