UPDATE CONFIG set version = '4.2';

ALTER TABLE PERSONNE_REFERENTE ADD conf_joursCalendAccueil INT;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur1Accueil INT;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur2Accueil INT;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur3Accueil INT;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur4Accueil INT;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur5Accueil INT;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur6Accueil INT;
UPDATE PERSONNE_REFERENTE SET conf_joursCalendAccueil = 15, conf_indicateur1Accueil = 1, conf_indicateur2Accueil = 1, conf_indicateur3Accueil = 1, conf_indicateur4Accueil = 1, conf_indicateur5Accueil = 1, conf_indicateur6Accueil = 1;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 4.2.');