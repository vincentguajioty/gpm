DROP VIEW IF EXISTS VIEW_MATERIEL_CATALOGUE_OPE;
DROP VIEW IF EXISTS VIEW_MATERIEL_CATALOGUE_VEHICULES;
DROP VIEW IF EXISTS VIEW_MATERIEL_CATALOGUE_TENUES;
DROP VIEW IF EXISTS VIEW_MATERIEL_CATALOGUE_VHF;
DROP VIEW IF EXISTS VIEW_VEHICULES_KM;
DROP VIEW IF EXISTS VIEW_HABILITATIONS;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_COMMANDES;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_VEHICULES;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_CANAL_VHF;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_PLAN_VHF;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_VHF;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_CENTRE_COUTS_COMMANDES;
DROP VIEW IF EXISTS VIEW_PERSONNE_REFERENTE;

CREATE OR REPLACE VIEW VIEW_PERSONNE_REFERENTE AS
	SELECT
		*
	FROM
		PERSONNE_REFERENTE
	WHERE
		cnil_anonyme = 0
;

CREATE OR REPLACE VIEW VIEW_HABILITATIONS AS
	SELECT
		p.idPersonne,
		p.identifiant,
		p.nomPersonne,
		p.prenomPersonne,
		p.mailPersonne,
		p.telPersonne,
		p.fonction,
		p.derniereConnexion,
		p.cnil_anonyme,
		p.disclaimerAccept,
		p.isActiveDirectory,
		p.mfaEnabled,
		p.notifications_abo_cejour,
		p.notif_lots_manquants,
		p.notif_lots_peremptions,
		p.notif_lots_inventaires,
		p.notif_lots_conformites,
		p.notif_reserves_manquants,
		p.notif_reserves_peremptions,
		p.notif_reserves_inventaires,
		p.notif_vehicules_desinfections,
		p.notif_vehicules_health,
		p.notif_tenues_stock,
		p.notif_tenues_retours,
		p.notif_benevoles_lots,
		p.notif_benevoles_vehicules,
        p.notif_benevoles_vhf,
		p.notif_consommations_lots,
		MAX(connexion_connexion)                       as connexion_connexion,
		MAX(annuaire_lecture)                          as annuaire_lecture,
		MAX(annuaire_ajout)                            as annuaire_ajout,
		MAX(annuaire_modification)                     as annuaire_modification,
		MAX(annuaire_mdp)                              as annuaire_mdp,
		MAX(annuaire_suppression)                      as annuaire_suppression,
		MAX(profils_lecture)                           as profils_lecture,
		MAX(profils_ajout)                             as profils_ajout,
		MAX(profils_modification)                      as profils_modification,
		MAX(profils_suppression)                       as profils_suppression,
		MAX(categories_lecture)                        as categories_lecture,
		MAX(categories_ajout)                          as categories_ajout,
		MAX(categories_modification)                   as categories_modification,
		MAX(categories_suppression)                    as categories_suppression,
		MAX(fournisseurs_lecture)                      as fournisseurs_lecture,
		MAX(fournisseurs_ajout)                        as fournisseurs_ajout,
		MAX(fournisseurs_modification)                 as fournisseurs_modification,
		MAX(fournisseurs_suppression)                  as fournisseurs_suppression,
		MAX(typesLots_lecture)                         as typesLots_lecture,
		MAX(typesLots_ajout)                           as typesLots_ajout,
		MAX(typesLots_modification)                    as typesLots_modification,
		MAX(typesLots_suppression)                     as typesLots_suppression,
		MAX(lieux_lecture)                             as lieux_lecture,
		MAX(lieux_ajout)                               as lieux_ajout,
		MAX(lieux_modification)                        as lieux_modification,
		MAX(lieux_suppression)                         as lieux_suppression,
		MAX(lots_lecture)                              as lots_lecture,
		MAX(lots_ajout)                                as lots_ajout,
		MAX(lots_modification)                         as lots_modification,
		MAX(lots_suppression)                          as lots_suppression,
		MAX(sac_lecture)                               as sac_lecture,
		MAX(sac_ajout)                                 as sac_ajout,
		MAX(sac_modification)                          as sac_modification,
		MAX(sac_suppression)                           as sac_suppression,
		MAX(catalogue_lecture)                         as catalogue_lecture,
		MAX(catalogue_ajout)                           as catalogue_ajout,
		MAX(catalogue_modification)                    as catalogue_modification,
		MAX(catalogue_suppression)                     as catalogue_suppression,
		MAX(materiel_lecture)                          as materiel_lecture,
		MAX(materiel_ajout)                            as materiel_ajout,
		MAX(materiel_modification)                     as materiel_modification,
		MAX(materiel_suppression)                      as materiel_suppression,
		MAX(messages_ajout)                            as messages_ajout,
		MAX(messages_suppression)                      as messages_suppression,
		MAX(commande_lecture)                          as commande_lecture,
		MAX(commande_ajout)                            as commande_ajout,
		MAX(commande_etreEnCharge)                     as commande_etreEnCharge,
		MAX(commande_abandonner)                       as commande_abandonner,
		MAX(commande_valider_delegate)                 as commande_valider_delegate,
		MAX(cout_lecture)                              as cout_lecture,
		MAX(cout_ajout)                                as cout_ajout,
		MAX(cout_etreEnCharge)                         as cout_etreEnCharge,
		MAX(cout_supprimer)                            as cout_supprimer,
		MAX(appli_conf)                                as appli_conf,
		MAX(reserve_lecture)                           as reserve_lecture,
		MAX(reserve_ajout)                             as reserve_ajout,
		MAX(reserve_modification)                      as reserve_modification,
		MAX(reserve_suppression)                       as reserve_suppression,
		MAX(reserve_cmdVersReserve)                    as reserve_cmdVersReserve,
		MAX(reserve_ReserveVersLot)                    as reserve_ReserveVersLot,
		MAX(vhf_canal_lecture)                         as vhf_canal_lecture,
		MAX(vhf_canal_ajout)                           as vhf_canal_ajout,
		MAX(vhf_canal_modification)                    as vhf_canal_modification,
		MAX(vhf_canal_suppression)                     as vhf_canal_suppression,
		MAX(vhf_plan_lecture)                          as vhf_plan_lecture,
		MAX(vhf_plan_ajout)                            as vhf_plan_ajout,
		MAX(vhf_plan_modification)                     as vhf_plan_modification,
		MAX(vhf_plan_suppression)                      as vhf_plan_suppression,
		MAX(vhf_equipement_lecture)                    as vhf_equipement_lecture,
		MAX(vhf_equipement_ajout)                      as vhf_equipement_ajout,
		MAX(vhf_equipement_modification)               as vhf_equipement_modification,
		MAX(vhf_equipement_suppression)                as vhf_equipement_suppression,
		MAX(vehicules_lecture)                         as vehicules_lecture,
		MAX(vehicules_ajout)                           as vehicules_ajout,
		MAX(vehicules_modification)                    as vehicules_modification,
		MAX(vehicules_suppression)                     as vehicules_suppression,
		MAX(vehicules_types_lecture)                   as vehicules_types_lecture,
		MAX(vehicules_types_ajout)                     as vehicules_types_ajout,
		MAX(vehicules_types_modification)              as vehicules_types_modification,
		MAX(vehicules_types_suppression)               as vehicules_types_suppression,
		MAX(tenues_lecture)                            as tenues_lecture,
		MAX(tenues_ajout)                              as tenues_ajout,
		MAX(tenues_modification)                       as tenues_modification,
		MAX(tenues_suppression)                        as tenues_suppression,
		MAX(tenuesCatalogue_lecture)                   as tenuesCatalogue_lecture,
		MAX(tenuesCatalogue_ajout)                     as tenuesCatalogue_ajout,
		MAX(tenuesCatalogue_modification)              as tenuesCatalogue_modification,
		MAX(tenuesCatalogue_suppression)               as tenuesCatalogue_suppression,
		MAX(cautions_lecture)                          as cautions_lecture,
		MAX(cautions_ajout)                            as cautions_ajout,
		MAX(cautions_modification)                     as cautions_modification,
		MAX(cautions_suppression)                      as cautions_suppression,
		MAX(maintenance)                               as maintenance,
		MAX(todolist_perso)                            as todolist_perso,
		MAX(todolist_lecture)                          as todolist_lecture,
		MAX(todolist_modification)                     as todolist_modification,
		MAX(contactMailGroupe)                         as contactMailGroupe,
		MAX(etats_lecture)                             as etats_lecture,
		MAX(etats_ajout)                               as etats_ajout,
		MAX(etats_modification)                        as etats_modification,
		MAX(etats_suppression)                         as etats_suppression,
		MAX(notifications)                             as notifications,
		MAX(actionsMassives)                           as actionsMassives,
		MAX(delegation)                                as delegation,
		MAX(desinfections_lecture)                     as desinfections_lecture,
		MAX(desinfections_ajout)                       as desinfections_ajout,
		MAX(desinfections_modification)                as desinfections_modification,
		MAX(desinfections_suppression)                 as desinfections_suppression,
		MAX(typesDesinfections_lecture)                as typesDesinfections_lecture,
		MAX(typesDesinfections_ajout)                  as typesDesinfections_ajout,
		MAX(typesDesinfections_modification)           as typesDesinfections_modification,
		MAX(typesDesinfections_suppression)            as typesDesinfections_suppression,
		MAX(carburants_lecture)                        as carburants_lecture,
		MAX(carburants_ajout)                          as carburants_ajout,
		MAX(carburants_modification)                   as carburants_modification,
		MAX(carburants_suppression)                    as carburants_suppression,
		MAX(vehiculeHealthType_lecture)                as vehiculeHealthType_lecture,
		MAX(vehiculeHealthType_ajout)                  as vehiculeHealthType_ajout,
		MAX(vehiculeHealthType_modification)           as vehiculeHealthType_modification,
		MAX(vehiculeHealthType_suppression)            as vehiculeHealthType_suppression,
		MAX(vehiculeHealth_lecture)                    as vehiculeHealth_lecture,
		MAX(vehiculeHealth_ajout)                      as vehiculeHealth_ajout,
		MAX(vehiculeHealth_modification)               as vehiculeHealth_modification,
		MAX(vehiculeHealth_suppression)                as vehiculeHealth_suppression,
		MAX(alertesBenevolesLots_lecture)              as alertesBenevolesLots_lecture,
		MAX(alertesBenevolesLots_affectation)          as alertesBenevolesLots_affectation,
		MAX(alertesBenevolesLots_affectationTier)      as alertesBenevolesLots_affectationTier,
		MAX(alertesBenevolesVehicules_lecture)         as alertesBenevolesVehicules_lecture,
		MAX(alertesBenevolesVehicules_affectation)     as alertesBenevolesVehicules_affectation,
		MAX(alertesBenevolesVehicules_affectationTier) as alertesBenevolesVehicules_affectationTier,
    MAX(alertesBenevolesVHF_lecture)               as alertesBenevolesVHF_lecture,
		MAX(alertesBenevolesVHF_affectation)           as alertesBenevolesVHF_affectation,
		MAX(alertesBenevolesVHF_affectationTier)       as alertesBenevolesVHF_affectationTier,
		MAX(consommationLots_lecture)                  as consommationLots_lecture,
		MAX(consommationLots_affectation)              as consommationLots_affectation,
		MAX(consommationLots_supression)               as consommationLots_supression,
		MAX(codeBarre_lecture)                         as codeBarre_lecture,
		MAX(codeBarre_ajout)                           as codeBarre_ajout,
		MAX(codeBarre_modification)                    as codeBarre_modification,
		MAX(codeBarre_suppression)                     as codeBarre_suppression
	FROM PERSONNE_REFERENTE p
		LEFT JOIN PROFILS_PERSONNES pp ON pp.idPersonne = p.idPersonne
		LEFT JOIN PROFILS po ON pp.idProfil = po.idProfil
	GROUP BY
		p.idPersonne
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_COMMANDES AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_COMMANDES c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocCommande ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_CENTRE_COUTS_COMMANDES AS
	(
		SELECT
			c.*,
			t.libelleTypeDocument,
			Null as 'idDocCommande'
		FROM
			DOCUMENTS_CENTRE_COUTS c
			LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
		ORDER BY
			nomDocCouts ASC
	)
	UNION
	(
		SELECT
			Null as 'idDocCouts',
			cc.idCentreDeCout as 'idCentreDeCout',
			c.nomDocCommande as 'nomDocCouts',
			c.formatDocCommande as 'formatDocCouts',
			c.dateDocCommande as 'dateDocCouts',
			c.urlFichierDocCommande as 'urlFichierDocCouts',
			c.idTypeDocument as 'idTypeDocument',
			t.libelleTypeDocument as 'libelleTypeDocument',
			c.idDocCommande as 'idDocCommande'
		FROM
			DOCUMENTS_COMMANDES c
			LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
			LEFT OUTER JOIN COMMANDES cc ON c.idCommande = cc.idCommande
		ORDER BY
			nomDocCommande ASC
	)
;


CREATE OR REPLACE VIEW VIEW_DOCUMENTS_VEHICULES AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_VEHICULES c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocVehicule ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_CANAL_VHF AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_CANAL_VHF c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocCanalVHF ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_PLAN_VHF AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_PLAN_VHF c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocPlanVHF ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_VHF AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_VHF c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocVHF ASC
;

CREATE OR REPLACE VIEW VIEW_VEHICULES_KM AS
	(SELECT idReleve, idVehicule, dateReleve, releveKilometrique, idPersonne FROM VEHICULES_RELEVES)
	UNION
	(SELECT NULL as idReleve, idVehicule, dateMaintenance as dateReleve, releveKilometrique, idExecutant as idPersonne  FROM VEHICULES_MAINTENANCE WHERE releveKilometrique IS NOT NULL)
	UNION
	(SELECT NULL as idReleve, idVehicule, dateHealth as dateReleve, releveKilometrique, idPersonne  FROM VEHICULES_HEALTH WHERE releveKilometrique IS NOT NULL)
;

CREATE OR REPLACE VIEW VIEW_MATERIEL_CATALOGUE_OPE AS
	SELECT
		*
	FROM
		MATERIEL_CATALOGUE
	WHERE
        modules_ope = true
;

CREATE OR REPLACE VIEW VIEW_MATERIEL_CATALOGUE_VEHICULES AS
	SELECT
		*
	FROM
		MATERIEL_CATALOGUE
	WHERE
        modules_vehicules = true
;

CREATE OR REPLACE VIEW VIEW_MATERIEL_CATALOGUE_TENUES AS
	SELECT
		*
	FROM
		MATERIEL_CATALOGUE
	WHERE
        modules_tenues = true
;

CREATE OR REPLACE VIEW VIEW_MATERIEL_CATALOGUE_VHF AS
	SELECT
		*
	FROM
		MATERIEL_CATALOGUE
	WHERE
        modules_vhf = true
;
