UPDATE CONFIG set version = '5.7';

ALTER TABLE CONFIG ADD maintenance BOOLEAN;
UPDATE CONFIG SET maintenance = 0;

ALTER TABLE PROFILS ADD maintenance BOOLEAN;
UPDATE PROFILS SET maintenance = 0;

ALTER TABLE PERSONNE_REFERENTE ADD notif_lots_manquants BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_lots_peremptions BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_lots_inventaires BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_lots_conformites BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_reserves_manquants BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_reserves_peremptions BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_reserves_inventaires BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_vehicules_assurances BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_vehicules_revisions BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_vehicules_ct BOOLEAN;
UPDATE PERSONNE_REFERENTE SET notif_lots_manquants = 1, notif_lots_peremptions = 1, notif_lots_inventaires = 1, notif_lots_conformites = 1, notif_reserves_manquants = 1, notif_reserves_peremptions = 1, notif_reserves_inventaires = 1, notif_vehicules_assurances = 1, notif_vehicules_revisions = 1, notif_vehicules_ct = 1;

ALTER TABLE PROFILS DROP notifications;

