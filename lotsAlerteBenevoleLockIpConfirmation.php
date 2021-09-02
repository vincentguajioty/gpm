<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

$query = $db->prepare('SELECT * FROM LOTS_ALERTES WHERE idAlerte = :idAlerte;');
$query->execute(array('idAlerte'=>$_GET['id']));
$declaration = $query->fetch();

$query = $db->prepare('SELECT COUNT(*) as nb FROM LOTS_ALERTES WHERE ipDeclarant = :ipDeclarant AND dateCreationAlerte > :dateCreationAlerte AND idLotsAlertesEtat = 1;');
$query->execute(array(
    'ipDeclarant'        => $declaration['ipDeclarant'],
    'dateCreationAlerte' => date('Y-m-d', strtotime('-1 day', strtotime($declaration['dateCreationAlerte']))),
));
$nb = $query->fetch();

?>

<div class="modal fade modal-danger" id="modalAlerteSuppress">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Déclaration frauduleuse</h4>
            </div>
            <div class="modal-body">
                Cette déclaration a été ouverte le <?=$declaration['dateCreationAlerte']?> depuis l'adresse IP <?=$declaration['ipDeclarant']?> et vous estimez qu'elle est frauduleuse. Il peut s'agir d'une personne externe à l'association qui utilise à tors l'interface bénévole de <?=$APPNAME?>, tout comme il peut s'agir d'une attaque informatique.
                <br/><br/>
                Si vous confirmez que cette déclaration est frauduleuse, les actions suivantes seront appliquées:
                <ul>
                	<li>Suppression de cette déclaration.</li>
                    <li>Suppression de toutes les déclarations non-affectées ouvertes ces 30 derniers jours depuis l'adresse IP <?=$declaration['ipDeclarant']?>, ce qui représente <?=$nb['nb']?> déclaration(s).</li>
                    <li>Verouillage de l'adresse IP <?=$declaration['ipDeclarant']?></li>
                </ul>
            </div>
            <form role="form" action="lotsAlerteBenevoleLockIpLock.php?id=<?=$_GET['id']?>" method="POST">
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-default pull-right">Je confirme que cette déclaration est frauduleuse</button>
                </div>
            </form>
        </div>
    </div>
</div>