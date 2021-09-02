ALTER TABLE TODOLIST ADD dateCloture DATETIME NULL AFTER dateExecution;

UPDATE CONFIG set version = '8.6';

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 8.6.');