<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['vhf_equipement_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['idVhfEtat'] = ($_POST['idVhfEtat'] == Null) ? Null : $_POST['idVhfEtat'];
    $_POST['idVhfType'] = ($_POST['idVhfType'] == Null) ? Null : $_POST['idVhfType'];
    $_POST['idVhfTechno'] = ($_POST['idVhfTechno'] == Null) ? Null : $_POST['idVhfTechno'];
    $_POST['idVhfPlan'] = ($_POST['idVhfPlan'] == Null) ? Null : $_POST['idVhfPlan'];
    $_POST['idResponsable'] = ($_POST['idResponsable'] == Null) ? Null : $_POST['idResponsable'];
    $_POST['dateDerniereProg'] = ($_POST['dateDerniereProg'] == Null) ? Null : $_POST['dateDerniereProg'];

    $query = $db->prepare('
        INSERT INTO
            VHF_EQUIPEMENTS
        SET
            vhfMarqueModele        = :vhfMarqueModele,
            vhfSN                  = :vhfSN,
            vhfIndicatif           = :vhfIndicatif,
            idVhfEtat              = :idVhfEtat,
            idVhfType              = :idVhfType,
            idVhfTechno            = :idVhfTechno,
            idVhfPlan              = :idVhfPlan,
            dateDerniereProg       = :dateDerniereProg,
            idResponsable          = :idResponsable,
            remarquesVhfEquipement = :remarquesVhfEquipement
        ;');
    $query->execute(array(
        'vhfMarqueModele'        => $_POST['vhfMarqueModele'],
        'vhfSN'                  => $_POST['vhfSN'],
        'vhfIndicatif'           => $_POST['vhfIndicatif'],
        'idVhfEtat'              => $_POST['idVhfEtat'],
        'idVhfType'              => $_POST['idVhfType'],
        'idVhfTechno'            => $_POST['idVhfTechno'],
        'idVhfPlan'              => $_POST['idVhfPlan'],
        'dateDerniereProg'       => $_POST['dateDerniereProg'],
        'idResponsable'          => $_POST['idResponsable'],
        'remarquesVhfEquipement' => $_POST['remarquesVhfEquipement']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout de l'équipement radio " . $_POST['vhfIndicatif'], '1', NULL);
            $_SESSION['returnMessage'] = 'Equipement radio ajouté avec succès.';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout de l'équipement radio " . $_POST['vhfIndicatif'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors de l'ajout de l'équipement radio.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>