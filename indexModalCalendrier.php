<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

switch ($_GET['case']) {
    case 'premptionLot':
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

    case 'inventaireReserve':
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
            </div>
        </div>
    </div>
</div>