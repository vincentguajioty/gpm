ALTER TABLE TENUES_AFFECTATION DROP FOREIGN KEY fk_tenuesAff_Catalogue;
ALTER TABLE TENUES_AFFECTATION DROP idCatalogueTenue;

ALTER TABLE TENUES_CATALOGUE DROP libelleCatalogueTenue;
ALTER TABLE TENUES_CATALOGUE DROP tailleCatalogueTenue;

UPDATE MATERIEL_CATALOGUE SET taille = null WHERE taille = "";
UPDATE MATERIEL_CATALOGUE SET conditionnementMultiple = null WHERE conditionnementMultiple = "";
UPDATE MATERIEL_CATALOGUE SET commentairesMateriel = null WHERE commentairesMateriel = "";

CREATE TABLE VEHICULES_STOCK(
	idVehiculesStock INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idMaterielCatalogue INT,
  idFournisseur INT,
  quantiteVehiculesStock INT,
  quantiteAlerteVehiculesStock INT,
  peremptionVehiculesStock DATE,
  peremptionAnticipationVehiculesStock INT,
  peremptionNotificationVehiculesStock DATE AS (DATE_SUB(peremptionVehiculesStock, INTERVAL peremptionAnticipationVehiculesStock DAY)),
  commentairesVehiculesStock TEXT,
	CONSTRAINT fk_vehStock_catalogue
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue),
    CONSTRAINT fk_vehStock_fournisseur
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur)
);
ALTER TABLE VEHICULES_STOCK ADD UNIQUE(idMaterielCatalogue);

CREATE TABLE VHF_STOCK(
	idVhfStock INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idMaterielCatalogue INT,
  idFournisseur INT,
  quantiteVhfStock INT,
  quantiteAlerteVhfStock INT,
  peremptionVhfStock DATE,
  peremptionAnticipationVhfStock INT,
  peremptionNotificationVhfStock DATE AS (DATE_SUB(peremptionVhfStock, INTERVAL peremptionAnticipationVhfStock DAY)),
  commentairesVhfStock TEXT,
	CONSTRAINT fk_vhfStock_catalogue
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue),
    CONSTRAINT fk_vhfStock_fournisseur
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur)
);
ALTER TABLE VHF_STOCK ADD UNIQUE(idMaterielCatalogue);




UPDATE
  TENUES_AFFECTATION t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.mailPersonneNonGPM = p.mailPersonne
WHERE
  t.idPersonne IS NOT NULL
;

UPDATE
  TENUES_AFFECTATION t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.personneNonGPM = CONCAT(p.nomPersonne, " ", p.prenomPersonne)
WHERE
  t.idPersonne IS NOT NULL
  AND p.nomPersonne IS NOT NULL
  AND p.prenomPersonne IS NOT NULL
;

UPDATE
  TENUES_AFFECTATION t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.personneNonGPM = p.identifiant
WHERE
  t.idPersonne IS NOT NULL
  AND t.personneNonGPM IS NULL
;

ALTER TABLE TENUES_AFFECTATION DROP CONSTRAINT fk_tenuesAff_Personne;
ALTER TABLE TENUES_AFFECTATION DROP idPersonne;


UPDATE
  CAUTIONS t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.mailPersonneNonGPM = p.mailPersonne
WHERE
  t.idPersonne IS NOT NULL
;

UPDATE
  CAUTIONS t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.personneNonGPM = CONCAT(p.nomPersonne, " ", p.prenomPersonne)
WHERE
  t.idPersonne IS NOT NULL
  AND p.nomPersonne IS NOT NULL
  AND p.prenomPersonne IS NOT NULL
;

UPDATE
  CAUTIONS t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.personneNonGPM = p.identifiant
WHERE
  t.idPersonne IS NOT NULL
  AND t.personneNonGPM IS NULL
;

ALTER TABLE CAUTIONS DROP CONSTRAINT fk_cautions_personnes;
ALTER TABLE CAUTIONS DROP idPersonne;


ALTER TABLE PROFILS ADD alertesBenevolesVHF_lecture BOOLEAN;
ALTER TABLE PROFILS ADD alertesBenevolesVHF_affectation BOOLEAN;
ALTER TABLE PROFILS ADD alertesBenevolesVHF_affectationTier BOOLEAN;

UPDATE PROFILS SET
	alertesBenevolesVHF_lecture     = 0,
	alertesBenevolesVHF_affectation = 0,
	alertesBenevolesVHF_affectationTier  = 0;

ALTER TABLE PERSONNE_REFERENTE ADD notif_benevoles_vhf BOOLEAN AFTER notif_benevoles_vehicules;
UPDATE PERSONNE_REFERENTE SET notif_benevoles_vhf = 0;

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

CREATE TABLE VHF_ALERTES_ETATS(
	idVHFAlertesEtat INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleVHFAlertesEtat TEXT,
	couleurVHFAlertesEtat VARCHAR(15)
);
INSERT INTO VHF_ALERTES_ETATS SET idVHFAlertesEtat = 1, libelleVHFAlertesEtat = 'Nouveau', couleurVHFAlertesEtat = 'danger';
INSERT INTO VHF_ALERTES_ETATS SET idVHFAlertesEtat = 2, libelleVHFAlertesEtat = 'En cours de traitement', couleurVHFAlertesEtat = 'info';
INSERT INTO VHF_ALERTES_ETATS SET idVHFAlertesEtat = 3, libelleVHFAlertesEtat = 'En attente', couleurVHFAlertesEtat = 'info';
INSERT INTO VHF_ALERTES_ETATS SET idVHFAlertesEtat = 4, libelleVHFAlertesEtat = 'Résolu', couleurVHFAlertesEtat = 'success';
INSERT INTO VHF_ALERTES_ETATS SET idVHFAlertesEtat = 5, libelleVHFAlertesEtat = 'Doublon', couleurVHFAlertesEtat = 'secondary';

CREATE TABLE VHF_ALERTES(
	idAlerte INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVHFAlertesEtat INT,
	dateCreationAlerte DATETIME,
	datePriseEnCompteAlerte DATETIME,
	dateResolutionAlerte DATETIME,
	nomDeclarant TEXT,
	mailDeclarant TEXT,
	ipDeclarant TEXT,
	idVhfEquipement INT,
	messageAlerteVHF TEXT,
	idTraitant INT,
	CONSTRAINT fk_alertesVHF_etat
		FOREIGN KEY (idVHFAlertesEtat)
		REFERENCES VHF_ALERTES_ETATS(idVHFAlertesEtat),
	CONSTRAINT fk_alertesVHF_VHF
		FOREIGN KEY (idVhfEquipement)
		REFERENCES VHF_EQUIPEMENTS(idVhfEquipement),
	CONSTRAINT fk_alertesVHF_traitant
		FOREIGN KEY (idTraitant)
		REFERENCES PERSONNE_REFERENTE(idPersonne));

ALTER TABLE CONFIG ADD alertes_benevoles_vhf BOOLEAN;
UPDATE CONFIG SET alertes_benevoles_vhf = 0;

ALTER TABLE VHF_EQUIPEMENTS ADD dispoBenevoles BOOLEAN;
UPDATE VHF_EQUIPEMENTS SET dispoBenevoles = 0;

CREATE TABLE PERSONNE_EXTERNE(
	idExterne INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	nomPrenomExterne VARCHAR(100),
	mailExterne VARCHAR(100)
);
ALTER TABLE PERSONNE_EXTERNE ADD UNIQUE(nomPrenomExterne, mailExterne);

ALTER TABLE TENUES_AFFECTATION ADD idExterne INT NULL AFTER idTenue;
ALTER TABLE TENUES_AFFECTATION ADD CONSTRAINT fk_externes_tenues FOREIGN KEY(idExterne) REFERENCES PERSONNE_EXTERNE(idExterne);
ALTER TABLE CAUTIONS ADD idExterne INT NULL AFTER idCaution;
ALTER TABLE CAUTIONS ADD CONSTRAINT fk_externes_cautions FOREIGN KEY(idExterne) REFERENCES PERSONNE_EXTERNE(idExterne);

INSERT INTO PERSONNE_EXTERNE (nomPrenomExterne, mailExterne)
SELECT
	allRecords.*
FROM    
	((
		SELECT DISTINCT
			personneNonGPM as nomPrenomExterne,
			mailPersonneNonGPM as mailExterne
		FROM
			TENUES_AFFECTATION
		WHERE
			personneNonGPM IS NOT NULL
	)
	UNION
	(
		SELECT DISTINCT
			personneNonGPM as nomPrenomExterne,
			mailPersonneNonGPM as mailExterne
		FROM
			CAUTIONS
		WHERE
			personneNonGPM IS NOT NULL
	)) allRecords
ORDER BY
	allRecords.nomPrenomExterne,
	allRecords.mailExterne
;

UPDATE
	TENUES_AFFECTATION t
    LEFT OUTER JOIN PERSONNE_EXTERNE e ON t.personneNonGPM = e.nomPrenomExterne
SET
	t.idExterne = e.idExterne,
	t.personneNonGPM = null,
	t.mailPersonneNonGPM = null
WHERE
	t.personneNonGPM IS NOT NULL
    AND t.mailPersonneNonGPM IS NOT NULL
    AND t.mailPersonneNonGPM = e.mailExterne
;
UPDATE
	TENUES_AFFECTATION t
    LEFT OUTER JOIN PERSONNE_EXTERNE e ON t.personneNonGPM = e.nomPrenomExterne
SET
	t.idExterne = e.idExterne,
	t.personneNonGPM = null
WHERE
	personneNonGPM IS NOT NULL
;
ALTER TABLE TENUES_AFFECTATION DROP personneNonGPM;
ALTER TABLE TENUES_AFFECTATION DROP mailPersonneNonGPM;

UPDATE
	CAUTIONS t
    LEFT OUTER JOIN PERSONNE_EXTERNE e ON t.personneNonGPM = e.nomPrenomExterne
SET
	t.idExterne = e.idExterne,
	t.personneNonGPM = null,
	t.mailPersonneNonGPM = null
WHERE
	t.personneNonGPM IS NOT NULL
    AND t.mailPersonneNonGPM IS NOT NULL
    AND t.mailPersonneNonGPM = e.mailExterne
;
UPDATE
	CAUTIONS t
    LEFT OUTER JOIN PERSONNE_EXTERNE e ON t.personneNonGPM = e.nomPrenomExterne
SET
	t.idExterne = e.idExterne,
	t.personneNonGPM = null
WHERE
	personneNonGPM IS NOT NULL
;
ALTER TABLE CAUTIONS DROP personneNonGPM;
ALTER TABLE CAUTIONS DROP mailPersonneNonGPM;

CREATE OR REPLACE VIEW VIEW_TENUES_AFFECTATION AS
	SELECT
		aff.*,
		ext.nomPrenomExterne,
		ext.mailExterne,
		tc.libelleMateriel,
		tc.taille
	FROM
		TENUES_AFFECTATION aff
		LEFT OUTER JOIN PERSONNE_EXTERNE ext ON aff.idExterne = ext.idExterne
		LEFT OUTER JOIN MATERIEL_CATALOGUE tc ON aff.idMaterielCatalogue = tc.idMaterielCatalogue
;

CREATE OR REPLACE VIEW VIEW_CAUTIONS AS
	SELECT
		cau.*,
		ext.nomPrenomExterne,
		ext.mailExterne
	FROM
		CAUTIONS cau
		LEFT OUTER JOIN PERSONNE_EXTERNE ext ON cau.idExterne = ext.idExterne
;

UPDATE CONFIG SET version = '16.0';