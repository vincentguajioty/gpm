UPDATE CONFIG set version = '3.1';

UPDATE LOTS_LOTS set idEtat = 2 WHERE idEtat IS NULL;

ALTER TABLE COMMANDES_MATERIEL ADD quantiteAtransferer INT;
UPDATE COMMANDES_MATERIEL SET quantiteAtransferer=0;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 3.1.');