<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

switch ($_GET['case']) {
    case 'annuaireDelete':
        $lien = "annuaireDelete.php?id=".$_GET['id'];
		$contenu = "<li>Les lots gérés par cette personne ne seront plus affectés.</li><li>Les centres de couts gérés par cette personne ne seront plus affectés.</li><li>Les commandes auquelles la personne était ratachée ne seront plus rattachées à personne (attention aux demandes de validation en attente).</li><li>Les messages publics postés par la personne n'auront plus d'auteur.</li><li>les inventaires réalisés par la personne n'auront plus d'auteur.</li><li>Les équipements radio gérés par cette personne ne seront plus affectés.</li><li>Les véhicules gérés par cette personne ne seront plus affectés.</li><li>Les taches réalisées de la TODOLIST de cet utilisateur seront supprimées.</li><li>Les taches que cet utilisateur n'a pas encore réalisée seront gardées mais non-affectées.</li><li>Les taches que cet utilisateur a donné à d'autres utilisateurs seront maintenues.</li>";
        break;
    case 'catalogueDelete':
        $lien = "catalogueDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les matériels de ce type seront supprimés des commandes qui les contiennent.</li><li>Les matériels de ce type seront supprimés des lots qui les contiennent.</li><li>Les matériels de ce type seront supprimés des inventaires (lots et réserves) qui les contiennent.</li><li>Les matériels de ce type seront supprimés des réserves qui les contiennent.</li>";
        break;
    case 'categoriesDelete':
        $lien = "categoriesDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les éléments de matériels qui sont rattachés à cette catégorie se verront sans catégories.</li>";
        break;
    case 'centreCoutsDelete':
        $lien = "centreCoutsDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les commandes rattachées à ce centre de coût se verront sans centre de coût.</li>";
        break;
    case 'commandesDelete':
        $lien = "commandesDelete.php?id=".$_GET['id'];
        $contenu = "<li>Le détail de la commande va être supprimé.</li><li>Tous les documents rattachés à la commande seront supprimés.</li><li>Tout l'historique de la commande sera supprimé.</li>";
        break;
    case 'emplacementsDelete':
        $lien = "emplacementsDelete.php?id=".$_GET['id'];
        $contenu = "Tous les éléments de matériels contenus dans l'emplacement se verront sans emplacement et ne feront donc plus parti d'un sac/lot.";
        break;
    case 'fournisseursDelete':
        $lien = "fournisseursDelete.php?id=".$_GET['id'];
        $contenu = "<li>Tous les sacs fournis par ce fournisseurs se verront sans fournisseur.</li><li>Tous les matériels (lots et réserves) fournis par ce fournisseurs se verront sans fournisseur.</li><li>Toutes les commandes rattachées à ce fournisseurs se verront sans fournisseur.</li>";
        break;
    case 'lieuxDelete':
        $lien = "lieuxDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les lots  rattachés à ce lieu seront rattachés à aucun lieu.</li><li>Les véhicules rattachés à ce lieu seront rattachés à aucun lieu.</li><li>Les commandes ayant ce lieu comme adresse de livraison n'auront plus d'adresse de livraison.</li><li>Les réserves rattachées à ce lieu seront rattachées à aucun lieu.</li>";
        break;
    case 'lotsDelete':
        $lien = "lotsDelete.php?id=".$_GET['id'];
        $contenu = "<li>Tous les inventaires rattachés à ce lot seront supprimés.</li><li>Tous les sacs composant ce lots ne seront plus rattachés à aucun lot.</li>";
        break;
    case 'materielsDelete':
        $lien = "materielsDelete.php?id=".$_GET['id'];
        $contenu = "Aucun impact collatéral";
        break;
    case 'messagesDelete':
        $lien = "messagesDelete.php?id=".$_GET['id'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;
    case 'profilsDelete':
        $lien = "profilsDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les utilisateurs rattachés à ce profil n'auront plus de profil et ne pourront plus se connecter tant qu'aucun nouveau profil ne leur sera attribué.</li>";
        break;
    case 'referentielsDelete':
        $lien = "referentielsDelete.php?id=".$_GET['id'];
        $contenu = "<li>Le détail du référentiel sera supprimé.</li><li>Les lots rattachés à ce référentiel ne seront plus analysés pour conformité tant qu'ils ne seront pas rattachés à un nouveau référentiel.</li>";
        break;
    case 'reserveConteneurDelete':
        $lien = "reserveConteneurDelete.php?id=".$_GET['id'];
        $contenu = "<li>Tous les inventaires du conteneur seront supprimés.</li><li>Tous les éléments de matériels contenus dans le conteneur se verront sans conteneur de réserve (ils ne seront pas supprimés).</li>";
        break;
    case 'reserveMaterielDelete':
        $lien = "reserveMaterielDelete.php?id=".$_GET['id'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;
    case 'sacsDelete':
        $lien = "sacsDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les emplacements contenus dans le sac se verront sans sac de rattachement, donc sans lot de rattachement.</li>";
        break;
    case 'vehiculesDelete':
        $lien = "vehiculesDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les lots rattachés à ce véhicules n'auront plus de véhicules de rattachement.</li><li>Les documents liés à ce véhicule seront supprimés.</li><li>Les taches de maitenance liées à ce véhicules seront supprimées.</li>";
        break;
    case 'vehiculesTypesDelete':
        $lien = "vehiculesTypesDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les véhicules liés à ce type se verront sans type de véhicules.</li>";
        break;
    case 'vhfCanauxDelete':
        $lien = "vhfCanauxDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les documents liés à ce canal seront supprimés.</li><li>Ce canal sera supprimé de tous les plans de programmation dans lesquel il était programmé.</li>";
        break;
    case 'vhfEquipementsDelete':
        $lien = "vhfEquipementsDelete.php?id=".$_GET['id'];
        $contenu = "<li>Les documents liés à cet équipement radio seront supprimés.</li>";
        break;
    case 'vhfPlansDelete':
        $lien = "vhfPlansDelete.php?id=".$_GET['id'];
        $contenu = "<li>Tous les documents liés à ce plan de fréquence seront supprimés.</li><li>Tous les équipements radio bénéficiant de ce plan de fréquences se verront sans plan de fréquence.</li>";
        break;
    case 'annuaireProfilOff':
        $lien = "annuaireProfilOff.php?id=".$_GET['id'];
        $contenu = "<li>L'utilisateur n'aura plus de profil et ne pourra plus se connecter.</li>";
        break;
    case 'referentielsDeleteItem':
        $lien = "referentielsDeleteItem.php?idLot=".$_GET['idLot']."&idMateriel=".$_GET['idMateriel'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;
    case 'reserveMaterielDelete':
        $lien = "reserveMaterielDelete.php?id=".$_GET['id'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;
    case 'reserveInventaireDelete':
        $lien = "reserveInventaireDelete.php?id=".$_GET['id'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;
    case 'vehiculesMaintenanceDelete':
        $lien = "vehiculesMaintenanceDelete.php?id=".$_GET['id'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;
    case 'verrouIPDelete':
        $lien = "verrouIPDelete.php?id=".$_GET['id'];
        $contenu = "<li>L'adresse IP va être débloquée et les utilisateurs pourront à nouveau se connecter depuis cette adresse IP.</li>";
        break;
    case 'vhfPlansCanauxDelete':
        $lien = "vhfPlansCanauxDelete.php?idVhfPlan=".$_GET['idVhfPlan']."&idVhfCanal=".$_GET['idVhfCanal'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;
    case 'lotsInventaireDelete':
        $lien = "lotsInventaireDelete.php?id=".$_GET['id'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;
    case 'tdlDelete':
        $lien = "todolistDelete.php?id=".$_GET['id'];
        $contenu = "<li>Aucun impact collatéral</li>";
        break;

}


?>

<div class="modal fade modal-danger" id="modalAlerteSuppress">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Avertissement avant suppression</h4>
            </div>
            
            <div class="modal-body">
                Cette suppression aura les impacts suivants:
                <ul>
                	<?php echo $contenu; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
                <a href="<?php echo $lien; ?>"><button type="button" class="btn btn-default pull-right">Confirmer la suppression</button></a>
            </div>
        </div>
    </div>
</div>