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

UPDATE CONFIG SET version = '15.0';