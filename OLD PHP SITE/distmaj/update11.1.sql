ALTER TABLE PERSONNE_REFERENTE ADD notif_benevoles_lots BOOLEAN AFTER notif_tenues_retours;
ALTER TABLE PERSONNE_REFERENTE ADD notif_benevoles_vehicules BOOLEAN AFTER notif_benevoles_lots;
UPDATE PERSONNE_REFERENTE SET notif_benevoles_lots = 1, notif_benevoles_vehicules = 1;

UPDATE CONFIG set version = '11.1';