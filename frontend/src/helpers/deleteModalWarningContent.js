export const annuaireDelete = <>
    Attention, vous allez supprimer un utilisateur. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les lots gérés par cette personne ne seront plus affectés.</li>
        <li>Les centres de couts gérés par cette personne ne seront plus affectés.</li>
        <li>Les commandes auxquelles la personne était ratachée ne seront plus rattachées à personne (attention aux demandes de validation en attente).</li>
        <li>Les messages publics postés par la personne n'auront plus d'auteur.</li>
        <li>Les inventaires réalisés par la personne n'auront plus d'auteur.</li>
        <li>Les équipements radio gérés par cette personne ne seront plus affectés.</li>
        <li>Les véhicules gérés par cette personne ne seront plus affectés (véhicules, taches de maintenance, relevés kilométriques, désinfections, maintenances régulières).</li>
        <li>Les taches ToDoList que cet utilisateur n'a pas encore réalisée seront gardées mais non-affectées.</li>
        <li>Les taches ToDoList que cet utilisateur a donné à d'autres utilisateurs seront maintenues.</li>
        <li>Les opérations saisies par cette personne dans les centres de couts seront anonymisées.</li>
    </ul>
</>;

export const annuaireCnilAnonyme = <>
    Attention, vous allez rendre anonyme un utilisateur. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Le nom et le prénom de l'utilisateur seront remplacés par ANONYME.</li>
        <li>L'identifiant de l'utilisateur sera écrasé.</li>
        <li>Les autres informations personnelles seront supprimées.</li>
        <li>Si l'utilisateur a à nouveau besoin de son compte, il faudra lui en recréer un.</li>
    </ul>
</>;

export const catalogueDelete = <>
    Attention, vous allez supprimer un item du catalogue de matériel. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les matériels de ce type seront supprimés des commandes qui les contiennent.</li>
        <li>Les matériels de ce type seront supprimés des référentiels opérationnels qui les contiennent.</li>
        <li>Les matériels de ce type seront supprimés des lots qui les contiennent.</li>
        <li>Les matériels de ce type seront supprimés des inventaires (lots et réserves) qui les contiennent.</li>
        <li>Les matériels de ce type seront supprimés des réserves qui les contiennent.</li>
        <li>Les codes barre enregistrés pour ce matériel seront supprimés.</li>
        <li>Les matériels de ce type seront supprimés des fiches de consommation des bénévoles.</li>
        <li>Les matériels de ce type seront supprimés des affectations de tenues.</li>
        <li>Les matériels de ce type seront supprimés des stocks de tenues.</li>
        <li>Les matériels de ce type seront supprimés des stocks de consommables des véhicules.</li>
        <li>Les matériels de ce type seront supprimés des stocks de consommables des transmission.</li>
    </ul>
</>;

export const categoriesDelete = <>
    Attention, vous allez supprimer une catégorie. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les éléments de matériels qui sont rattachés à cette catégorie se verront sans catégories.</li>
    </ul>
</>;

export const centreCoutsDelete = <>
    Attention, vous allez supprimer un centre de cout. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les commandes rattachées à ce centre de coût se verront sans centre de coût.</li>
        <li>Les commandes pourront être à nouveau intégrées dans un autre centre de cout.</li>
        <li>Toutes les entrées liées à ce centre de cout vont être supprimées (livre de comptes, documents, ...).</li>
    </ul>
</>;

export const operationCentreCoutsDelete = <>
    Attention, vous allez supprimer une opération du centre de cout. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Le solde du centre sera recalculé.</li>
        <li>Si cette opération est liée à une intégration de commande, la commande devra à nouveau être intégrée.</li>
    </ul>
</>;

export const gerantCentreCoutsDelete = <>
    Attention, vous allez supprimer un gérant du centre de cout. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const commandesDelete = <>
    Attention, vous êtes sur le point d'abandonner ou supprimer cette commande. Cette action est irreversible (pas d'autre fenêtre de confirmation). Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Le détail de la commande va être supprimé.</li>
        <li>Tous les documents rattachés à la commande seront supprimés.</li>
        <li>Tout l'historique de la commande sera supprimé.</li>
        <li>Toute trace de cette commande disparaitera du centre de coûts.</li>
    </ul>
</>;

export const itemCommandesDelete = <>
    Attention, vous allez supprimer un item de la commande. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Le montant de la commande va être recalculé.</li>
    </ul>
</>;

export const emplacementsDelete = <>
    Attention, vous allez supprimer un emplacement. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les éléments de matériels contenus dans l'emplacement se verront sans emplacement et ne feront donc plus parti d'un sac/lot.</li>
        <li>Toutes les fiches de consommation de matériel remontées par les bénévoles sur cet emplacement ne seront plus liées à aucun lot/sac/emplacement.</li>
    </ul>
</>;

export const fournisseursDelete = <>
    Attention, vous allez supprimer un fournisseur. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les sacs fournis par ce fournisseur se verront sans fournisseur.</li>
        <li>Tous les matériels (lots et réserves) fournis par ce fournisseur se verront sans fournisseur.</li>
        <li>Toutes les entrées catalogue indiquant ce fournisseur comme fournisseur de prédilection n'auront plus de fournisseur.</li>
        <li>Toutes les commandes rattachées à ce fournisseur se verront sans fournisseur.</li>
        <li>Les éléments de tenues fournis par ce fournisseur se verront sans fournisseur.</li>
        <li>Les éléments de stock consommables des véhicules fournis par ce fournisseur se verront sans fournisseur.</li>
        <li>Les éléments de stock consommables des transmissions fournis par ce fournisseur se verront sans fournisseur.</li>
    </ul>
</>;

export const lieuxDelete = <>
    Attention, vous allez supprimer un lieu. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les lots rattachés à ce lieu seront rattachés à aucun lieu.</li>
        <li>Les véhicules rattachés à ce lieu seront rattachés à aucun lieu.</li>
        <li>Les commandes ayant ce lieu comme adresse de livraison n'auront plus d'adresse de livraison.</li>
        <li>Les réserves rattachées à ce lieu seront rattachées à aucun lieu.</li>
    </ul>
</>;

export const lotsDelete = <>
    Attention, vous allez supprimer un lot. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les inventaires rattachés à ce lot seront supprimés.</li>
        <li>Toutes les alertes bénévoles ouvertes rattachées à ce lot seront supprimées.</li>
        <li>Tous les sacs composant ce lot ne seront plus rattachés à aucun lot.</li>
        <li>Les rapports de consommation des bénévoles n'auront pas de lot de rattachement.</li>
    </ul>
</>;

export const materielsDelete = <>
    Attention, vous allez supprimer élément de matériel. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const messagesDelete = <>
    Attention, vous allez supprimer un message général. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const profilsDelete = <>
    Attention, vous allez supprimer un profil. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les utilisateurs qui bénéficient de ce profil seront déconnectés et devront se reconnecter sur tous leurs appareils.</li>
        <li>Les utilisateurs rattachés à ce profil n'auront plus de profil et ne pourront plus se connecter à moins qu'un autre profil ne leur en donne le droit.</li>
        <li>De part la perte de leurs habilitations, certains utilisateurs pourrons perdre leurs droits sur des centres de couts et des commandes.</li>
    </ul>
</>;

export const referentielsDelete = <>
    Attention, vous allez supprimer un référentiel. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Le détail du référentiel sera supprimé.</li>
        <li>Les lots rattachés à ce référentiel ne seront plus analysés pour conformité tant qu'ils ne seront pas rattachés à un nouveau référentiel.</li>
    </ul>
</>;

export const reserveConteneurDelete = <>
    Attention, vous allez supprimer un conteneur de réserve. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les inventaires du conteneur seront supprimés.</li>
        <li>Tous les éléments de matériels contenus dans le conteneur se verront sans conteneur de réserve (ils ne seront pas supprimés).</li>
        <li>Les fiches de consommation de matériel remontées par les bénévoles ne seront plus liées aux réserves</li>
    </ul>
</>;

export const reserveMaterielDelete = <>
    Attention, vous allez supprimer un matériel de réserve. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const sacsDelete = <>
    Attention, vous allez supprimer un sac. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les emplacements contenus dans le sac seront également supprimés.</li>
        <li>Le matériel contenu dans les emplacement sera sans sac ni lot mais ne sera pas supprimé.</li>
    </ul>
</>;

export const vehiculesDelete = <>
    Attention, vous allez supprimer un véhicule. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les lots rattachés à ce véhicule n'auront plus de véhicule de rattachement.</li>
        <li>Les documents liés à ce véhicule seront supprimés.</li>
        <li>Les taches de maitenance liées à ce véhicule seront supprimées.</li>
        <li>Les relevés kilométriques liés à ce véhicule seront supprimés.</li>
        <li>Les taches de désinfections liées à ce véhicule seront supprimées.</li>
        <li>Les taches de maintenance régulières liées à ce véhicule seront supprimées.</li>
        <li>Les alertes bénévoles liées à ce véhicule seront supprimées.</li>
    </ul>
</>;

export const vehiculesTypesDelete = <>
    Attention, vous allez supprimer un type de véhicule. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les véhicules liés à ce type se verront sans type de véhicules.</li>
    </ul>
</>;

export const vhfCanauxDelete = <>
    Attention, vous allez supprimer un canal radio. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les documents liés à ce canal seront supprimés.</li>
        <li>Ce canal sera supprimé de tous les plans de programmation dans lesquel il était programmé.</li>
    </ul>
</>;

export const vhfEquipementsDelete = <>
    Attention, vous allez supprimer un équipement radio. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les documents liés à cet équipement radio seront supprimés.</li>
        <li>Les accessoires liés à cet équipement radio seront supprimés.</li>
    </ul>
</>;

export const vhfPlansDelete = <>
    Attention, vous allez supprimer un plan de fréquence. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les documents liés à ce plan de fréquences seront supprimés.</li>
        <li>Tous les équipements radio bénéficiant de ce plan de fréquences se verront sans plan de fréquences.</li>
    </ul>
</>;

export const reserveInventaireDelete = <>
    Attention, vous allez supprimer un inventaire de réserve. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const vehiculesMaintenanceDelete = <>
    Attention, vous allez supprimer une maintenance de véhicule. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const vehiculesReleveDelete = <>
    Attention, vous allez supprimer un relevé kilométrique. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const vehiculesStockDelete = <>
    Attention, vous allez supprimer un élément du stock de consommables véhicules. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const lotsInventaireDelete = <>
    Attention, vous allez supprimer un inventaire de lot. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const tdlDelete = <>
    Attention, vous allez supprimer une tache. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const tenuesCatalogueDelete = <>
    Attention, vous allez supprimer une tenue du catalogue de matériel. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les éléments de tenue en stock seront supprimés.</li>
        <li>Tous les éléments de tenue qui sont affectés ne seront pas supprimés.</li>
    </ul>
</>;

export const tenuesAffectationsDelete = <>
    Un élément de tenue vous a été rendu ? Confirmez vous votre action ?
    <ul>
        <li>La tenue va être ré-intégrée dans le stock.</li>
        <li>L'affectation va être définitivement supprimée.</li>
    </ul>
</>;

export const tenuesAffectationsDefinitiveDelete = <>
    Un élément de tenue vous a été perdu ou jetée ? Confirmez vous votre action ?
    <ul>
        <li>La tenue ne pas être ré-intégrée dans le stock.</li>
        <li>L'affectation va être définitivement supprimée.</li>
    </ul>
</>;

export const cautionsDelete = <>
    Attention, vous allez supprimer une caution. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const etatsLotsDelete = <>
    Attention, vous allez supprimer un état de lot opérationnel. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les lots rattachés à cet état n'auront plus d'état de rattachement.</li>
    </ul>
</>;

export const etatsMaterielsDelete = <>
    Attention, vous allez supprimer un état de matériel. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les matériels rattachés à cet état n'auront plus d'état de rattachement.</li>
    </ul>
</>;

export const etatsVehiculesDelete = <>
    Attention, vous allez supprimer un état de véhicule. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les véhicules rattachés à cet état n'auront plus d'état de rattachement.</li>
    </ul>
</>;

export const vehiculesTypesDesinfectionsDelete = <>
    Attention, vous allez supprimer un type de désinfection de véhicule. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les désinfections rattachées à cet état seront supprimées.</li>
    </ul>
</>;

export const vehiculesDesinfectionsDelete = <>
    Attention, vous allez supprimer une désinfection de véhicule. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const vehiculesCarburantsDelete = <>
    Attention, vous allez supprimer un carburant. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les véhicules rattachés à ce carburant n'auront plus de carburant de spécifié.</li>
    </ul>
</>;

export const vehiculesPneumatiquesDelete = <>
    Attention, vous allez supprimer un type de pneumatiques. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les véhicules rattachés à ce type n'auront plus de type de pneumatique de spécifié (train avant et/ou arrière).</li>
    </ul>
</>;

export const vhfEquipementsAccessoiresDelete = <>
    Attention, vous allez supprimer un accessoire. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const vehiculesTypesMaintenanceDelete = <>
    Attention, vous allez supprimer type de maintenance ponctuelle de véhicule. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les tâches de maintenance rattachées à ce type seront non-spécifiées (sans type) mais ne seront pas supprimées.</li>
    </ul>
</>;

export const vehiculestypesHealthDelete = <>
    Attention, vous allez supprimer type de maintenance régulière de véhicule. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Les tâches de maintenance rattachées à ce type seront supprimées au sein des maintenances régulières.</li>
    </ul>
</>;

export const vehiculesHealthDelete = <>
    Attention, vous allez supprimer une maintenance régulière. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Le détail de cette maintenance sera supprimé.</li>
    </ul>
</>;

export const appliConfAESDrop = <>
    Attention, vous allez supprimer la clef de chiffrement AES. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Toutes les données chiffrées fournisseurs seront perdues.</li>
    </ul>
</>;

export const codesBarreDelete = <>
    Attention, vous allez supprimer un code barre. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les éléments possédant ce code barre ne seront plus reconnus lors des inventaires.</li>
    </ul>
</>;

export const lotsConsommationsDelete = <>
    Attention, vous allez supprimer un rapport de consommation. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const vhfEquipementsTypesDelete = <>
    Attention, vous allez supprimer un type d'équipement radio. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les équipements de ce type se verront être sans type.</li>
    </ul>
</>;

export const vhfAccessoiresTypesDelete = <>
    Attention, vous allez supprimer un type d'accessoire radio. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les accessoires de ce type se verront être sans type.</li>
    </ul>
</>;

export const vhfEtatsEquipementsDelete = <>
    Attention, vous allez supprimer un état d'équipement radio. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les équipements en cet état se verront être sans état.</li>
    </ul>
</>;

export const vhfTechnologiesDelete = <>
    Attention, vous allez supprimer une technologie radio. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Tous les équipements en cette technologie se verront être sans technologie.</li>
    </ul>
</>;

export const vhfStockDelete = <>
    Attention, vous allez supprimer un élément du stock de consommables transmissions. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;

export const commandeRegleMetierDelete = <>
    Attention, vous allez supprimer une règle métier relative au processus de commande. Les impacts collatéraux à cette suppression sont:
    <ul>
        <li>Aucun impact collatéral</li>
    </ul>
</>;
