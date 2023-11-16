ALTER TABLE CONFIG DROP SMTPhost;
ALTER TABLE CONFIG DROP SMTPport;
ALTER TABLE CONFIG DROP SMTPssl;
ALTER TABLE CONFIG DROP SMTPtls;
ALTER TABLE CONFIG DROP SMTPauth;
ALTER TABLE CONFIG DROP SMTPuser;
ALTER TABLE CONFIG DROP SMTPpwd;
ALTER TABLE CONFIG DROP mailIsSMTP;
ALTER TABLE CONFIG DROP LDAP_DOMAIN;
ALTER TABLE CONFIG DROP LDAP_BASEDN;
ALTER TABLE CONFIG DROP LDAP_ISWINAD;
ALTER TABLE CONFIG DROP LDAP_SSL;
ALTER TABLE CONFIG DROP LDAP_USER;
ALTER TABLE CONFIG DROP LDAP_PASSWORD;
ALTER TABLE CONFIG DROP reCaptcha_enable;
ALTER TABLE CONFIG DROP reCaptcha_siteKey;
ALTER TABLE CONFIG DROP reCaptcha_secretKey;
ALTER TABLE CONFIG DROP reCaptcha_scoreMin;
ALTER TABLE CONFIG DROP verrouillage_ip_occurances;
ALTER TABLE CONFIG DROP verrouillage_ip_temps;
ALTER TABLE CONFIG DROP selPre;
ALTER TABLE CONFIG DROP selPost;
ALTER TABLE CONFIG DROP confirmationSuppression;
ALTER TABLE CONFIG DROP logouttemp;
ALTER TABLE CONFIG DROP sitecolor;

CREATE TABLE JWT_SESSIONS(
	idSession       INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idPersonne      INT,
	createdDateTime DATETIME,
	jwtToken        TEXT,
	jwtRefreshToken TEXT,
	tokenValidity   DATETIME,
	refreshValidity DATETIME,
	CONSTRAINT fk_sessions_personne
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne)
);

CREATE TABLE JWT_SESSIONS_BLACKLIST(
	idBlackList     INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	blockedDateTime DATETIME,
	jwtToken        TEXT
);

CREATE TABLE MAIL_QUEUE(
	idMailQueue  INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	typeMail     VARCHAR(20),
	idObject     INT,
	idSecondaire INT,
	lastTry      DATETIME,
	retryCounter INT DEFAULT 0
);

DROP TABLE NOTIFICATIONS_MAILS;

ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur1Accueil conf_indicateur1Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur2Accueil conf_indicateur2Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur3Accueil conf_indicateur3Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur4Accueil conf_indicateur4Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur5Accueil conf_indicateur5Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur6Accueil conf_indicateur6Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur9Accueil conf_indicateur9Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur10Accueil conf_indicateur10Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur11Accueil conf_indicateur11Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_indicateur12Accueil conf_indicateur12Accueil BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE conf_accueilRefresh conf_accueilRefresh BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_lots_manquants notif_lots_manquants BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_lots_peremptions notif_lots_peremptions BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_lots_inventaires notif_lots_inventaires BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_lots_conformites notif_lots_conformites BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_reserves_manquants notif_reserves_manquants BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_reserves_peremptions notif_reserves_peremptions BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_reserves_inventaires notif_reserves_inventaires BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_vehicules_assurances notif_vehicules_assurances BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_vehicules_revisions notif_vehicules_revisions BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_vehicules_desinfections notif_vehicules_desinfections BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_vehicules_ct notif_vehicules_ct BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_vehicules_health notif_vehicules_health BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_tenues_stock notif_tenues_stock BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_tenues_retours notif_tenues_retours BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_benevoles_lots notif_benevoles_lots BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE CHANGE notif_benevoles_vehicules notif_benevoles_vehicules BOOLEAN;

ALTER TABLE PERSONNE_REFERENTE DROP tableRowPerso;
ALTER TABLE PERSONNE_REFERENTE DROP layout;
ALTER TABLE PERSONNE_REFERENTE DROP conf_accueilRefresh;

ALTER TABLE PERSONNE_REFERENTE DROP agenda_lots_peremption;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_reserves_peremption;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_lots_inventaireAF;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_lots_inventaireF;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_commandes_livraison;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_vehicules_revision;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_vehicules_ct;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_vehicules_assurance;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_vehicules_maintenance;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_desinfections_desinfectionF;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_desinfections_desinfectionAF;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_reserves_inventaireAF;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_reserves_inventaireF;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_tenues_tenues;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_tenues_toDoList;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_healthF;
ALTER TABLE PERSONNE_REFERENTE DROP agenda_healthAF;

ALTER TABLE PERSONNE_REFERENTE DROP notif_vehicules_assurances;
ALTER TABLE PERSONNE_REFERENTE DROP notif_vehicules_revisions;
ALTER TABLE PERSONNE_REFERENTE DROP notif_vehicules_ct;

ALTER TABLE PERSONNE_REFERENTE CHANGE doubleAuthSecret mfaSecret TEXT;
ALTER TABLE PERSONNE_REFERENTE ADD COLUMN mfaEnabled BOOLEAN AFTER notifications_abo_cejour;
UPDATE PERSONNE_REFERENTE SET mfaEnabled = false;
UPDATE PERSONNE_REFERENTE SET mfaEnabled = true WHERE mfaSecret IS NOT NULL AND mfaSecret <> "";

ALTER TABLE LIEUX CHANGE adresseLieu adresseLieu TEXT;
ALTER TABLE LIEUX CHANGE detailsLieu detailsLieu TEXT;

UPDATE DOCUMENTS_CENTRE_COUTS SET urlFichierDocCouts    = REPLACE(urlFichierDocCouts,    'documents/centresCouts/', '');
UPDATE DOCUMENTS_COMMANDES    SET urlFichierDocCommande = REPLACE(urlFichierDocCommande, 'documents/commandes/', '');
UPDATE DOCUMENTS_VEHICULES    SET urlFichierDocVehicule = REPLACE(urlFichierDocVehicule, 'documents/vehicules/', '');
UPDATE DOCUMENTS_CANAL_VHF    SET urlFichierDocCanalVHF = REPLACE(urlFichierDocCanalVHF, 'documents/vhfCanaux/', '');
UPDATE DOCUMENTS_VHF          SET urlFichierDocVHF      = REPLACE(urlFichierDocVHF,      'documents/vhfEquipements/', '');
UPDATE DOCUMENTS_PLAN_VHF     SET urlFichierDocPlanVHF  = REPLACE(urlFichierDocPlanVHF,  'documents/vhfPlans/', '');

ALTER TABLE PROFILS CHANGE descriptifProfil descriptifProfil TEXT;

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
		MAX(sac2_lecture)                              as sac2_lecture,
		MAX(sac2_ajout)                                as sac2_ajout,
		MAX(sac2_modification)                         as sac2_modification,
		MAX(sac2_suppression)                          as sac2_suppression,
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
		MAX(verrouIP)                                  as verrouIP,
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

ALTER TABLE MESSAGES ADD isPublic BOOLEAN DEFAULT FALSE;
ALTER TABLE MESSAGES_TYPES DROP COLUMN iconMessageType;
UPDATE MESSAGES_TYPES SET couleurMessageType = 'info' WHERE idMessageType = 1;
UPDATE MESSAGES_TYPES SET couleurMessageType = 'warning' WHERE idMessageType = 2;
UPDATE MESSAGES_TYPES SET couleurMessageType = 'danger' WHERE idMessageType = 3;

ALTER TABLE TODOLIST DROP COLUMN realisee;

ALTER TABLE LOTS_LOTS DROP CONSTRAINT fk_lot_etat;
ALTER TABLE VEHICULES DROP CONSTRAINT fk_vehicules_etat;
RENAME TABLE ETATS TO NOTIFICATIONS_ENABLED;
ALTER TABLE NOTIFICATIONS_ENABLED CHANGE idEtat idNotificationEnabled INT;
ALTER TABLE NOTIFICATIONS_ENABLED CHANGE libelleEtat libelleNotificationEnabled VARCHAR(100);
ALTER TABLE NOTIFICATIONS_ENABLED ADD COLUMN notifiationEnabled BOOLEAN AFTER libelleNotificationEnabled;
UPDATE NOTIFICATIONS_ENABLED SET notifiationEnabled = true WHERE idNotificationEnabled = 1;
UPDATE NOTIFICATIONS_ENABLED SET notifiationEnabled = false WHERE idNotificationEnabled = 2;
ALTER TABLE LOTS_LOTS CHANGE idEtat idNotificationEnabled INT;
ALTER TABLE VEHICULES CHANGE idEtat idNotificationEnabled INT;
ALTER TABLE LOTS_LOTS ADD CONSTRAINT fk_lot_notif FOREIGN KEY(idNotificationEnabled) REFERENCES NOTIFICATIONS_ENABLED(idNotificationEnabled);
ALTER TABLE VEHICULES ADD CONSTRAINT fk_vehicules_notif FOREIGN KEY(idNotificationEnabled) REFERENCES NOTIFICATIONS_ENABLED(idNotificationEnabled);

ALTER TABLE VEHICULES DROP couleurGraph;

UPDATE VEHICULES_ALERTES_ETATS SET couleurVehiuclesAlertesEtat = 'danger' WHERE couleurVehiuclesAlertesEtat = 'red';
UPDATE VEHICULES_ALERTES_ETATS SET couleurVehiuclesAlertesEtat = 'info' WHERE couleurVehiuclesAlertesEtat = 'blue';
UPDATE VEHICULES_ALERTES_ETATS SET couleurVehiuclesAlertesEtat = 'success' WHERE couleurVehiuclesAlertesEtat = 'green';
UPDATE VEHICULES_ALERTES_ETATS SET couleurVehiuclesAlertesEtat = 'secondary' WHERE couleurVehiuclesAlertesEtat = 'gray';
UPDATE LOTS_ALERTES_ETATS SET couleurLotsAlertesEtat = 'danger' WHERE couleurLotsAlertesEtat = 'red';
UPDATE LOTS_ALERTES_ETATS SET couleurLotsAlertesEtat = 'info' WHERE couleurLotsAlertesEtat = 'blue';
UPDATE LOTS_ALERTES_ETATS SET couleurLotsAlertesEtat = 'success' WHERE couleurLotsAlertesEtat = 'green';
UPDATE LOTS_ALERTES_ETATS SET couleurLotsAlertesEtat = 'secondary' WHERE couleurLotsAlertesEtat = 'gray';

UPDATE CONFIG SET version = '15.0';