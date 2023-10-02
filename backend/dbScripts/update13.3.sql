ALTER TABLE CONFIG ADD LDAP_USER TEXT AFTER LDAP_SSL;
ALTER TABLE CONFIG ADD LDAP_PASSWORD TEXT AFTER LDAP_USER;

INSERT INTO VEHICULES_HEALTH_TYPES SET libelleHealthType = 'Révision', affichageSynthese = 1;
INSERT INTO VEHICULES_HEALTH_TYPES SET libelleHealthType = 'CT', affichageSynthese = 1;
INSERT INTO VEHICULES_HEALTH_TYPES SET libelleHealthType = 'Renouvellement Assurance', affichageSynthese = 1;

ALTER TABLE PERSONNE_REFERENTE DROP conf_indicateur7Accueil;
ALTER TABLE PERSONNE_REFERENTE DROP conf_indicateur8Accueil;

ALTER TABLE CONFIG DROP vehicules_ct_delais_notif;
ALTER TABLE CONFIG DROP vehicules_revision_delais_notif;
ALTER TABLE CONFIG DROP vehicules_assurance_delais_notif;

ALTER TABLE VEHICULES DROP assuranceExpiration;
ALTER TABLE VEHICULES DROP dateNextRevision;
ALTER TABLE VEHICULES DROP dateNextCT;

UPDATE CONFIG set version = '13.3';