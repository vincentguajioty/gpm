UPDATE CONFIG set version = '2.6';

ALTER TABLE MATERIEL_SAC CHANGE couleur couleur VARCHAR(50);

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 2.6.');