<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

switch ($_GET['case']) {
    case 'premptionLot':
        $url = "materiels.php";
        $urlName = "Accéder aux matériels";
        $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE idElement = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Matériel: " . $data['libelleMateriel'] . "</li>
                <li>Emplacement: " . $data['libelleLot'] . ' > ' . $data['libelleSac'] . ' > ' . $data['libelleEmplacement'] . "</li>
                <li>Péremption: " . $data['peremption'] . "</li>
                <li>Notification: " . $data['peremptionNotification'] . "</li>
                <li>Quantité: " . $data['quantite'] . "</li>
                <li>Quantité d'alerte: " . $data['quantiteAlerte'] . "</li>
            </ul>
        ";
        break;

    case 'premptionReserve':
        $url = "reserveMateriel.php";
        $urlName = "Accéder aux reserves";
        $query = $db->prepare('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE idReserveElement = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Matériel: " . $data['libelleMateriel'] . "</li>
                <li>Emplacement: " . $data['libelleConteneur'] . "</li>
                <li>Péremption: " . $data['peremptionReserve'] . "</li>
                <li>Notification: " . $data['peremptionNotificationReserve'] . "</li>
                <li>Quantité: " . $data['quantiteReserve'] . "</li>
                <li>Quantité d'alerte: " . $data['quantiteAlerteReserve'] . "</li>
            </ul>
        ";
        break;

    case 'inventaireLot':
        $url = "lotsContenu.php?id=".$_GET['id'];
        $urlName = "Accéder au lot";
        $query = $db->prepare('SELECT * FROM LOTS_LOTS WHERE idLot = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Lot: " . $data['libelleLot'] . "</li>
                <li>Dernier inventaire: " . $data['dateDernierInventaire'] . "</li>
                <li>Fréquence d'inventaire: " . $data['frequenceInventaire'] . " jours</li>
            </ul>
        ";
        break;

    case 'commandes':
        $url = "commandeView.php?id=".$_GET['id'];
        $urlName = "Accéder à la commande";
        $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur LEFT OUTER JOIN LIEUX l ON c.idLieuLivraison = l.idLieu WHERE idCommande = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Fournisseur: " . $data['nomFournisseur'] . "</li>
                <li>Date de commande: " . $data['datePassage'] . "</li>
                <li>Référence commande fournisseur: " . $data['numCommandeFournisseur'] . "</li>
                <li>Référence commande interne: " . $data['idCommande'] . "</li>
                <li>Date prévisionnelle de livraison: " . $data['dateLivraisonPrevue'] . "</li>
                <li>Lieu de livraison: " . $data['libelleLieu'] . "</li>
                <li>Gérée par: ";
                
                $query = $db->prepare('SELECT * FROM COMMANDES_AFFECTEES aff JOIN PERSONNE_REFERENTE p ON aff.idAffectee = p.idPersonne WHERE idCommande = :id');
                $query->execute(array('id'=>$_GET['id']));
                while($data = $query->fetch())
                {
                	$contenu = $contenu . $data['nomPersonne'] . ' ' . $data['prenomPersonne'] . "; ";
                }
                
                $contenu = $contenu . "</li>
            </ul>
        ";
        break;

    case 'vehiculesRev':
        $url = "vehiculesContenu.php?id=".$_GET['id'];
        $urlName = "Accéder au véhicule";
        $query = $db->prepare('SELECT * FROM VEHICULES WHERE idVehicule = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Véhicule: " . $data['libelleVehicule'] . "</li>
                <li>Immatriculation: " . $data['immatriculation'] . "</li>
                <li>Prochaine révision avant le: " . $data['dateNextRevision'] . "</li>
                <li>Prochain contrôle technique avant le: " . $data['dateNextCT'] . "</li>
                <li>Expiration de l'assurance le: " . $data['assuranceExpiration'] . "</li>
            </ul>
        ";
        break;

    case 'vehiculesCT':
        $url = "vehiculesContenu.php?id=".$_GET['id'];
        $urlName = "Accéder au véhicule";
        $query = $db->prepare('SELECT * FROM VEHICULES WHERE idVehicule = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Véhicule: " . $data['libelleVehicule'] . "</li>
                <li>Immatriculation: " . $data['immatriculation'] . "</li>
                <li>Prochaine révision avant le: " . $data['dateNextRevision'] . "</li>
                <li>Prochain contrôle technique avant le: " . $data['dateNextCT'] . "</li>
                <li>Expiration de l'assurance le: " . $data['assuranceExpiration'] . "</li>
            </ul>
        ";
        break;

    case 'vehiculesAssu':
        $url = "vehiculesContenu.php?id=".$_GET['id'];
        $urlName = "Accéder au véhicule";
        $query = $db->prepare('SELECT * FROM VEHICULES WHERE idVehicule = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Véhicule: " . $data['libelleVehicule'] . "</li>
                <li>Immatriculation: " . $data['immatriculation'] . "</li>
                <li>Prochaine révision avant le: " . $data['dateNextRevision'] . "</li>
                <li>Prochain contrôle technique avant le: " . $data['dateNextCT'] . "</li>
                <li>Expiration de l'assurance le: " . $data['assuranceExpiration'] . "</li>
            </ul>
        ";
        break;

    case 'vehiculesMaintenance':
        $urlName = "Accéder au véhicule";
        $query = $db->prepare('SELECT m.idVehicule, libelleVehicule, immatriculation, libelleTypeMaintenance, identifiant, detailsMaintenance FROM VEHICULES_MAINTENANCE m LEFT OUTER JOIN VEHICULES v ON m.idVehicule = v.idVehicule LEFT OUTER JOIN VEHICULES_MAINTENANCE_TYPES t ON m.idTypeMaintenance = t.idTypeMaintenance LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idExecutant = p.idPersonne WHERE m.idMaintenance = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Véhicule: " . $data['libelleVehicule'] . "</li>
                <li>Immatriculation: " . $data['immatriculation'] . "</li>
                <li>Type de maintenance: " . $data['libelleTypeMaintenance'] . "</li>
                <li>Faite par: " . $data['identifiant'] . "</li>
                <li>Détails: " . $data['detailsMaintenance'] . "</li>
            </ul>
        ";
        $url = "vehiculesContenu.php?id=".$data['idVehicule'];
        break;
        
    case 'vehiculesDesinfectionFaite':
        $query = $db->prepare('SELECT libelleVehicule, immatriculation, libelleVehiculesDesinfectionsType, dateDesinfection, identifiant, v.idVehicule FROM VEHICULES_DESINFECTIONS d LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON d.idVehiculesDesinfectionsType = t.idVehiculesDesinfectionsType LEFT OUTER JOIN PERSONNE_REFERENTE p ON d.idExecutant = p.idPersonne WHERE idVehiculesDesinfection = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Véhicule: " . $data['libelleVehicule'] . "</li>
                <li>Immatriculation: " . $data['immatriculation'] . "</li>
                <li>Type de désinfection: " . $data['libelleVehiculesDesinfectionsType'] . "</li>
                <li>Faite le: " . $data['dateDesinfection'] . "</li>
                <li>Faite par: " . $data['identifiant'] . "</li>
            </ul>
        ";
        $url = "vehiculesContenu.php?id=".$data['idVehicule'];
        $urlName = "Accéder au véhicule";
        break;
        
    case 'vehiculesDesinfectionAFaire':
        $query = $db->prepare('
        	SELECT
                a.*,
                vv.libelleVehicule,
                vv.idVehicule,
                vv.immatriculation,
                t.libelleVehiculesDesinfectionsType,
                MAX(v.dateDesinfection) as dateDesinfection,
                p.identifiant
            FROM
                VEHICULES_DESINFECTIONS_ALERTES a
                LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON a.idVehiculesDesinfectionsType=t.idVehiculesDesinfectionsType
                LEFT OUTER JOIN VEHICULES_DESINFECTIONS v ON a.idVehiculesDesinfectionsType = v.idVehiculesDesinfectionsType AND a.idVehicule=v.idVehicule
                LEFT OUTER JOIN VEHICULES vv ON a.idVehicule = vv.idVehicule
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON v.idExecutant = p.idPersonne
            WHERE
            	a.idDesinfectionsAlerte = :id
            GROUP BY
                a.idDesinfectionsAlerte
        ;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Véhicule: " . $data['libelleVehicule'] . "</li>
                <li>Immatriculation: " . $data['immatriculation'] . "</li>
                <li>Type de désinfection: " . $data['libelleVehiculesDesinfectionsType'] . "</li>
                <li>Faite dernièrement le: " . $data['dateDesinfection'] . "</li>
                <li>Faite dernièrement par: " . $data['identifiant'] . "</li>
                <li>Prochaine désinfection à faire le: " . date('Y-m-d', strtotime($data['dateDesinfection']. ' + '.$data['frequenceDesinfection'].' days')) . "</li>
            </ul>
        ";
        $url = "vehiculesContenu.php?id=".$data['idVehicule'];
        $urlName = "Accéder au véhicule";
        break;

    case 'vehiculesMaintenanceFaite':
        $query = $db->prepare('SELECT * FROM VEHICULES_HEALTH h LEFT OUTER JOIN VEHICULES v ON h.idVehicule = v.idVehicule LEFT OUTER JOIN PERSONNE_REFERENTE p ON h.idPersonne = p.idPersonne WHERE idVehiculeHealth = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Véhicule: " . $data['libelleVehicule'] . "</li>
                <li>Immatriculation: " . $data['immatriculation'] . "</li>
                <li>Type de maintenance: ";
                $query2 = $db->prepare('SELECT * FROM VEHICULES_HEALTH_CHECKS c LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON c.idHealthType = t.idHealthType WHERE c.idVehiculeHealth = :id');
                $query2->execute(array('id'=>$_GET['id']));
                while($tache=$query2->fetch())
                {
                    $contenu .= $tache['libelleHealthType'].' ; ';
                }
        $contenu .= "</li>
                <li>Faite le: " . $data['dateHealth'] . "</li>
                <li>Faite par: " . $data['identifiant'] . "</li>
            </ul>
        ";
        $urlName = "Accéder au véhicule";
        $url = "vehiculesContenu.php?id=".$data['idVehicule'];
        break;

    case 'vehiculesMaintenanceAFaire':
        $query = $db->prepare('SELECT * FROM VEHICULES_HEALTH_ALERTES a LEFT OUTER JOIN VEHICULES v ON a.idVehicule=v.idVehicule LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON a.idHealthType = t.idHealthType WHERE idHealthAlerte = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Véhicule: " . $data['libelleVehicule'] . "</li>
                <li>Immatriculation: " . $data['immatriculation'] . "</li>
                <li>Type de maintenance: " . $data['libelleHealthType'] . "</li>
            </ul>
        ";
        $urlName = "Accéder au véhicule";
        $url = "vehiculesContenu.php?id=".$data['idVehicule'];
        break;

    case 'inventaireReserve':
    	$url = "reserveConteneurContenu.php?id=".$_GET['id'];
        $urlName = "Accéder à la reserve";
        $query = $db->prepare('SELECT * FROM RESERVES_CONTENEUR WHERE idConteneur = :id;');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Conteneur: " . $data['libelleConteneur'] . "</li>
                <li>Dernier inventaire: " . $data['dateDernierInventaire'] . "</li>
                <li>Fréquence d'inventaire: " . $data['frequenceInventaire'] . " jours</li>
            </ul>
        ";
        break;
        
    case 'tenuesRecup':
        $url = "tenuesAffectations.php";
        $urlName = "Accéder à l'affectation";
        $query = $db->prepare('SELECT * FROM TENUES_AFFECTATION ta JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue LEFT OUTER JOIN PERSONNE_REFERENTE p ON ta.idPersonne = p.idPersonne WHERE idTenue = :id');
        $query->execute(array('id'=>$_GET['id']));
        $data = $query->fetch();

        $contenu = "
            <ul>
                <li>Tenue: " . $data['libelleCatalogueTenue'] . " (" . $data['tailleCatalogueTenue'] . ")</li>
                <li>Affectée à: " . $data['nomPersonne'] . ' ' . $data['prenomPersonne'] . $data['personneNonGPM'] . "</li>
                <li>Affectée le: " . $data['dateAffectation'] . "</li>
                <li>Retour prévu le: " . $data['dateRetour'] . "</li>
            </ul>
        ";
        break;
        
        
    
}


?>

<div class="modal fade modal-default" id="modalAcceuilCalendDetails">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Evènement du calendrier</h4>
            </div>
            
            <div class="modal-body">
                <?php echo $contenu; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
                <a href="<?=$url?>" class="btn btn-default"><?=$urlName?></a>
            </div>
        </div>
    </div>
</div>