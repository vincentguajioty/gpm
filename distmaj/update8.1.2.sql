UPDATE CONFIG set version = '8.1';

ALTER TABLE CENTRE_COUTS DROP FOREIGN KEY fk_couts_responsable;
ALTER TABLE CENTRE_COUTS DROP idResponsable;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 8.1.');