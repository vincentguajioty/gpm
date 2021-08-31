UPDATE CONFIG set version = '5.1';

UPDATE VEHICULES SET assuranceExpiration = Null WHERE assuranceExpiration = '0000-00-00';
ALTER TABLE LOTS_LOTS ADD alerteConfRef BOOLEAN;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 5.1.');