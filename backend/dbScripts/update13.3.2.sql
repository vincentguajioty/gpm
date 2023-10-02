ALTER TABLE PERSONNE_REFERENTE DROP conf_indicateur7Accueil;
ALTER TABLE PERSONNE_REFERENTE DROP conf_indicateur8Accueil;

ALTER TABLE CONFIG DROP vehicules_ct_delais_notif;
ALTER TABLE CONFIG DROP vehicules_revision_delais_notif;
ALTER TABLE CONFIG DROP vehicules_assurance_delais_notif;

ALTER TABLE VEHICULES DROP assuranceExpiration;
ALTER TABLE VEHICULES DROP dateNextRevision;
ALTER TABLE VEHICULES DROP dateNextCT;

UPDATE CONFIG set version = '13.3';