<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if ($_SESSION['actionsMassives']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    $sacs = $db->query('SELECT idSac FROM MATERIEL_SAC WHERE idLot IS NULL;');
    while($sac = $sacs->fetch())
    {
        $emplacements = $db->prepare('SELECT idEmplacement FROM MATERIEL_EMPLACEMENT WHERE idSac = :idSac;');
        $emplacements->execute(array('idSac'=>$sac['idSac']));
        while($emplacement = $emplacements->fetch())
        {
            $materiels = $db->prepare('SELECT idElement FROM MATERIEL_ELEMENT WHERE idEmplacement = :idEmplacement;');
            $materiels->execute(array('idEmplacement'=>$emplacement['idEmplacement']));
            while($materiel = $materiels->fetch())
            {
                $delete = $db->prepare('DELETE FROM MATERIEL_ELEMENT WHERE idElement = :idElement;');
                $delete->execute(array('idElement'=>$materiel['idElement']));
            }
            $delete = $db->prepare('DELETE FROM MATERIEL_EMPLACEMENT WHERE idEmplacement = :idEmplacement;');
            $delete->execute(array('idEmplacement'=>$emplacement['idEmplacement']));
        }
        $delete = $db->prepare('DELETE FROM MATERIEL_SAC WHERE idSac = :idSac;');
        $delete->execute(array('idSac'=>$sac['idSac']));
    }

    writeInLogs("Action massive 33", '4');
    $_SESSION['returnMessage'] = 'Requète lancée et terminée';
    $_SESSION['returnType'] = '1';

	checkAllConf();

    $_SESSION['actionsMassives_authent_ok'] = 1;
    echo "<script type='text/javascript'>document.location.replace('actionsMassives.php');</script>";
}
?>