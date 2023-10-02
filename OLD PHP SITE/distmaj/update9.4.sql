ALTER TABLE PERSONNE_REFERENTE ADD agenda_lots_peremption VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_reserves_peremption VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_lots_inventaireAF VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_lots_inventaireF VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_commandes_livraison VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_vehicules_revision VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_vehicules_ct VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_vehicules_assurance VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_vehicules_maintenance VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_desinfections_desinfectionF VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_desinfections_desinfectionAF VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_reserves_inventaireAF VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_reserves_inventaireF VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_tenues_tenues VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_tenues_toDoList VARCHAR(10);

UPDATE PERSONNE_REFERENTE SET agenda_lots_peremption = '#dd4b39';
UPDATE PERSONNE_REFERENTE SET agenda_reserves_peremption = '#dd4b39';
UPDATE PERSONNE_REFERENTE SET agenda_lots_inventaireAF = '#00c0ef';
UPDATE PERSONNE_REFERENTE SET agenda_lots_inventaireF = '#00c0ef';
UPDATE PERSONNE_REFERENTE SET agenda_commandes_livraison = '#00a65a';
UPDATE PERSONNE_REFERENTE SET agenda_vehicules_revision = '#f39c12';
UPDATE PERSONNE_REFERENTE SET agenda_vehicules_ct = '#f39c12';
UPDATE PERSONNE_REFERENTE SET agenda_vehicules_assurance = '#f39c12';
UPDATE PERSONNE_REFERENTE SET agenda_vehicules_maintenance = '#f39c12';
UPDATE PERSONNE_REFERENTE SET agenda_desinfections_desinfectionF = '#f39c12';
UPDATE PERSONNE_REFERENTE SET agenda_desinfections_desinfectionAF = '#f39c12';
UPDATE PERSONNE_REFERENTE SET agenda_reserves_inventaireAF = '#3c8dbc';
UPDATE PERSONNE_REFERENTE SET agenda_reserves_inventaireF = '#3c8dbc';
UPDATE PERSONNE_REFERENTE SET agenda_tenues_tenues = '#00a65a';
UPDATE PERSONNE_REFERENTE SET agenda_tenues_toDoList = '#3c8dbc';

ALTER TABLE CONFIG ADD vehicules_ct_delais_notif INT;
ALTER TABLE CONFIG ADD vehicules_revision_delais_notif INT;
ALTER TABLE CONFIG ADD vehicules_assurance_delais_notif INT;

UPDATE CONFIG SET vehicules_ct_delais_notif = 30, vehicules_revision_delais_notif = 30, vehicules_assurance_delais_notif = 30;

UPDATE CONFIG set version = '9.4';